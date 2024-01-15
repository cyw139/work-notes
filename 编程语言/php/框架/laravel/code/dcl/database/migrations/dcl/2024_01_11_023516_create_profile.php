<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfile extends Migration
{
    protected $table = 'profile';
    protected $model = null;
    protected function getModel()
    {
        return Schema::connection('mysql_dcl_fb_auto');
    }
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->getModel()->create($this->table, function (Blueprint $table) {
            $table->id();
            $table->integer('ixb_profile_id')->default(0)->comment('ixb_browser profile_id');
            $table->bigInteger('fb_account_id')->default(0)->comment('fb账号ID');
            $table->string('fb_account_name', 50)->default('')->comment('fb账号名称');
            $table->string('mobile', 20)->default('')->comment('手机号码');
            $table->string('gender', 20)->default('')->comment('性别');
            $table->string('email', 100)->default('')->comment('email');
            $table->string('birth_date', 20)->default('')->comment('出生月份和日期');
            $table->string('birth_year', 20)->default('')->comment('出生年份');
            $table->string('categories', 255)->default('')->comment('用户类别');
            $table->string('college', 100)->default('')->comment('大学');
            $table->string('current_city', 100)->default('')->comment('当前城市');
            $table->string('high_school', 100)->default('')->comment('中学');
            $table->string('hometown', 100)->default('')->comment('家乡');
            $table->string('work_city_or_town', 100)->default('')->comment('工作所在城市或乡村');
            $table->string('work_company', 100)->default('')->comment('就职企业');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->getModel()->dropIfExists($this->table);
    }
}
