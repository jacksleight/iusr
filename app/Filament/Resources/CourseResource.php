<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\CourseStructure;
use App\Filament\Resources\CourseResource\Pages;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                CourseStructure::make()
                    ->hidden(fn (string $operation) => $operation !== 'edit-structure'),
                Section::make()
                    ->hidden(fn (string $operation) => $operation === 'edit-structure')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('description')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('level_id')
                            ->relationship('level', 'name')
                            ->required(),
                        Forms\Components\Select::make('language_id')
                            ->relationship('language', 'name')
                            ->required(),
                        Forms\Components\Select::make('topics')
                            ->relationship('topics', 'name')
                            ->searchable()
                            ->preload()
                            ->multiple(),
                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('title')
            ->recordUrl(fn ($record) => route('filament.admin.resources.courses.view', $record))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('language.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('level.name'),
                Tables\Columns\TextColumn::make('topics.name')
                    ->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('language_id')
                    ->relationship('language', 'name')
                    ->label('Language'),
                Tables\Filters\SelectFilter::make('level_id')
                    ->relationship('level', 'name')
                    ->label('Level'),
                Tables\Filters\SelectFilter::make('topics')
                    ->relationship('topics', 'name')
                    ->label('Topics')
                    ->preload()
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'view' => Pages\ViewCourse::route('/{record}'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
