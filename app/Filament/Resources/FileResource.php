<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FileResource\Pages;
use App\Filament\Resources\FileResource\RelationManagers;
use App\Models\File;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Storage;

class FileResource extends Resource
{
    protected static ?string $model = File::class;

    protected static ?string $navigationLabel = "Files";

    protected static ?string $modelLabel = "File";

    protected static ?string $navigationGroup = "Files";

    protected static ?string $slug = "file";

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = "heroicon-o-rectangle-stack";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make("General details")
                    ->description("Fill all the general details")
                    ->collapsible()
                    ->aside()
                    ->schema([
                        Forms\Components\TextInput::make("name")
                            ->unique(ignoreRecord: true)
                            ->minLength(1)
                            ->maxLength(100)
                            ->required()
                            ->default(null),
                        Forms\Components\RichEditor::make("description")
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make("file")
                            ->columnSpanFull()
                            ->image()
                            ->directory(directory: "files")
                            ->default(null),
                        Forms\Components\Toggle::make("is_active")
                            ->label("Active")
                            ->required()
                            ->default(true),
                    ])
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make("General")
                    ->description("General details of this file")
                    ->collapsible()
                    ->aside()
                    ->schema([
                        Infolists\Components\TextEntry::make("id")->label("ID")->placeholder("N/A"),
                        Infolists\Components\TextEntry::make("name")->placeholder("N/A"),
                        Infolists\Components\TextEntry::make("description")->placeholder("N/A"),
                        Infolists\Components\TextEntry::make("createdBy.name")->placeholder("N/A"),
                        Infolists\Components\TextEntry::make("is_active")
                            ->badge()
                            ->label("Active")
                            ->formatStateUsing(fn(string $state): string => $state ? "Yes" : "No")
                            ->color(fn(string $state): string => $state ? "success" : "danger"),
                        Infolists\Components\TextEntry::make("created_at")
                            ->dateTime()
                            ->since()
                            ->tooltip(fn(string $state): string => date("F d, Y H:i A", strtotime($state))),
                        Infolists\Components\TextEntry::make("updated_at")
                            ->dateTime()
                            ->since()
                            ->tooltip(fn(string $state): string => date("F d, Y H:i A", strtotime($state))),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->searchable()
            ->deferLoading()
            ->heading("Files")
            ->description("Manage your files here.")
            ->columns([
                Tables\Columns\TextColumn::make("id")
                    ->limit(10)
                    ->label("ID")
                    ->sortable()
                    ->placeholder("N/A")
                    ->searchable(),
                Tables\Columns\TextColumn::make("name")
                    ->limit(10)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make("createdBy.name")
                    ->limit(10)
                    ->sortable()
                    ->label("Created by")
                    ->tooltip(fn($state): string => $state)
                    ->searchable(),
                Tables\Columns\ToggleColumn::make("is_active")->label("Active"),
                Tables\Columns\TextColumn::make("created_at")
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip(fn($state): string => date("F d, Y H:i A", strtotime($state))),
                Tables\Columns\TextColumn::make("updated_at")
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip(fn($state): string => date("F d, Y H:i A", strtotime($state))),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->color("primary"),
                    Tables\Actions\EditAction::make()->color("success"),
                    Tables\Actions\DeleteAction::make()->color("danger"),
                    Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon("heroicon-o-arrow-down-tray")
                    ->action(function ($record) {
                        return Storage::disk('public')->download($record->file);
                    }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading("No files yet")
            ->emptyStateDescription("Once you create a file, it will appear here.")
            ->emptyStateIcon("heroicon-o-document")
            ->emptyStateActions([
                Tables\Actions\Action::make("create")
                    ->label("Create record")
                    ->url(fn() => FileResource::getUrl("create"))
                    ->icon("heroicon-o-plus-circle")
                    ->button(),
            ]);
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
            "index" => Pages\ListFiles::route("/"),
            "create" => Pages\CreateFile::route("/create"),
            "view" => Pages\ViewFile::route("/{record}"),
            "edit" => Pages\EditFile::route("/{record}/edit"),
        ];
    }
}
