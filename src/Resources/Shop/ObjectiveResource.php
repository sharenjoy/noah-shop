<?php

namespace Sharenjoy\NoahShop\Resources\Shop;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Sharenjoy\NoahShop\Actions\Shop\FetchAddressRelatedSelectOptions;
use Sharenjoy\NoahShop\Actions\Shop\FetchCountryRelatedSelectOptions;
use Sharenjoy\NoahShop\Actions\Shop\GetDeCryptExtendCondition;
use Sharenjoy\NoahShop\Actions\Shop\ResolveObjectiveTarget;
use Sharenjoy\NoahShop\Enums\ObjectiveType;
use Sharenjoy\NoahCms\Models\Category;
use Sharenjoy\NoahShop\Models\Objective;
use Sharenjoy\NoahShop\Models\Product;
use Sharenjoy\NoahCms\Models\Tag;
use Sharenjoy\NoahShop\Models\User;
use Sharenjoy\NoahShop\Models\UserLevel;
use Sharenjoy\NoahShop\Resources\Shop\ObjectiveResource\Pages;
use Sharenjoy\NoahShop\Resources\Shop\ObjectiveResource\RelationManagers\PromosRelationManager;
use Sharenjoy\NoahShop\Resources\Traits\CanViewShop;
use Sharenjoy\NoahCms\Resources\Traits\NoahBaseResource;

class ObjectiveResource extends Resource implements HasShieldPermissions
{
    use NoahBaseResource;
    use CanViewShop;

    protected static ?string $model = Objective::class;

    protected static ?string $navigationIcon = 'heroicon-o-viewfinder-circle';

    protected static ?int $navigationSort = 32;

    public static function getNavigationGroup(): ?string
    {
        return __('noah-shop::noah-shop.promo');
    }

    public static function getModelLabel(): string
    {
        return __('noah-shop::noah-shop.objective');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema(array_merge(\Sharenjoy\NoahCms\Utils\Form::make(static::getModel(), $form->getOperation()), [
                Section::make('')
                    ->schema([
                        Select::make('type')
                            ->label(__('noah-shop::noah-shop.type'))
                            ->prefixIcon('heroicon-o-circle-stack')
                            ->options(ObjectiveType::class)
                            ->required()
                            ->preload()
                            ->live(),
                    ]),
                Section::make('目標商品設定')
                    ->visible(fn(Get $get): bool => $get('type') === ObjectiveType::Product->value)
                    ->schema([
                        Radio::make('product.all')
                            ->label(__('noah-shop::noah-shop.shop.promo.title.all_products'))
                            ->options([
                                true => __('noah-shop::noah-shop.yes'),
                                false => __('noah-shop::noah-shop.no'),
                            ])
                            ->default(true)
                            ->inline()
                            ->inlineLabel(false)
                            ->live(),

                        Fieldset::make('add_products')
                            ->columns(1)
                            ->label(__('noah-shop::noah-shop.shop.promo.title.add_products'))
                            ->visible(fn(Get $get): bool => !$get('product.all'))
                            ->schema([
                                Select::make('product.add.categories')
                                    ->label(__('noah-shop::noah-shop.shop.promo.title.product_category'))
                                    ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.product_category')))
                                    ->preload()
                                    ->prefixIcon('heroicon-c-circle-stack')
                                    ->options(Category::orderBy('order')->pluck('title', 'id'))
                                    ->searchable(['title', 'description', 'content', 'slug'])
                                    ->multiple(),
                                Select::make('product.add.tags')
                                    ->label(__('noah-shop::noah-shop.shop.promo.title.product_tag'))
                                    ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.product_tag')))
                                    ->preload()
                                    ->prefixIcon('heroicon-c-tag')
                                    ->options(Tag::whereType('product')->pluck('name', 'id'))
                                    ->searchable(['name', 'slug'])
                                    ->multiple(),
                                Select::make('product.add.products')
                                    ->label(__('noah-shop::noah-shop.product'))
                                    ->options(function () {
                                        return Product::all()->pluck('title', 'id');
                                    })
                                    ->searchable(['slug', 'title', 'description', 'content', 'specs'])
                                    ->preload()
                                    ->multiple(),
                            ]),
                        Fieldset::make('remove_products')
                            ->columns(1)
                            ->label(__('noah-shop::noah-shop.shop.promo.title.remove_products'))
                            ->schema([
                                Select::make('product.remove.categories')
                                    ->label(__('noah-shop::noah-shop.shop.promo.title.product_category'))
                                    ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.product_category')))
                                    ->preload()
                                    ->prefixIcon('heroicon-c-circle-stack')
                                    ->options(Category::orderBy('order')->pluck('title', 'id'))
                                    ->searchable(['title', 'description', 'content', 'slug'])
                                    ->multiple(),
                                Select::make('product.remove.tags')
                                    ->label(__('noah-shop::noah-shop.shop.promo.title.product_tag'))
                                    ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.product_tag')))
                                    ->preload()
                                    ->prefixIcon('heroicon-c-tag')
                                    ->options(Tag::whereType('product')->pluck('name', 'id'))
                                    ->searchable(['name', 'slug'])
                                    ->multiple(),
                                Select::make('product.remove.products')
                                    ->label(__('noah-shop::noah-shop.product'))
                                    ->options(function () {
                                        return Product::all()->pluck('title', 'id');
                                    })
                                    ->searchable(['slug', 'title', 'description', 'content', 'specs'])
                                    ->preload()
                                    ->multiple(),
                            ]),
                        Select::make('product.extend_condition')
                            ->label(__('noah-shop::noah-shop.shop.promo.title.extend_condition'))
                            ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.extend_condition')))
                            ->options(GetDeCryptExtendCondition::run('product')),
                    ]),
                Section::make('目標使用者設定')
                    ->visible(fn(Get $get): bool => $get('type') === ObjectiveType::User->value)
                    ->schema([
                        Radio::make('user.all')
                            ->label(__('noah-shop::noah-shop.shop.promo.title.all_users'))
                            ->options([
                                true => __('noah-shop::noah-shop.yes'),
                                false => __('noah-shop::noah-shop.no'),
                            ])
                            ->default(true)
                            ->inline()
                            ->inlineLabel(false)
                            ->live(),
                        Fieldset::make('user_parameters')
                            ->columns(1)
                            ->label(__('noah-shop::noah-shop.shop.promo.title.user_parameters'))
                            ->visible(fn(Get $get): bool => !$get('user.all'))
                            ->schema([
                                Section::make()
                                    ->columns(4)
                                    ->schema([
                                        TextInput::make('user.parameter.age.age_start')
                                            ->label(__('noah-shop::noah-shop.shop.promo.title.age_start'))
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(100)
                                            ->placeholder('20')
                                            ->suffix('歲')
                                            ->prefixIcon('heroicon-o-arrow-down-right')
                                            ->live(),

                                        TextInput::make('user.parameter.age.age_end')
                                            ->label(__('noah-shop::noah-shop.shop.promo.title.age_end'))
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(100)
                                            ->placeholder('60')
                                            ->suffix('歲')
                                            ->prefixIcon('heroicon-o-arrow-up-right')
                                            ->rules(['after_or_equal:user.parameter.age.age_start'])
                                            ->minValue(fn(Get $get) => $get('user.parameter.age.age_start')),
                                    ])
                                    ->hiddenLabel(),
                                Section::make('')
                                    ->columns(4)
                                    ->schema([
                                        Select::make('user.parameter.location.country')
                                            ->label(__('noah-shop::noah-shop.activity.label.country'))
                                            ->options(FetchCountryRelatedSelectOptions::run('country'))
                                            ->searchable()
                                            ->live(),
                                        Select::make('user.parameter.location.city')
                                            ->label(__('noah-shop::noah-shop.activity.label.city'))
                                            ->visible(fn(Get $get): bool => $get('user.parameter.location.country') == 'Taiwan')
                                            ->options(FetchAddressRelatedSelectOptions::run('city'))
                                            ->searchable()
                                            ->live(),
                                        Select::make('user.parameter.location.district')
                                            ->label(__('noah-shop::noah-shop.activity.label.district'))
                                            ->options(fn(Get $get) => FetchAddressRelatedSelectOptions::run('district', $get('user.parameter.location.city')))
                                            ->searchable()
                                            ->visible(fn(Get $get): bool => $get('user.parameter.location.country') == 'Taiwan'),
                                    ])
                                    ->hiddenLabel(),
                            ]),
                        Fieldset::make('add_users')
                            ->columns(1)
                            ->label(__('noah-shop::noah-shop.shop.promo.title.add_users'))
                            ->visible(fn(Get $get): bool => !$get('user.all'))
                            ->schema([
                                Select::make('user.add.user_levels')
                                    ->label(__('noah-shop::noah-shop.user_level'))
                                    ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.user_level')))
                                    ->preload()
                                    ->prefixIcon('heroicon-c-chart-bar')
                                    ->options(UserLevel::orderBy('order_column')->get()->pluck('title', 'id'))
                                    ->searchable(['title', 'description', 'content'])
                                    ->multiple(),
                                Select::make('user.add.tags')
                                    ->label(__('noah-shop::noah-shop.shop.promo.title.user_tag'))
                                    ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.user_tag')))
                                    ->preload()
                                    ->prefixIcon('heroicon-c-tag')
                                    ->options(Tag::whereType('user')->pluck('name', 'id'))
                                    ->searchable(['name', 'slug'])
                                    ->multiple(),
                                Select::make('user.add.users')
                                    ->label(__('noah-shop::noah-shop.user'))
                                    ->options(User::all()->pluck('name', 'id'))
                                    ->searchable(['name', 'email'])
                                    ->preload()
                                    ->multiple(),
                            ]),
                        Fieldset::make('remove_users')
                            ->columns(1)
                            ->label(__('noah-shop::noah-shop.shop.promo.title.remove_users'))
                            ->schema([
                                Select::make('user.remove.user_levels')
                                    ->label(__('noah-shop::noah-shop.user_level'))
                                    ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.user_level')))
                                    ->preload()
                                    ->prefixIcon('heroicon-c-chart-bar')
                                    ->options(UserLevel::orderBy('order_column')->get()->pluck('title', 'id'))
                                    ->searchable(['title', 'description', 'content'])
                                    ->multiple(),
                                Select::make('user.remove.tags')
                                    ->label(__('noah-shop::noah-shop.shop.promo.title.user_tag'))
                                    ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.user_tag')))
                                    ->preload()
                                    ->prefixIcon('heroicon-c-tag')
                                    ->options(Tag::whereType('user')->pluck('name', 'id'))
                                    ->searchable(['name', 'slug'])
                                    ->multiple(),
                                Select::make('user.remove.users')
                                    ->label(__('noah-shop::noah-shop.user'))
                                    ->options(User::all()->pluck('name', 'id'))
                                    ->searchable(['name', 'email'])
                                    ->preload()
                                    ->multiple(),
                            ]),
                        Fieldset::make('extend_condition')
                            ->columns(1)
                            ->label(__('noah-shop::noah-shop.shop.promo.title.extend_condition'))
                            ->schema([
                                Select::make('user.extend_condition')
                                    ->label(__('noah-shop::noah-shop.shop.promo.title.extend_condition'))
                                    ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.extend_condition')))
                                    ->options(GetDeCryptExtendCondition::run('user')),
                            ]),
                    ]),
            ]));
    }

    public static function table(Table $table): Table
    {
        $table = static::chainTableFunctions($table);
        return $table
            ->poll('10s')
            ->columns(array_merge(static::getTableStartColumns(), \Sharenjoy\NoahCms\Utils\Table::make(static::getModel())))
            ->filters(\Sharenjoy\NoahCms\Utils\Filter::make(static::getModel()))
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make(array_merge(static::getTableActions(), [
                    Tables\Actions\Action::make('dispatch')
                        ->label(__('noah-shop::noah-shop.start_generate'))
                        ->action(fn(Objective $record) => dispatch(ResolveObjectiveTarget::makeJob($record)))
                        ->requiresConfirmation()
                        ->icon('heroicon-o-play'),
                ])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make(array_merge(static::getBulkActions(), [
                    Tables\Actions\BulkAction::make('dispatch')
                        ->label(__('noah-shop::noah-shop.start_generate'))
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                dispatch(ResolveObjectiveTarget::makeJob($record));
                            }
                        })
                        ->requiresConfirmation()
                        ->icon('heroicon-o-play'),
                ])),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PromosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListObjectives::route('/'),
            'create' => Pages\CreateObjective::route('/create'),
            'edit' => Pages\EditObjective::route('/{record}/edit'),
            'view' => Pages\ViewObjective::route('/{record}'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return array_merge(static::getCommonPermissionPrefixes(), []);
    }
}
