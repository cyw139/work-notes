<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CreateCourseware extends Migration
{
    protected $table = 'courseware';
    protected $model = null;
    protected $upyun_client = null;

    protected function getModel()
    {
        return Schema::connection('mysql_kspx');
    }

    protected function hasColumn($name)
    {
        return $this->getModel()->hasColumn($this->table, $name);
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 迁移表结构
        $this->migrateTable();

        // 迁移数据
        $this->migrateData();
    }

    protected function migrateTable()
    {
        DB::connection('mysql_kspx')->statement("ALTER TABLE `" . env('DB_KSPX_PREFIX') . "attach` engine=innodb"); // 修改附件表为innodb引擎
        // 创建：课件表
        $this->getModel()->create('courseware', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->id()->comment('课件ID');
            $table->char('title', 100)->nullable(false)->index()->comment('课件标题');
            $table->integer('uid')->default(0)->nullable(false)->index()->comment('前台用户ID');
            $table->bigInteger('admin_id')->default(0)->nullable(false)->index()->comment('后台管理员ID');
            $table->unsignedInteger('category')->default(0)->nullable(false)->comment('课件分类');
            $table->unsignedTinyInteger('status')->default(1)->nullable(false)->comment('状态：0禁用,1正常');
//            $table->unsignedInteger('duration')->default(0)->nullable(false)
//                ->comment('时长01:25:30==>课时60+25+30/60=85.5分钟==>学时85.5/45=1.900(保留4个小数点)');
            $table->unsignedInteger('sort')->default(0)->comment('排序：越大越靠前');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::connection('mysql_kspx')->statement("ALTER TABLE `" . env('DB_KSPX_PREFIX') . "courseware` comment'课件表'");
        // 修改：附件表
        $this->getModel()->table('attach', function (Blueprint $table) {
            $table->renameColumn('ctime', 'created_at');
            $table->integer('zy_video_data_id')->nullable()->default(0)->index()->comment('旧课件表ID：zy_video_data表');
            $table->tinyInteger('transcoding_status')->nullable()->default(-1)->index()->comment('转码状态【-1:未转码;0:转码失败;1:已经转码;2:等待转码;】');
            $table->tinyInteger('storage_type')->default(-1)->comment('存储位置：【-1:未知;0:本地;1:七牛;2:阿里云;4:cc储存;5:百度DOC服务,6:华为云储存,7:又拍云,8:外链】');
            $table->bigInteger('admin_id')->default(0)->nullable(false)->index()->comment('后台管理员ID');
            $table->tinyInteger('resource_type')->default(0)->nullable(false)
                ->comment('资源类型【0:未知;1:视频;2:音频;3:图片;4:文档;5:视频链接;】');
            $table->unsignedInteger('duration')->default(0)->nullable(false)->comment('时长');
            $table->text('meta')->nullable()->comment('资源meta信息');
            $table->integer('updated_at')->nullable()->default(null)->comment('更新时间');
            $table->softDeletes();
        });
        // 创建：课件附件关联表
        $this->getModel()->create('courseware_attach', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->id()->comment('课件附件关联ID');
            $table->unsignedBigInteger('courseware_id')->default(0)->nullable(false)->index()->comment('课件ID');
            $table->integer('attach_id')->default(0)->nullable(false)->index()->comment('附件ID');
            $table->char('alias', 100)->default('')->nullable(false)->comment('资源别名');
//            $table->unsignedInteger('duration')->default(0)->nullable(false)
//                ->comment('时长01:25:30==>课时60+25+30/60=85.5分钟==>学时85.5/45=1.900(保留4个小数点)');
            $table->unsignedInteger('sort')->default(0)->comment('排序：越大越靠前');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('attach_id')->references('attach_id')->on('attach');
            $table->foreign('courseware_id')->references('id')->on('courseware');
        });
        DB::connection('mysql_kspx')->statement("ALTER TABLE `" . env('DB_KSPX_PREFIX') . "courseware_attach` comment'课件附件关联表'");
    }

    protected function migrateData()
    {
        $this->upyun_client = Storage::disk('upyun_kspx_video');
        DB::beginTransaction();
        $model = DB::connection('mysql_kspx')->table('zy_video_data');
        $model->chunkById(200, function ($items) {
            DB::connection('mysql_kspx')->enableQueryLog();
            foreach ($items as $item) {
                if ($item->type !== 1 || $item->video_type !== 7) { // 视频1 - 又拍云7
                    continue;
                }
//                if ($item->is_del === 1) { // 已删除状态
//                    continue;
//                }
                $model_courseware = DB::connection('mysql_kspx')->table('courseware');
                $model_attach = DB::connection('mysql_kspx')->table('attach');
                $model_courseware_attach = DB::connection('mysql_kspx')->table('courseware_attach');
                // ①课件迁移
                if ($courseware = $model_courseware->find($item->id))  {
                    $courseware_id = $courseware->id;
                } else {
                    $courseware = $this->getCoursewareData($item);
                    $courseware_id = $model_courseware->insertGetId($courseware);
                    $model_courseware->insertOrIgnore([$courseware]);
                }

                // ②附件迁移 => 起始ID=47089（dev）
                $attach = $model_attach->where(['zy_video_data_id' => $item->id])->get();
                if (count($attach) > 0) {
                    $meta = $attach[0]->meta ? json_decode($attach[0]->meta, true) : [];
                    $duration = $attach[0]->duration;
                    if ($meta && $duration === 0) {
                        $model_attach->update(['duration' => $meta['format']['duration']]);
                    }
                    $attach_id = $attach[0]->attach_id;
                } else {
                    $attach = $this->getAttachData($item);
                    $attach_id = $model_attach->insertGetId($attach);
                }

                // ③课件附件关联
                $courseware_attach = $model_courseware_attach->where(['attach_id' => $attach_id, 'courseware_id' => $courseware_id])->get('id');
                if(count($courseware_attach) > 0) {
                    $courseware_attach_id = $courseware_attach[0]->id;
                } else {
                    $courseware_attach = $this->getCoursewareAttachData($item, $courseware_id, $attach_id);
                    $courseware_attach_id = $model_courseware_attach->insertGetId($courseware_attach);
                }
                if (!$courseware_attach_id) {
                    dd($item);
                }
            }
        });
        DB::commit();
    }

    protected function getCoursewareAttachData($item, $courseware_id, $attach_id): array
    {
        return [
            'courseware_id' => $courseware_id,
            'attach_id' => $attach_id,
            'alias' => '',
            'created_at' => date('y-m-d H:i:s', $item->ctime),
            'updated_at' => date('y-m-d H:i:s'),
        ];
    }

    protected function getAttachData($item): array
    {
        if ($item->type === 1 && $item->video_type === 7) { // 视频1 - 又拍云7
            return $this->getUpYunVideoData($item);
        }
    }

    protected function getUpYunVideoData($item): array
    {
        /**
         *
         *  ["https://video.ksaqpx.cn/21/06/10/60c18af6a9a02.m3u8"],
         *  ["21/06/10/"],
         *  ["60c18af6a9a02.m3u8"],
         *  ["m3u8"]
         */
        // 获取path、name、extension
        $pattern = '/https:\/\/video.ksaqpx.cn\/(\d{2}\/\d{2}\/\d{2}\/)+([a-zA-Z0-9]+\.(m3u8))$/';
        $flag = preg_match_all($pattern, $item->video_address, $matches);
        /**
         * path = 21/06/10/60c18af6a9a02.m3u8
         * meta
         * "streams": [
         * {
         * "bit_depth": 8,
         * "codec": "h264",
         * "codec_desc": "H.264 / AVC / MPEG-4 AVC / MPEG-4 part 10",
         * "index": 0,
         * "metadata": {
         * "variant_bitrate": "0"
         * },
         * "pix_fmt": "yuv420p",
         * "sample_aspect_ratio": "1:1",
         * "type": "video",
         * "video_fps": 25,
         * "video_height": 480,
         * "video_width": 840
         * },
         * {
         * "audio_channels": 2,
         * "audio_samplerate": 48000,
         * "codec": "aac",
         * "codec_desc": "AAC (Advanced Audio Coding)",
         * "index": 1,
         * "metadata": {
         * "variant_bitrate": "0"
         * },
         * "type": "audio"
         * }
         * ],
         * "format": {
         * "duration": 3027.639991,
         * "fullname": "Apple HTTP Live Streaming",
         * "filesize": 15603,
         * "format": "hls,applehttp"
         * }
         */
        // 获取视频meta信息
        $width = $height = $duration = 0;
        $extension = $save_path = $save_name = '';
        $avMeta = null;
        if ($flag) {
            $extension = $flag ? $matches[3][0] : '';
            $save_path = $flag ? $matches[1][0] : '';
            $save_name = $flag ? $matches[2][0] : '';
            $path = $matches[1][0] . $matches[2][0];
            $resourceExists = $this->upyun_client->exists($path);
            if ($resourceExists) {
                $avMeta = $this->upyun_client->avMeta($path);
                if ($avMeta && $avMeta['streams']) {
                    $width = $avMeta['streams'][0]['video_width'] ?? 0;
                    $height = $avMeta['streams'][0]['video_height'] ?? 0;
                    $duration = $avMeta['format']['duration'] ? $avMeta['format']['duration'] : 0;
                }
            }
        }
        return [
            'app_name' => '',
            'table' => '',
            'row_id' => 0,
            'type' => '',
            'attach_type' => 'courseware',
            'transcoding_status' => $item->transcoding_status,
            'uid' => $item->uid,
            'created_at' => $item->ctime,
            'updated_at' => time(),
            'deleted_at' => $item->is_del === 1 || !$flag || !$resourceExists || !$avMeta || $duration === 0  ? date('y-m-d H:i:s') : null,
            'name' => $item->title,
            'size' => $item->filesize,
            'extension' => $extension,
            'save_path' => $save_path,
            'save_name' => $save_name,
            'meta' => $flag && $avMeta ? json_encode($avMeta) : '',
            'width' => $width,
            'height' => $height,
            'duration' => $duration,
            'from' => 0,
            'resource_type' => $item->type,
            'storage_type' => $item->video_type,
            'zy_video_data_id' => $item->id,
        ];
    }

    protected function getCoursewareData($item): array
    {
        return [
            'id' => $item->id,
            'title' => $item->title,
            'uid' => $item->uid,
            'category' => $item->video_category,
            'status' => $item->status,
            'sort' => $item->sort,
            'created_at' => date('y-m-d H:i:s', $item->ctime),
            'updated_at' => date('y-m-d H:i:s'),
        ];
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        $this->getModel()->dropIfExists('courseware_attach');
//        $this->getModel()->dropIfExists('courseware');
//        DB::connection('mysql_kspx')->table('el_attach')->where(['attach_type' => 'courseware'])->delete();
//        $this->getModel()->table('attach', function (Blueprint $table) {
//            $table->renameColumn('created_at', 'ctime');
//            $table->dropColumn('zy_video_data_id');
//            $table->dropColumn('transcoding_status');
//            $table->dropColumn('storage_type');
//            $table->dropColumn('admin_id');
//            $table->dropColumn('resource_type');
//            $table->dropColumn('duration');
//            $table->dropColumn('meta');
//            $table->dropColumn('updated_at'); // 更新时间
//            $table->dropSoftDeletes();
//        });
//        DB::connection('mysql_kspx')->statement("ALTER TABLE `" . env('DB_KSPX_PREFIX') . "attach` engine=myisam");
    }
}
