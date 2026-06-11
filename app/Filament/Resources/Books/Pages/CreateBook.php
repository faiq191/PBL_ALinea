<?php

namespace App\Filament\Resources\Books\Pages;

use App\Filament\Resources\Books\BookResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBook extends CreateRecord
{
    protected static string $resource = BookResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Priority: URL > file upload
        if (!empty($data['image_url'])) {
            $data['image'] = $data['image_url'];
        } elseif (!empty($data['image_file'])) {
            $data['image'] = $data['image_file'];
        }
        // Clean up virtual fields
        unset($data['image_url'], $data['image_file']);
        return $data;
    }
}
