<?php

namespace App\StorageLayer;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Mentor and mentee CVs. They are stored on the local disk, outside the
 * public folder that `storage:link` exposes, under a random name. Only the
 * staff download routes serve them.
 */
class CvFileStorage {
    private const DIRECTORY = 'private/cv_files';

    // Where CVs were stored before 2026-10, on the public disk. Download only.
    private const LEGACY_DIRECTORY = 'uploads/cv_files';

    public function save(UploadedFile $cvFile): string {
        $fileName = Str::random(40) . '.' . $cvFile->extension();
        Storage::disk('local')->putFileAs(self::DIRECTORY, $cvFile, $fileName);

        return $fileName;
    }

    public function download(?string $fileName): StreamedResponse {
        $fileName = basename((string) $fileName);
        if ($fileName !== '' && Storage::disk('local')->exists(self::DIRECTORY . '/' . $fileName)) {
            return Storage::disk('local')->download(self::DIRECTORY . '/' . $fileName);
        }
        if ($fileName !== '' && Storage::disk('public')->exists(self::LEGACY_DIRECTORY . '/' . $fileName)) {
            return Storage::disk('public')->download(self::LEGACY_DIRECTORY . '/' . $fileName);
        }

        throw new NotFoundHttpException('CV file not found.');
    }
}
