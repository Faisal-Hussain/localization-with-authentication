<?php

namespace App\Http\Controllers;

use App\Http\Requests\TranslationRequest;
use App\Models\Translation;
use App\Repositories\TranslationRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    private TranslationRepository $repository;

    public function __construct(TranslationRepository $repository)
    {

        $this->repository = $repository;
    }

    // ✅ Create a new translation
    public function store(TranslationRequest $request): JsonResponse
    {

        $translation = $this->repository->create($request->validated());
        return response()->json(['message' => 'Translation created successfully', 'data' => $translation], 201);
    }

    // ✅ Update a translation
    public function update(TranslationRequest $request, Translation $translation): JsonResponse
    {
        $this->repository->update($translation, $request->validated());
        return response()->json(['message' => 'Translation updated successfully', 'data' => $translation]);
    }

    // ✅ Retrieve a translation by key & locale
    public function show(Request $request): JsonResponse
    {
        $translation = $this->repository->findByKeyAndLocale($request->key, $request->locale);

        if (!$translation) {
            return response()->json(['message' => 'Translation not found'], 404);
        }

        return response()->json(['data' => $translation]);
    }

    // ✅ Search translations by key, content, or tags
    public function search(Request $request): JsonResponse
    {
        $query = $request->query('q', '');
        $translations = $this->repository->search($query);
        return response()->json(['data' => $translations]);
    }
}
