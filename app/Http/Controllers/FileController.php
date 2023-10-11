<?php

namespace App\Http\Controllers;


use App\Http\Requests\StoreFileRequest;
use App\Services\FileService\DTO\StoreFileDTO;
use App\Services\FileService\StoreVideoService;

class FileController extends Controller
{
    public function __construct(private StoreVideoService $storeVideoService)
    {
    }

    public function storeVideo(StoreFileRequest $request)
    {
        $storeFileDTO = StoreFileDTO::fromRequest($request);
        $path = $this->storeVideoService->storeFile($storeFileDTO);
    }
}
