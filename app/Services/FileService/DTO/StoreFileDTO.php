<?php

namespace App\Services\FileService\DTO;

use App\Http\Requests\StoreFileRequest;
use Illuminate\Http\UploadedFile;

class StoreFileDTO
{
    public function __construct(
        private readonly UploadedFile $file,
        private readonly string $fileName,
        private readonly int $chunkIndex,
        private readonly int $totalChunks,
    ) {
        // method body
    }

    public static function fromRequest(StoreFileRequest $request): StoreFileDTO
    {
        return new self(
            $request->file('file'),
            $request->fileName,
            $request->chunkIndex,
            $request->totalChunks,
        );
    }

    public function getFile(): UploadedFile
    {
        return $this->file;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getChunkIndex(): int
    {
        return $this->chunkIndex;
    }

    public function getTotalChunks(): int
    {
        return $this->totalChunks;
    }
}
