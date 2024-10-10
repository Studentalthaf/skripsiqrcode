<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade'); // Relasi dengan tabel events
            $table->string('nama_peserta');
            $table->string('instansi')->nullable();
            $table->text('signature')->nullable();  // Bisa berisi tanda tangan digital
            $table->string('logo')->nullable();  // Path untuk logo
            $table->string('serial_number')->unique();  // Serial unik untuk setiap peserta
            $table->string('key');  // Key untuk enkripsi (Base64)
            $table->string('nonce');  // Nonce untuk enkripsi
            $table->string('qrcode')->nullable();  // Path ke gambar QR Code yang dihasilkan
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
        Schema::dropIfExists('participants');
    }
}
