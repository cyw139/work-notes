<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateExamsPaper extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_kspx')->table('exams_paper', function (Blueprint $table) {
            $table->bigInteger('admin_id')->default(0)->index()->comment('后台管理员ID');
            $table->softDeletes();
        });
        Schema::connection('mysql_kspx')->table('exams_paper', function (Blueprint $table) {
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
        Schema::connection('mysql_kspx')->table('exams_paper', function (Blueprint $table) {
            $table->dropColumn('admin_id');
            $table->dropSoftDeletes();
        });
    }
}
