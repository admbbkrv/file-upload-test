<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function index(Request $request)
    {
        $file = $request->file('file');
        $fileName = $request->filename;
        $chunkIndex = $request->chunkIndex;
        $totalChunks = $request->totalChunks;
        $date = $request->date ?? null;
        File::storeFile($file, $fileName, $chunkIndex, $totalChunks, $date);
    }
}
