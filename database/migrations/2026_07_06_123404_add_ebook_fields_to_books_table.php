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
        Schema::table('books', function (Blueprint $table) {
            $table->enum('book_type', [
                'physical',
                'ebook'
            ])->default('physical');
            $table->integer('pages')->nullable();
            $table->string('language')->default('English');
            $table->longText('description')->nullable();
            $table->unsignedInteger('download_count')->default(0);
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('ebook_file')->nullable(); // pdf/epub
            $table->string('publisher')->nullable();
            $table->year('publication_year')->nullable();
            $table->unsignedInteger('copies')->default(1);
            $table->unsignedInteger('available_copies')->default(1);
        });
    }

    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn([
                'book_type',
                'pages',
                'language',
                'download_count',
                'file_size',
                'ebook_file',
                'publisher',
                'publication_year',
                'description'
            ]);
        });
    }
};
