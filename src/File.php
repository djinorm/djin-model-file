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
    protected $pathPrefix;

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

    /** @var string */
    protected $storage;

    /** @var string */
    protected $entityType = '';

    /** @var Id */
    protected $entityId;

    /** @var string */
    protected $tag;

    /** @var int */
    protected $downloads = 0;

    public function __construct(FileDTO $file, string $storage, string $pathPrefix = '', $tag = '')
    {
        $this->id = new Id(UuidGenerator::generate());
        $this->uploadedAt = new DateTimeImmutable();

        $this->storage = $storage;
        $this->pathPrefix = $pathPrefix;
        $this->tag = $tag;

        $fileNameParts = [];
        if (preg_match('~(.+)\.([^\.]+)$~', $file->name, $fileNameParts)) {
            $this->clientFileName = $fileNameParts[1];
            $this->extension = mb_strtolower($fileNameParts[2]);
        } else {
            $this->clientFileName = $file->name;
        }

        $this->size = $file->size;
        $this->mime = $file->mime;

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

    public function getStorage(): string
    {
        return $this->storage;
    }

    public function setStorage(string $storage)
    {
        $this->storage = $storage;
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function getEntityId(): ?Id
    {
        return $this->entityId;
    }

    public function setEntity(string $type, Id $id)
    {
        $this->entityType = $type;
        $this->entityId = $id;
    }

    public function getTag(): string
    {
        return $this->tag;
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
        $prefix = $this->getPathPrefix();
        if (!empty($prefix)) {
            $prefix = mb_substr($prefix, 0, 1) . '/' . $prefix . '/';
        }

        $dir = $prefix . $this->uploadedAt->format('Y/m/d/');
        return $withFileName ? ($dir . $this->getInternalName(true)) : $dir;
    }

    public function getDownloads(): int
    {
        return $this->downloads;
    }

    public static function createFromUploaded(UploadedFileInterface $file, string $storage, string $pathPrefix = '', $tag = ''): self
    {
        $fileDTO = new FileDTO($file->getClientFilename(), $file->getSize(), $file->getClientMediaType());
        return new self($fileDTO, $storage, $pathPrefix, $tag);
    }

    protected function getPathPrefix(): string
    {
        return $this->pathPrefix;
    }

    private function getExtensionWithDot(): string
    {
        return empty($this->extension) ? '' : ".{$this->extension}";
    }

}