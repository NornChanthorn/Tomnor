<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSponsorFieldsToClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('sponsor_gender')->nullable()->after('sponsor_name');
            $table->string('sponsor_dob')->nullable()->after('sponsor_phone');
            $table->string('sponsor_phone_2')->nullable()->after('sponsor_phone');

            $table->string('sponsor_profile_photo')->nullable()->after('sponsor_work_card');
            $table->string('sponsor_id_card_photo')->nullable()->after('sponsor_work_card');

            $table->unsignedInteger('sponsor_village_id')->nullable()->after('sponsor_work_card');
            $table->unsignedInteger('sponsor_commune_id')->nullable()->after('sponsor_work_card');
            $table->unsignedInteger('sponsor_district_id')->nullable()->after('sponsor_work_card');
            $table->unsignedInteger('sponsor_province_id')->nullable()->after('sponsor_work_card');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'sponsor_gender',
                'sponsor_dob',
                'sponsor_phone_2',
                'sponsor_profile_photo',
                'sponsor_id_card_photo',
                'sponsor_village_id',
                'sponsor_commune_id',
                'sponsor_district_id',
                'sponsor_province_id',
            ]);
        });
    }
}
