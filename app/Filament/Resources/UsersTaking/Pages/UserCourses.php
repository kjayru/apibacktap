<?php

namespace App\Filament\Resources\UsersTaking\Pages;

use App\Filament\Resources\UsersTaking\UsersTakingResource;
use App\Models\ExamCourse;
use App\Models\ExamQuestion;
use App\Models\UserCourse;
use App\Models\UserCourseExam;
use Filament\Actions\Action;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserCourses extends Page implements HasTable
{
    use InteractsWithRecord;
    use InteractsWithTable;

    protected static string $resource = UsersTakingResource::class;

    protected string $view = 'filament.resources.users-taking.pages.user-courses';

    protected static ?string $breadcrumb = 'User Course';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getTitle(): string | Htmlable
    {
        return 'User Course';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => UserCourse::query()
                ->where('user_id', $this->getRecord()->getKey())
                ->with(['course.certification'])
                ->latest('id'))
            ->heading('Users courses')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('course.titulo')
                    ->label('Course')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Initial date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('finish_date')
                    ->label('Finish date')
                    ->state(fn (UserCourse $record): ?string => $this->getFinishDate($record))
                    ->placeholder('-'),
                TextColumn::make('result')
                    ->label('Result')
                    ->badge()
                    ->state(fn (UserCourse $record): string => $this->getResultLabel($record))
                    ->color(fn (string $state): string => match ($state) {
                        'Approved' => 'success',
                        'Expired' => 'warning',
                        'Failed' => 'danger',
                        default => 'info',
                    }),
                TextColumn::make('percent')
                    ->label('Porcent')
                    ->state(fn (UserCourse $record): string => $this->getPercentText($record))
                    ->html(),
            ])
            ->recordActionsColumnLabel('Detail exam / Certificated')
            ->recordActions([
                Action::make('detail')
                    ->label('Detail')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->color('danger')
                    ->modalHeading('Exam results')
                    ->modalWidth(Width::FiveExtraLarge)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalContent(fn (UserCourse $record) => view('filament.user-courses.exam-results', [
                        'questions' => $this->getExamResultQuestions($record),
                    ])),
                Action::make('certificate')
                    ->label('View Certificate')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->url(fn (Model $record): string => route('admin.user-courses.certificate', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (Model $record): bool => (bool) $record->aprobado),
            ]);
    }

    private function getFinishDate(UserCourse $record): ?string
    {
        if (
            (bool) $record->aprobado ||
            ((bool) $record->caducado && ! (bool) $record->aprobado) ||
            ((int) $record->intentos > 0 && ! (bool) $record->aprobado) ||
            ((bool) $record->reiniciado && filled($record->parent_id) && ! (bool) $record->aprobado)
        ) {
            return $record->updated_at?->format('Y-m-d H:i:s');
        }

        return null;
    }

    private function getResultLabel(UserCourse $record): string
    {
        if ((bool) $record->aprobado) {
            return 'Approved';
        }

        if ((bool) $record->caducado && ! (bool) $record->aprobado) {
            return 'Expired';
        }

        if (
            ((bool) $record->reiniciado && filled($record->parent_id) && ! (bool) $record->aprobado) ||
            ((int) $record->intentos > 0 && ! (bool) $record->aprobado)
        ) {
            return 'Failed';
        }

        return 'In Progress';
    }

    private function getPercentText(UserCourse $record): string
    {
        $stats = $this->getExamStats($record);

        return "{$stats->percentage}%<br>{$stats->correct} corrects";
    }

    private function getExamStats(UserCourse $record): object
    {
        $userCourseExam = $this->getUserCourseExam($record);

        if (! $userCourseExam) {
            return (object) [
                'total' => 0,
                'correct' => 0,
                'percentage' => '0.00',
            ];
        }

        $attempt = $this->getAttemptNumber($userCourseExam);

        $stats = DB::table('user_course_exam_results')
            ->where('user_course_exam_id', $userCourseExam->id)
            ->where('attempt_number', $attempt)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN result = 1 THEN 1 ELSE 0 END) as correct')
            ->first();

        $total = (int) ($stats->total ?? 0);
        $correct = (int) ($stats->correct ?? 0);

        return (object) [
            'total' => $total,
            'correct' => $correct,
            'percentage' => number_format($total > 0 ? (($correct * 100) / $total) : 0, 2),
        ];
    }

    private function getUserCourseExam(UserCourse $record): ?UserCourseExam
    {
        $examId = ExamCourse::where('course_id', $record->course_id)->value('exam_id');

        if (! $examId) {
            return null;
        }

        return UserCourseExam::query()
            ->where('user_course_id', $record->id)
            ->where('exam_id', $examId)
            ->latest('id')
            ->first();
    }

    private function getAttemptNumber(UserCourseExam $userCourseExam): int
    {
        if ((int) $userCourseExam->intentos > 0) {
            return (int) $userCourseExam->intentos;
        }

        return (int) DB::table('user_course_exam_results')
            ->where('user_course_exam_id', $userCourseExam->id)
            ->max('attempt_number') ?: 1;
    }

    private function getExamResultQuestions(UserCourse $record): array
    {
        $userCourseExam = $this->getUserCourseExam($record);

        if (! $userCourseExam) {
            return [];
        }

        $attempt = $this->getAttemptNumber($userCourseExam);

        return ExamQuestion::query()
            ->where('exam_id', $userCourseExam->exam_id)
            ->with(['examquestionoptions' => function ($query) use ($userCourseExam, $attempt) {
                $query
                    ->leftJoin('user_course_exam_results', function ($join) use ($userCourseExam, $attempt) {
                        $join->on('exam_question_options.id', '=', 'user_course_exam_results.exam_question_option_id')
                            ->where('user_course_exam_results.user_course_exam_id', $userCourseExam->id)
                            ->where('user_course_exam_results.attempt_number', $attempt);
                    })
                    ->select([
                        'exam_question_options.*',
                        DB::raw('CASE WHEN user_course_exam_results.exam_question_option_id IS NOT NULL THEN 1 ELSE 0 END as user_answer'),
                    ]);
            }])
            ->get()
            ->map(fn (ExamQuestion $question): array => [
                'question' => $question->question,
                'options' => $question->examquestionoptions->map(fn ($option): array => [
                    'text' => $option->opcion,
                    'is_correct' => (bool) $option->resultado,
                    'is_user_answer' => (bool) $option->user_answer,
                ])->all(),
            ])
            ->all();
    }
}
