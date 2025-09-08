<?php

namespace App\Filament\Resources\CourseClasses\Pages;

use App\Filament\Resources\CourseClasses\CourseClassResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCourseClass extends CreateRecord
{
    protected static string $resource = CourseClassResource::class;
}
