<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class UserResource extends Resource
{
    protected static ?string $model = User::class;


    protected static ?string $navigationLabel = "Users";

    protected static ?string $modelLabel = "User";

    protected static ?string $navigationGroup = "Account and roles";

    protected static ?string $slug = "user";

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
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make("email")
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make("password")
                            ->password()
                            ->maxLength(255),
                    ]),
                Forms\Components\Section::make("Roles")
                    ->description("Set roles for this user")
                    ->collapsible()
                    ->aside()
                    ->schema([
                        Forms\Components\Select::make("roles")
                            ->relationship("roles", "name")
                            ->multiple()
                            ->preload()
                            ->searchable()
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->searchable()
            ->deferLoading()
            ->heading("Users")
            ->description("Manage your users here.")
            ->columns([
                Tables\Columns\TextColumn::make("name")
                    ->placeholder("N/A")
                    ->searchable(),
                Tables\Columns\TextColumn::make("email")
                    ->placeholder("N/A")
                    ->searchable(),
                Tables\Columns\TextColumn::make("roles.name")
                    ->placeholder("N/A")
                    ->searchable(),
                Tables\Columns\TextColumn::make("created_at")
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make("updated_at")
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->color("primary"),
                    Tables\Actions\EditAction::make()->color("success"),
                    Tables\Actions\DeleteAction::make()->color("danger"),

                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading("No users yet")
            ->emptyStateDescription("Once you create a user, it will appear here.")
            ->emptyStateIcon("heroicon-o-user")
            ->emptyStateActions([
                Tables\Actions\Action::make("create")
                    ->label("Create record")
                    ->url(fn() => UserResource::getUrl("create"))
                    ->icon("heroicon-o-plus-circle")
                    ->button(),
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
                        Infolists\Components\TextEntry::make("email")->placeholder("N/A"),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListUsers::route("/"),
            "create" => Pages\CreateUser::route("/create"),
            "view" => Pages\ViewUser::route("/{record}"),
            "edit" => Pages\EditUser::route("/{record}/edit"),
        ];
    }
}
