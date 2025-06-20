<?php

namespace Sharenjoy\NoahShop\Pages\Settings;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Closure;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;
use Sharenjoy\NoahCms\Actions\Shop\RoleCan;
use Sharenjoy\NoahCms\Actions\Shop\ShopFeatured;

class SyntaxSettings extends BaseSettings
{
    use HasPageShield;

    protected static ?int $navigationSort = 78;

    protected static ?string $navigationIcon = null;

    public static function getNavigationGroup(): string
    {
        return __('noah-cms::noah-cms.settings');
    }

    public static function getNavigationLabel(): string
    {
        return '語法';
    }

    public function schema(): array|Closure
    {
        return [
            Section::make('語法相關')
                ->visible(fn(): bool => RoleCan::run(role: 'creator'))
                ->schema([
                    Section::make('使用者條件設定(此區塊保留給維護工程人員使用)')
                        ->schema([
                            Repeater::make('code.user_conditions')
                                ->label('條件')
                                ->schema([
                                    TextInput::make('name')
                                        ->label('條件名稱')
                                        ->required()
                                        ->placeholder('輸入條件名稱'),

                                    Textarea::make('code')
                                        ->label('條件程式碼')
                                        ->rows(5)
                                        ->required()
                                        ->placeholder('輸入條件程式碼')
                                ])
                                ->addActionLabel('新增條件') // 自訂新增按鈕的文字
                                ->collapsible(false) // 允許展開/摺疊每個項目
                                ->defaultItems(1) // 預設新增一個條件
                                ->deletable(true) // 禁止刪除
                                ->reorderable(true)
                                ->minItems(1), // 最少需要一個條件

                        ]),
                    Section::make('商品條件設定(此區塊保留給維護工程人員使用)')
                        ->schema([
                            Repeater::make('code.product_conditions')
                                ->label('條件')
                                ->schema([
                                    TextInput::make('name')
                                        ->label('條件名稱')
                                        ->required()
                                        ->placeholder('輸入條件名稱'),

                                    Textarea::make('code')
                                        ->label('條件程式碼')
                                        ->rows(5)
                                        ->required()
                                        ->placeholder('輸入條件程式碼')
                                ])
                                ->addActionLabel('新增條件') // 自訂新增按鈕的文字
                                ->collapsible(false) // 允許展開/摺疊每個項目
                                ->defaultItems(1) // 預設新增一個條件
                                ->deletable(true) // 禁止刪除
                                ->reorderable(true)
                                ->minItems(1), // 最少需要一個條件

                        ]),
                ]),
        ];
    }
}
