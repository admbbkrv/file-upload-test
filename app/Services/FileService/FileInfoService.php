<?php

namespace App\Services\FileService;

use App\Services\FileService\Interfaces\FileInfoInterface;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use Throwable;

class FileInfoService implements FileInfoInterface
{

    public function getFileInfo(string $pathFile): array
    {
        try {
            if (Storage::exists($pathFile)) {
                return pathinfo($pathFile);
            } else {
                throw new FileNotFoundException();
            }
        } catch (FileNotFoundException $e) {
            echo 'Файл не найден: ' . $e->getMessage(); // не знаю еще как правильно ошибку обрабатывать
        } catch (Throwable $throwable) {
            throw  $throwable;
        }
    }

    public function getFileExtension(string $pathFile): string
    {
        $fileInfoArray = $this->getFileInfo($pathFile);
        return $fileInfoArray['extension'];
    }
}
