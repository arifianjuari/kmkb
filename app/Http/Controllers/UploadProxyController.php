<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UploadProxyController extends Controller
{
    /**
     * Proxy uploads stored on Object Storage so they can be served under the same origin.
     */
    public function __invoke(Request $request, string $path): StreamedResponse
    {
        $cleanPath = ltrim(str_replace(['..', '\\'], '', $path), '/');
        $disk = uploads_disk();
        $storage = Storage::disk($disk);

        if (!$storage->exists($cleanPath)) {
            abort(404);
        }

        $mimeType = $storage->mimeType($cleanPath) ?? 'application/octet-stream';
        $lastModified = $storage->lastModified($cleanPath);

        $stream = $storage->readStream($cleanPath);

        return Response::stream(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000',
            'Last-Modified' => gmdate('D, d M Y H:i:s', $lastModified) . ' GMT',
        ]);
    }
}

