<?php

namespace Sharenjoy\NoahShop\Pages\Settings;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Closure;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;
use Sharenjoy\NoahCms\Actions\Shop\ShopFeatured;

class OrderSettings extends BaseSettings
{
    use HasPageShield;

    protected static ?int $navigationSort = 74;

    protected static ?string $navigationIcon = null;

    public function getTitle(): string
    {
        return __('noah-shop::noah-shop.order_setting');
    }

    public static function getNavigationGroup(): string
    {
        return __('noah-shop::noah-shop.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('noah-shop::noah-shop.order');
    }

    public function schema(): array|Closure
    {
        return [
            Section::make('訂單相關')
                ->visible(fn(): bool => ShopFeatured::run('shop'))
                ->schema([
                    Section::make('運費相關')->schema([
                        TextInput::make('order.delivery_free_limit')
                            ->label('免運金額')
                            ->numeric()
                            ->required(),
                        KeyValue::make('order.delivery_price')
                            ->label('運費設定')
                            ->addable(false)   // 禁止新增
                            ->deletable(false) // 禁止刪除
                            ->keyLabel('地區')
                            ->valueLabel('運費')
                            ->required()
                            ->columnSpanFull(),
                    ]),
                    Section::make('注意事項')->schema([
                        Textarea::make('order.order_info_list.notice')
                            ->label('訂單明細-注意事項')
                            ->rows(4)
                            ->translatable(true, null, [
                                'zh_TW' => ['required'],
                            ]),
                        Textarea::make('order.picking_list.notice')
                            ->label('揀貨單-注意事項')
                            ->rows(4)
                            ->translatable(true, null, [
                                'zh_TW' => ['required'],
                            ]),
                    ]),
                ]),
        ];
    }
}
