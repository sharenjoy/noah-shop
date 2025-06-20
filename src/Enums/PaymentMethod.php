<?php

namespace Sharenjoy\NoahShop\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;
use Sharenjoy\NoahCms\Enums\Traits\BaseEnum;

enum PaymentMethod: string implements HasLabel, HasDescription, HasColor
{
    use BaseEnum;

    case CreditCard = 'creditcard';
    case ATM = 'atm';
    case COD = 'cod';
    case LINEPay = 'linepay';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CreditCard => __('noah-shop::noah-shop.shop.type.title.payment.creditcard'),
            self::ATM => __('noah-shop::noah-shop.shop.type.title.payment.atm'),
            self::COD => __('noah-shop::noah-shop.shop.type.title.payment.cod'),
            self::LINEPay => __('noah-shop::noah-shop.shop.type.title.payment.linepay'),
        };
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            self::CreditCard => __('noah-shop::noah-shop.shop.type.description.payment.creditcard'),
            self::ATM => __('noah-shop::noah-shop.shop.type.description.payment.atm'),
            self::COD => __('noah-shop::noah-shop.shop.type.description.payment.cod'),
            self::LINEPay => __('noah-shop::noah-shop.shop.type.description.payment.linepay'),
        };
    }

    public function getColor(): array|string|null
    {
        return match ($this) {
            self::CreditCard => Color::Amber,
            self::ATM => Color::Amber,
            self::COD => Color::Amber,
            self::LINEPay => Color::Amber,
        };
    }
}
