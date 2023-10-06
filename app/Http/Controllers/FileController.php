<?php

namespace App\Http\Controllers;


use App\Http\Requests\StoreFileRequest;
use App\Services\FileService\DTO\StoreFileDTO;
use App\Services\FileService\GetFileService;
use App\Services\FileService\StoreFileService;
use App\Services\StoreVideoService;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    private StoreVideoService $storeVideoService;

    public function __construct(StoreVideoService $storeVideoService)
    {
        $this->storeVideoService = $storeVideoService;
    }

    public function storeVideo(StoreFileRequest $request)
    {
        $storeFileDTO = StoreFileDTO::fromRequest($request);
        $path = $this->storeVideoService->storeFile($storeFileDTO);
        dump($path);
    }
}
