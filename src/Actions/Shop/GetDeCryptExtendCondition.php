<?php

namespace Sharenjoy\NoahShop\Actions\Shop;

use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Lorisleiva\Actions\Concerns\AsAction;
use Sharenjoy\NoahShop\Models\User;
use Sharenjoy\NoahCms\Pages\Settings\Settings;

class GetDeCryptExtendCondition
{
    use AsAction;

    public function handle(string $type, ?string $key = null): array|string|null
    {
        $events = [];
        $code = null;
        $divider = config('noah-shop.promo.conditions_divider');

        $conditions = setting('code.' . $type . '_conditions');

        foreach ($conditions ?? [] as $condition) {
            if (!isset($condition['code']) || !isset($condition['name'])) {
                continue;
            }

            try {
                $decrypted = Crypt::decryptString($condition['code']);

                $check = explode($divider, $decrypted);

                if (head($check) !== config('noah-shop.promo.conditions_decrypter') || end($check) !== config('noah-shop.promo.conditions_decrypter')) {
                    continue;
                }

                if (count($check) !== 4) {
                    continue;
                }

                if ($key && $key == $check[1]) {
                    $code = $check[2];
                }

                $events[$check[1]] = $condition['name'];
            } catch (DecryptException $e) {
                Notification::make()
                    ->danger()
                    ->title($type . '條件設定解碼錯誤')
                    ->body($e->getMessage())
                    ->actions([
                        Action::make('View')->url(Settings::getUrl(['tab' => '-code-tab'])),
                    ])
                    ->sendToDatabase(User::query()->whereIn('email', config('noah-cms.creator_emails'))->get());
            }
        }

        if ($key) {
            return $code;
        }

        return $events;
    }
}
