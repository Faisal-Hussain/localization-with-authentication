<?php

namespace App\Http\Controllers;

use App\Http\Requests\TranslationRequest;
use App\Models\Translation;
use App\Repositories\TranslationRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Storage;

/**
 * TranslationController handles all translation-related API requests.
 *
 * This controller follows the SOLID principles by delegating business logic to a repository
 * and providing clear, maintainable methods for managing translations.
 */
class TranslationController extends Controller
{
    /**
     * @var TranslationRepository $repository
     * Dependency Injection to utilize the TranslationRepository.
     */
    private TranslationRepository $repository;

    /**
     * TranslationController constructor.
     *
     * @param TranslationRepository $repository
     */
    public function __construct(TranslationRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Store a new translation in the database.
     *
     * This method receives validated data from the request and stores a new translation.
     * The API returns a success message with the created translation.
     *
     * @param TranslationRequest $request
     * @return JsonResponse
     */
    public function store(TranslationRequest $request): JsonResponse
    {
        $translation = $this->repository->create($request->validated());

        return ApiResponse::success($translation, 'Translation created successfully', 201);
    }

    /**
     * Update an existing translation in the database.
     *
     * This method updates the translation based on the provided request data.
     * It returns a success message with the updated translation.
     *
     * @param TranslationRequest $request
     * @param Translation $translation
     * @return JsonResponse
     */
    public function update(TranslationRequest $request, Translation $translation): JsonResponse
    {
        $this->repository->update($translation, $request->validated());

        return ApiResponse::success($translation, 'Translation updated successfully');
    }

    /**
     * Retrieve a translation by its key and locale.
     *
     * This method returns the translation based on the provided key and locale.
     * If no translation is found, it returns an error response.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        $translation = $this->repository->findByKeyAndLocale($request->key, $request->locale);
        if (!$translation) {
            return ApiResponse::error('Translation not found', 404);
        }

        return ApiResponse::success($translation);
    }

    /**
     * Search for translations based on key, content, or tags.
     *
     * This method performs a search query on translations using the provided search term.
     * It returns a collection of translations that match the search criteria.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->query('search', '');
        $translations = $this->repository->search($query);

        return ApiResponse::success($translations);
    }


    /**
     * Export all translations to a JSON file and return a download link.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function exportTranslations()
    {
        $translations = $this->repository->getAllTranslations();

        return ApiResponse::success($translations);
    }
}
