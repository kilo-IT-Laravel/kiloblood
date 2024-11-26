<?php

namespace Storage\utils;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait KobeniS3
{
    protected string $disk = 's3';

    protected string $path = 'uploads';

    protected string $visibility = 'public';

    public function uploadFile(UploadedFile $file, ?string $path = null, ?string $disk = null, ?string $visibility = null)
    {
        $path = $path ?? $this->path;
        $disk = $disk ?? $this->disk;
        $visibility = $visibility ?? $this->visibility;

        $filename = $this->generateFilename($file);
        $fullPath = $file->storeAs($path, $filename, [
            'disk' => $disk,
            'visibility' => $visibility
        ]);

        return [
            'path' => $fullPath,
            'filename' => $filename,
            'url' => $this->getFileUrl($fullPath, $disk),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'visibility' => $visibility
        ];
    }

    public function uploadFiles(
        array $files,
        ?string $path = null,
        ?string $disk = null,
        ?string $visibility = null
    ): array {
        $uploadedFiles = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $uploadedFiles[] = $this->uploadFile($file, $path, $disk, $visibility);
            }
        }

        return $uploadedFiles;
    }

    public function deleteFile(string $path, ?string $disk = null): bool
    {
        $disk = $disk ?? $this->disk;

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }

    public function deleteFiles(array $paths, ?string $disk = null): array
    {
        $results = [];

        foreach ($paths as $path) {
            $results[$path] = $this->deleteFile($path, $disk);
        }

        return $results;
    }

    protected function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

        return $filename . '_' . time() . '_' . Str::random(10) . '.' . $extension;
    }

    public function setVisibility(string $visibility): void
    {
        $this->visibility = $visibility;
    }

    public function setDisk(string $disk): void
    {
        $this->disk = $disk;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }
}
