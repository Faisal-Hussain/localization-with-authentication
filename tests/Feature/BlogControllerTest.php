<?php

use App\Models\Blog;
use App\Models\User;
use App\Repositories\Blog\BlogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Mockery;

class BlogControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;
    protected $blogRepository;

    /**
     * Set up test environment before each test case.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);

        Storage::fake('public');

        $this->blogRepository = Mockery::mock(BlogRepository::class);
        $this->app->instance(BlogRepository::class, $this->blogRepository);
    }

    /**
     * Get authentication headers for requests.
     */
    private function getAuthHeaders()
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    /**
     * Test fetching all blogs with pagination.
     */
    public function it_can_fetch_all_blogs()
    {
        $fakeBlogs = Blog::factory()->count(3)->make();

        $paginatedBlogs = new LengthAwarePaginator(
            $fakeBlogs,
            $fakeBlogs->count(),
            10,
            1
        );

        $this
            ->blogRepository
            ->shouldReceive('getAllBlogs')
            ->once()
            ->with(null, $this->user->id)
            ->andReturn($paginatedBlogs);

        $response = $this->withHeaders($this->getAuthHeaders())->getJson('/api/blogs');

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    /**
     * Test creating a new blog.
     */
    public function it_can_create_a_blog()
    {
        $fakeBlogData = [
            'title' => 'Mocked Blog',
            'description' => 'This is a mocked blog description',
            'tags' => 'mock,testing',
            'image' => UploadedFile::fake()->image('fake_image.jpg')
        ];

        $createdBlog = Blog::factory()->make([
            'title' => 'Mocked Blog',
            'description' => 'This is a mocked blog description',
            'tags' => 'mock,testing',
            'image' => 'fake_image.jpg',
            'user_id' => $this->user->id,
        ]);

        $this
            ->blogRepository
            ->shouldReceive('createBlog')
            ->once()
            ->with(Mockery::on(fn($arg) => $arg['title'] === 'Mocked Blog'), $this->user->id)
            ->andReturn($createdBlog);

        $response = $this->withHeaders($this->getAuthHeaders())->postJson('/api/blogs', $fakeBlogData);

        $response
            ->assertStatus(201)
            ->assertJson([
                'message' => 'Blog created successfully',
                'data' => [
                    'title' => 'Mocked Blog',
                    'description' => 'This is a mocked blog description',
                    'tags' => 'mock,testing'
                ]
            ]);
    }

    /**
     * Test fetching a single blog by UID.
     */
    public function it_can_fetch_a_single_blog()
    {
        $blog = Blog::factory()->make([
            'user_id' => $this->user->id,
            'u_id' => 'b9790b20-3a83-411a-b411-672ec0aad397'
        ]);

        $this
            ->blogRepository
            ->shouldReceive('getBlogByUId')
            ->once()
            ->with('b9790b20-3a83-411a-b411-672ec0aad397', $this->user->id)
            ->andReturn($blog);

        $response = $this->withHeaders($this->getAuthHeaders())->getJson("/api/blogs/{$blog->u_id}");

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'u_id' => (string) $blog->u_id,
                    'user_id' => $blog->user_id,
                    'title' => $blog->title,
                    'description' => $blog->description,
                    'tags' => $blog->tags,
                ]
            ]);
    }

    /**
     * test to update the blog
     */
    public function test_it_can_update_a_blog()
    {
        $blog = Blog::factory()->make([
            'user_id' => $this->user->id,
            'u_id' => 'b9790b20-3a83-411a-b411-672ec0aad3990'
        ]);
        $updatedData = ['title' => 'Updated Title', 'description' => 'Updated content'];

        $this
            ->blogRepository
            ->shouldReceive('getBlogByUId')
            ->once()
            ->with($blog->u_id, $this->user->id)
            ->andReturn($blog);

        $this
            ->blogRepository
            ->shouldReceive('updateBlog')
            ->once()
            ->with(
                Mockery::on(fn($arg) => $arg instanceof Blog && $arg->user_id === $this->user->id),
                Mockery::subset($updatedData),
                $this->user->id
            )
            ->andReturn(new Blog(array_merge($blog->toArray(), $updatedData)));

        $response = $this
            ->withHeaders($this->getAuthHeaders())
            ->postJson("/api/blogs/{$blog->u_id}", $updatedData);

        $response
            ->assertStatus(200)
            ->assertJson(['message' => 'Blog updated successfully']);
    }

    /**
     * Test deleting a blog.
     */
    public function it_can_delete_a_blog()
    {
        $blog = Blog::factory()->make([
            'user_id' => $this->user->id,
            'u_id' => 'b9790b20-3a83-411a-b411-672ec0aad876'  // Explicitly setting UUID
        ]);

        $this
            ->blogRepository
            ->shouldReceive('getBlogByUId')
            ->once()
            ->with($blog->u_id, $this->user->id)
            ->andReturn($blog);

        $this
            ->blogRepository
            ->shouldReceive('deleteBlog')
            ->once()
            ->with(Mockery::on(fn($arg) => $arg->u_id === $blog->u_id), $this->user->id)
            ->andReturn(true);

        $response = $this->withHeaders($this->getAuthHeaders())->deleteJson("/api/blogs/{$blog->u_id}");

        $response
            ->assertStatus(200)
            ->assertJson(['message' => 'Blog deleted successfully']);
    }

    /**
     * Clean up after each test case.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
