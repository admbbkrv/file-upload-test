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
    protected const CHUNKS_PATH = 'chunks';

    public function __construct(
        protected GetFileService $getFileService,
        protected DeleteFileService $deleteFileService,
        protected FileInfoService $fileInfoService,
    ) {

    }

    public function storeFile(StoreFileDTO $dto): string | false
    {
        if ($dto->getTotalChunks() === 1) {
            return $this->storeSingleChankedFile($dto);
        }

        $pathChunksStored = $this->storeChunk($dto);
        $createdChunks = $this->getFileService->getFilesFromDirectory($pathChunksStored);
        $countedChunks = count($createdChunks);

        if ($countedChunks === $dto->getTotalChunks()) {
            return $this->storeFileFromChunks($dto, $pathChunksStored);
        }

        return $pathChunksStored;
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

    protected function storeChunk(StoreFileDTO $dto): string
    {
        try {
            $chunk = $dto->getFile();
            $pathDirectory = self::CHUNKS_PATH . '/' . $dto->getFileName();
            $chunk->storeAs(
                $pathDirectory,
                $dto->getChunkIndex(),
            );
            return $pathDirectory;
        } catch (Throwable $throwable) {
            $this->deleteFileService->deleteDirectory($pathDirectory);
            throw  $throwable;
        }
    }

    protected function storeFileFromChunks(StoreFileDTO $dto, string $pathChunksStored)
    {
        try {
            $chunksArray = $this->getFileService->getFilesFromDirectory($pathChunksStored);
//            $extension = $this->fileInfoService->getFileExtension($chunksArray[0]);
//            $outputFileName = Str::uuid() . '.' . $extension;
            $outputFileName = $dto->getFileName();
            $outputFile = $this->pathPublicStore . '/' . $outputFileName;

            Storage::put($this->pathPublicStore . '/' . $outputFileName, '');

            for ($i = 0; $i < $dto->getTotalChunks(); $i++) {
                $chunkContent = $this->getFileService->getFile($chunksArray[$i]);
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
