<?php
/**
 * Created for djin-file-manager.
 * Datetime: 21.11.2017 11:32
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Models\File;


class FileDTO
{
    /** @var string */
    public $name;

    /** @var int */
    public $size;

    /** @var string */
    public $mime;

    /** @var string */
    public $variation = '';

    /** @var string */
    public $type = '';

    public function __construct(string $name, int $sizeInBytes, string $mime)
    {
        $this->name = $name;
        $this->size = $sizeInBytes;
        $this->mime = $mime;
    }

}