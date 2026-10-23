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
        // basename() keeps the name inside the folder; fileExists() is false for '.' and '..'.
        $fileName = basename((string) $fileName);
        if ($fileName !== '' && Storage::disk('local')->fileExists(self::DIRECTORY . '/' . $fileName)) {
            return Storage::disk('local')->download(self::DIRECTORY . '/' . $fileName);
        }
        if ($fileName !== '' && Storage::disk('public')->fileExists(self::LEGACY_DIRECTORY . '/' . $fileName)) {
            return Storage::disk('public')->download(self::LEGACY_DIRECTORY . '/' . $fileName);
        }

        throw new NotFoundHttpException('CV file not found.');
    }

    /**
     * Moves the CVs of the old public folder to private storage, keeping their
     * names. Dotfiles such as .gitignore stay. Returns the number moved.
     */
    public function moveLegacyFilesToPrivate(): int {
        $moved = 0;
        foreach (Storage::disk('public')->files(self::LEGACY_DIRECTORY) as $path) {
            $fileName = basename($path);
            if (str_starts_with($fileName, '.')) {
                continue;
            }
            $stream = Storage::disk('public')->readStream($path);
            if ($stream === null) {
                continue;
            }
            Storage::disk('local')->writeStream(self::DIRECTORY . '/' . $fileName, $stream);
            Storage::disk('public')->delete($path);
            $moved++;
        }

        return $moved;
    }
}
