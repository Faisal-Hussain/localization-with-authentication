<?php

namespace App\Repositories;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
/**
 * TranslationRepository handles data access logic related to translations.
 *
 * This repository encapsulates the logic for interacting with the `translations` table.
 * It provides methods for creating, updating, searching, and retrieving translations
 * based on key and locale. It follows the repository pattern to separate concerns
 * and make data access logic reusable and testable.
 */
class TranslationRepository
{
    /**
     * Create a new translation record in the database.
     *
     * This method takes an array of data and creates a new `Translation` instance
     * in the database, returning the newly created translation.
     *
     * @param array $data
     * @return Translation
     */
    public function create(array $data): Translation
    {
        return Translation::create($data);
    }

    /**
     * Update an existing translation record in the database.
     *
     * This method updates the given `Translation` instance with the provided data
     * and returns a boolean indicating whether the update was successful.
     *
     * @param Translation $translation
     * @param array $data
     * @return bool
     */
    public function update(Translation $translation, array $data): bool
    {
        return $translation->update($data);
    }

    /**
     * Find a translation by its key and locale.
     *
     * This method retrieves a translation record from the database based on the
     * provided `key` and `locale`. It returns the first matching record or null
     * if no match is found.
     *
     * @param string $key
     * @param string $locale
     * @return Translation|null
     */
    public function findByKeyAndLocale(string $key, string $locale): ?Translation
    {
        return Translation::where('key', $key)
            ->where('locale', $locale)
            ->first();
    }

    /**
     * Search for translations by content, key, or tags.
     *
     * This method performs a search for translations based on the provided query.
     * It searches within the `content` field, the `key`, and the `tags` field (both
     * the JSON column and the indexed column). The search returns a collection of
     * matching translations.
     *
     * @param string $query
     * @return Collection
     */
    public function search(string $query): Collection
    {
        return Translation::whereFullText('content', $query)
            ->orWhere('tags_index', $query)
            ->orWhereRaw('JSON_CONTAINS(tags, ?)', [json_encode($query)])
            ->orWhere('key', $query)
            ->select('id', 'key', 'locale', 'content', 'tags')
            ->limit(100)
            ->get();
    }
    /**
     * Fetch complete record in translation table .
     * @return Collection
     */

    public function getAllTranslations()
    {
        return DB::table('translations')
        ->select('id', 'key', 'locale', 'content', 'tags')
        ->get();
    }
}
