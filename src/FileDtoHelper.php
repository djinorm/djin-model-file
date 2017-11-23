<?php
/**
 * Created for djin-file-manager.
 * Datetime: 23.11.2017 14:04
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Models\File;


use Psr\Http\Message\UploadedFileInterface;

class FileDtoHelper
{
    public static function uploadedFileInterfaceToFileDTO(UploadedFileInterface $file): FileDTO
    {
        return new FileDTO($file->getClientFilename(), $file->getSize(), $file->getClientMediaType());
    }

    public static function localFileToFileDTO(string $pathToFile): FileDTO
    {
        if (!file_exists($pathToFile) || !is_file($pathToFile)) {
            throw new \RuntimeException('File not exists or unavailable');
        }

        $name = basename($pathToFile);
        $size = filesize($pathToFile);
        $mime = mime_content_type($pathToFile);
        return new FileDTO($name, $size, $mime);
    }

}