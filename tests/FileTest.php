<?php
/**
 * Created for DjinORM File model.
 * Datetime: 20.11.2017 11:07
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Models\File;

use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Model\ModelInterface;
use DjinORM\Djin\Model\ModelTrait;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{

    /** @var File */
    private $file;

    /** @var FileDTO */
    private $fileDTO;

    /** @var ModelInterface */
    private $model_1;

    /** @var ModelInterface */
    private $model_2;

    public function setUp()
    {
        $this->fileDTO = new FileDTO('image.jpg', 1024, 'image/jpeg');
        $this->fileDTO->type = File::TYPE_BINARY;
        $this->fileDTO->variation = 'image_500';

        $this->model_1 = new class() implements ModelInterface {
            use ModelTrait;

            protected $id;

            public function __construct()
            {
                $this->id = new Id(1);
            }

            public static function getModelName(): string
            {
                return 'user';
            }
        };

        $this->model_2 = new class() implements ModelInterface {
            use ModelTrait;

            protected $id;

            public function __construct()
            {
                $this->id = new Id(2);
            }

            public static function getModelName(): string
            {
                return 'user';
            }
        };

        $this->file = new File($this->fileDTO, $this->model_1::getModelName(), 'local', 'qwerty');
    }

    public function testConstructEmptyPrefixAndTag()
    {
        $file = new File($this->fileDTO, $this->model_1::getModelName(), 'local');

        $path = $file->getUploadedAt()->format('Y/m/d/');
        $this->assertEquals($path . $file->getInternalName(true), $file->getPath(true));
        $this->assertEquals($path, $file->getPath(false));

        $this->assertEmpty($file->getTag());
    }

    public function testConstructFileWithoutExtension()
    {
        $this->fileDTO->name = 'image';
        $file = new File($this->fileDTO, $this->model_1::getModelName(), 'local');
        $this->assertEquals('image', $file->getClientFileName(true));
        $this->assertEquals('image', $file->getClientFileName(false));
        $this->assertEquals('', $file->getExtension());
    }

    public function testGetUploadedAt()
    {
        $this->assertEquals(
            date('Y-m-d H:i'),
            $this->file->getUploadedAt()->format('Y-m-d H:i')
        );
    }

    public function testGetClientFileName()
    {
        $this->assertEquals('image.jpg', $this->file->getClientFileName(true));
        $this->assertEquals('image', $this->file->getClientFileName(false));
    }

    public function testGetExtension()
    {
        $this->assertEquals('jpg', $this->file->getExtension());
    }

    public function testGetSize()
    {
        $this->assertEquals(1024, $this->file->getSize());
    }

    public function testGetMime()
    {
        $this->assertEquals('image/jpeg', $this->file->getMime());
    }

    public function testGetStorage()
    {
        $this->assertEquals('local', $this->file->getStorage());
    }

    public function testSetStorage()
    {
        $this->file->setStorage('remote');
        $this->assertEquals('remote', $this->file->getStorage());
    }

    public function testGetEntityType()
    {
        $this->assertEquals('user', $this->file->getEntityType());
    }

    public function testGetEntityId()
    {
        $this->assertNull($this->file->getEntityId());
    }

    /**
     * @throws FileAlreadyHasEntityException
     */
    public function testSetEntity()
    {
        $this->file->setEntity($this->model_1);
        $this->assertEquals('user', $this->file->getEntityType());
        $this->assertEquals(new Id(1), $this->file->getEntityId());
    }

    /**
     * @throws FileAlreadyHasEntityException
     */
    public function testSetSameEntity()
    {
        $this->file->setEntity($this->model_1);
        $this->file->setEntity($this->model_1);
        $this->assertEquals('user', $this->file->getEntityType());
        $this->assertEquals(new Id(1), $this->file->getEntityId());
    }

    /**
     * @throws FileAlreadyHasEntityException
     */
    public function testSetNotSameEntity()
    {
        $this->expectException(FileAlreadyHasEntityException::class);
        $this->file->setEntity($this->model_1);
        $this->file->setEntity($this->model_2);
    }

    /**
     * @throws FileAlreadyHasEntityException
     */
    public function testRemoveEntity()
    {
        $this->file->setEntity($this->model_1);
        $this->file->removeEntity();
        $this->assertNull($this->file->getEntityId());
    }

    public function testGetType()
    {
        $this->assertEquals(File::TYPE_BINARY, $this->file->getType());
    }

    public function testGetVariation()
    {
        $this->assertEquals('image_500', $this->file->getVariation());
    }

    public function testGetTag()
    {
        $this->assertEquals('', $this->file->getTag());
    }

    public function testSetTag()
    {
        $this->file->setTag('hello world');
        $this->assertEquals('hello world', $this->file->getTag());
    }

    public function testGetInternalFileName()
    {
        $this->assertRegExp('~^[a-z\d]{8}-([a-z\d]{4}-){3}[a-z\d]{12}\.jpg$~', $this->file->getInternalName(true));
        $this->assertRegExp('~^[a-z\d]{8}-([a-z\d]{4}-){3}[a-z\d]{12}$~', $this->file->getInternalName(false));
    }

    public function testGetPath()
    {
        $path = 'q/qwerty/' . $this->file->getUploadedAt()->format('Y/m/d/');
        $this->assertEquals($path . $this->file->getInternalName(true), $this->file->getPath(true));
        $this->assertEquals($path, $this->file->getPath(false));
    }

    public function testGetDownloads()
    {
        $this->assertEquals(0, $this->file->getDownloads());
    }

}
