<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->unsignedBigInteger('user_id'); // Foreign Key ke tabel users
            $table->unsignedBigInteger('event_id'); // Foreign Key ke tabel events
            $table->text('encrypted_data'); // Kolom untuk menyimpan data peserta yang dienkripsi
            $table->timestamps(); // Kolom created_at dan updated_at

            // Relasi ke tabel users (hanya role 'user' yang bisa masuk)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // Relasi event_id mengacu ke tabel events
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('participants');
    }
}
