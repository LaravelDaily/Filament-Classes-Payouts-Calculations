<?php

namespace App\Filament\Resources\CourseClasses\Pages;

use App\Filament\Resources\CourseClasses\CourseClassResource;
use App\Models\Attendance;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditCourseClass extends EditRecord
{
    protected static string $resource = CourseClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Persist attendance based on checkbox list state
        $state = $this->form->getRawState();
        $selectedIds = collect($state['attendance_student_ids'] ?? [])->filter()->values();

        DB::transaction(function () use ($selectedIds) {
            $existingIds = $this->record->attendances()->pluck('student_id');

            $toAdd = $selectedIds->diff($existingIds);
            $toRemove = $existingIds->diff($selectedIds);

            foreach ($toAdd as $studentId) {
                Attendance::updateOrCreate([
                    'course_class_id' => $this->record->id,
                    'student_id' => $studentId,
                ]);
            }

            if ($toRemove->isNotEmpty()) {
                Attendance::where('course_class_id', $this->record->id)
                    ->whereIn('student_id', $toRemove)
                    ->delete();
            }
        });
    }
}
