<?php

namespace Sharenjoy\NoahShop\Resources\UserResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Sharenjoy\NoahShop\Models\Objective;
use Sharenjoy\NoahShop\Models\User;
use Sharenjoy\NoahShop\Resources\Shop\ObjectiveResource;
use Sharenjoy\NoahShop\Resources\Traits\CanViewShop;
use Sharenjoy\NoahCms\Resources\Traits\NoahBaseRelationManager;

class ObjectivesRelationManager extends RelationManager
{
    use NoahBaseRelationManager;
    use CanViewShop;

    protected static string $relationship = 'objectives';

    protected static ?string $icon = 'heroicon-o-viewfinder-circle';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('noah-shop::noah-shop.objective');
    }

    protected static function getRecordLabel(): ?string
    {
        return __('noah-shop::noah-shop.objective');
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return $ownerRecord->objectives->count();
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema(\Sharenjoy\NoahCms\Utils\Form::make(Objective::class, $form->getOperation()));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitle(fn(Objective $record): string => "({$record->id}) {$record->title}")
            ->heading(__('noah-shop::noah-shop.objective'))
            ->columns(array_merge(static::getTableStartColumns(ObjectiveResource::class), \Sharenjoy\NoahCms\Utils\Table::make(Objective::class)))
            ->filters(\Sharenjoy\NoahCms\Utils\Filter::make(Objective::class, User::class))
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make()->preloadRecordSelect()->recordSelectSearchColumns(['title', 'type', 'description'])->multiple(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
