<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCourseware extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_kspx')->table('zy_video_data', function (Blueprint $table) {
            $table->bigInteger('admin_id')->default(0)->index()->comment('后台管理员ID');
            $table->softDeletes();
            $table->integer('utime')->comment('更新时间');
        });
        Schema::connection('mysql_kspx')->table('zy_video_data', function (Blueprint $table) {
            $table->unsignedInteger('deleted_at')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_kspx')->table('zy_video_data', function (Blueprint $table) {
            $table->dropColumn('admin_id');
            $table->dropSoftDeletes();
            $table->dropColumn('utime'); // 更新时间
        });
    }
}
