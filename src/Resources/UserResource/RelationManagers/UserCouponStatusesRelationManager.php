<?php

namespace Sharenjoy\NoahShop\Resources\UserResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Sharenjoy\NoahShop\Models\UserCouponStatus;
use Sharenjoy\NoahShop\Resources\Traits\CanViewShop;

class UserCouponStatusesRelationManager extends RelationManager
{
    use CanViewShop;

    protected static string $relationship = 'userCouponStatuses';

    protected static ?string $icon = 'heroicon-o-ticket';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('noah-cms::noah-cms.user_coupon_status');
    }

    protected static function getRecordLabel(): ?string
    {
        return __('noah-cms::noah-cms.user_coupon_status');
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return $ownerRecord->userCouponStatuses->count();
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema(\Sharenjoy\NoahCms\Utils\Form::make(UserCouponStatus::class, $form->getOperation()));
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('noah-cms::noah-cms.user_coupon_status'))
            ->columns(\Sharenjoy\NoahCms\Utils\Table::make(UserCouponStatus::class))
            ->filters(\Sharenjoy\NoahCms\Utils\Filter::make(UserCouponStatus::class))
            ->searchable(false)
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                // Tables\Actions\AttachAction::make()->preloadRecordSelect()->recordSelectSearchColumns(['code'])->multiple(),
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
            ])
            ->defaultSort('created_at', 'desc');
    }
}
