<?php

namespace App\Services\FileService;

use App\Services\FileService\DTO\StoreFileDTO;
use App\Services\FileService\Interfaces\StoreFileInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class StoreFileService implements StoreFileInterface
{
    protected string $pathPublicStore = 'public/files';
    protected string $pathChunksStore = 'chunks';
    protected GetFileService $getFileService;
    protected DeleteFileService $deleteFileService;
    protected FileInfoService $fileInfoService;
    protected CountFilesService $countFilesService;

    public function __construct(
        GetFileService $getFileService,
        DeleteFileService $deleteFileService,
        FileInfoService $fileInfoService,
        CountFilesService $countFilesService
    ) {
        $this->getFileService = $getFileService;
        $this->deleteFileService = $deleteFileService;
        $this->fileInfoService = $fileInfoService;
        $this->countFilesService = $countFilesService;
    }

    public function storeFile(StoreFileDTO $dto): string
    {
        if ($dto->getTotalChunks() === 1) {
            return $this->storeSingleChankedFile($dto);
        } else {
            $pathChunksStored = $this->storeChunk($dto);
            $countedChunks = $this->countFilesService->countFiles($pathChunksStored);

            if ($countedChunks === $dto->getTotalChunks()) {
                return $this->storeFileFromChunks($dto, $pathChunksStored);
            } else {
                return $pathChunksStored;
            }
        }
    }

    protected function storeSingleChankedFile(StoreFileDTO $dto): string
    {
        try {
            $file = $dto->getFile();
            return $file->store($this->pathPublicStore);
        } catch (Throwable $throwable) {
            throw  $throwable;
        }
    }

    private function storeChunk(StoreFileDTO $dto): string
    {
        try {
            $chunk = $dto->getFile();
            $pathDirectory = $this->pathChunksStore . '/' . $dto->getFileName();
            $chunk->storeAs(
                $pathDirectory,
                $dto->getChunkIndex() . '.' . $dto->getFileExtension()
            );
            return $pathDirectory;
        } catch (Throwable $throwable) {
            $this->deleteFileService->deleteDirectory($pathDirectory);
            throw  $throwable;
        }
    }

    private function storeFileFromChunks(StoreFileDTO $dto, string $pathChunksStored)
    {
        try {
            $chuksArray = $this->getFileService->getFilesFromDirectory($pathChunksStored);
            $extension = $this->fileInfoService->getFileExtension($chuksArray[0]);
            $outputFileName = Str::uuid() . '.' . $extension;
            $outputFile = $this->pathPublicStore . '/' . $outputFileName;

            Storage::put($this->pathPublicStore . '/' . $outputFileName, '');

            for ($i = 0; $i < $dto->getTotalChunks(); $i++) {
                $chunkContent = $this->getFileService->getFile($chuksArray[$i]);
                Storage::append($outputFile, $chunkContent, null);
            }

            $this->deleteFileService->deleteDirectory($pathChunksStored);

            return $outputFile;
        } catch (Throwable $throwable) {
            $this->deleteFileService->deleteDirectory($pathChunksStored);
            throw  $throwable;
        }
    }
}
