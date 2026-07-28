<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;
use Throwable;

class CroppedImageUploadService
{
    public function __construct(private CloudinaryUploadService $cloudinaryUploadService)
    {
    }

    public function storeFromDataUrl(string $dataUrl, string $folder, string $prefix, string $errorField): array
    {
        if (!preg_match('/^data:image\/[a-zA-Z0-9.+-]+;base64,/', $dataUrl)) {
            throw ValidationException::withMessages([
                $errorField => 'Invalid cropped image payload.',
            ]);
        }

        try {
            return $this->cloudinaryUploadService->uploadDataUrl($dataUrl, $folder, $prefix, 'image');
        } catch (Throwable) {
            throw ValidationException::withMessages([
                $errorField => 'Unable to process cropped image.',
            ]);
        }
    }
}
