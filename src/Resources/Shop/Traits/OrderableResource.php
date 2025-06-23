<?php

namespace Sharenjoy\NoahShop\Resources\Shop\Traits;

use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use RalphJSmit\Filament\Activitylog\Infolists\Components\Timeline;
use Sharenjoy\NoahShop\Actions\Shop\DisplayOrderShipmentDetail;
use Sharenjoy\NoahShop\Enums\DeliveryProvider;
use Sharenjoy\NoahShop\Enums\DeliveryType;
use Sharenjoy\NoahShop\Enums\InvoiceType;
use Sharenjoy\NoahShop\Enums\OrderShipmentStatus;
use Sharenjoy\NoahShop\Enums\OrderStatus;
use Sharenjoy\NoahShop\Enums\PaymentMethod;
use Sharenjoy\NoahShop\Enums\PaymentProvider;
use Sharenjoy\NoahShop\Enums\TransactionStatus;
use Sharenjoy\NoahShop\Infolists\Components\OrderEntry;
use Sharenjoy\NoahShop\Models\Invoice;
use Sharenjoy\NoahShop\Models\InvoicePrice;
use Sharenjoy\NoahShop\Models\OrderItem;
use Sharenjoy\NoahShop\Models\OrderShipment;
use Sharenjoy\NoahShop\Models\Transaction;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\RelationManagers\InvoicePricesRelationManager;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\RelationManagers\OrderItemsRelationManager;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\RelationManagers\UserRelationManager;
use Sharenjoy\NoahCms\Resources\Traits\NoahBaseResource;
use Spatie\Activitylog\Models\Activity;

trait OrderableResource
{
    use NoahBaseResource;

    public static function getNavigationGroup(): ?string
    {
        return __('noah-shop::noah-shop.order');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user.orders', 'user.validOrders', 'user.tags', 'shipment', 'shipments', 'invoice', 'transaction', 'items']);
    }

    public static function form(Form $form): Form
    {
        return $form->columns(3)->schema([]);
    }

    public static function table(Table $table): Table
    {
        $table = static::chainTableFunctions($table);
        return $table
            ->columns(array_merge(static::getTableStartColumns(), \Sharenjoy\NoahCms\Utils\Table::make(static::getModel())))
            ->filters([
                Filter::make('shipment')
                    ->label(__('noah-shop::noah-shop.order_shipment_status'))
                    ->form([
                        Select::make('shipment')
                            ->label(__('noah-shop::noah-shop.order_shipment_status'))
                            ->options(OrderShipmentStatus::options()),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['shipment'] ?? null) {
                            $query->whereHas('shipment', function (Builder $q) use ($data) {
                                $q->where('status', $data['shipment']);
                            });
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['shipment'] ?? null) {
                            return __('noah-shop::noah-shop.order_shipment_status') . ': ' . OrderShipmentStatus::tryFrom($data['shipment'])->getLabel();
                        }
                        return null;
                    }),

                Filter::make('delivery_provider')
                    ->label(__('noah-shop::noah-shop.delivery_provider'))
                    ->form([
                        Select::make('delivery_provider')
                            ->label(__('noah-shop::noah-shop.delivery_provider'))
                            ->options(DeliveryProvider::options()),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['delivery_provider'] ?? null) {
                            $query->whereHas('shipment', function (Builder $q) use ($data) {
                                $q->where('provider', $data['delivery_provider']);
                            });
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['delivery_provider'] ?? null) {
                            return __('noah-shop::noah-shop.delivery_provider') . ': ' . DeliveryProvider::tryFrom($data['delivery_provider'])->getLabel();
                        }
                        return null;
                    }),

                Filter::make('delivery_type')
                    ->label(__('noah-shop::noah-shop.activity.label.delivery_type'))
                    ->form([
                        Select::make('delivery_type')
                            ->label(__('noah-shop::noah-shop.activity.label.delivery_type'))
                            ->options(DeliveryType::options()),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['delivery_type'] ?? null) {
                            $query->whereHas('shipment', function (Builder $q) use ($data) {
                                $q->where('delivery_type', $data['delivery_type']);
                            });
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['delivery_type'] ?? null) {
                            return __('noah-shop::noah-shop.activity.label.delivery_type') . ': ' . DeliveryType::tryFrom($data['delivery_type'])->getLabel();
                        }
                        return null;
                    }),

                Filter::make('transaction')
                    ->label(__('noah-shop::noah-shop.transaction_status'))
                    ->form([
                        Select::make('transaction')
                            ->label(__('noah-shop::noah-shop.transaction_status'))
                            ->options(TransactionStatus::options()),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['transaction'] ?? null) {
                            $query->whereHas('transaction', function (Builder $q) use ($data) {
                                $q->where('status', $data['transaction']);
                            });
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['transaction'] ?? null) {
                            return __('noah-shop::noah-shop.transaction_status') . ': ' . TransactionStatus::tryFrom($data['transaction'])->getLabel();
                        }
                        return null;
                    }),

                Filter::make('payment_provider')
                    ->label(__('noah-shop::noah-shop.payment_provider'))
                    ->form([
                        Select::make('payment_provider')
                            ->label(__('noah-shop::noah-shop.payment_provider'))
                            ->options(PaymentProvider::options()),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['payment_provider'] ?? null) {
                            $query->whereHas('transaction', function (Builder $q) use ($data) {
                                $q->where('provider', $data['payment_provider']);
                            });
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['payment_provider'] ?? null) {
                            return __('noah-shop::noah-shop.payment_provider') . ': ' . PaymentProvider::tryFrom($data['payment_provider'])->getLabel();
                        }
                        return null;
                    }),

                Filter::make('payment_method')
                    ->label(__('noah-shop::noah-shop.activity.label.payment_method'))
                    ->form([
                        Select::make('payment_method')
                            ->label(__('noah-shop::noah-shop.activity.label.payment_method'))
                            ->options(PaymentMethod::options()),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['payment_method'] ?? null) {
                            $query->whereHas('transaction', function (Builder $q) use ($data) {
                                $q->where('payment_method', $data['payment_method']);
                            });
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['payment_method'] ?? null) {
                            return __('noah-shop::noah-shop.activity.label.payment_method') . ': ' . PaymentMethod::tryFrom($data['payment_method'])->getLabel();
                        }
                        return null;
                    }),

                Filter::make('invoice')
                    ->label(__('noah-shop::noah-shop.invoice_type'))
                    ->form([
                        Select::make('invoice')
                            ->label(__('noah-shop::noah-shop.invoice_type'))
                            ->options(InvoiceType::options()),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['invoice'] ?? null) {
                            $query->whereHas('invoice', function (Builder $q) use ($data) {
                                $q->where('type', $data['invoice']);
                            });
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['invoice'] ?? null) {
                            return __('noah-shop::noah-shop.invoice_type') . ': ' . InvoiceType::tryFrom($data['invoice'])->getLabel();
                        }
                        return null;
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('view_order_info_list')
                        ->icon('heroicon-o-document-text')
                        ->label(__('noah-shop::noah-shop.view_order_info_list'))
                        ->url(function ($record) {
                            return self::getUrl('info-list', ['record' => $record]);
                        }),
                    Tables\Actions\Action::make('view_order_picking_list')
                        ->icon('heroicon-o-document-text')
                        ->label(__('noah-shop::noah-shop.view_order_picking_list'))
                        ->url(function ($record) {
                            return self::getUrl('picking-list', ['record' => $record]);
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make(array_merge(static::getBulkActions(), [])),
            ])
            ->reorderable(false);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(__('noah-shop::noah-shop.order'))
                    ->schema([
                        OrderEntry::make(''),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
                // \Filament\Infolists\Components\Section::make(__('noah-shop::noah-shop.order_items'))
                //     ->schema([
                //         \Sharenjoy\NoahCms\Infolists\Components\OrderItemsEntry::make(''),
                //     ])
                //     ->collapsible()
                //     ->columnSpanFull(),
                Section::make(__('noah-shop::noah-shop.timeline'))
                    ->schema([
                        Timeline::make()
                            ->searchable()
                            ->hiddenLabel()
                            ->withRelations(['items', 'invoice', 'transaction', 'invoicePrices', 'shipment'])
                            ->eventDescriptions(
                                descriptions: [
                                    'updated-order-status' => function (Activity $activity) {
                                        $options = OrderStatus::options();
                                        $log = "**{$activity->causer->name}** 更新了 **狀態** 從 " . $options[$activity->properties['old']['status']] . ' 變更為 **' . $options[$activity->properties['attributes']['status']] . '**';
                                        if ($activity->properties['notes'] ?? null) {
                                            $log .= ' **備註** ' . $activity->properties['notes'];
                                        }
                                        return $log;
                                    },
                                ]
                            )
                            ->itemIcon('updated-order-status', 'heroicon-o-arrows-right-left')
                            ->itemIconColor('updated-order-status', 'warning')
                            ->getRecordTitleUsing(OrderItem::class, function (OrderItem $model) {
                                return $model->product->title . '(' . implode(',', $model->product_details['spec_detail_name'] ?? []) . ') x ' . $model->quantity . ' ' . __('noah-shop::noah-shop.activity.label.item_subtotal') . ' ' . currency_format($model->subtotal, $model->currency);
                            })
                            ->getRecordTitleUsing(Invoice::class, function (Invoice $model) {
                                return $model->type->getLabel();
                            })
                            ->getRecordTitleUsing(InvoicePrice::class, function (InvoicePrice $model) {
                                return $model->type->getLabel() . ' ' . $model->value;
                            })
                            ->getRecordTitleUsing(Transaction::class, function (Transaction $model) {
                                return $model->status->getLabel() . ' ' . $model->provider->getLabel() . ' ' . $model->payment_method->getLabel() . ' ' . currency_format($model->total_price, $model->currency);
                            })
                            ->getRecordTitleUsing(OrderShipment::class, function (OrderShipment $model) {
                                return $model->provider->getLabel() . ' ' . $model->delivery_type->getLabel() . ' ' . str_replace('<br>', ' ', DisplayOrderShipmentDetail::run($model));
                            })
                            ->attributeLabel('delivery_type', __('noah-shop::noah-shop.activity.label.delivery_type'))
                            ->attributeLabel('name', __('noah-shop::noah-shop.activity.label.name'))
                            ->attributeLabel('calling_code', __('noah-shop::noah-shop.activity.label.calling_code'))
                            ->attributeLabel('mobile', __('noah-shop::noah-shop.activity.label.mobile'))
                            ->attributeLabel('country', __('noah-shop::noah-shop.activity.label.country'))
                            ->attributeLabel('postcode', __('noah-shop::noah-shop.activity.label.postcode'))
                            ->attributeLabel('city', __('noah-shop::noah-shop.activity.label.city'))
                            ->attributeLabel('district', __('noah-shop::noah-shop.activity.label.district'))
                            ->attributeLabel('address', __('noah-shop::noah-shop.activity.label.address'))
                            ->attributeLabel('pickup_store_no', __('noah-shop::noah-shop.activity.label.pickup_store_no'))
                            ->attributeLabel('pickup_store_name', __('noah-shop::noah-shop.activity.label.pickup_store_name'))
                            ->attributeLabel('pickup_store_address', __('noah-shop::noah-shop.activity.label.pickup_store_address'))
                            ->attributeLabel('pickup_retail_name', __('noah-shop::noah-shop.activity.label.pickup_retail_name'))
                            ->attributeLabel('postoffice_delivery_code', __('noah-shop::noah-shop.activity.label.postoffice_delivery_code'))
                            ->attributeLabel('provider', __('noah-shop::noah-shop.activity.label.provider'))
                            ->attributeLabel('status', __('noah-shop::noah-shop.activity.label.status'))
                            ->attributeLabel('price', __('noah-shop::noah-shop.activity.label.price'))
                            ->attributeLabel('discount', __('noah-shop::noah-shop.activity.label.discount'))
                            ->attributeLabel('total_price', __('noah-shop::noah-shop.activity.label.total_price'))
                            ->attributeLabel('donate_code', __('noah-shop::noah-shop.activity.label.donate_code'))
                            ->attributeLabel('holder_code', __('noah-shop::noah-shop.activity.label.holder_code'))
                            ->attributeLabel('holder_type', __('noah-shop::noah-shop.activity.label.holder_type'))
                            ->attributeLabel('company_title', __('noah-shop::noah-shop.activity.label.company_title'))
                            ->attributeLabel('company_code', __('noah-shop::noah-shop.activity.label.company_code'))
                            ->attributeLabel('type', __('noah-shop::noah-shop.activity.label.type'))
                            ->attributeLabel('payment_method', __('noah-shop::noah-shop.activity.label.payment_method')),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            OrderItemsRelationManager::class,
            InvoicePricesRelationManager::class,
            // OrderShipmentsRelationManager::class,
            // TransactionsRelationManager::class,
            UserRelationManager::class,
        ];
    }
}
