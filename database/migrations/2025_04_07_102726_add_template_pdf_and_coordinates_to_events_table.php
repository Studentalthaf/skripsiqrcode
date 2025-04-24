<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTemplatePdfAndCoordinatesToEventsTable extends Migration
{
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('template_pdf')->nullable()->after('logo');
            $table->integer('name_x')->nullable()->after('template_pdf');
            $table->integer('name_y')->nullable()->after('name_x');
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['template_pdf', 'name_x', 'name_y']);
        });
    }
}
