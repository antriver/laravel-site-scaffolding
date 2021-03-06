<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration auto-generated by Sequel Pro Laravel Export (1.5.0)
 *
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreateEmailVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'email_verifications',
            function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('userId');
                $table->string('email', 191)->index();
                $table->string('token', 64)->unique();
                $table->enum('type', ['signup', 'change', 'reverify']);
                \Antriver\LaravelSiteUtils\Migrations\MigrationHelper::addCreatedAt($table);
                $table->dateTime('resentAt')->nullable();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_verifications');
    }
}
