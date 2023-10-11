<?php

namespace App\Services\FileService;

use App\Services\FileService\Interfaces\GetFileInterface;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class GetFileService implements GetFileInterface
{

    public function getFile(string $pathFile): string
    {
        try {
            if (! Storage::exists($pathFile)) {
                throw new FileNotFoundException();
            }
            return Storage::get($pathFile);
        } catch (FileNotFoundException $e) {
            echo 'Файл не найден: ' . $e->getMessage(); // не знаю еще как правильно ошибку обрабатывать
        } catch (Throwable $throwable) {
            throw  $throwable;
        }
    }

    public function getFilesFromDirectory(string $pathDirectory): array
    {
        try {
            if (! Storage::directoryExists($pathDirectory)) {
                throw new FileNotFoundException();
            }
            return Storage::files($pathDirectory);
        } catch (FileNotFoundException $e) {
            echo 'Директория не найдена: ' . $e->getMessage(); // не знаю еще как правильно ошибку обрабатывать
        } catch (Throwable $throwable) {
            throw  $throwable;
        }
    }
}
