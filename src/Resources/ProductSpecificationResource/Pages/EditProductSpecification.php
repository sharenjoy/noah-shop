<?php

namespace Sharenjoy\NoahShop\Resources\ProductSpecificationResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Sharenjoy\NoahShop\Actions\StoreRecordBackToProductSpecs;
use Sharenjoy\NoahShop\Resources\ProductSpecificationResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahEditRecord;

class EditProductSpecification extends EditRecord
{
    use NoahEditRecord;

    protected static string $resource = ProductSpecificationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function beforeSave(): void
    {
        $data = $this->form->getState();

        try {
            StoreRecordBackToProductSpecs::run($data['spec_detail_name'] ?? [], $this->record->product, 'edit', $this->record);
        } catch (\Exception $e) {
            Notification::make()
                ->title(__('noah-cms::noah-cms.error'))
                ->body($e->getMessage())
                ->danger()
                ->send();

            $this->halt();
        }
    }
}
