<?php

namespace Sharenjoy\NoahShop\Actions\Shop;

use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Lorisleiva\Actions\Concerns\AsAction;
use Sharenjoy\NoahShop\Enums\ObjectiveStatus;
use Sharenjoy\NoahShop\Enums\ObjectiveType;
use Sharenjoy\NoahShop\Models\Objective;
use Sharenjoy\NoahShop\Models\Product;
use Sharenjoy\NoahShop\Models\User;
use Sharenjoy\NoahShop\Resources\Shop\ObjectiveResource;

class ResolveObjectiveTarget
{
    use AsAction;

    public function handle(Objective $objective): void
    {
        $objective->status = ObjectiveStatus::Processing;
        $objective->save();

        try {
            if ($objective->type === ObjectiveType::Product) {
                $this->resolveProduct($objective);
            } elseif ($objective->type === ObjectiveType::User) {
                $this->resolveUser($objective);
            }
        } catch (\Throwable $th) {
            // TODO: Log error
            $objective->status = ObjectiveStatus::Failed;
            $objective->save();

            Notification::make()
                ->danger()
                ->title('產生目標對象時發生錯誤')
                ->body($th->getMessage())
                ->actions([
                    Action::make('View')->url(ObjectiveResource::getUrl('edit', ['record' => $objective])),
                ])
                ->sendToDatabase(User::superAdmin()->get());
        }

        $objective->status = ObjectiveStatus::Finished;
        $objective->generated_at = now();
        $objective->save();
    }

    protected function resolveProduct($objective)
    {
        $query = Product::query();
        $setting = $objective->product;

        $removeCategories = $setting['remove']['categories'] ?? [];
        $removeTags = $setting['remove']['tags'] ?? [];
        $removeProducts = $setting['remove']['products'] ?? [];

        if ($setting['all']) {
            $query->where(function ($q) use ($removeCategories, $removeTags, $removeProducts) {
                if (!empty($removeCategories)) {
                    $q->whereDoesntHave('categories', fn($q) => $q->whereIn('id', $removeCategories));
                }
                if (!empty($removeTags)) {
                    $q->whereDoesntHave('tags', fn($q) => $q->whereIn('id', $removeTags));
                }
                if (!empty($removeProducts)) {
                    $q->whereNotIn('id', $removeProducts);
                }
            });
        } else {
            $addCategories = array_diff($setting['add']['categories'] ?? [], $removeCategories);
            $addTags = array_diff($setting['add']['tags'] ?? [], $removeTags);
            $addProducts = array_diff($setting['add']['products'] ?? [], $removeProducts);

            $query->where(function ($q) use ($addCategories, $addTags, $addProducts) {
                if (!empty($addCategories)) {
                    $q->whereHas('categories', fn($q) => $q->whereIn('id', $addCategories));
                }
                if (!empty($addTags)) {
                    $q->orWhereHas('tags', fn($q) => $q->whereIn('id', $addTags));
                }
                if (!empty($addProducts)) {
                    $q->orWhereIn('id', $addProducts);
                }
            })->where(function ($q) use ($removeCategories, $removeTags, $removeProducts) {
                if (!empty($removeCategories)) {
                    $q->whereDoesntHave('categories', fn($q) => $q->whereIn('id', $removeCategories));
                }
                if (!empty($removeTags)) {
                    $q->whereDoesntHave('tags', fn($q) => $q->whereIn('id', $removeTags));
                }
                if (!empty($removeProducts)) {
                    $q->whereNotIn('id', $removeProducts);
                }
            });
        }

        if ($setting['extend_condition'] ?? null) {
            $conditionCode = GetDeCryptExtendCondition::run('product', $setting['extend_condition']);

            if (!$conditionCode) {
                throw new \Exception(__('noah-shop::noah-shop.shop.promo.title.no_condition_code'));
            }

            $query->where(eval("return $conditionCode;"));
        }

        // 寫入objectiveables
        $objective->products()->sync($query->get()->pluck('id'), detaching: true);
    }

    protected function resolveUser($objective)
    {
        $query = User::query();
        $setting = $objective->user;

        $removeUserLevels = $setting['remove']['user_levels'] ?? [];
        $removeTags = $setting['remove']['tags'] ?? [];
        $removeUsers = $setting['remove']['users'] ?? [];

        if ($setting['all']) {
            $query->where(function ($q) use ($removeUserLevels, $removeTags, $removeUsers) {
                if (!empty($removeUserLevels)) {
                    $q->whereNotIn('user_level_id', $removeUserLevels);
                }
                if (!empty($removeTags)) {
                    $q->whereDoesntHave('tags', fn($q) => $q->whereIn('id', $removeTags));
                }
                if (!empty($removeUsers)) {
                    $q->whereNotIn('id', $removeUsers);
                }
            });
        } else {
            $addUserLevels = array_diff($setting['add']['user_levels'] ?? [], $removeUserLevels);
            $addTags = array_diff($setting['add']['tags'] ?? [], $removeTags);
            $addUsers = array_diff($setting['add']['users'] ?? [], $removeUsers);

            $query->where(function ($q) use ($addUserLevels, $addTags, $addUsers, $setting) {
                if (!empty($addUserLevels)) {
                    $q->orWhereIn('user_level_id', $addUserLevels);
                }
                if (!empty($addTags)) {
                    $q->orWhereHas('tags', fn($q) => $q->whereIn('id', $addTags));
                }
                if (!empty($addUsers)) {
                    $q->orWhereIn('id', $addUsers);
                }

                // Age filtering
                if (!empty($setting['parameter']['age']['age_start']) && !empty($setting['parameter']['age']['age_end'])) {
                    // 依據年齡區間算出什麼樣的birthday符合
                    $ageStart = now()->subYears((int)$setting['parameter']['age']['age_start'])->format('Y-m-d');
                    $ageEnd = now()->subYears((int)$setting['parameter']['age']['age_end'])->format('Y-m-d');
                    $q->orWhere(function ($q) use ($ageStart, $ageEnd) {
                        $q->where('birthday', '<=', $ageStart)
                            ->where('birthday', '>=', $ageEnd);
                    });
                }

                // Location filtering
                if (!empty($setting['parameter']['location']['country']) || !empty($setting['parameter']['location']['city']) || !empty($setting['parameter']['location']['district'])) {
                    $q->orWhereHas('addresses', function ($q) use ($setting) {
                        if (!empty($setting['parameter']['location']['country'])) {
                            $q->where('country', $setting['parameter']['location']['country']);
                        }
                        if (!empty($setting['parameter']['location']['city'])) {
                            $q->where('city', $setting['parameter']['location']['city']);
                        }
                        if (!empty($setting['parameter']['location']['district'])) {
                            $q->where('district', $setting['parameter']['location']['district']);
                        }
                    });
                }
            })->where(function ($q) use ($removeUserLevels, $removeTags, $removeUsers) {
                if (!empty($removeUserLevels)) {
                    $q->whereNotIn('user_level_id', $removeUserLevels);
                }
                if (!empty($removeTags)) {
                    $q->whereDoesntHave('tags', fn($q) => $q->whereIn('id', $removeTags));
                }
                if (!empty($removeUsers)) {
                    $q->whereNotIn('id', $removeUsers);
                }
            });
        }

        if ($setting['extend_condition'] ?? null) {
            $conditionCode = GetDeCryptExtendCondition::run('user', $setting['extend_condition']);

            if (!$conditionCode) {
                throw new \Exception(__('noah-shop::noah-shop.shop.promo.title.no_condition_code'));
            }

            $query->where(eval("return $conditionCode;"));
        }

        // 寫入objectiveables
        $objective->users()->sync($query->get()->pluck('id'), detaching: true);
    }

    public function asJob(Objective $objective): void
    {
        $this->handle($objective);
    }
}
