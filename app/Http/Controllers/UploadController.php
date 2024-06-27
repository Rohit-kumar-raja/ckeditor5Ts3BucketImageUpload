<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{

    public $dir = 'uploads/test/';
    public function upload(Request $request)
    {



        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time()."_".  $file->getClientOriginalName();
            $destination = $this->dir . $fileName;
            $path = $file->store($destination, 's3');
            $url = Storage::disk('s3')->url($path);

            return response()->json([
                'url' => $url,
            ]);
        }

        return response()->json(['error' => 'No file uploaded.'], 400);
    }


    public function  deleteFile(Request $request)
    {
        $path = parse_url($request->input('url'), PHP_URL_PATH);
        $fileKey = ltrim($path, '/');  // Remove leading slash from path

        if (Storage::disk('s3')->exists($fileKey)) {
            Storage::disk('s3')->delete($fileKey);
            return response()->json(['message' => 'File deleted successfully'], 200);
        }

        return response()->json(['message' => 'File not found'], 404);
    }
}
