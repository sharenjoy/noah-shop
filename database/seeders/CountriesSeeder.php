<?php

namespace Sharenjoy\NoahShop\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webpatser\Countries\Countries;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Empty the countries table
        DB::table(config('countries.table_name'))->delete();

        //Get all of the countries
        $countries = (new Countries())->getList();
        foreach ($countries as $countryId => $country) {
            DB::table(config('countries.table_name'))->insert(array(
                'id' => $countryId,
                'capital' => ((isset($country['capital'])) ? $country['capital'] : null),
                'citizenship' => ((isset($country['citizenship'])) ? $country['citizenship'] : null),
                'country_code' => $country['country-code'],
                'currency' => ((isset($country['currency'])) ? $country['currency'] : null),
                'currency_code' => ((isset($country['currency_code'])) ? $country['currency_code'] : null),
                'currency_sub_unit' => ((isset($country['currency_sub_unit'])) ? $country['currency_sub_unit'] : null),
                'currency_decimals' => ((isset($country['currency_decimals'])) ? $country['currency_decimals'] : null),
                'full_name' => ((isset($country['full_name'])) ? $country['full_name'] : null),
                'iso_3166_2' => $country['iso_3166_2'],
                'iso_3166_3' => $country['iso_3166_3'],
                'name' => $country['name'],
                'region_code' => $country['region-code'],
                'sub_region_code' => $country['sub-region-code'],
                'eea' => (bool)$country['eea'],
                'calling_code' => $country['calling_code'],
                'currency_symbol' => ((isset($country['currency_symbol'])) ? $country['currency_symbol'] : null),
                'flag' => ((isset($country['flag'])) ? $country['flag'] : null),
            ));
        }
    }
}
