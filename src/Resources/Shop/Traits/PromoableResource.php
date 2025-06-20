<?php

namespace Sharenjoy\NoahShop\Resources\Shop\Traits;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Sharenjoy\NoahShop\Enums\PromoDiscountType;
use Sharenjoy\NoahShop\Resources\Traits\CanViewShop;
use Sharenjoy\NoahCms\Resources\Traits\NoahBaseResource;

trait PromoableResource
{
    use NoahBaseResource;
    use CanViewShop;

    public static function getNavigationGroup(): ?string
    {
        return __('noah-cms::noah-cms.promo');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema(array_merge(
                \Sharenjoy\NoahCms\Utils\Form::make(static::getModel(), $form->getOperation()),
                [
                    Section::make('促銷規則設定')
                        ->schema([
                            TextInput::make('min_order_amount')
                                ->label(__('noah-shop::noah-shop.shop.promo.title.min_order_amount'))
                                ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.min_order_amount')))
                                ->prefixIcon('heroicon-o-currency-dollar')
                                ->default(0)
                                ->placeholder(1200)
                                ->minValue(0)
                                ->numeric()
                                ->required()
                                ->rules(['numeric', 'min:0']),
                            Fieldset::make('level')
                                ->columns(1)
                                ->label(__('noah-shop::noah-shop.shop.promo.title.level'))
                                ->schema([
                                    Radio::make('combined')
                                        ->label(__('noah-shop::noah-shop.shop.promo.title.combined'))
                                        ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.combined')))
                                        ->options([
                                            true => __('noah-cms::noah-cms.yes'),
                                            false => __('noah-cms::noah-cms.no'),
                                        ])
                                        ->default(true)
                                        ->inline()
                                        ->inlineLabel(false)
                                        ->live(),
                                    Select::make('promo_tags')
                                        ->label(__('noah-shop::noah-shop.shop.promo.title.promo_tag'))
                                        ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.promo_tag')))
                                        ->preload()
                                        ->prefixIcon('heroicon-c-tag')
                                        ->relationship(name: 'promoTags', titleAttribute: 'name')
                                        ->searchable(['name', 'slug'])
                                        ->multiple()
                                        ->minItems(1)
                                        ->maxItems(3)
                                        ->required()
                                        ->visible(fn(Get $get): bool => !$get('combined')),
                                ]),
                            Fieldset::make('duration')
                                ->columns(1)
                                ->label(__('noah-shop::noah-shop.shop.promo.title.duration'))
                                ->schema([
                                    Radio::make('forever')
                                        ->label(__('noah-shop::noah-shop.shop.promo.title.forever'))
                                        ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.forever')))
                                        ->options([
                                            true => __('noah-cms::noah-cms.yes'),
                                            false => __('noah-cms::noah-cms.no'),
                                        ])
                                        ->default(false)
                                        ->inline()
                                        ->inlineLabel(false)
                                        ->live(),
                                    DateTimePicker::make('started_at')
                                        ->displayFormat('Y-m-d H:i:s') // 顯示格式
                                        ->label(__('noah-shop::noah-shop.shop.promo.title.started_at'))
                                        ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.started_at')))
                                        ->placeholder('2020-03-18 09:48:00')
                                        ->prefixIcon('heroicon-o-clock')
                                        ->format('Y-m-d H:i:s')
                                        ->required()
                                        ->native(false)
                                        ->live()
                                        ->visible(fn(Get $get): bool => !$get('forever')),
                                    DateTimePicker::make('expired_at')
                                        ->displayFormat('Y-m-d H:i:s') // 顯示格式
                                        ->label(__('noah-shop::noah-shop.shop.promo.title.expired_at'))
                                        ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.expired_at')))
                                        ->placeholder('2020-03-18 09:48:00')
                                        ->prefixIcon('heroicon-o-clock')
                                        ->format('Y-m-d H:i:s')
                                        ->required()
                                        ->rules(['required', 'date', 'after_or_equal:started_at'])
                                        ->native(false)
                                        ->minDate(fn(Get $get) => $get('started_at'))
                                        ->visible(fn(Get $get): bool => !$get('forever')),
                                ]),
                            Fieldset::make('discount_set')
                                ->columns(1)
                                ->label(__('noah-shop::noah-shop.shop.promo.title.discount_set'))
                                ->schema([
                                    Select::make('discount_type')
                                        ->label(__('noah-shop::noah-shop.shop.promo.title.discount_type'))
                                        ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.discount_type')))
                                        ->options(PromoDiscountType::class)
                                        ->required()
                                        ->live(),
                                    TextInput::make('discount_amount')
                                        ->label(__('noah-shop::noah-shop.shop.promo.title.discount_amount'))
                                        ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.discount_amount')))
                                        ->prefixIcon('heroicon-o-currency-dollar')
                                        ->placeholder(1200)
                                        ->numeric()
                                        ->minValue(0)
                                        ->required()
                                        ->rules(['required', 'numeric', 'min:0'])
                                        ->visible(fn(Get $get): bool => $get('discount_type') == PromoDiscountType::Amount->value),
                                    TextInput::make('discount_percent')
                                        ->label(__('noah-shop::noah-shop.shop.promo.title.discount_percent'))
                                        ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.discount_percent')))
                                        ->prefixIcon('heroicon-o-percent-badge')
                                        ->suffix('%')
                                        ->placeholder(20)
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->required()
                                        ->visible(fn(Get $get): bool => $get('discount_type') == PromoDiscountType::Percent->value),
                                    TextInput::make('discount_percent_limit_amount')
                                        ->label(__('noah-shop::noah-shop.shop.promo.title.discount_percent_limit_amount'))
                                        ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.discount_percent_limit_amount')))
                                        ->prefixIcon('heroicon-o-currency-dollar')
                                        ->placeholder(200)
                                        ->numeric()
                                        ->minValue(0)
                                        ->visible(fn(Get $get): bool => $get('discount_type') == PromoDiscountType::Percent->value),
                                    Select::make('giftproducts')
                                        ->label(__('noah-shop::noah-shop.shop.promo.title.gitproduct'))
                                        ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.gitproduct')))
                                        ->prefixIcon('heroicon-o-gift-top')
                                        ->preload()
                                        ->multiple()
                                        ->required()
                                        ->relationship(name: 'giftproducts', titleAttribute: 'title')
                                        ->searchable(['title', 'description'])
                                        ->visible(fn(Get $get): bool => $get('discount_type') == PromoDiscountType::Gift->value),
                                ]),
                        ]),
                ],
                [
                    Section::make('目標商品設定')
                        ->schema([
                            Select::make('productObjectives')
                                ->label(__('noah-shop::noah-shop.shop.promo.title.objective_product'))
                                ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.objective_product')))
                                ->prefixIcon('heroicon-o-squares-plus')
                                ->preload()
                                ->multiple()
                                ->relationship(name: 'productObjectives', titleAttribute: 'title')
                                ->searchable(['title', 'description']),
                        ]),

                    Section::make('目標使用者設定')
                        ->schema([
                            Select::make('userObjectives')
                                ->label(__('noah-shop::noah-shop.shop.promo.title.objective_user'))
                                ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.objective_user')))
                                ->prefixIcon('heroicon-o-user-circle')
                                ->preload()
                                ->multiple()
                                ->relationship(name: 'userObjectives', titleAttribute: 'title')
                                ->searchable(['title', 'description']),
                        ]),
                ],
                static::getPromoFormSchema(),
            ));
    }

    public static function table(Table $table): Table
    {
        $table = static::chainTableFunctions($table);
        return $table
            ->columns(array_merge(static::getTableStartColumns(), \Sharenjoy\NoahCms\Utils\Table::make(static::getModel())))
            ->filters(\Sharenjoy\NoahCms\Utils\Filter::make(static::getModel()))
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make(array_merge(static::getTableActions(), [])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make(array_merge(static::getBulkActions(), [])),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPermissionPrefixes(): array
    {
        return array_merge(static::getCommonPermissionPrefixes(), []);
    }
}
