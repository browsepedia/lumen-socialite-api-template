<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('email', 50)->unique();
            $table->string('name', 50);
            $table->string('password', 80)->nullable();
            $table->string('token', 350)->nullable();
            $table->string('provider', 20)->nullable();
            $table->string('imageUrl', 255)->nullable();

            $table->timestamp('lastLogin')->nullable();
            $table->timestamp('updatedAt')->nullable();
            $table->timestamp('createdAt')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
