<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PublicApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createLegacyTables();
    }

    public function test_home_endpoint_returns_legacy_content_blocks(): void
    {
        $this->seedContent();

        $this->getJson('/api/v1/home')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.featured_categories.0.slug', 'commercial-security')
            ->assertJsonPath('data.featured_posts.0.slug', 'related-post');
    }

    public function test_blog_detail_endpoint_returns_post_payload(): void
    {
        $this->seedContent();

        $this->getJson('/api/v1/blog/thanksgiving-auto-theft-alert')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', 'Thanksgiving Auto Theft Alert')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'slug',
                    'summary',
                    'content_html',
                    'card_image_url',
                    'banner_image_url',
                    'related_posts',
                ],
            ]);
    }

    public function test_course_detail_endpoint_includes_chapters_and_contents(): void
    {
        $this->seedContent();

        $this->getJson('/api/v1/courses/private-security-level-ii')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', 'Private Security Level II')
            ->assertJsonPath('data.chapters.0.title', 'Security basics')
            ->assertJsonPath('data.chapters.0.contents.0.title', 'Introduction');
    }

    public function test_contact_endpoint_persists_submission(): void
    {
        $this->postJson('/api/v1/contact', [
            'name' => 'Jane Applicant',
            'email' => 'jane@example.com',
            'phone' => '555-0100',
            'message' => 'I need more information about your services.',
            'origen' => 'test',
        ])
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', 1);

        $this->assertDatabaseHas('contacts', [
            'email' => 'jane@example.com',
            'origen' => 'test',
        ]);
    }

    private function createLegacyTables(): void
    {
        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->string('card')->nullable();
            $table->string('banner')->nullable();
            $table->integer('orden')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();
        });

        Schema::create('industries', function (Blueprint $table): void {
            $table->id();
            $table->string('titulo')->nullable();
            $table->string('slug')->nullable();
            $table->string('banner')->nullable();
            $table->string('card')->nullable();
            $table->text('contenido')->nullable();
            $table->integer('orden')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table): void {
            $table->id();
            $table->string('titulo')->nullable();
            $table->string('slug')->nullable();
            $table->string('card')->nullable();
            $table->string('banner')->nullable();
            $table->text('contenido')->nullable();
            $table->text('resumen')->nullable();
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table): void {
            $table->id();
            $table->string('titulo')->nullable();
            $table->string('subtitulo')->nullable();
            $table->string('slug')->nullable();
            $table->string('banner')->nullable();
            $table->string('video')->nullable();
            $table->text('resumen')->nullable();
            $table->text('contenido')->nullable();
            $table->decimal('precio', 8, 2)->nullable();
            $table->date('disponible')->nullable();
            $table->integer('capitulos')->nullable();
            $table->string('audio')->nullable();
            $table->string('nivel')->nullable();
            $table->string('language')->nullable();
            $table->string('responsable')->nullable();
            $table->integer('tiempovalido')->nullable();
            $table->unsignedBigInteger('certification_id')->nullable();
            $table->timestamps();
        });

        Schema::create('chapters', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->string('video')->nullable();
            $table->boolean('audio')->default(false);
            $table->boolean('reading')->default(false);
            $table->string('quiz')->nullable();
            $table->integer('order')->nullable();
            $table->timestamps();
        });

        Schema::create('chaptercontents', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('chapter_id')->nullable();
            $table->string('titulo')->nullable();
            $table->string('slug')->nullable();
            $table->string('video')->nullable();
            $table->string('audio')->nullable();
            $table->text('contenido')->nullable();
            $table->integer('order')->nullable();
            $table->timestamps();
        });

        Schema::create('chapter_quizzes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('chapter_id')->nullable();
            $table->text('question')->nullable();
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table): void {
            $table->id();
            $table->string('title')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->string('duration')->nullable();
            $table->text('excerpt')->nullable();
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('start_hour')->nullable();
            $table->string('slug')->nullable();
            $table->timestamps();
        });

        Schema::create('contacts', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('message')->nullable();
            $table->string('origen')->nullable();
            $table->timestamps();
        });
    }

    private function seedContent(): void
    {
        $now = now();

        $categoryId = \DB::table('categories')->insertGetId([
            'name' => 'Commercial Security',
            'slug' => 'commercial-security',
            'card' => 'card/category.jpg',
            'banner' => 'banner/category.jpg',
            'orden' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        \DB::table('industries')->insert([
            'titulo' => 'Auto Dealerships',
            'slug' => 'auto-dealerships',
            'contenido' => '<p>Industry content</p>',
            'category_id' => $categoryId,
            'orden' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        \DB::table('posts')->insert([
            [
                'titulo' => 'Thanksgiving Auto Theft Alert',
                'slug' => 'thanksgiving-auto-theft-alert',
                'resumen' => 'Holiday theft prevention tips',
                'contenido' => '<p>Blog body</p>',
                'card' => 'card/post.jpg',
                'banner' => 'banner/post.jpg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'titulo' => 'Related Post',
                'slug' => 'related-post',
                'resumen' => 'Related summary',
                'contenido' => '<p>Related body</p>',
                'card' => null,
                'banner' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $courseId = \DB::table('courses')->insertGetId([
            'titulo' => 'Private Security Level II',
            'slug' => 'private-security-level-ii',
            'resumen' => 'Course summary',
            'contenido' => '<p>Course body</p>',
            'banner' => 'banner/course.png',
            'precio' => 45,
            'nivel' => 'Beginner',
            'capitulos' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        \DB::table('courses')->insert([
            'titulo' => 'Related Course',
            'slug' => 'related-course',
            'resumen' => 'Related course summary',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $chapterId = \DB::table('chapters')->insertGetId([
            'course_id' => $courseId,
            'title' => 'Security basics',
            'slug' => 'security-basics',
            'order' => 1,
            'reading' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        \DB::table('chaptercontents')->insert([
            'chapter_id' => $chapterId,
            'titulo' => 'Introduction',
            'slug' => 'introduction',
            'contenido' => '<p>Intro content</p>',
            'order' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
