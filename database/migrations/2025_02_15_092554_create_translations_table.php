<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('locale', 5);
            $table->text('content');
            $table->json('tags')->nullable();
            $table->timestamps();

            $table->index(['key', 'locale']);
            $table->fullText('content');
            $table->string('tag_search')->storedAs("JSON_UNQUOTE(JSON_EXTRACT(tags, '$[0]'))")->nullable();
            $table->index('tag_search');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
