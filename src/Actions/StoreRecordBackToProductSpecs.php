<?php

namespace Sharenjoy\NoahShop\Actions;

use Exception;
use Illuminate\Support\Arr;
use Lorisleiva\Actions\Concerns\AsAction;
use Sharenjoy\NoahShop\Models\Product;
use Sharenjoy\NoahShop\Models\ProductSpecification;

class StoreRecordBackToProductSpecs
{
    use AsAction;

    protected array $specDetails;
    protected Product $model;
    protected string $action;
    protected ?ProductSpecification $record;

    public function handle(array $specDetails, Product $model, string $action, ?ProductSpecification $record = null)
    {
        $this->specDetails = $specDetails;
        $this->model = $model;
        $this->action = $action;
        $this->record = $record;

        if ($this->model->is_single_spec) {
            return;
        }

        $this->{$action . 'Record'}();
    }

    protected function createRecord()
    {
        $specResults = $this->model->specifications->pluck('spec_detail_name')->toArray();

        if (!$this->isDuplicateCombination($this->specDetails, $specResults)) {
            $specResults[] = $this->specDetails;
        } else {
            throw new Exception('"' . json_encode($this->specDetails, JSON_UNESCAPED_UNICODE) . '" 此規格組合已經存在！');
        }

        $this->model->update([
            'specs' => $this->rebuildSpecsFromCombinations($specResults)
        ]);
    }

    protected function editRecord()
    {
        if ($this->record->spec_detail_name === $this->specDetails) {
            return;
        }

        $specResults = $this->model->specifications->pluck('spec_detail_name')->toArray();

        if (!$this->isDuplicateCombination($this->specDetails, $specResults)) {
            $specResults[] = $this->specDetails;
        } else {
            throw new Exception('"' . json_encode($this->specDetails, JSON_UNESCAPED_UNICODE) . '" 此規格組合已經存在！');
        }

        $this->model->update([
            'specs' => $this->rebuildSpecsFromCombinations($specResults)
        ]);
    }

    protected function isDuplicateCombination(array $newCombination, array $existingCombinations): bool
    {
        foreach ($existingCombinations as $existing) {
            if ($newCombination == $existing) {
                return true; // 找到重複的
            }
        }
        return false; // 沒有重複
    }

    protected function rebuildSpecsFromCombinations(array $combinations): array
    {
        $specMap = [];

        foreach ($combinations as $combo) {
            foreach ($combo as $specName => $detailName) {
                // 如果這個 spec_name 還沒加入，就初始化
                if (!isset($specMap[$specName])) {
                    $specMap[$specName] = [];
                }
                // 用值當 key 避免重複
                $specMap[$specName][$detailName] = true;
            }
        }

        // 把 map 組回你原本的格式
        $result = [];
        foreach ($specMap as $specName => $detailNames) {
            $details = [];
            foreach (array_keys($detailNames) as $name) {
                $details[] = ['detail_name' => $name];
            }

            $result[] = [
                'spec_name' => $specName,
                'spec_details' => $details
            ];
        }

        return $result;
    }
}
