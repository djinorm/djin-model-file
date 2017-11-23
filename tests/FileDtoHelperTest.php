<?php
/**
 * Created for djin-file-manager.
 * Datetime: 23.11.2017 14:05
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Models\File;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use SebastianBergmann\GlobalState\RuntimeException;

class FileDtoHelperTest extends TestCase
{

    public function testUploadedFileInterfaceToFileDTO()
    {
        /** @var UploadedFileInterface $uploaded */
        $uploaded = $this->createMock(UploadedFileInterface::class);
        $uploaded->method('getClientFilename')->willReturn('image.jpg');
        $uploaded->method('getSize')->willReturn(2048);
        $uploaded->method('getClientMediaType')->willReturn('image/jpeg');

        $expected = new FileDTO('image.jpg', 2048, 'image/jpeg');
        $actual = FileDtoHelper::uploadedFileInterfaceToFileDTO($uploaded);

        $this->assertEquals($expected, $actual);
    }

    public function testLocalFileToFileDTO()
    {
        $fileDTO = new FileDTO('test.txt', 11, 'text/plain');
        $this->assertEquals($fileDTO, FileDtoHelper::localFileToFileDTO(__DIR__ . '/test.txt'));
    }

    public function testLocalFileToFileDTONoFile()
    {
        $this->expectException(RuntimeException::class);
        FileDtoHelper::localFileToFileDTO(__DIR__ . '/qwerty.txt');
    }

}
