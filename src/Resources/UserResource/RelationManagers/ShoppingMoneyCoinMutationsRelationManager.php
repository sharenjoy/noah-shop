<?php

namespace Sharenjoy\NoahShop\Resources\UserResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Sharenjoy\NoahShop\Actions\Shop\RoleCan;
use Sharenjoy\NoahShop\Actions\Shop\ShopFeatured;
use Sharenjoy\NoahShop\Enums\CoinType;
use Sharenjoy\NoahShop\Models\CoinMutation;
use Sharenjoy\NoahShop\Models\User;
use Sharenjoy\NoahShop\Resources\Traits\CanViewShop;

class ShoppingMoneyCoinMutationsRelationManager extends RelationManager
{
    use CanViewShop;

    protected static string $relationship = 'shoppingmoneyCoinMutations';

    protected static ?string $icon = 'heroicon-o-currency-dollar';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        if (! ShopFeatured::run('coin-shoppingmoney')) {
            return false;
        }

        return parent::canViewForRecord($ownerRecord, $pageClass);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('noah-cms::noah-cms.user_shoppingmoney_record');
    }

    protected static function getRecordLabel(): ?string
    {
        return __('noah-cms::noah-cms.user_shoppingmoney_record');
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return $ownerRecord->shoppingmoneyCoinMutations->count();
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema(\Sharenjoy\NoahCms\Utils\Form::make(CoinMutation::class, $form->getOperation()));
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('noah-cms::noah-cms.user_shoppingmoney_record'))
            ->columns(\Sharenjoy\NoahCms\Utils\Table::make(CoinMutation::class))
            ->filters(\Sharenjoy\NoahCms\Utils\Filter::make(CoinMutation::class))
            ->searchable(false)
            ->headerActions([
                Tables\Actions\CreateAction::make()->mutateFormDataUsing(function (array $data): array {
                    $data['reference_type'] = User::class; // 設定建立者
                    $data['reference_id'] = Auth::user()->id; // 設定建立者
                    $data['type'] = CoinType::ShoppingMoney->value;
                    return $data;
                })->visible(fn(): bool => RoleCan::run(role: 'super_admin')),
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
