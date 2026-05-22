<?php

namespace App\Filament\Resources\Discussions\Pages;

use App\Filament\Resources\Discussions\DiscussionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDiscussion extends CreateRecord
{
    protected static string $resource = DiscussionResource::class;
}
