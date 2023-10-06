<?php

namespace App\Services;

use App\Services\FileService\StoreFileService;

class StoreVideoService extends StoreFileService
{
    protected string $pathPublicStore = 'public/videos';
}
