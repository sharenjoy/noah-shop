<?php

namespace Sharenjoy\NoahShop\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Sharenjoy\NoahShop\Models\Product;

class ResolveProductSpecsDataToRecords
{
    use AsAction;

    protected ?array $specs;
    protected Product $model;
    protected array $specResults = [];

    public function handle(?array $specs, Product $model, string $action)
    {
        $this->specs = $specs;
        $this->model = $model;

        $this->{$action . 'Records'}();
    }

    protected function createRecords()
    {
        if ($this->model->is_single_spec) {
            $this->model->specifications()->create([
                'spec_detail_name' => 'single_spec',
                'is_active' => true,
            ]);

            return;
        }

        $this->generateCombinations($this->specs);

        foreach ($this->specResults as $result) {
            $this->model->specifications()->create([
                'spec_detail_name' => $result,
                'is_active' => true,
            ]);
        }
    }

    // 遞迴函數，生成交錯組合
    protected function generateCombinations(array $specs, $index = 0, $current = [])
    {
        if ($index === count($specs)) {
            // 當前組合完成，輸出結果
            $this->specResults[] = $current;
            return;
        }
        if (count($specs[$index]['spec_details'])) {
            foreach ($specs[$index]['spec_details'] as $detail) {
                $newCombination = $current;
                $newCombination[$specs[$index]['spec_name']] = $detail['detail_name'];
                $this->generateCombinations($specs, $index + 1, $newCombination);
            }
        } else {
            $this->generateCombinations($specs, $index + 1, $current);
        }
    }
}
