<?php

namespace Tests\Unit;

use App\Models\Blog;
use App\Repositories\Blog\BlogRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlogRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected BlogRepository $blogRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->blogRepository = new BlogRepository();
    }

    /** @test */
    public function test_can_get_all_blogs()
    {
        Blog::factory()->count(5)->create();
        $blogs = $this->blogRepository->getAllBlogs();
        $this->assertEquals(5, $blogs->total());
    }

    /** @test */

    public function test_can_create_blog()
    {
        Storage::fake('public');

        // Create a user first
        $user = \App\Models\User::factory()->create();

        $data = [
            'title' => 'Test Blog',
            'description' => 'This is a test blog description.',
            'tags' => 'laravel,php',
            'image' => UploadedFile::fake()->image('blog.jpg')
        ];

        // Pass the correct user ID
        $blog = $this->blogRepository->createBlog($data, $user->id);

        $this->assertInstanceOf(Blog::class, $blog);
        $this->assertEquals($user->id, $blog->user_id); // Ensure blog belongs to the correct user

        Storage::disk('public')->assertExists($blog->image);
    }


    /** @test */
    public function test_can_get_blog_by_u_id()
    {
        $blog = Blog::factory()->create();
        $foundBlog = $this->blogRepository->getBlogByUId($blog->u_id, $blog->user_id);

        $this->assertNotNull($foundBlog);
        $this->assertEquals($blog->id, $foundBlog->id);
    }

    /** @test */
    public function test_can_update_blog()
    {
        $blog = Blog::factory()->create();
        $updatedData = ['title' => 'Updated Blog Title'];

        $updatedBlog = $this->blogRepository->updateBlog($blog, $updatedData, $blog->user_id);

        $this->assertEquals('Updated Blog Title', $updatedBlog->title);
    }

    /** @test */
    public function test_can_delete_blog()
    {
        Storage::fake('public');

        $blog = Blog::factory()->create(['image' => 'blog_images/sample.jpg']);

        $deleted = $this->blogRepository->deleteBlog($blog, $blog->user_id);

        $this->assertTrue($deleted);
        Storage::disk('public')->assertMissing('blog_images/sample.jpg');
    }
}
