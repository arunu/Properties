<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('properties', function(Blueprint $table)
        {
            $table->increments('id');

            $table->string('type');

            $table->boolean('multiple');

            $table->string('entity');

            $table->string('name');
            $table->string('label');
            $table->string('placeholder');
            $table->string('help_block');

            $table->integer('property_category_id');

            $table->timestamps();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('properties');
	}

}
