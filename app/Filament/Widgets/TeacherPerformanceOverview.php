<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\CourseClass;
use App\Models\TeacherPayout;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class TeacherPerformanceOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 2; // Second widget for teachers

    public static function canView(): bool
    {
        return Auth::user()?->role?->name === 'Teacher';
    }

    protected function getStats(): array
    {
        $teacherId = Auth::id();

        $thisWeekClasses = CourseClass::where('teacher_id', $teacherId)
            ->whereBetween('scheduled_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $thisMonthEarnings = TeacherPayout::where('teacher_id', $teacherId)
            ->where('month', now()->format('Y-m'))
            ->value('total_pay') ?? 0;

        $payoutStatus = TeacherPayout::where('teacher_id', $teacherId)
            ->where('month', now()->format('Y-m'))
            ->first();

        $attendanceRate = CourseClass::where('teacher_id', $teacherId)
            ->where('scheduled_date', '>=', now()->subMonth())
            ->get()
            ->map(function ($class) {
                $attendance = Attendance::where('course_class_id', $class->id)->count();

                return $class->student_count > 0 ? ($attendance / $class->student_count) * 100 : 0;
            })
            ->average();

        return [
            Stat::make('Classes This Week', $thisWeekClasses)
                ->description('Scheduled classes')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('primary'),

            Stat::make('Monthly Earnings', '$'.number_format($thisMonthEarnings, 2))
                ->description($payoutStatus && $payoutStatus->is_paid ? 'Paid' : 'Pending')
                ->descriptionIcon($payoutStatus && $payoutStatus->is_paid ? 'heroicon-o-check-circle' : 'heroicon-o-clock')
                ->color($payoutStatus && $payoutStatus->is_paid ? 'success' : 'warning'),

            Stat::make('Avg. Attendance Rate', number_format($attendanceRate ?? 0, 1).'%')
                ->description('Student attendance in your classes')
                ->descriptionIcon('heroicon-o-users')
                ->color($attendanceRate >= 80 ? 'success' : ($attendanceRate >= 60 ? 'warning' : 'danger')),
        ];
    }
}
