<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlaceholdersToEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->json('placeholders')->nullable()->after('template_pdf');
            // Opsional: Hapus kolom name_x dan name_y jika tidak diperlukan
            // $table->dropColumn(['name_x', 'name_y']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('placeholders');
            // Opsional: Tambahkan kembali name_x dan name_y jika dihapus
            // $table->integer('name_x')->nullable();
            // $table->integer('name_y')->nullable();
        });
    }
}