<?php

namespace Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions;

use Filament\Actions\Action;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Sharenjoy\NoahShop\Actions\Shop\FetchAddressRelatedSelectOptions;
use Sharenjoy\NoahShop\Actions\Shop\FetchCountryRelatedSelectOptions;
use Sharenjoy\NoahShop\Enums\DeliveryProvider;
use Sharenjoy\NoahShop\Enums\DeliveryType;
use Sharenjoy\NoahShop\Models\BaseOrder;

class EditShipmentAction
{
    public static function make(BaseOrder $order = null)
    {
        return Action::make('editShipment')
            ->label('編輯運送資訊')
            ->modalHeading('編輯運送資訊')
            ->icon('heroicon-o-truck')
            ->form(self::form('edit'))
            ->mountUsing(function (ComponentContainer $form, $record) {
                $form->fill(['shipment' => $record->shipment->toArray()]);
            })
            ->action(function (array $data, $record) {
                $record->shipment->update($data['shipment']);
            })
            ->requiresConfirmation();
    }

    public static function form(string $action)
    {
        $extraAttributes = $action == 'edit' ? ['style' => 'background-color: #f8f8f8'] : [];

        return [
            Section::make('物流資訊')
                ->extraAttributes($extraAttributes)
                ->columnSpanFull()
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Select::make('shipment.provider')
                                ->label(__('noah-shop::noah-shop.activity.label.provider'))
                                ->options(DeliveryProvider::visibleOptions())
                                ->required()
                                ->live(),

                            Select::make('shipment.delivery_type')
                                ->label(__('noah-shop::noah-shop.activity.label.delivery_type'))
                                ->options(DeliveryType::visibleOptions())
                                ->required()
                                ->live(),
                        ]),
                ]),

            Section::make('收件人資訊')
                ->extraAttributes($extraAttributes)
                ->columnSpanFull()
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Select::make('shipment.calling_code')
                                ->label(__('noah-shop::noah-shop.activity.label.calling_code'))
                                ->options(FetchCountryRelatedSelectOptions::run('calling_code'))
                                ->searchable()
                                ->required(),
                            TextInput::make('shipment.mobile')->label(__('noah-shop::noah-shop.activity.label.mobile'))->required(),
                        ]),
                    Grid::make(1)
                        ->schema([
                            TextInput::make('shipment.name')->label(__('noah-shop::noah-shop.activity.label.name'))->required(),
                        ]),
                ]),

            Section::make('運送地址')
                ->extraAttributes($extraAttributes)
                ->columnSpanFull()
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Select::make('shipment.country')
                                ->label(__('noah-shop::noah-shop.activity.label.country'))
                                ->options(FetchCountryRelatedSelectOptions::run('country'))
                                ->searchable()
                                ->required()
                                ->live(),
                            TextInput::make('shipment.postcode')->label(__('noah-shop::noah-shop.activity.label.postcode'))->required(),
                            Select::make('shipment.city')
                                ->label(__('noah-shop::noah-shop.activity.label.city'))
                                ->visible(fn(Get $get): bool => $get('shipment.country') == 'Taiwan')
                                ->options(FetchAddressRelatedSelectOptions::run('city'))
                                ->searchable()
                                ->required()
                                ->live(),
                            Select::make('shipment.district')
                                ->label(__('noah-shop::noah-shop.activity.label.district'))
                                ->options(fn(Get $get) => FetchAddressRelatedSelectOptions::run('district', $get('shipment.city')))
                                ->searchable()
                                ->required()
                                ->visible(fn(Get $get): bool => $get('shipment.country') == 'Taiwan'),
                        ]),
                    Grid::make(1)
                        ->schema([
                            Textarea::make('shipment.address')->rows(2)->label(__('noah-shop::noah-shop.activity.label.address'))->required(),
                        ]),
                ])->visible(fn(Get $get): bool => $get('shipment.delivery_type') == DeliveryType::Address->value),

            Section::make('超商取貨資訊')
                ->extraAttributes($extraAttributes)
                ->columnSpanFull()
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('shipment.pickup_store_no')->label(__('noah-shop::noah-shop.activity.label.pickup_store_no'))->required(),
                            TextInput::make('shipment.pickup_store_name')->label(__('noah-shop::noah-shop.activity.label.pickup_store_name'))->required(),
                        ]),
                    Grid::make(1)
                        ->schema([
                            Textarea::make('shipment.pickup_store_address')->rows(2)->label(__('noah-shop::noah-shop.activity.label.pickup_store_address'))->required(),
                        ]),
                ])->visible(fn(Get $get): bool => $get('shipment.delivery_type') == DeliveryType::Pickinstore->value),

            Section::make('門市取貨資訊')
                ->extraAttributes($extraAttributes)
                ->columnSpanFull()
                ->schema([
                    Grid::make(1)
                        ->schema([
                            TextInput::make('shipment.pickup_retail_name')->label(__('noah-shop::noah-shop.activity.label.pickup_retail_name'))->required(),
                        ]),
                ])->visible(fn(Get $get): bool => $get('shipment.delivery_type') == DeliveryType::Pickinretail->value),

            Section::make('郵局取貨資訊')
                ->extraAttributes($extraAttributes)
                ->columnSpanFull()
                ->schema([
                    Grid::make(1)
                        ->schema([
                            TextInput::make('shipment.postoffice_delivery_code')->label(__('noah-shop::noah-shop.activity.label.postoffice_delivery_code'))->required(),
                        ]),
                ])->visible(fn(Get $get): bool => ($get('shipment.provider') == DeliveryProvider::Postoffice->value && $get('shipment.delivery_type') == DeliveryType::Address->value)),

        ];
    }
}
