<?php
/**
 * Created for DjinORM File model.
 * Datetime: 20.11.2017 11:07
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Models;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

class FileTest extends TestCase
{

    /** @var File */
    private $file;

    public function setUp()
    {
        $this->file = new File('image.jpg', 1024, 'image/jpeg', 'qwerty');
    }

    public function testConstructEmptyPrefix()
    {
        $file = new File('image.jpg', 1024);
        $this->assertRegExp('~^\d\/\d{1,3}\/\d{4}\/\d{2}\/\d{2}\/~', $file->getPath());
    }

    public function testConstructFileWithoutExtension()
    {
        $file = new File('image', 1024);
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

    public function testCreateFromUploaded()
    {
        /** @var UploadedFileInterface $uploaded */
        $uploaded = $this->createMock(UploadedFileInterface::class);
        $uploaded->method('getClientFilename')->willReturn('image.jpg');
        $uploaded->method('getSize')->willReturn(2048);
        $uploaded->method('getClientMediaType')->willReturn('image/jpeg');

        $file = File::createFromUploaded($uploaded);
        $this->assertEquals('image.jpg', $file->getClientFileName(true));
        $this->assertEquals(2048, $file->getSize());
        $this->assertEquals('image/jpeg', $file->getMime());
    }

}
