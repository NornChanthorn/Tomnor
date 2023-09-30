<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('gender')->nullable();
            $table->smallInteger('age')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('id_card_number')->nullable();
            $table->string('first_phone')->nullable();
            $table->string('second_phone')->nullable();
            $table->string('email')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();

            // Link to address table
            $table->unsignedInteger('village_id')->nullable();
            $table->unsignedInteger('commune_id')->nullable();
            $table->unsignedInteger('district_id')->nullable();
            $table->unsignedInteger('province_id')->nullable();

            // Work info
            $table->string('company_name')->nullable();
            $table->string('company_position')->nullable();
            $table->date('start_working_date')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('building_number')->nullable();
            $table->double('salary')->nullable();
            $table->string('employee_id')->nullable();
            
            // Sponsor info
            $table->string('sponsor_name')->nullable();
            $table->string('sponsor_phone')->nullable();
            $table->string('sponsor_id_card')->nullable();
            $table->string('sponsor_work_card')->nullable();

            // Other info
            $table->string('family_name')->nullable();
            $table->string('family_phone')->nullable();
            $table->string('relative_name')->nullable();
            $table->string('relative_phone')->nullable();
            $table->string('friend_name')->nullable();
            $table->string('friend_phone')->nullable();
            
            // Related documents
            $table->string('profile_photo')->nullable();
            $table->string('id_card_photo')->nullable();
            $table->string('employee_card')->nullable();
            $table->string('salary_slip')->nullable();
            $table->string('related_document_1')->nullable();
            $table->string('related_document_2')->nullable();

            $table->string('type')->nullable();
            $table->text('note')->nullable();
            $table->boolean('is_default')->default(0);
            $table->boolean('is_active')->default(1);
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
        Schema::dropIfExists('clients');
    }
}
