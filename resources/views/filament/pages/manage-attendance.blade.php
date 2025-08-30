<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Schedule Information Card --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Learning Class</h3>
                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $this->record->learningClass->name }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Date</h3>
                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $this->record->scheduled_date->format('M j, Y') }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Time</h3>
                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $this->record->start_time }} - {{ $this->record->end_time }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Teacher</h3>
                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $this->record->substituteTeacher?->name ?? $this->record->teacher->name }}</p>
                </div>
            </div>
        </div>

        {{-- Attendance Table --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Student Attendance</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Select Present or Absent for each student, then save attendance</p>
            </div>
            
            {{ $this->table }}
            
            @if($this->getEnrolledStudents()->isNotEmpty())
                <div class="mt-6 flex justify-end">
                    <x-filament::button
                        wire:click="save"
                        color="primary"
                    >
                        Save Attendance
                    </x-filament::button>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>