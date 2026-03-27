<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImageController extends Controller
{
    public function image(string $image): BinaryFileResponse
    {
        $repository = app()->make('articles');

        $imagePath = $repository->getImagePath($image);

        if ($imagePath === null || ! Storage::disk('articles')->exists($imagePath)) {
            throw new NotFoundHttpException("Image not found: $image");
        }

        return response()->file(
            Storage::disk('articles')->path($imagePath)
        );
    }
}
