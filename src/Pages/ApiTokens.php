<?php

namespace Sharenjoy\NoahShop\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class ApiTokens extends Page implements HasForms
{
    use HasPageShield;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static string $view = 'noah-shop::pages.api-tokens';

    protected static ?int $navigationSort = 50;

    public static function getNavigationGroup(): ?string
    {
        return __('noah-shop::noah-shop.resource');
    }

    public ?array $data = [];
    public ?string $plainTextToken = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Token 名稱')
                    ->required(),

                CheckboxList::make('abilities')
                    ->label('權限')
                    // 預設選擇全部權限
                    ->default(['*'])
                    ->options([
                        // 'orders:read' => '讀取訂單',
                        // 'orders:create' => '建立訂單',
                        '*' => '全部權限',
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function createToken()
    {
        $user = Auth::user();

        $token = $user->createToken(
            $this->data['name'],
            $this->data['abilities'] ?? ['*']
        );

        $this->plainTextToken = $token->plainTextToken;

        $this->form->fill(); // reset form
    }
}
