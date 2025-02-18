<?php

declare(strict_types=1);

namespace Cloudflex\LogsManagement\Test\Unit\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Shell;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Filesystem\Io\File;
use Cloudflex\LogsManagement\Service\TailReader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TailReaderTest extends TestCase
{
    /**
     * @var MockObject
     */
    private MockObject $filesystemMock;

    /**
     * @var MockObject
     */
    private MockObject $fileMock;

    /**
     * @var MockObject
     */
    private MockObject $fileDriverMock;

    /**
     * @var MockObject
     */
    private MockObject $configMock;

    /**
     * @var MockObject
     */
    private MockObject $shellMock;

    /**
     * @var TailReader
     */
    private TailReader $tailReader;

    protected function setUp(): void
    {
        $this->filesystemMock = $this->createMock(Filesystem::class);
        $this->fileMock = $this->createMock(File::class);
        $this->fileDriverMock = $this->createMock(FileDriver::class);
        $this->configMock = $this->createMock(ScopeConfigInterface::class);
        $this->shellMock = $this->createMock(Shell::class);

        $this->tailReader = new TailReader(
            $this->filesystemMock,
            $this->fileMock,
            $this->fileDriverMock,
            $this->configMock,
            $this->shellMock
        );
    }

    public function testSuccessfullyReadsFile(): void
    {
        $path = 'system.log';
        $logDirMock = $this->createMock(ReadInterface::class);
        $absolutePath = 'var/log/' . $path;
        $content = '[2024-05-20T19:20:25.014117+00:00] main.INFO: Broken reference: the \'catalog.compare.sidebar';
        $numberOfLines = '1';

        $this->fileMock->expects($this->once())
            ->method('getPathInfo')
            ->with($path)
            ->willReturn([
                'dirname' => '.',
                'basename' => 'system.log',
                'extension' => 'log',
                'filename' => 'system'
            ]);

        $this->filesystemMock->expects($this->once())
            ->method('getDirectoryRead')
            ->with(DirectoryList::LOG)
            ->willReturn($logDirMock);

        $logDirMock->expects($this->once())
            ->method('getAbsolutePath')
            ->with($path)
            ->willReturn($absolutePath);

        $this->fileDriverMock->expects($this->once())
            ->method('isExists')
            ->with($absolutePath)
            ->willReturn(true);

        $this->configMock->expects($this->once())
            ->method('getValue')
            ->with('system/logs_management/lines_number')
            ->willReturn($numberOfLines);

        $this->shellMock->expects($this->once())
            ->method('execute')
            ->with('tail %s %s %s', ['-n', $numberOfLines, $absolutePath])
            ->willReturn($content);

        $this->assertSame($content, $this->tailReader->read($path));
    }

    public function testPathIsEmptyString(): void
    {
        $path = '';

        $this->fileMock->expects($this->once())
            ->method('getPathInfo')
            ->with($path)
            ->willReturn([
                'basename' => '',
                'filename' => '',
            ]);

        $this->expectException(FileSystemException::class);
        $this->expectExceptionMessage('Invalid file extension.');
        $this->tailReader->read($path);
    }

    public function testFileExtensionIsInvalid(): void
    {
        $path = 'system.txt';

        $this->fileMock->expects($this->once())
            ->method('getPathInfo')
            ->with($path)
            ->willReturn([
                'dirname' => '.',
                'basename' => 'system.txt',
                'extension' => 'txt',
                'filename' => 'system'
            ]);

        $this->expectException(FileSystemException::class);
        $this->expectExceptionMessage('Invalid file extension.');
        $this->tailReader->read($path);
    }

    public function testFileNotExists(): void
    {
        $path = 'system.log';
        $logDirMock = $this->createMock(ReadInterface::class);
        $absolutePath = 'var/log/' . $path;

        $this->fileMock->expects($this->once())
            ->method('getPathInfo')
            ->with($path)
            ->willReturn([
                'dirname' => '.',
                'basename' => 'system.log',
                'extension' => 'log',
                'filename' => 'system'
            ]);

        $this->filesystemMock->expects($this->once())
            ->method('getDirectoryRead')
            ->with(DirectoryList::LOG)
            ->willReturn($logDirMock);

        $logDirMock->expects($this->once())
            ->method('getAbsolutePath')
            ->with($path)
            ->willReturn($absolutePath);

        $this->fileDriverMock->expects($this->once())
            ->method('isExists')
            ->with($absolutePath)
            ->willReturn(false);

        $this->expectException(FileSystemException::class);
        $this->expectExceptionMessage('The file was not found.');
        $this->tailReader->read($path);
    }
}
