<?php

namespace App\Filament\Resources\CourseClasses\Schemas;

use App\Filament\Schemas\Components\TimeSelect;
use App\Models\Attendance;
use App\Models\CourseClass;
use App\Models\Student;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class CourseClassForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Schedule Details')
                    ->schema([
                        Select::make('course_id')
                            ->label('Course')
                            ->relationship('course', 'name')
                            ->required()
                            ->searchable(),
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('scheduled_date')
                                    ->required()
                                    ->native(false),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TimeSelect::make('start_time')
                                    ->label('Start Time')
                                    ->required(),
                                TimeSelect::make('end_time')
                                    ->label('End Time')
                                    ->required(),
                            ]),
                        Grid::make(2)
                            ->schema([
                                Select::make('substitute_teacher_id')
                                    ->label('Substitute Teacher')
                                    ->relationship('substituteTeacher', 'name')
                                    ->searchable()
                                    ->nullable(),
                            ]),
                    ]),

                // Attendance management for this class
                Section::make('Attendance')
                    ->schema([
                        CheckboxList::make('attendance_student_ids')
                            ->label('Present Students')
                            ->columns(2)
                            ->columnSpanFull()
                            ->options(function (Get $get): array {
                                $courseId = $get('course_id');
                                $scheduledDate = $get('scheduled_date');

                                if (! $courseId || ! $scheduledDate) {
                                    return [];
                                }

                                return Student::query()
                                    ->whereHas('courses', function ($query) use ($courseId, $scheduledDate) {
                                        $query->where('courses.id', $courseId)
                                            ->where('enrollments.start_date', '<=', $scheduledDate)
                                            ->where(function ($q) use ($scheduledDate) {
                                                $q->whereNull('enrollments.end_date')
                                                    ->orWhere('enrollments.end_date', '>=', $scheduledDate);
                                            });
                                    })
                                    ->orderBy('first_name')
                                    ->orderBy('last_name')
                                    ->get()
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->afterStateHydrated(function (Set $set, ?CourseClass $record): void {
                                if (! $record) {
                                    $set('attendance_student_ids', []);

                                    return;
                                }

                                $set('attendance_student_ids', $record->attendances()->pluck('student_id')->all());
                            })
                            ->dehydrated(false)
                            ->afterStateUpdated(function (Set $set, $state, ?CourseClass $record): void {
                                if (! $record) {
                                    return;
                                }

                                // Sync attendance records
                                static::syncAttendanceRecords($record, $state ?? []);

                                // Recalculate pay totals based on actual attendance
                                static::recalculatePayTotals($record);
                            }),
                    ])
                    ->hidden(fn (?CourseClass $record) => $record === null),
            ]);
    }

    protected static function syncAttendanceRecords(CourseClass $courseClass, array $studentIds): void
    {
        // Get current attendance records
        $currentAttendance = $courseClass->attendances()->pluck('student_id')->toArray();

        // Students to add (in new list but not in current)
        $studentsToAdd = array_diff($studentIds, $currentAttendance);

        // Students to remove (in current but not in new list)
        $studentsToRemove = array_diff($currentAttendance, $studentIds);

        // Add new attendance records
        foreach ($studentsToAdd as $studentId) {
            Attendance::create([
                'course_class_id' => $courseClass->id,
                'student_id' => $studentId,
            ]);
        }

        // Remove attendance records
        if (! empty($studentsToRemove)) {
            $courseClass->attendances()
                ->whereIn('student_id', $studentsToRemove)
                ->delete();
        }
    }

    protected static function recalculatePayTotals(CourseClass $courseClass): void
    {
        // Get actual attendance count
        $attendanceCount = $courseClass->attendances()->count();

        // Get pay configuration
        $basePay = config('teacher_pay.base_pay', 50.00);
        $bonusPerStudent = config('teacher_pay.bonus_per_student', 2.50);

        // Calculate pay amounts
        $bonusPay = $attendanceCount * $bonusPerStudent;
        $totalPay = $basePay + $bonusPay;

        // Update course class with recalculated pay
        $updateData = [
            'student_count' => $attendanceCount,
            'teacher_base_pay' => $basePay,
            'teacher_bonus_pay' => $bonusPay,
            'teacher_total_pay' => $totalPay,
        ];

        // If there's a substitute teacher, calculate their pay too
        if ($courseClass->substitute_teacher_id) {
            $updateData['substitute_base_pay'] = $basePay;
            $updateData['substitute_bonus_pay'] = $bonusPay;
            $updateData['substitute_total_pay'] = $totalPay;
        }

        $courseClass->update($updateData);
    }
}
