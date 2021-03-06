<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration auto-generated by Sequel Pro Laravel Export (1.5.0)
 *
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreateEmailBouncesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'email_bounces',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('email', 191)->index();
                $table->unsignedInteger('userId')->nullable();
                $table->longText('message');
                \Antriver\LaravelSiteUtils\Migrations\MigrationHelper::addCreatedAt($table);
                $table->enum('type', ['bounce', 'complaint'])->nullable();

                $table->foreign(['userId'], 'email_bounces_user')
                    ->references(['id'])
                    ->on('users')
                    ->onDelete('SET NULL')
                    ->onUpdate('CASCADE');

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
        Schema::dropIfExists('email_bounces');
    }
}
