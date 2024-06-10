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
        if (!Storage::disk('private')->exists($fileRecord->path)) {
            abort(404);
        }

        // Return the file as a response
        return response()->download(storage_path('/app/private/' . $fileRecord->path));
    }
}
