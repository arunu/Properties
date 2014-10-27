<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateValuesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('values', function(Blueprint $table)
        {
            $table->increments('id');

            $table->integer('property_id');

            $table->string('value')->nullable();

            $table->string('entity_type');
            $table->integer('entity_id');

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
		Schema::drop('values');
	}

}
