<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id(); // Kolom id (Primary Key)
            $table->unsignedBigInteger('user_id'); // Kolom user_id (Foreign Key)
            $table->string('title'); // Kolom title
            $table->text('description')->nullable(); // Kolom description, boleh null
            $table->date('date'); // Kolom date
            $table->string('type_event'); // Kolom type_event
            $table->timestamps(); // Kolom created_at dan updated_at

            // Menambahkan foreign key untuk user_id yang mengacu ke tabel users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
