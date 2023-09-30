<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('loan_id');
            $table->date('payment_date');
            $table->double('principal');
            $table->float('interest')->nullable();
            $table->double('total')->nullable();
            $table->double('outstanding')->nullable();
            $table->double('penalty')->nullable();
            $table->date('paid_date')->nullable();
            $table->double('paid_principal')->nullable();
            $table->decimal('paid_interest')->nullable();
            $table->double('paid_total')->nullable();
            $table->double('paid_penalty')->nullable();
            $table->boolean('paid_status')->default(0);
            $table->boolean('discount_status')->default(0);
            $table->timestamps();
            
            $table->foreign('loan_id')->references('id')->on('loans')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedules');
    }
}
