<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('locale');
            $table->text('content');
            $table->json('tags')->nullable();
            $table->timestamps();

            if (Schema::getConnection()->getDriverName() === 'mysql') {
                $table->index('key');
                $table->index('locale');
                $table->fullText('content');
                $table
                    ->string('tags_index')
                    ->virtualAs("JSON_UNQUOTE(JSON_EXTRACT(tags, '\$[0]'))")
                    ->stored();

                $table->index('tags_index');
            }


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
