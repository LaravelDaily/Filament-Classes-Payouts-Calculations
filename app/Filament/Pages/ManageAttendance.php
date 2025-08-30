<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ClassSchedules\ClassScheduleResource;
use App\Models\Attendance;
use App\Models\ClassSchedule;
use App\Models\Student;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ManageAttendance extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static string $resource = ClassScheduleResource::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static bool $shouldRegisterNavigation = false;

    public $record;

    public array $attendanceData = [];

    public function getView(): string
    {
        return 'filament.pages.manage-attendance';
    }

    public function mount(int $record): void
    {
        $this->record = ClassSchedule::with([
            'learningClass.enrollments.student',
            'attendances',
            'teacher',
            'substituteTeacher',
        ])->findOrFail($record);

        $this->loadAttendanceData();
    }

    protected function loadAttendanceData(): void
    {
        $existingAttendance = $this->record->attendances->keyBy('student_id');

        foreach ($this->getEnrolledStudents() as $student) {
            $attendance = $existingAttendance->get($student->id);
            $this->attendanceData[$student->id] = $attendance ? 'present' : 'absent';
        }
    }

    public function getTitle(): string
    {
        return "Manage Attendance - {$this->record->learningClass->name}";
    }

    public function getBreadcrumbs(): array
    {
        return [
            ClassScheduleResource::getUrl('index') => 'Class Schedules',
            '' => 'Manage Attendance',
        ];
    }

    public function getEnrolledStudents(): Collection
    {
        return Student::whereHas('enrollments', function ($query) {
            $query->where('learning_class_id', $this->record->learning_class_id)
                ->where('start_date', '<=', $this->record->scheduled_date)
                ->where(function ($q) {
                    $q->whereNull('end_date')
                        ->orWhere('end_date', '>=', $this->record->scheduled_date);
                });
        })->orderBy('name')->get();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Student::query()
                    ->whereHas('enrollments', function ($query) {
                        $query->where('learning_class_id', $this->record->learning_class_id)
                            ->where('start_date', '<=', $this->record->scheduled_date)
                            ->where(function ($q) {
                                $q->whereNull('end_date')
                                    ->orWhere('end_date', '>=', $this->record->scheduled_date);
                            });
                    })
                    ->orderBy('name')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Student Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                SelectColumn::make('attendance_status')
                    ->label('Attendance')
                    ->options([
                        'present' => 'Present',
                        'absent' => 'Absent',
                    ])
                    ->getStateUsing(function (Student $record) {
                        return $this->attendanceData[$record->id] ?? 'absent';
                    })
                    ->updateStateUsing(function (Student $record, $state) {
                        $this->attendanceData[$record->id] = $state;

                        return $state;
                    }),
            ])
            ->emptyStateHeading('No Enrolled Students')
            ->emptyStateDescription('There are no students enrolled in this class for the scheduled date.')
            ->emptyStateIcon('heroicon-o-users');
    }

    public function save(): void
    {
        DB::transaction(function () {
            $students = $this->getEnrolledStudents();

            foreach ($students as $student) {
                $status = $this->attendanceData[$student->id] ?? 'absent';

                if ($status === 'present') {
                    Attendance::updateOrCreate(
                        [
                            'class_schedule_id' => $this->record->id,
                            'student_id' => $student->id,
                        ]
                    );
                } else {
                    Attendance::where([
                        'class_schedule_id' => $this->record->id,
                        'student_id' => $student->id,
                    ])->delete();
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
                ->label('Back to Schedules')
                ->url(ClassScheduleResource::getUrl('index'))
                ->color('gray'),
        ];
    }
}
