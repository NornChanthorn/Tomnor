<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->comment('Link to extended_properties table')->after('name');
            $table->string('code')->nullable()->after('brand');
            $table->string('sku')->nullable()->after('code');
            $table->double('cost')->nullable()->after('quantity');
            $table->integer('alert_quantity')->nullable()->after('quantity');
            $table->boolean('promotion')->default(0)->after('price');
            $table->double('promotion_price')->nullable()->after('promotion');
            $table->date('promotion_start_date')->nullable()->after('promotion_price');
            $table->date('promotion_end_date')->nullable()->after('promotion_start_date');
            $table->double('tax')->nullable()->after('promotion_end_date');
            $table->boolean('active')->default(1)->comment('0 is inactive')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'category_id',
                'code',
                'sku',
                'cost',
                'alert_quantity',
                'promotion',
                'promotion_price',
                'promotion_start_date',
                'promotion_end_date',
                'tax',
                'active',
            ]);
        });
    }
}
