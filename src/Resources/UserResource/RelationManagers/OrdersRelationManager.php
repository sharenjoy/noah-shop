<?php

namespace Sharenjoy\NoahShop\Resources\UserResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Sharenjoy\NoahShop\Models\Order;
use Sharenjoy\NoahShop\Models\User;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahBaseRelationManager;

class OrdersRelationManager extends RelationManager
{
    use NoahBaseRelationManager;

    protected static string $relationship = 'orders';

    protected static ?string $icon = 'heroicon-o-shopping-bag';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('noah-shop::noah-shop.order');
    }

    protected static function getRecordLabel(): ?string
    {
        return __('noah-shop::noah-shop.order');
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return $ownerRecord->orders->count();
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema(\Sharenjoy\NoahCms\Utils\Form::make(Order::class, $form->getOperation()));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitle(fn(Order $record): string => "({$record->id}) {$record->title}")
            ->heading(__('noah-shop::noah-shop.order'))
            ->columns(array_merge(static::getTableStartColumns(OrderResource::class), \Sharenjoy\NoahCms\Utils\Table::make(Order::class)))
            ->filters(\Sharenjoy\NoahCms\Utils\Filter::make(Order::class, User::class))
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                // Tables\Actions\AttachAction::make()->preloadRecordSelect()->recordSelectSearchColumns(['title', 'description', 'slug'])->multiple(),
            ])
            ->actions([
                // Tables\Actions\DetachAction::make(),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DetachBulkAction::make(),
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
