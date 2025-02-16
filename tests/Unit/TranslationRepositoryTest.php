<?php

namespace Tests\Unit;

use App\Models\Translation;
use App\Repositories\TranslationRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

/**
 * Unit test class for the TranslationRepository.
 *
 * This class contains various tests to verify the functionality of the TranslationRepository class.
 * It ensures that translations can be created, updated, searched, and retrieved from the database correctly.
 * The tests are written to mock database interactions where needed, allowing the logic of the repository
 * to be isolated and tested independently.
 */
class TranslationRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected TranslationRepository $translationRepository;

    /**
     * Setup the necessary preconditions for the test class.
     * This is run before each test function is executed.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->translationRepository = Mockery::mock(TranslationRepository::class);
    }

    /**
     * Test case to ensure that a translation can be successfully created.
     */
    /** @test */
    public function test_can_create_translation()
    {
        $data = [
            'key' => 'greeting',
            'locale' => 'en',
            'content' => 'Hello, World!',
            'tags' => ['greeting', 'english'],
        ];

        $this->mock(TranslationRepository::class, function ($mock) use ($data) {
            $mock
                ->shouldReceive('create')
                ->once()
                ->with($data)
                ->andReturnUsing(function ($data) {
                    return Translation::create($data);
                });
        });

        $translation = app(TranslationRepository::class)->create($data);

        $this->assertInstanceOf(Translation::class, $translation);
        $this->assertEquals('greeting', $translation->key);
        $this->assertEquals('en', $translation->locale);
        $this->assertEquals(['greeting', 'english'], $translation->tags);
    }

    /**
     * Test case to verify that a translation can be updated.
     */
    /** @test */
    public function test_can_update_translation()
    {
        $translation = Translation::factory()->create();
        $updatedData = ['content' => 'Updated content for translation'];

        $this->translationRepository->shouldReceive('update')->with($translation, $updatedData)->andReturn(true);

        $updatedTranslation = $this->translationRepository->update($translation, $updatedData);

        $this->assertTrue($updatedTranslation);
    }

    /**
     * Test case to verify that a translation can be found by its key and locale.
     */
    /** @test */
    public function test_can_find_translation_by_key_and_locale()
    {
        $translation = Translation::factory()->create();

        $this->translationRepository->shouldReceive('findByKeyAndLocale')->with($translation->key, $translation->locale)->andReturn($translation);

        $foundTranslation = $this->translationRepository->findByKeyAndLocale($translation->key, $translation->locale);

        $this->assertNotNull($foundTranslation);
        $this->assertEquals($translation->key, $foundTranslation->key);
        $this->assertEquals($translation->locale, $foundTranslation->locale);
    }

    /**
     * Test case to verify the search functionality of translations.
     */
    /** @test */
    public function test_can_search_translations()
    {
        Translation::factory()->create(['content' => 'Hello, World!']);
        Translation::factory()->create(['content' => 'Bonjour, le monde!']);
        Translation::factory()->create(['content' => 'Hola, Mundo!']);

        $this->translationRepository->shouldReceive('search')->with('Hello')->andReturn(Translation::where('content', 'Hello, World!')->get());

        $translations = $this->translationRepository->search('Hello');

        $this->assertEquals(1, $translations->count());
        $this->assertEquals('Hello, World!', $translations->first()->content);
    }

    /**
     * Test case to verify that all translations can be retrieved.
     */
    /** @test */
    public function test_can_get_all_translations()
    {
        Translation::factory()->count(5)->create();

        $this->translationRepository->shouldReceive('getAllTranslations')->andReturn(Translation::all());

        $translations = $this->translationRepository->getAllTranslations();

        $this->assertEquals(5, $translations->count());
    }

    /**
     * Test case to verify that a non-existing translation returns null when searched.
     */
    /** @test */
    public function test_can_find_translation_by_key_and_locale_when_not_found()
    {
        $this->translationRepository->shouldReceive('findByKeyAndLocale')->with('nonexistent_key', 'en')->andReturn(null);

        $translation = $this->translationRepository->findByKeyAndLocale('nonexistent_key', 'en');

        $this->assertNull($translation);
    }
}
