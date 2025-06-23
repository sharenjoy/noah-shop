<?php

namespace Sharenjoy\NoahShop\Resources\UserResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Sharenjoy\NoahShop\Models\Address;
use Sharenjoy\NoahShop\Models\User;

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    protected static ?string $icon = 'heroicon-o-map-pin';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('noah-shop::noah-shop.address');
    }

    protected static function getRecordLabel(): ?string
    {
        return __('noah-shop::noah-shop.address');
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return $ownerRecord->addresses->count();
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema(\Sharenjoy\NoahCms\Utils\Form::make(Address::class, $form->getOperation()));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitle(fn(Address $record): string => "({$record->id}) {$record->title}")
            ->heading(__('noah-shop::noah-shop.address'))
            ->columns(\Sharenjoy\NoahCms\Utils\Table::make(Address::class))
            ->filters(\Sharenjoy\NoahCms\Utils\Filter::make(Address::class, User::class))
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                // Tables\Actions\AttachAction::make()->preloadRecordSelect()->recordSelectSearchColumns(['title', 'description', 'slug'])->multiple(),
            ])
            ->actions([
                // Tables\Actions\DetachAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($action, $record) {
                        if ($record->is_default) {
                            Notification::make()
                                ->danger()
                                ->title('刪除失敗')
                                ->body('此筆資料為預設項目，無法刪除。如需要刪除請先將預設項目更改為其他選項！')
                                ->send();

                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DetachBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
