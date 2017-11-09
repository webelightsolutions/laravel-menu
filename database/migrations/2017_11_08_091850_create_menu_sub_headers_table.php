<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuSubHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_sub_headers', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('menu_header_id')->unsigned()->index();
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
        Schema::dropIfExists('menu_sub_headers');
    }
}
