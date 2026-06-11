<?php

namespace App\Filament\Resources\Discussions\Pages;

use App\Filament\Resources\Discussions\DiscussionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDiscussion extends CreateRecord
{
    protected static string $resource = DiscussionResource::class;

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
