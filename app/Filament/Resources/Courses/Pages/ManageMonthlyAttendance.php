<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Student;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ManageMonthlyAttendance extends Page implements HasForms
{
    use InteractsWithForms;
    use InteractsWithRecord;

    protected static string $resource = CourseResource::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static bool $shouldRegisterNavigation = false;

    public string $selectedMonth;

    public array $attendanceData = [];

    public function mount(): void
    {
        // Ensure record is properly resolved
        if (is_string($this->record) || is_int($this->record)) {
            $this->record = Course::findOrFail($this->record);
        }

        $this->selectedMonth = request('month', now()->format('Y-m'));
        $this->loadAttendanceData();
    }

    public function getView(): string
    {
        return 'filament.pages.manage-monthly-attendance';
    }

    public function getTitle(): string
    {
        return "Monthly Attendance - {$this->record->name}";
    }

    protected function loadAttendanceData(): void
    {
        $students = $this->getEnrolledStudents();
        $courseClasses = $this->getCourseClassesForMonth();

        $existingAttendance = Attendance::whereIn('course_class_id', $courseClasses->pluck('id'))
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->groupBy('student_id')
            ->map(fn ($attendances) => $attendances->keyBy('course_class_id'));

        foreach ($students as $student) {
            foreach ($courseClasses as $courseClass) {
                $key = "{$student->id}-{$courseClass->id}";
                $this->attendanceData[$key] = $existingAttendance->get($student->id)?->has($courseClass->id) ? 'present' : 'absent';
            }
        }
    }

    public function getEnrolledStudents(): Collection
    {
        $monthStart = Carbon::createFromFormat('Y-m', $this->selectedMonth)->startOfMonth();
        $monthEnd = Carbon::createFromFormat('Y-m', $this->selectedMonth)->endOfMonth();

        return Student::whereHas('enrollments', function ($query) use ($monthStart, $monthEnd) {
            $query->where('course_id', $this->record->id)
                ->where('start_date', '<=', $monthEnd)
                ->where(function ($q) use ($monthStart) {
                    $q->whereNull('end_date')
                        ->orWhere('end_date', '>=', $monthStart);
                });
        })->orderBy('name')->get();
    }

    public function getCourseClassesForMonth(): Collection
    {
        $monthStart = Carbon::createFromFormat('Y-m', $this->selectedMonth)->startOfMonth();
        $monthEnd = Carbon::createFromFormat('Y-m', $this->selectedMonth)->endOfMonth();

        return CourseClass::where('course_id', $this->record->id)
            ->whereBetween('scheduled_date', [$monthStart, $monthEnd])
            ->orderBy('scheduled_date')
            ->orderBy('start_time')
            ->get();
    }

    public function updatedSelectedMonth(): void
    {
        $this->loadAttendanceData();
    }

    public function updateMonth(): void
    {
        $this->redirect(CourseResource::getUrl('attendance', ['record' => $this->record, 'month' => $this->selectedMonth]));
    }

    public function save(): void
    {
        DB::transaction(function () {
            $students = $this->getEnrolledStudents();
            $courseClasses = $this->getCourseClassesForMonth();

            foreach ($students as $student) {
                foreach ($courseClasses as $courseClass) {
                    $key = "{$student->id}-{$courseClass->id}";
                    $status = $this->attendanceData[$key] ?? 'absent';

                    if ($status === 'present') {
                        Attendance::updateOrCreate([
                            'course_class_id' => $courseClass->id,
                            'student_id' => $student->id,
                        ]);
                    } else {
                        Attendance::where([
                            'course_class_id' => $courseClass->id,
                            'student_id' => $student->id,
                        ])->delete();
                    }
                }
            }
        });

        Notification::make()
            ->title('Attendance saved successfully')
            ->success()
            ->send();

        $this->loadAttendanceData();
    }

    protected function getActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to Classes')
                ->url(CourseResource::getUrl('index'))
                ->color('gray'),
        ];
    }

    public function getMonthOptions(): array
    {
        $options = [];
        $current = now()->startOfMonth();

        for ($i = -6; $i <= 6; $i++) {
            $date = $current->copy()->addMonths($i);
            $options[$date->format('Y-m')] = $date->format('F Y');
        }

        return $options;
    }
}
