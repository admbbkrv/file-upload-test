<?php

namespace App\Http\Controllers;

use App\DTO\StoreFileDTO;
use App\Services\FileService\FileService;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function index(Request $request)
    {
        $storeFileDTO = new StoreFileDTO(
            $request->file('file'),
            $request->fileName,
            $request->file('file')->extension(),
            $request->file('file')->getMimeType(),
            $request->chunkIndex,
            $request->totalChunks,
        );
        $fileService = new FileService();
        $pathToFile = $fileService->store($storeFileDTO);

    }
}
