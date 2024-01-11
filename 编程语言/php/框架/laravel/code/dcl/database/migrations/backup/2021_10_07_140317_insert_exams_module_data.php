<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class InsertExamsModuleData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $model = DB::connection('mysql_kspx')->table('exams_module');
        $model->updateOrInsert(['exams_module_id' => '-1'],
            ['exams_module_id' => '-1', 'title' => '课时考试', 'icon' => 1, 'btn_text' => '课时', 'is_practice' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $model = DB::connection('mysql_kspx')->table('exams_module');
        $model->where(['exams_module_id' => '-1'])->delete();
    }
}
