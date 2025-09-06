<?php

namespace App\Filament\Resources\LearningClasses;

use App\Filament\Resources\LearningClasses\Pages\CreateLearningClass;
use App\Filament\Resources\LearningClasses\Pages\EditLearningClass;
use App\Filament\Resources\LearningClasses\Pages\ListLearningClasses;
use App\Filament\Resources\LearningClasses\Pages\ManageMonthlyAttendance;
use App\Filament\Resources\LearningClasses\Schemas\LearningClassForm;
use App\Filament\Resources\LearningClasses\Tables\LearningClassesTable;
use App\Models\LearningClass;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LearningClassResource extends Resource
{
    protected static ?string $model = LearningClass::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return LearningClassForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LearningClassesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLearningClasses::route('/'),
            'create' => CreateLearningClass::route('/create'),
            'edit' => EditLearningClass::route('/{record}/edit'),
            'attendance' => ManageMonthlyAttendance::route('/{record}/attendance'),
        ];
    }
}
