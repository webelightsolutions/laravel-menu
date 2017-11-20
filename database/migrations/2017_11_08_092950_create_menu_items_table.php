<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('menu_sub_header_id')->unsigned()->index();
            $table->string('name');
            $table->boolean('is_parent')->default(true);
            $table->integer('position')->default(0);
            $table->enum('target', ['_blank', '_self', '_parent', '_top']);
            $table->longtext('url')->nullable();
            $table->string('route')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_items');
    }
}
