<?php

namespace App\Repositories;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Collection;

class TranslationRepository
{
    public function create(array $data): Translation
    {
        return Translation::create($data);
    }

    public function update(Translation $translation, array $data): bool
    {
        return $translation->update($data);
    }

    public function findByKeyAndLocale(string $key, string $locale): ?Translation
    {
        return Translation::where('key', $key)->where('locale', $locale)->first();
    }

    public function search(string $query): Collection
    {
        return Translation::where('content', 'LIKE', "%$query%")
            ->orWhereJsonContains('tags', $query)
            ->get();
    }
}

