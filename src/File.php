<?php
/**
 * Created for DjinORM File model
 * Datetime: 17.11.2017 17:24
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Models;


use DateTimeImmutable;
use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Id\UuidGenerator;
use DjinORM\Djin\Model\ModelInterface;
use DjinORM\Djin\Model\ModelTrait;
use Psr\Http\Message\UploadedFileInterface;

class File implements ModelInterface
{

    use ModelTrait;

    /** @var Id */
    protected $id;

    /** @var string */
    protected $prefix;

    /** @var DateTimeImmutable */
    protected $uploadedAt;

    /** @var string */
    protected $clientFileName;

    /** @var string */
    protected $extension = '';

    /** @var string */
    protected $mime;

    /** @var int */
    protected $size;

    /** @var int */
    protected $downloads = 0;

    public function __construct(string $fileName, int $fileSizeInBytes, string $mime = null, string $prefix = null)
    {
        if (is_null($prefix) || $prefix == '') {
            $prefix = rand(1,999);
        }

        $this->id = new Id(UuidGenerator::generate());
        $this->uploadedAt = new DateTimeImmutable();
        $this->prefix = $prefix;
        $this->mime = $mime;

        $fileNameParts = [];
        if (preg_match('~(.+)\.([^\.]+)$~', $fileName, $fileNameParts)) {
            $this->clientFileName = $fileNameParts[1];
            $this->extension = mb_strtolower($fileNameParts[2]);
        } else {
            $this->clientFileName = $fileName;
        }

        $this->size = $fileSizeInBytes;
    }

    public function getUploadedAt()
    {
        return $this->uploadedAt;
    }

    /**
     * @param bool $withExtension
     * @return string
     */
    public function getClientFileName($withExtension = true): string
    {
        if ($withExtension) {
            return $this->clientFileName . $this->getExtensionWithDot();
        }
        return $this->clientFileName;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @return int размер файла в байтах
     */
    public function getSize(): int
    {
        return $this->size;
    }

    public function getMime()
    {
        return $this->mime;
    }

    public function getInternalName($withExtension = true)
    {
        if ($withExtension) {
            return $this->id->toScalar() . $this->getExtensionWithDot();
        }
        return $this->id->toScalar();
    }

    public function getPath($withFileName = true)
    {
        $prefix = mb_substr($this->getPrefix(), 0, 1) . '/' . $this->getPrefix();
        $dir = $prefix . '/' . $this->uploadedAt->format('Y/m/d/');
        return $withFileName ? ($dir . $this->getInternalName(true)) : $dir;
    }

    public function getDownloads(): int
    {
        return $this->downloads;
    }

    public static function createFromUploaded(UploadedFileInterface $file, string $prefix = null): self
    {
        return new self($file->getClientFilename(), $file->getSize(), $file->getClientMediaType(), $prefix);
    }

    protected function getPrefix(): string
    {
        return $this->prefix;
    }

    private function getExtensionWithDot(): string
    {
        return empty($this->extension) ? '' : ".{$this->extension}";
    }

}