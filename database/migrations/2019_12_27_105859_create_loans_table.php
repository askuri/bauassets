<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('date_given')->nullable();
            $table->timestamp('date_returned')->nullable();
            $table->string('borrower_name');
            $table->integer('borrower_room');
            $table->unsignedBigInteger('issuer_user_id');
            $table->string('comment')->nullable();
            $table->timestamps();
            
            $table->foreign('issuer_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans');
    }
}
