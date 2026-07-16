<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('candidates', function (Blueprint $table) {
        $table->dropColumn('position'); // Hapus kolom string lama
        $table->foreignId('position_id')->after('name')->constrained('positions')->onDelete('cascade'); // Hubungkan ke tabel posisi
    });
}

public function down()
{
    Schema::table('candidates', function (Blueprint $table) {
        $table->dropForeign(['position_id']);
        $table->dropColumn('position_id');
        $table->string('position');
    });
}
};
