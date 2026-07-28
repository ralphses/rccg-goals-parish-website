<?php

namespace App\Http\Controllers;

use App\Models\SermonAttachment;
use App\Services\CloudinaryUploadService;

class SermonAttachmentController extends Controller
{
    public function __construct(private CloudinaryUploadService $cloudinaryUploadService)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SermonAttachment $attachment)
    {
        $this->cloudinaryUploadService->deleteByUrl($attachment->file_path, 'raw');

        $attachment->delete();

        return back()->with('success', 'Attachment deleted successfully.');
    }
}
