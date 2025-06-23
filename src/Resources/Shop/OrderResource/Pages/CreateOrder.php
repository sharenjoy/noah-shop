<?php

namespace Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sharenjoy\NoahShop\Actions\Shop\OrderCreator;
use Sharenjoy\NoahShop\Models\Order;
use Sharenjoy\NoahShop\Models\ProductSpecification;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions\EditInvoiceAction;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions\EditShipmentAction;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions\EditTransactionAction;
use Sharenjoy\NoahCms\Resources\Traits\NoahCreateRecord;

class CreateOrder extends CreateRecord
{
    use NoahCreateRecord;
    use HasWizard;

    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                Wizard::make($this->getSteps())
                    ->startOnStep($this->getStartStep())
                    ->cancelAction($this->getCancelFormAction())
                    ->submitAction($this->getSubmitFormAction())
                    ->skippable($this->hasSkippableSteps())
                    ->contained(false),
            ])
            ->columns(null);
    }

    protected function handleRecordCreation(array $data): Model
    {
        DB::beginTransaction();

        try {
            $data = $this->form->getState();
            $order = OrderCreator::run($data);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $order;
    }

    protected function afterCreate(): void
    {
        /** @var Order $order */
        $order = $this->record;

        // $recipients = [
        //     Auth::user(), // 當前登入的使用者
        //     $order->user, // 訂單的購買人
        // ];

        Notification::make()
            ->title('新訂單建立成功')
            ->icon('heroicon-o-shopping-bag')
            ->body("{$order->user->name} 訂購了 {$order->items->count()} 個商品")
            ->actions([
                Action::make('View')
                    ->url(OrderResource::getUrl('view', ['record' => $order])),
            ]);
        // ->sendToDatabase($recipients);
    }

    /** @return Step[] */
    protected function getSteps(): array
    {
        return [
            Step::make('選擇購買人')
                ->schema([
                    Section::make()->schema([
                        Select::make('user_id')
                            ->label('購買人')
                            ->relationship('user', 'name')
                            ->searchable(['name', 'email'])
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} ({$record->email})")
                            ->preload()
                            ->required()
                            ->placeholder('選擇購買人'),

                        Textarea::make('notes')->rows(3)->label(__('noah-shop::noah-shop.order_notes')),

                    ])->columns(1)->extraAttributes(['style' => 'max-width: 600px; margin: 0 auto;']),
                ]),

            Step::make(__('noah-shop::noah-shop.order_items'))
                ->schema([
                    Section::make(__('noah-shop::noah-shop.order_items'))
                        ->schema([
                            Repeater::make('items')
                                ->label(__('noah-shop::noah-shop.order_items'))
                                ->schema([
                                    Select::make('product_specification_id')
                                        ->label(__('noah-shop::noah-shop.product'))
                                        ->options(function () {
                                            return ProductSpecification::all()->pluck('label', 'id');
                                        })
                                        ->searchable(['no', 'spec_detail_name', 'sku', 'barcode'])
                                        ->preload()
                                        ->required()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, Set $set) {
                                            $productSpec = ProductSpecification::find($state);
                                            $set('price', $productSpec?->price ?? 0);
                                            $set('product_id', $productSpec?->product_id);
                                            $set('product', $productSpec?->product);
                                            $set('productSpecification', $productSpec);
                                        })
                                        ->distinct()
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                        ->columnSpan([
                                            'md' => 5,
                                        ]),

                                    TextInput::make('quantity')
                                        ->label('數量')
                                        ->numeric()
                                        ->minValue(1)
                                        ->default(1)
                                        ->columnSpan([
                                            'md' => 2,
                                        ])
                                        ->required(),

                                    TextInput::make('price')
                                        ->label('金額')
                                        ->disabled()
                                        ->dehydrated()
                                        ->numeric()
                                        ->required()
                                        ->columnSpan([
                                            'md' => 3,
                                        ]),
                                ])
                                // ->orderColumn('order_column')
                                ->defaultItems(1)
                                ->hiddenLabel()
                                ->columns([
                                    'md' => 10,
                                ])
                                ->required()
                        ]),
                ]),

            Step::make('選擇運送方式')->schema(EditShipmentAction::form('create')),
            Step::make('選擇發票類型')->schema(EditInvoiceAction::form('create')),
            Step::make('選擇付款方式')->schema(EditTransactionAction::form('create')),
        ];
    }
}
