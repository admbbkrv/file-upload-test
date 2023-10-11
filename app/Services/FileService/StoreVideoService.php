<?php

namespace App\Services\FileService;

use App\Http\Requests\StoreFileRequest;

class StoreVideoService extends StoreFileService
{
    protected string $pathPublicStore = 'public/videos';
}
