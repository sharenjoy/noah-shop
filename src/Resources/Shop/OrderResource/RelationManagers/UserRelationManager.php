<?php

namespace Sharenjoy\NoahShop\Resources\Shop\OrderResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Sharenjoy\NoahCms\Models\User;
use Sharenjoy\NoahCms\Resources\Traits\NoahBaseRelationManager;
use Sharenjoy\NoahCms\Resources\UserResource;

class UserRelationManager extends RelationManager
{
    use NoahBaseRelationManager;

    protected static string $relationship = 'user';

    protected static ?string $icon = 'heroicon-o-user-circle';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('noah-cms::noah-cms.user');
    }

    protected static function getRecordLabel(): ?string
    {
        return __('noah-cms::noah-cms.user');
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema(\Sharenjoy\NoahCms\Utils\Form::make(User::class, $form->getOperation()));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitle(fn(User $record): string => "({$record->id}) {$record->name}\r{$record->email}")
            ->heading(__('noah-cms::noah-cms.user'))
            ->searchable(false)
            ->columns(array_merge(static::getTableStartColumns(UserResource::class), \Sharenjoy\NoahCms\Utils\Table::make(User::class)))
            // ->filters(\Sharenjoy\NoahCms\Utils\Filter::make(User::class, Role::class))
            ->headerActions([
                // Tables\Actions\AttachAction::make()->preloadRecordSelect()->recordSelectSearchColumns(['name', 'email'])->multiple(),
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DetachBulkAction::make(),
                ]),
            ])->recordUrl(null);
    }
}
