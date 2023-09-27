<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Illuminate\Support\Facades\Log;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'path',
    ];

    public static function storeFile($file, $fileName, $chunkIndex, $totalChunks, $date)
    {
        if ($chunkIndex < $totalChunks) {
            self::storeFileChunk($file, $fileName, $chunkIndex);
            dump(1);
        }
        if (++$chunkIndex == $totalChunks) {
            try {
                $path = self::creatingFileFromChunks($fileName, $totalChunks);
                $data = [
                    'name' => $fileName,
                    'path' => $path,
                ];
                self::create($data);
                Storage::disk('local')->deleteDirectory($fileName);
            } catch (Throwable $throwable) {
                Log::error('Ошибка: ' . $throwable->getMessage() . '. Файл: ' . $throwable->getFile() . '. Строка: ' . $throwable->getLine());
                Storage::disk('local')->deleteDirectory($fileName);
                if ($path) Storage::disk('public')->delete($path);
            }
        }
    }

    protected static function storeFileChunk($file, $fileName, $chunkIndex)
    {
        $path = 'chunks/' . $fileName;
        try {
            Storage::disk('local')->putFileAs($path, $file, 'part_' . $chunkIndex);
        } catch (Throwable $throwable) {
            Log::error('Ошибка: ' . $throwable->getMessage() . '. Файл: ' . $throwable->getFile() . '. Строка: ' . $throwable->getLine());
            Storage::disk('local')->deleteDirectory($path);
        }
    }

    protected static function creatingFileFromChunks($fileName, $totalChunks)
    {
        try {
            $endFile = 'files/' . $fileName;
            $directoryChunks = 'chunks/' . $fileName;

            Storage::disk('public')->put($endFile, '');
            for ($i = 0; $i < $totalChunks; $i++) {
                $pathChunk = $directoryChunks . '/' . 'part_' . $i;
                $chunk = Storage::disk('local')->get($pathChunk);
                Storage::disk('public')->append($endFile, $chunk);
            }
            return $endFile;

        } catch (Throwable $throwable) {
            Log::error('Ошибка: ' . $throwable->getMessage() . '. Файл: ' . $throwable->getFile() . '. Строка: ' . $throwable->getLine());
            Storage::disk('local')->deleteDirectory($directoryChunks);
            Storage::disk('public')->delete($endFile);
        }
    }
}
