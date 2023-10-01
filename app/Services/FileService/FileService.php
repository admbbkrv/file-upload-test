<?php

namespace App\Services\FileService;

use App\DTO\StoreChunkDTO;
use App\DTO\StoreFileDTO;
use App\DTO\StoreFileFromChunksDTO;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;
use function PHPUnit\Framework\fileExists;

class FileService implements FileServiceInterface
{

    public function store(StoreFileDTO $dto)
    {
        // Если исходный файл меньше 10мб и не разбит на чанки, то он будет сразу сохранен
        if ($dto->totalChunks === 1){
            try {
                return Storage::disk('public')->putFile('videos', new File($dto->file)); //возвращает путь к файлу
            } catch (Throwable $throwable){
                Log::error('Ошибка: ' . $throwable->getMessage() . '. Файл: ' . $throwable->getFile() . '. Строка: ' . $throwable->getLine());
            }
        } elseif ($dto->totalChunks > 1){
            try {
                $storeChunkDTO = new StoreChunkDTO($dto->file, $dto->fileName, $dto->fileExtension, $dto->chunkIndex);
                $pathChunksDirectory = $this->storeChunk($storeChunkDTO);


                if ($dto->chunkIndex + 1 === $dto->totalChunks){
                    $storeFileFromChunksDTO = new StoreFileFromChunksDTO($pathChunksDirectory, $dto->totalChunks);
                    return $this->storeFileFromChunks($storeFileFromChunksDTO); //возвращает путь к файлу
                }
            } catch (Throwable $throwable){
                Log::error('Ошибка: ' . $throwable->getMessage() . '. Файл: ' . $throwable->getFile() . '. Строка: ' . $throwable->getLine());
            }
        }
    }

    public function get()
    {
        // TODO: Implement get() method.
    }

    private function storeChunk(StoreChunkDTO $dto)
    {
        try {
            $pathChunksDirectory = 'chunks/' . $dto->fileName;
            Storage::disk('local')->putFileAs($pathChunksDirectory, new File($dto->file), $dto->chunkIndex . '.' . $dto->fileExtension);
            return $pathChunksDirectory;
        } catch (Throwable $throwable){
            Log::error('Ошибка: ' . $throwable->getMessage() . '. Файл: ' . $throwable->getFile() . '. Строка: ' . $throwable->getLine());
            Storage::deleteDirectory($pathChunksDirectory);
        }
    }

    public function storeFileFromChunks(StoreFileFromChunksDTO $dto)
    {
        try {
                $createdChunks = Storage::disk('local')->files($dto->pathChunksDirectory);
                $pathFirstChunk = storage_path('app/' . $createdChunks[0]);
                $outputFile = Storage::disk('public')->putFile('videos', new File($pathFirstChunk));
                $pathOutputFile = storage_path('app/public/' . $outputFile);
                for ($i = 1; $i < $dto->totalChunks; $i++){
                    $pathCurrentChunk = storage_path('app/' . $createdChunks[$i]);
                    $currentChunkContent = file_get_contents($pathCurrentChunk);
                    file_put_contents($pathOutputFile, $currentChunkContent, FILE_APPEND);
                }
                return $pathOutputFile;
        } catch (Throwable $throwable){
            Log::error('Ошибка: ' . $throwable->getMessage() . '. Файл: ' . $throwable->getFile() . '. Строка: ' . $throwable->getLine());
            Storage::deleteDirectory($dto->pathChunksDirectory);
            if (fileExists($pathOutputFile)) Storage::delete($pathOutputFile);
        }
    }

}
