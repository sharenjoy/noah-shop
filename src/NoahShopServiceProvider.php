<?php

namespace Sharenjoy\NoahShop;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schedule;
use RalphJSmit\Filament\Activitylog\Infolists\Components\Timeline;
use Sharenjoy\NoahShop\Commands\GenerateCouponPromos;
use Sharenjoy\NoahShop\Commands\UpdateObjectiveTargets;
use Sharenjoy\NoahShop\Http\View\Composers\Survey\SurveyComposer;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NoahShopServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('noah-shop')
            ->hasRoutes(['web', 'api'])
            ->hasConfigFile([
                'noah-shop',
                'filament-shield',
                'countries',
                'currency',
            ])
            ->hasViews()
            ->hasTranslations()
            ->discoversMigrations()
            ->hasAssets()
            ->hasCommands([
                UpdateObjectiveTargets::class,
                GenerateCouponPromos::class,
            ]);
    }

    public function bootingPackage()
    {
        app()->make(ViewFactory::class)->composer('noah-shop::standard', SurveyComposer::class);
    }

    public function packageBooted()
    {
        Model::unguard();

        $this->enforceMorphMap();

        Schedule::command('noah-shop:update-objective-targets')->dailyAt('00:30');
        Schedule::command('noah-shop:generate-coupon-promos')->dailyAt('01:30');

        Timeline::configureUsing(function (Timeline $timeline) {
            return $timeline
                ->compact()
                ->modelLabels([
                    Models\Invoice::class => __('noah-shop::noah-shop.activity.title.invoice'),
                    Models\Order::class => __('noah-shop::noah-shop.activity.title.order'),
                    Models\InvoicePrice::class => __('noah-shop::noah-shop.activity.title.invoice_price'),
                    Models\OrderItem::class => __('noah-shop::noah-shop.activity.title.order_item'),
                    Models\OrderShipment::class => __('noah-shop::noah-shop.activity.title.order_shipment'),
                    Models\User::class => __('noah-shop::noah-shop.activity.title.user'),
                    Models\Transaction::class => __('noah-shop::noah-shop.activity.title.transaction'),
                    Models\Promo::class => __('noah-shop::noah-shop.activity.title.promo'),
                ])
                ->itemIconColors([
                    'created' => 'info',
                    'deleted' => 'danger',
                ])
                ->itemIcons([
                    'created' => 'heroicon-o-plus',
                    'deleted' => 'heroicon-o-trash',
                ]);
        });
    }

    protected function enforceMorphMap(): void
    {
        $models = [
            Models\Address::class,
            Models\Brand::class,
            Models\CoinMutation::class,
            Models\Country::class,
            Models\Currency::class,
            Models\Giftproduct::class,
            Models\Invoice::class,
            Models\InvoicePrice::class,
            Models\Objective::class,
            Models\Order::class,
            Models\OrderItem::class,
            Models\OrderShipment::class,
            Models\Product::class,
            Models\ProductSpecification::class,
            Models\Promo::class,
            Models\StockMutation::class,
            Models\Survey\Answer::class,
            Models\Survey\Entry::class,
            Models\Survey\Question::class,
            Models\Survey\Section::class,
            Models\Survey\Survey::class,
            Models\Transaction::class,
            Models\User::class,
            Models\UserCoupon::class,
            Models\UserCouponStatus::class,
            Models\UserLevel::class,
            Models\UserLevelStatus::class,
        ];

        Relation::enforceMorphMap(
            collect($models)
                ->mapWithKeys(fn (string $model): array => [class_basename($model) => $model])
                ->all()
        );
    }
}
