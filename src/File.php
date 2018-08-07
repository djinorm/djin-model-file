<?php
/**
 * Created for DjinORM File model
 * Datetime: 17.11.2017 17:24
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Models\File;


use DateTimeImmutable;
use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Id\UuidGenerator;
use DjinORM\Djin\Model\ModelInterface;
use DjinORM\Djin\Model\ModelTrait;

abstract class File implements ModelInterface
{

    const TYPE_BINARY = 'binary';
    const TYPE_IMAGE = 'image';

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
    protected $type;

    /** @var string */
    protected $variation;

    /** @var string */
    protected $tag = '';

    /** @var int */
    protected $downloads = 0;

    /**
     * File constructor.
     * @param FileDTO $file
     * @param string $entityType
     * @param string $storage
     * @param string $pathPrefix
     * @throws \DjinORM\Djin\Exceptions\InvalidArgumentException
     * @throws \DjinORM\Djin\Exceptions\LogicException
     * @throws \Exception
     */
    public function __construct(FileDTO $file, string $entityType, string $storage, string $pathPrefix = '')
    {
        $this->id = new Id(UuidGenerator::generate());
        $this->uploadedAt = new DateTimeImmutable();

        $this->entityType = $entityType;

        $this->storage = $storage;
        $this->pathPrefix = $pathPrefix;

        $fileNameParts = [];
        if (preg_match('~(.+)\.([^\.]+)$~', $file->name, $fileNameParts)) {
            $this->clientFileName = $fileNameParts[1];
            $this->extension = mb_strtolower($fileNameParts[2]);
        } else {
            $this->clientFileName = $file->name;
        }

        $this->size = $file->size;
        $this->mime = $file->mime;


        $this->variation = $file->variation;
        $this->type = $file->type;
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
     * @return string file extension without dot
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @return int file size in bytes
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return string mime type
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * @return string storage name
     */
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

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param ModelInterface $model
     * @throws FileAlreadyHasEntityException
     */
    public function setEntity(ModelInterface $model)
    {
        $this->guardAlreadyHasEntity($model);
        $this->entityId = $model->getId();
    }

    public function removeEntity()
    {
        $this->entityId = null;
    }

    /**
     * For example, it can be 'image_500', 'image_1200' etc, for different image sizes, or contain file version
     * @return string
     */
    public function getVariation(): string
    {
        return $this->variation;
    }

    /**
     * Helper for filtering files
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
    }

    public function setTag(string $tag)
    {
        $this->tag = $tag;
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

    protected function getPathPrefix(): string
    {
        return $this->pathPrefix;
    }

    private function getExtensionWithDot(): string
    {
        return empty($this->extension) ? '' : ".{$this->extension}";
    }

    /**
     * @param ModelInterface $model
     * @throws FileAlreadyHasEntityException
     */
    private function guardAlreadyHasEntity(ModelInterface $model)
    {
        $sameType = $this->entityType === $model::getModelName();
        $sameId = $model->getId()->isEqual($this->entityId);
        $same = $sameType && $sameId;
        if (!$same && $this->entityId) {
            throw new FileAlreadyHasEntityException("EntityType: {$this->entityType}, Id: {$this->entityId->toScalar()}");
        }
    }

}