<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileDownloadController extends Controller
{
    public function download($fileId)
    {
        // Find the file record by its ID
        $fileRecord = File::findOrFail($fileId);
        // Ensure the file exists
        if (!Storage::disk('public')->exists($fileRecord->path)) {
            abort(404);
        }

        // Return the file as a response
        return Storage::disk('public')->download($fileRecord->path);
    }
}
