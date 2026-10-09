<?php

namespace Tests\Feature;

use App\Exceptions\CartException;
use App\Models\Course;
use App\Models\User;
use App\Models\UserCourse;
use App\Services\CourseAccessService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Reglas del curso del tablero TAP Security (fichas #525 y #526). El esquema es el
 * heredado del sitio anterior, sin migraciones, así que se crea a mano lo mínimo.
 */
class CourseRulesTest extends TestCase
{
    private User $user;

    private Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createLegacyTables();
        $this->seedCourse();
    }

    public function test_exactly_75_percent_passes_the_chapter_quiz(): void
    {
        $this->enroll();
        Sanctum::actingAs($this->user);

        // 4 preguntas, 3 correctas = 75.00 %
        $answers = DB::table('chapter_quiz_options')->where('estado', 1)->orderBy('id')->limit(3)->get();
        foreach ($answers as $option) {
            $this->postJson('/api/v1/learn/courses/basic/chapters/chapter-1/quiz', [
                'chapter_quiz_id' => $option->chapter_quiz_id,
                'chapter_quiz_option_id' => $option->id,
            ])->assertOk();
        }
        $wrong = DB::table('chapter_quiz_options')->where('chapter_quiz_id', 4)->where('estado', 0)->first();
        $response = $this->postJson('/api/v1/learn/courses/basic/chapters/chapter-1/quiz', [
            'chapter_quiz_id' => 4,
            'chapter_quiz_option_id' => $wrong->id,
        ])->assertOk();

        $this->assertSame(75.0, (float) $response->json('data.percentage'));
        $this->assertTrue($response->json('data.passed'));
    }

    public function test_second_chapter_is_locked_until_first_quiz_is_passed(): void
    {
        $this->enroll();
        Sanctum::actingAs($this->user);

        $this->getJson('/api/v1/learn/courses/basic/chapters/chapter-2/contents/content-2')
            ->assertStatus(403)
            ->assertJsonFragment(['message' => 'You need to pass the quiz of "Chapter 1" before continuing to this chapter.']);

        $this->getJson('/api/v1/learn/courses/basic/chapters/chapter-2/quiz')->assertStatus(403);

        $this->completeChapterOne();

        $this->getJson('/api/v1/learn/courses/basic/chapters/chapter-2/contents/content-2')->assertOk();
    }

    public function test_exam_grades_against_every_question_not_just_the_answers_sent(): void
    {
        $this->enroll();
        Sanctum::actingAs($this->user);
        $this->completeCourseContents();

        $correct = DB::table('exam_question_options')->where('resultado', 1)->orderBy('id')->first();

        $response = $this->postJson('/api/v1/learn/courses/basic/exam', [
            'answers' => [['exam_question_id' => $correct->exam_question_id, 'exam_question_option_id' => $correct->id]],
        ])->assertOk();

        $this->assertFalse($response->json('data.passed'));
        $this->assertSame(25.0, (float) $response->json('data.percentage'));
        $this->assertSame(4, $response->json('data.total_questions'));
        $this->assertSame(1, $response->json('data.attempts_used'));
    }

    public function test_third_failure_locks_the_exam_and_offers_a_single_restart_with_15_days(): void
    {
        $userCourse = $this->enroll();
        Sanctum::actingAs($this->user);
        $this->completeCourseContents();

        for ($i = 1; $i <= 3; $i++) {
            $this->postJson('/api/v1/learn/courses/basic/exam', ['answers' => []])->assertOk()
                ->assertJsonPath('data.attempts_used', $i);
        }

        $this->getJson('/api/v1/learn/courses/basic/exam')->assertStatus(403);
        $this->postJson('/api/v1/learn/courses/basic/exam', ['answers' => []])->assertStatus(403);
        $this->assertSame('exam_locked', $this->getJson('/api/v1/learn/courses/basic')->json('data.status'));

        $restart = $this->postJson('/api/v1/learn/courses/basic/restart')->assertOk();
        $this->assertSame(15, $restart->json('data.days_left'));
        $this->assertSame('active', $restart->json('data.status'));
        $this->assertSame(1, (int) $userCourse->fresh()->finalizado);

        $new = UserCourse::latest('id')->first();
        $this->assertSame($userCourse->id, (int) $new->parent_id);
        $this->assertSame(1, (int) $new->reiniciado);

        // El reinicio es único: tras agotar de nuevo los intentos ya sólo cabe recomprar.
        $new->update(['intentos' => 3]);
        $this->postJson('/api/v1/learn/courses/basic/restart')->assertStatus(422);
        $this->assertTrue($this->getJson('/api/v1/learn/courses/basic')->json('data.can_repurchase'));
    }

    public function test_a_chapter_is_read_from_its_own_course_even_if_another_course_repeats_the_name(): void
    {
        $this->enroll();
        $this->enroll(['course_id' => 2]);
        Sanctum::actingAs($this->user);

        // Los cursos repiten los mismos títulos de capítulo y contenido. Al buscarlos
        // sueltos salía siempre el del primer curso, así que el alumno del otro curso se
        // quedaba con "contenido no encontrado" y no podía avanzar (#1803).
        $this->getJson('/api/v1/learn/courses/other/chapters/chapter-1/contents/content-1')
            ->assertOk()
            ->assertJsonPath('data.id', 11);

        $this->getJson('/api/v1/learn/courses/basic/chapters/chapter-1/contents/content-1')
            ->assertOk()
            ->assertJsonPath('data.id', 1);

        $this->postJson('/api/v1/learn/courses/other/chapters/chapter-1/contents/content-1/complete')
            ->assertOk();
    }

    public function test_an_abandoned_exam_counts_as_a_used_attempt_once_the_time_is_up(): void
    {
        $this->enroll();
        Sanctum::actingAs($this->user);
        $this->completeCourseContents();

        $this->postJson('/api/v1/learn/courses/basic/exam/start')->assertOk()
            ->assertJsonPath('data.attempts_used', 0);

        // El alumno cierra la ventana: nadie avisa de que se acabó el tiempo.
        Carbon::setTestNow(Carbon::now()->addHours(3));

        $this->getJson('/api/v1/learn/courses/basic/exam')->assertOk()
            ->assertJsonPath('data.attempts_used', 1)
            ->assertJsonPath('data.attempts_left', 2)
            ->assertJsonPath('data.in_progress', false);

        Carbon::setTestNow();
    }

    public function test_answers_sent_after_the_deadline_are_not_graded(): void
    {
        $this->enroll();
        Sanctum::actingAs($this->user);
        $this->completeCourseContents();

        $this->postJson('/api/v1/learn/courses/basic/exam/start')->assertOk();

        $correct = DB::table('exam_question_options')->where('resultado', 1)->orderBy('id')->get();

        Carbon::setTestNow(Carbon::now()->addHours(3));

        $response = $this->postJson('/api/v1/learn/courses/basic/exam', [
            'answers' => $correct->map(fn ($option) => [
                'exam_question_id' => $option->exam_question_id,
                'exam_question_option_id' => $option->id,
            ])->all(),
        ])->assertOk();

        $this->assertTrue($response->json('data.expired'));
        $this->assertFalse($response->json('data.completed'));
        $this->assertSame(1, $response->json('data.attempts_used'));

        Carbon::setTestNow();
    }

    public function test_reloading_the_exam_does_not_give_a_fresh_clock(): void
    {
        $this->enroll();
        Sanctum::actingAs($this->user);
        $this->completeCourseContents();

        $this->postJson('/api/v1/learn/courses/basic/exam/start')->assertOk()
            ->assertJsonPath('data.remaining_time', '01:00:00');

        Carbon::setTestNow(Carbon::now()->addMinutes(30));

        $this->getJson('/api/v1/learn/courses/basic/exam')->assertOk()
            ->assertJsonPath('data.in_progress', true)
            ->assertJsonPath('data.remaining_time', '00:30:00');

        Carbon::setTestNow();
    }

    public function test_buying_the_course_again_starts_the_exam_attempts_from_zero(): void
    {
        $agotada = $this->enroll(['intentos' => 3, 'finalizado' => 1]);
        Sanctum::actingAs($this->user);
        $this->completeCourseContents();

        // Con los intentos agotados el examen está cerrado.
        $this->getJson('/api/v1/learn/courses/basic/exam')->assertStatus(403);

        // La compra abre una matrícula nueva, como hace el webhook de Stripe: el curso
        // se vuelve a cursar desde el principio y el examen arranca con sus 3 intentos.
        $nueva = $this->enroll();
        $this->completeCourseContents();

        $this->getJson('/api/v1/learn/courses/basic/exam')->assertOk()
            ->assertJsonPath('data.attempts_used', 0)
            ->assertJsonPath('data.attempts_left', 3);

        $this->assertSame(3, (int) $agotada->fresh()->intentos);
        $this->assertSame(0, (int) $nueva->fresh()->intentos);
    }

    public function test_expired_enrollment_blocks_access_and_allows_repurchase(): void
    {
        $this->enroll(['fecha_inicio' => Carbon::now()->subDays(31)->toDateString(), 'dias_activo' => 30]);
        Sanctum::actingAs($this->user);

        $this->getJson('/api/v1/learn/my-courses')->assertOk()
            ->assertJsonPath('data.0.status', 'expired')
            ->assertJsonPath('data.0.can_repurchase', true);

        $this->getJson('/api/v1/learn/courses/basic')->assertStatus(403);
        $this->getJson('/api/v1/learn/courses/basic/verify-access')->assertOk()
            ->assertJsonPath('data.has_access', false)
            ->assertJsonPath('data.reason', 'expired');

        $this->assertSame(1, (int) UserCourse::first()->caducado);

        app(CourseAccessService::class)->assertPurchasable($this->user, $this->course);
        $this->assertTrue(true);
    }

    public function test_active_enrollment_blocks_repurchase_but_approved_one_allows_it(): void
    {
        $userCourse = $this->enroll();
        $service = app(CourseAccessService::class);

        try {
            $service->assertPurchasable($this->user, $this->course);
            $this->fail('An active enrollment must block a second purchase.');
        } catch (CartException $e) {
            $this->assertStringContainsString('in progress', $e->getMessage());
        }

        $userCourse->update(['aprobado' => 1, 'finalizado' => 1]);
        $service->assertPurchasable($this->user, $this->course);

        // Aprobado no caduca nunca, por muy vieja que sea la matrícula.
        $userCourse->update(['fecha_inicio' => '2020-01-01']);
        $this->assertSame('approved', $service->state($service->latestEnrollment($this->user, $this->course))['status']);
    }

    public function test_the_latest_enrollment_is_the_one_being_taken(): void
    {
        $old = $this->enroll(['aprobado' => 1, 'finalizado' => 1]);
        $new = $this->enroll();
        Sanctum::actingAs($this->user);

        $this->getJson('/api/v1/learn/courses/basic')->assertOk()
            ->assertJsonPath('data.user_course_id', $new->id)
            ->assertJsonPath('data.status', 'active');

        $courses = $this->getJson('/api/v1/learn/my-courses')->json('data');
        $this->assertSame([true, false], [$courses[0]['is_current'], $courses[1]['is_current']]);
        $this->assertSame($old->id, $courses[1]['user_course_id']);
    }

    // ---------------------------------------------------------------- helpers

    private function enroll(array $overrides = []): UserCourse
    {
        return UserCourse::create(array_merge([
            'user_id' => $this->user->id,
            'course_id' => $this->course->id,
            'fecha_inicio' => Carbon::now()->toDateString(),
            'dias_activo' => 30,
            'aprobado' => 0,
            'intentos' => 0,
            'caducado' => 0,
            'finalizado' => 0,
        ], $overrides));
    }

    private function completeChapterOne(): void
    {
        $this->postJson('/api/v1/learn/courses/basic/chapters/chapter-1/contents/content-1/complete')->assertOk();
        foreach (DB::table('chapter_quiz_options')->where('estado', 1)->get() as $option) {
            $this->postJson('/api/v1/learn/courses/basic/chapters/chapter-1/quiz', [
                'chapter_quiz_id' => $option->chapter_quiz_id,
                'chapter_quiz_option_id' => $option->id,
            ])->assertOk();
        }
    }

    private function completeCourseContents(): void
    {
        $this->completeChapterOne();
        $this->postJson('/api/v1/learn/courses/basic/chapters/chapter-2/contents/content-2/complete')->assertOk();
    }

    private function seedCourse(): void
    {
        $this->user = User::forceCreate(['name' => 'Student', 'email' => 'student@example.com', 'password' => bcrypt('secret')]);
        $this->course = Course::forceCreate(['titulo' => 'Basic', 'slug' => 'basic', 'precio' => 45, 'tiempovalido' => 30]);

        DB::table('chapters')->insert([
            ['id' => 1, 'course_id' => 1, 'title' => 'Chapter 1', 'slug' => 'chapter-1', 'order' => 1],
            ['id' => 2, 'course_id' => 1, 'title' => 'Chapter 2', 'slug' => 'chapter-2', 'order' => 2],
        ]);
        DB::table('chaptercontents')->insert([
            ['id' => 1, 'chapter_id' => 1, 'titulo' => 'Content 1', 'slug' => 'content-1', 'order' => 1],
            ['id' => 2, 'chapter_id' => 2, 'titulo' => 'Content 2', 'slug' => 'content-2', 'order' => 1],
        ]);

        // Otro curso con capítulos y contenidos de igual nombre: así son los cursos
        // reales, que repiten "Chapter 2: Introduction" y sus títulos (#1803).
        Course::forceCreate(['titulo' => 'Other', 'slug' => 'other', 'precio' => 45, 'tiempovalido' => 30]);
        DB::table('chapters')->insert([
            ['id' => 11, 'course_id' => 2, 'title' => 'Chapter 1', 'slug' => 'chapter-1', 'order' => 1],
        ]);
        DB::table('chaptercontents')->insert([
            ['id' => 11, 'chapter_id' => 11, 'titulo' => 'Content 1', 'slug' => 'content-1', 'order' => 1],
        ]);

        // Capítulo 1 con 4 preguntas de 2 opciones; el 2 sin quiz.
        for ($q = 1; $q <= 4; $q++) {
            DB::table('chapter_quizzes')->insert(['id' => $q, 'chapter_id' => 1, 'question' => "Q{$q}"]);
            DB::table('chapter_quiz_options')->insert([
                ['chapter_quiz_id' => $q, 'option' => 'right', 'estado' => 1],
                ['chapter_quiz_id' => $q, 'option' => 'wrong', 'estado' => 0],
            ]);
        }

        DB::table('exams')->insert(['id' => 1, 'title' => 'Final', 'duration' => 60]);
        DB::table('exam_courses')->insert(['exam_id' => 1, 'course_id' => 1]);
        for ($q = 1; $q <= 4; $q++) {
            DB::table('exam_questions')->insert(['id' => $q, 'exam_id' => 1, 'question' => "E{$q}"]);
            DB::table('exam_question_options')->insert([
                ['exam_question_id' => $q, 'opcion' => 'right', 'resultado' => 1],
                ['exam_question_id' => $q, 'opcion' => 'wrong', 'resultado' => 0],
            ]);
        }
    }

    private function createLegacyTables(): void
    {
        Schema::create('users', function (Blueprint $t): void {
            $t->id();
            $t->string('name');
            $t->string('email');
            $t->string('password');
            $t->timestamps();
        });
        Schema::create('courses', function (Blueprint $t): void {
            $t->id();
            $t->string('titulo');
            $t->string('slug');
            $t->decimal('precio', 10, 2)->nullable();
            $t->string('tiempovalido')->nullable();
            $t->string('banner')->nullable();
            $t->string('nivel')->nullable();
            $t->string('responsable')->nullable();
            $t->unsignedBigInteger('certification_id')->nullable();
            $t->timestamps();
        });
        Schema::create('chapters', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('course_id');
            $t->string('title');
            $t->string('slug');
            $t->text('contenido')->nullable();
            $t->string('video')->nullable();
            $t->boolean('reading')->nullable();
            $t->boolean('audio')->nullable();
            $t->string('quiz')->nullable();
            $t->integer('order');
            $t->timestamps();
        });
        Schema::create('chaptercontents', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('chapter_id');
            $t->string('titulo');
            $t->string('slug');
            $t->text('contenido')->nullable();
            $t->string('video')->nullable();
            $t->string('audio')->nullable();
            $t->string('poster')->nullable();
            $t->integer('order');
            $t->timestamps();
        });
        Schema::create('chapter_quizzes', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('chapter_id');
            $t->string('question');
            $t->timestamps();
        });
        Schema::create('chapter_quiz_options', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('chapter_quiz_id');
            $t->string('option');
            $t->integer('estado')->default(0);
            $t->timestamps();
        });
        Schema::create('user_courses', function (Blueprint $t): void {
            $t->id();
            $t->integer('user_id');
            $t->unsignedBigInteger('course_id');
            $t->string('fecha_inicio');
            $t->string('dias_activo')->nullable();
            $t->integer('aprobado')->nullable()->default(0);
            $t->integer('intentos')->nullable()->default(0);
            $t->integer('reiniciado')->nullable()->default(0);
            $t->integer('caducado')->nullable()->default(0);
            $t->integer('finalizado')->nullable()->default(0);
            $t->unsignedBigInteger('parent_id')->nullable();
            $t->timestamps();
        });
        Schema::create('user_course_chapters', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('user_course_id');
            $t->integer('chapter_id');
            $t->integer('quiz_result')->nullable();
            $t->timestamps();
        });
        Schema::create('user_course_chapter_contents', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('user_course_chapter_id');
            $t->integer('content_id');
            $t->timestamps();
        });
        Schema::create('user_course_chapter_quizzes', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('chapter_quiz_id');
            $t->unsignedBigInteger('user_course_chapter_id');
            $t->unsignedBigInteger('quiz_question_option_id');
            $t->integer('result')->nullable();
            $t->string('timequiz')->nullable();
            $t->timestamps();
        });
        Schema::create('exams', function (Blueprint $t): void {
            $t->id();
            $t->string('title');
            $t->text('description')->nullable();
            $t->integer('duration')->nullable();
            $t->timestamps();
        });
        Schema::create('exam_courses', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('exam_id');
            $t->unsignedBigInteger('course_id');
            $t->timestamps();
        });
        Schema::create('exam_questions', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('exam_id');
            $t->string('question');
            $t->timestamps();
        });
        Schema::create('exam_question_options', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('exam_question_id');
            $t->string('opcion');
            $t->integer('resultado')->default(0);
            $t->timestamps();
        });
        Schema::create('user_course_exams', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('user_course_id');
            $t->unsignedBigInteger('exam_id');
            $t->string('tiempo')->nullable();
            $t->timestamp('started_at')->nullable();
            $t->integer('intentos')->nullable();
            $t->integer('resultado')->nullable()->default(0);
            $t->integer('complete')->nullable();
            $t->string('evento')->nullable();
            $t->timestamps();
        });
        Schema::create('user_course_exam_results', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('user_course_exam_id');
            $t->integer('attempt_number')->default(1);
            $t->unsignedBigInteger('exam_question_id');
            $t->unsignedBigInteger('exam_question_option_id');
            $t->integer('result')->nullable();
            $t->timestamps();
        });
    }
}
