<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    protected Cloudinary $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key'    => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
            ],
        ]);
    }

    public function upload(UploadedFile $file, string $folder = 'websoto'): string
    {
        return $this->uploadFromPath($file->getRealPath(), $folder);
    }

    public function uploadFromPath(string $filePath, string $folder = 'websoto'): string
    {
        $uploaded = $this->cloudinary->uploadApi()->upload(
            $filePath,
            ['folder' => $folder]
        );

        return $uploaded['secure_url'];
    }

    public function uploadFromContent(string $content, string $publicId, string $folder = 'websoto'): string
    {
        $uploaded = $this->cloudinary->uploadApi()->upload(
            'data:image/svg+xml;base64,' . base64_encode($content),
            [
                'folder' => $folder,
                'public_id' => $publicId,
            ]
        );

        return $uploaded['secure_url'];
    }

    public function delete(string $url): bool
    {
        $publicId = $this->getPublicId($url);
        if (!$publicId) {
            return false;
        }

        $this->cloudinary->uploadApi()->destroy($publicId);
        return true;
    }

    protected function getPublicId(string $url): ?string
    {
        if (!str_starts_with($url, 'https://res.cloudinary.com/')) {
            return null;
        }

        $parts = explode('/', $url);
        $versionIndex = array_search('upload', $parts);
        if ($versionIndex === false) {
            return null;
        }

        $publicIdParts = array_slice($parts, $versionIndex + 2);
        $publicId = implode('/', $publicIdParts);
        $publicId = preg_replace('/\.\w+$/', '', $publicId);

        return $publicId;
    }

    public static function isCloudinaryUrl(?string $path): bool
    {
        return $path && str_starts_with($path, 'https://res.cloudinary.com/');
    }

    public static function getImageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (self::isCloudinaryUrl($path)) {
            return $path;
        }

        return \Illuminate\Support\Facades\Storage::url($path);
    }
}
