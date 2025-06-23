<?php

namespace Sharenjoy\NoahShop\Resources\Survey\EntryResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Sharenjoy\NoahShop\Enums\SurveyEntryStatus;

class StatusesRelationManager extends RelationManager
{
    protected static string $relationship = 'statuses';

    protected static ?string $icon = 'heroicon-o-light-bulb';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('noah-shop::noah-shop.status');
    }

    protected static function getRecordLabel(): ?string
    {
        return __('noah-shop::noah-shop.status');
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return $ownerRecord->statuses->count();
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('noah-shop::noah-shop.status'))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('noah-shop::noah-shop.status'))
                    ->formatStateUsing(function ($state) {
                        $enum = SurveyEntryStatus::tryFrom($state);
                        return $enum->getLabel();
                    })
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('reason')
                    ->label(__('noah-shop::noah-shop.content'))
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('noah-shop::noah-shop.created_at'))
                    ->since()
                    ->toggleable(),
            ])
            ->filters([])
            ->searchable(false)
            // ->headerActions([
            //     // Tables\Actions\CreateAction::make(),
            //     // Tables\Actions\AttachAction::make()->preloadRecordSelect()->recordSelectSearchColumns(['code'])->multiple(),
            // ])
            // ->actions([
            //     // Tables\Actions\DetachAction::make(),
            //     // Tables\Actions\EditAction::make(),
            //     // Tables\Actions\DeleteAction::make(),
            // ])
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         // Tables\Actions\DetachBulkAction::make(),
            //         // Tables\Actions\DeleteBulkAction::make(),
            //     ]),
            // ])
            ->defaultSort('created_at', 'desc');
    }
}
