<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Month Selector --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-content p-6">
                <div class="flex items-center justify-between">
                    <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        Select Month
                    </h3>
                    <div class="w-48">
                        <select 
                            wire:model.live="selectedMonth"
                            wire:change="updateMonth"
                            class="fi-select-input block w-full border-none bg-transparent py-1.5 pe-8 ps-3 text-base text-gray-950 outline-none transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] dark:text-white dark:placeholder:text-gray-500 sm:text-sm sm:leading-6 [&:not(:focus)]:shadow-none"
                        >
                            @foreach($this->getMonthOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Attendance Grid --}}
        @php
            $students = $this->getEnrolledStudents();
            $courseClasses = $this->getCourseClassesForMonth();
        @endphp

        @if($students->isEmpty())
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="fi-section-content p-6">
                    <div class="fi-ta-empty-state-content mx-auto flex max-w-lg flex-col items-center justify-center text-center">
                        <div class="fi-ta-empty-state-icon-ctn mb-4 rounded-full bg-gray-100 p-3 dark:bg-gray-500/20">
                            <x-filament::icon icon="heroicon-o-users" class="fi-ta-empty-state-icon h-6 w-6 text-gray-500 dark:text-gray-400" />
                        </div>
                        <h4 class="fi-ta-empty-state-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                            No Students Enrolled
                        </h4>
                        <p class="fi-ta-empty-state-description text-sm text-gray-500 dark:text-gray-400">
                            There are no students enrolled in this course for the selected month.
                        </p>
                    </div>
                </div>
            </div>
        @elseif($courseClasses->isEmpty())
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="fi-section-content p-6">
                    <div class="fi-ta-empty-state-content mx-auto flex max-w-lg flex-col items-center justify-center text-center">
                        <div class="fi-ta-empty-state-icon-ctn mb-4 rounded-full bg-gray-100 p-3 dark:bg-gray-500/20">
                            <x-filament::icon icon="heroicon-o-calendar" class="fi-ta-empty-state-icon h-6 w-6 text-gray-500 dark:text-gray-400" />
                        </div>
                        <h4 class="fi-ta-empty-state-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                            No Course Classes Scheduled
                        </h4>
                        <p class="fi-ta-empty-state-description text-sm text-gray-500 dark:text-gray-400">
                            There are no course classes scheduled for this month.
                        </p>
                    </div>
                </div>
            </div>
        @else
            <form wire:submit="save">
                <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div class="fi-section-content-ctn">
                        <div class="fi-section-content p-6">
                            <div class="overflow-hidden overflow-x-auto">
                                <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                                    <thead class="divide-y divide-gray-200 dark:divide-white/5">
                                        <tr class="bg-gray-50 dark:bg-white/5">
                                            <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-student">
                                                <span class="group flex w-full items-center justify-between">
                                                    <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                                        Student
                                                    </span>
                                                </span>
                                            </th>
                                            @foreach($courseClasses as $courseClass)
                                                <th class="fi-ta-header-cell px-3 py-3.5 text-center">
                                                    <span class="group flex w-full items-center justify-center">
                                                        <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                                            <div>{{ $courseClass->scheduled_date->format('M j') }}</div>
                                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                                {{ \Carbon\Carbon::parse($courseClass->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($courseClass->end_time)->format('H:i') }}
                                                            </div>
                                                        </span>
                                                    </span>
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                                        @foreach($students as $student)
                                            <tr class="fi-ta-row [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75 hover:bg-gray-50 dark:hover:bg-white/5">
                                                <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                                    <div class="fi-ta-col-wrp">
                                                        <div class="fi-ta-text px-3 py-4">
                                                            <div class="fi-ta-text-item inline-flex items-center gap-1.5">
                                                                <span class="fi-ta-text-item-label text-sm leading-6 text-gray-950 dark:text-white">
                                                                    {{ $student->name }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                @foreach($courseClasses as $courseClass)
                                                    @php $key = "{$student->id}-{$courseClass->id}"; @endphp
                                                    <td class="fi-ta-cell p-0 text-center">
                                                        <div class="fi-ta-col-wrp">
                                                            <div class="px-3 py-4">
                                                                <div class="flex justify-center">
                                                                    <label class="inline-flex items-center">
                                                                        <input 
                                                                            type="checkbox"
                                                                            wire:model.defer="attendanceData.{{ $key }}"
                                                                            @if($attendanceData[$key] ?? false) checked @endif
                                                                            class="fi-checkbox-input rounded border-gray-300 bg-white text-primary-600 shadow-sm ring-0 focus:ring-2 focus:ring-primary-600 checked:bg-primary-600 checked:border-primary-600 disabled:bg-gray-50 disabled:text-gray-50 dark:bg-gray-900 dark:border-gray-600 dark:checked:bg-primary-500 dark:checked:border-primary-500 dark:focus:ring-primary-500"
                                                                        />
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-6">
                    <button 
                        type="submit"
                        class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-primary-600 text-white hover:bg-primary-500 focus-visible:ring-primary-500/50 dark:bg-primary-500 dark:hover:bg-primary-400 dark:focus-visible:ring-primary-400/50"
                    >
                        Save Attendance
                    </button>
                </div>
            </form>
        @endif
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
