<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->unsignedInteger('branch_id')->nullable()->after('id');
            $table->unsignedInteger('village_id')->nullable()->after('position');
            $table->unsignedInteger('commune_id')->nullable()->after('position');
            $table->unsignedInteger('district_id')->nullable()->after('position');
            $table->unsignedInteger('province_id')->nullable()->after('position');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn(['branch_id', 'village_id', 'commune_id', 'district_id', 'province_id']);
        });
    }
}
