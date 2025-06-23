<?php

namespace Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions;

use Filament\Actions\Action;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Sharenjoy\NoahShop\Enums\InvoiceHolderType;
use Sharenjoy\NoahShop\Enums\InvoiceType;
use Sharenjoy\NoahShop\Models\BaseOrder;

class EditInvoiceAction
{
    public static function make(BaseOrder $order = null)
    {
        return Action::make('editInvoice')
            ->label('編輯發票資訊')
            ->modalHeading('編輯發票資訊')
            ->icon('heroicon-o-document-currency-dollar')
            ->form(self::form('edit'))
            ->mountUsing(function (ComponentContainer $form, $record) {
                $form->fill(['invoice' => $record->invoice->toArray()]);
            })
            ->action(function (array $data, $record) {
                $record->invoice->update($data['invoice']);
            })
            ->requiresConfirmation();
    }

    public static function form(string $action)
    {
        $extraAttributes = $action == 'edit' ? ['style' => 'background-color: #f8f8f8'] : [];

        return [
            Section::make('發票類型')
                ->extraAttributes($extraAttributes)
                ->columnSpanFull()
                ->schema([
                    Grid::make(1)
                        ->schema([
                            Select::make('invoice.type')
                                ->label(__('noah-shop::noah-shop.invoice_type'))
                                ->options(InvoiceType::class)
                                ->required()
                                ->live(),
                            Select::make('invoice.holder_type')
                                ->label(__('noah-shop::noah-shop.invoice_holder_type'))
                                ->options(InvoiceHolderType::class)
                                ->required()
                                ->visible(fn(Get $get): bool => $get('invoice.type') == InvoiceType::Holder->value),
                        ]),
                ]),

            Section::make('載具資訊')
                ->extraAttributes($extraAttributes)
                ->columnSpanFull()
                ->schema([
                    Grid::make(1)
                        ->schema([
                            TextInput::make('invoice.holder_code')
                                ->required()
                                ->label(__('noah-shop::noah-shop.invoice_holder_code')),
                        ]),
                ])
                ->visible(fn(Get $get): bool => $get('invoice.type') == InvoiceType::Holder->value),

            Section::make('捐贈單位')
                ->extraAttributes($extraAttributes)
                ->columnSpanFull()
                ->schema([
                    Grid::make(1)
                        ->schema([
                            Select::make('invoice.donate_code')
                                ->label(__('noah-shop::noah-shop.invoice_donate_code'))
                                ->required()
                                ->options(config('noah-shop.donate_code')),
                        ]),
                ])
                ->visible(fn(Get $get): bool => $get('invoice.type') == InvoiceType::Donate->value),

            Section::make('公司發票')
                ->extraAttributes($extraAttributes)
                ->columnSpanFull()
                ->schema([
                    Grid::make(1)
                        ->schema([
                            TextInput::make('invoice.company_title')
                                ->required()
                                ->label(__('noah-shop::noah-shop.invoice_company_title')),
                            TextInput::make('invoice.company_code')
                                ->required()
                                ->label(__('noah-shop::noah-shop.invoice_company_code')),
                        ]),
                ])
                ->visible(fn(Get $get): bool => $get('invoice.type') == InvoiceType::Company->value),
        ];
    }
}
