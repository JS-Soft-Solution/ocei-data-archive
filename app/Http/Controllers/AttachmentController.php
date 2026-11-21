<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AttachmentController extends Controller
{
    use AuthorizesRequests;
    /**
     * Upload and store attachment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,webp',
            'attachable_type' => 'required|string',
            'attachable_id' => 'required|integer',
            'attachment_type' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $attachableType = $request->attachable_type;
            $attachableId = $request->attachable_id;

            // Get the attachable model
            $attachable = app($attachableType)::findOrFail($attachableId);

            // Check authorization
            $this->authorize('uploadAttachment', $attachable);

            // Determine storage path
            $permitType = match ($attachableType) {
                'App\\Models\\ExElectricianRenewApplication' => 'electrician',
                'App\\Models\\ExSupervisorRenewApplication' => 'supervisor',
                'App\\Models\\ExContractorRenewApplication' => 'contractor',
                default => 'unknown',
            };

            $storagePath = "legacy_permits/{$permitType}/{$attachableId}";
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs($storagePath, $fileName, 'private');

            // Create attachment record
            $attachment = $attachable->attachments()->create([
                'file_name' => $fileName,
                'file_path' => $filePath,
                'original_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'attachment_type' => $request->attachment_type,
                'uploaded_by' => Auth::id(),
                'uploaded_at' => now(),
            ]);

            // Log attachment addition
            $attachable->logAttachmentAdded($fileName);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Attachment uploaded successfully.',
                'attachment' => $attachment,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload attachment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download attachment (secure).
     */
    public function download(Attachment $attachment)
    {
        // Get the attachable model to check authorization
        $attachable = $attachment->attachable;

        $this->authorize('view', $attachable);

        if (!Storage::disk('private')->exists($attachment->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('private')->download(
            $attachment->file_path,
            $attachment->original_name
        );
    }

    /**
     * Preview attachment (inline display).
     */
    public function preview(Attachment $attachment)
    {
        // Get the attachable model to check authorization
        $attachable = $attachment->attachable;

        $this->authorize('view', $attachable);

        if (!Storage::disk('private')->exists($attachment->file_path)) {
            abort(404, 'File not found.');
        }

        return response()->file(
            Storage::disk('private')->path($attachment->file_path),
            [
                'Content-Type' => $attachment->mime_type,
                'Content-Disposition' => 'inline; filename="' . $attachment->original_name . '"'
            ]
        );
    }

    /**
     * Delete attachment.
     */
    public function destroy(Attachment $attachment)
    {
        // Get the attachable model to check authorization
        $attachable = $attachment->attachable;

        $this->authorize('deleteAttachment', $attachable);

        DB::beginTransaction();
        try {
            // Delete physical file
            if (Storage::disk('private')->exists($attachment->file_path)) {
                Storage::disk('private')->delete($attachment->file_path);
            }

            // Log attachment deletion
            $attachable->logAttachmentDeleted($attachment->file_name);

            // Soft delete attachment record
            $attachment->update(['deleted_by' => Auth::id()]);
            $attachment->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Attachment deleted successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete attachment: ' . $e->getMessage(),
            ], 500);
        }
    }
}
