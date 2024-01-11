<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCoursewareCategory extends Migration
{
    protected $table = 'zy_currency_category';
    protected $model = null;

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
        $this->getModel()->table($this->table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
        });
        $this->getModel()->table($this->table, function (Blueprint $table) {
            !$this->hasColumn('updated_at') && $table->timestamps();
            !$this->hasColumn('deleted_at') && $table->softDeletes();
            $table->integer('pid', false, false)->nullable()->change();
            $table->integer('zy_currency_category_id', true, false)->change();
            !$this->hasColumn('_lft') && $table->unsignedInteger('_lft');
            !$this->hasColumn('_rgt') && $table->unsignedInteger('_rgt');
        });

        \App\Models\Kspx\CoursewareCategory::fixTree();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->getModel()->table($this->table, function (Blueprint $table) {
            $table->dropTimestamps();
            $table->dropSoftDeletes();
            $table->dropColumn(['_lft', '_rgt']);
        });
    }
}
