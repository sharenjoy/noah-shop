<?php

namespace Sharenjoy\NoahShop\Resources\Shop;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Sharenjoy\NoahShop\Models\UserLevel;
use Sharenjoy\NoahShop\Resources\Shop\UserLevelResource\Pages;
use Sharenjoy\NoahShop\Resources\Shop\UserLevelResource\RelationManagers\UsersRelationManager;
use Sharenjoy\NoahShop\Resources\Traits\CanViewShop;
use Sharenjoy\NoahCms\Resources\Traits\NoahBaseResource;

class UserLevelResource extends Resource implements HasShieldPermissions
{
    use NoahBaseResource;
    use CanViewShop;

    protected static ?string $model = UserLevel::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?int $navigationSort = 30;

    public static function getNavigationGroup(): ?string
    {
        return __('noah-shop::noah-shop.promo');
    }

    public static function getModelLabel(): string
    {
        return __('noah-shop::noah-shop.user_level');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema(array_merge(\Sharenjoy\NoahCms\Utils\Form::make(static::getModel(), $form->getOperation()), [
                Section::make('折扣碼設定')
                    ->schema([
                        TextInput::make('discount_percent')
                            ->label(__('noah-shop::noah-shop.shop.promo.title.discount_percent'))
                            ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.userlevel_discount_percent')))
                            ->prefixIcon('heroicon-o-percent-badge')
                            ->suffix('%')
                            ->placeholder(25)
                            ->minValue(1)
                            ->maxValue(100)
                            ->numeric()
                            ->rules(['numeric', 'min:1']),
                        TextInput::make('point_times')
                            ->label(__('noah-shop::noah-shop.shop.promo.title.point_times'))
                            ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.point_times')))
                            ->prefixIcon('heroicon-o-cursor-arrow-rays')
                            ->suffix('倍數')
                            ->placeholder('1.2')
                            ->required()
                            ->rules(['required', 'regex:/^[1-9]\d*(\.\d{1,2})?$/']),
                        TextInput::make('level_up_amount')
                            ->label(__('noah-shop::noah-shop.shop.promo.title.level_up_amount'))
                            ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.level_up_amount')))
                            ->prefixIcon('heroicon-o-arrow-trending-up')
                            ->suffix('元')
                            ->placeholder(3000)
                            ->minValue(1)
                            ->numeric()
                            ->rules(['numeric', 'min:1']),
                        Radio::make('auto_level_up')
                            ->label(__('noah-shop::noah-shop.shop.promo.title.auto_level_up'))
                            ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.auto_level_up')))
                            ->options([
                                true => __('noah-shop::noah-shop.yes'),
                                false => __('noah-shop::noah-shop.no'),
                            ])
                            ->default(false)
                            ->inline()
                            ->inlineLabel(false),
                        Radio::make('forever')
                            ->label(__('noah-shop::noah-shop.shop.promo.title.forever'))
                            ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.userlevel_forever')))
                            ->options([
                                true => __('noah-shop::noah-shop.yes'),
                                false => __('noah-shop::noah-shop.no'),
                            ])
                            ->default(false)
                            ->inline()
                            ->inlineLabel(false)
                            ->live(),
                        Select::make('level_duration')
                            ->label(__('noah-shop::noah-shop.shop.promo.title.level_duration'))
                            ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.level_duration')))
                            ->options([
                                1 => '1年',
                                2 => '2年',
                                3 => '3年',
                                4 => '4年',
                                5 => '5年',
                            ])
                            ->required()
                            ->visible(fn(Get $get): bool => !$get('forever')),
                    ]),
            ]));
    }

    public static function table(Table $table): Table
    {
        $table = static::chainTableFunctions($table);
        return $table
            ->columns(array_merge(static::getTableStartColumns(), \Sharenjoy\NoahCms\Utils\Table::make(static::getModel())))
            ->filters(\Sharenjoy\NoahCms\Utils\Filter::make(static::getModel()))
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\RestoreAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
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
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserLevels::route('/'),
            'create' => Pages\CreateUserLevel::route('/create'),
            'edit' => Pages\EditUserLevel::route('/{record}/edit'),
            'view' => Pages\ViewUserLevel::route('/{record}'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return array_merge(static::getCommonPermissionPrefixes(), []);
    }
}
