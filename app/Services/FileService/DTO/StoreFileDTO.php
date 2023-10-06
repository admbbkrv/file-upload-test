<?php

namespace App\Services\FileService\DTO;

use App\Http\Requests\StoreFileRequest;
use Illuminate\Http\UploadedFile;

class StoreFileDTO
{
    public function __construct(
        private readonly UploadedFile $file,
        private readonly string $fileName,
        private readonly string $fileExtension,
        private readonly string $fileMimeType,
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
            $request->file('file')->extension(),
            $request->file('file')->getMimeType(),
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

    public function getFileExtension(): string
    {
        return $this->fileExtension;
    }

    public function getFileMymeType(): string
    {
        return $this->fileMimeType;
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
