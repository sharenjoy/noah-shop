<?php

namespace Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions;

use Filament\Actions\Action;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Sharenjoy\NoahShop\Enums\PaymentMethod;
use Sharenjoy\NoahShop\Enums\PaymentProvider;
use Sharenjoy\NoahShop\Models\BaseOrder;

class EditTransactionAction
{
    public static function make(BaseOrder $order = null)
    {
        return Action::make('editTransaction')
            ->label('編輯付款資訊')
            ->modalHeading('編輯付款資訊')
            ->icon('heroicon-o-cube-transparent')
            ->form(self::form('edit'))
            ->mountUsing(function (ComponentContainer $form, $record) {
                $form->fill(['transaction' => $record->transaction->toArray()]);
            })
            ->action(function (array $data, $record) {
                $record->transaction->update($data['transaction']);
            })
            ->requiresConfirmation();
    }

    public static function form(string $action)
    {
        $extraAttributes = $action == 'edit' ? ['style' => 'background-color: #f8f8f8'] : [];

        return [
            Section::make('付款方式')
                ->extraAttributes($extraAttributes)
                ->columnSpanFull()
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Select::make('transaction.provider')
                                ->label(__('noah-cms::noah-cms.activity.label.provider'))
                                ->options(PaymentProvider::class)
                                ->required()
                                ->live(),

                            Select::make('transaction.payment_method')
                                ->label(__('noah-cms::noah-cms.activity.label.payment_method'))
                                ->options(PaymentMethod::class)
                                ->required()
                                ->live(),
                        ]),
                ]),

            // Section::make('ATM')
            //     ->extraAttributes($extraAttributes)
            //     ->columnSpanFull()
            //     ->schema([
            //         Grid::make(2)
            //             ->schema([
            //                 TextInput::make('transaction.atm_code')
            //                     ->required()
            //                     ->label(__('noah-cms::noah-cms.activity.label.atm_code')),
            //                 DateTimePicker::make('transaction.expired_at')
            //                     ->required()
            //                     ->prefixIcon('heroicon-o-clock')
            //                     ->format('Y-m-d H:i:s')
            //                     ->native(false)
            //                     ->label(__('noah-cms::noah-cms.activity.label.expired_at')),
            //             ]),
            //     ])
            //     ->visible(fn(Get $get): bool => $get('transaction.payment_method') == PaymentMethod::ATM->value),
        ];
    }
}
