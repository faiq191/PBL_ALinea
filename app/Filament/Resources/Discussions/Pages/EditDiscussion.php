<?php

namespace App\Filament\Resources\Discussions\Pages;

use App\Filament\Resources\Discussions\DiscussionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditDiscussion extends EditRecord
{
    protected static string $resource = DiscussionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Pre-populate image_url if existing image is a URL
        if (!empty($data['image']) && Str::startsWith($data['image'], 'http')) {
            $data['image_url'] = $data['image'];
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
