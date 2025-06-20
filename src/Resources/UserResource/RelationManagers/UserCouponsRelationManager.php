<?php

namespace Sharenjoy\NoahShop\Resources\UserResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Actions\Table\RelationManagerAction;
use Illuminate\Database\Eloquent\Model;
use Sharenjoy\NoahShop\Models\UserCoupon;
use Sharenjoy\NoahShop\Resources\Traits\CanViewShop;
use Sharenjoy\NoahShop\Resources\UserResource\RelationManagers\UserCouponStatusesRelationManager;

class UserCouponsRelationManager extends RelationManager
{
    use CanViewShop;

    protected static string $relationship = 'coupons';

    protected static ?string $icon = 'heroicon-o-ticket';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('noah-cms::noah-cms.coupon_promo');
    }

    protected static function getRecordLabel(): ?string
    {
        return __('noah-cms::noah-cms.coupon_promo');
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return $ownerRecord->coupons->count();
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema(\Sharenjoy\NoahCms\Utils\Form::make(UserCoupon::class, $form->getOperation()));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitle(fn(UserCoupon $record): string => "({$record->id}) {$record->code}")
            ->heading(__('noah-cms::noah-cms.coupon_promo'))
            ->columns(\Sharenjoy\NoahCms\Utils\Table::make(UserCoupon::class))
            ->filters(\Sharenjoy\NoahCms\Utils\Filter::make(UserCoupon::class))
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                // Tables\Actions\AttachAction::make()->preloadRecordSelect()->recordSelectSearchColumns(['code'])->multiple(),
            ])
            ->actions([
                // Tables\Actions\DetachAction::make(),
                // Tables\Actions\EditAction::make(),
                RelationManagerAction::make('user-coupon-status-relation-manager')
                    ->label(__('noah-cms::noah-cms.user_coupon_statuses'))
                    ->icon('heroicon-o-ticket')
                    ->relationManager(UserCouponStatusesRelationManager::make()),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DetachBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
