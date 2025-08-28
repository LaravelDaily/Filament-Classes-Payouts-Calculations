<?php

namespace App\Filament\Resources\TeacherPayConfigs;

use App\Filament\Resources\TeacherPayConfigs\Pages\CreateTeacherPayConfig;
use App\Filament\Resources\TeacherPayConfigs\Pages\EditTeacherPayConfig;
use App\Filament\Resources\TeacherPayConfigs\Pages\ListTeacherPayConfigs;
use App\Filament\Resources\TeacherPayConfigs\Schemas\TeacherPayConfigForm;
use App\Filament\Resources\TeacherPayConfigs\Tables\TeacherPayConfigsTable;
use App\Models\TeacherPayConfig;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TeacherPayConfigResource extends Resource
{
    protected static ?string $model = TeacherPayConfig::class;

    protected static ?string $navigationLabel = 'Teacher Pay Config';

    protected static string|UnitEnum|null $navigationGroup = 'User Management';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    public static function form(Schema $schema): Schema
    {
        return TeacherPayConfigForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TeacherPayConfigsTable::configure($table);
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
            'index' => ListTeacherPayConfigs::route('/'),
            'create' => CreateTeacherPayConfig::route('/create'),
            'edit' => EditTeacherPayConfig::route('/{record}/edit'),
        ];
    }
}
