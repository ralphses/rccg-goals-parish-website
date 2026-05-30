<?php

namespace App\Http\Controllers;

use App\Models\SermonAttachment;
use Illuminate\Http\Request;

class SermonAttachmentController extends Controller
{
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SermonAttachment $attachment)
    {
        // Delete the file from storage
        if (file_exists(public_path($attachment->file_path))) {
            unlink(public_path($attachment->file_path));
        }

        $attachment->delete();

        return back()->with('success', 'Attachment deleted successfully.');
    }
}
