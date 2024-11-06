<?php

declare(strict_types=1);

namespace QubaByte\LogsManagement\Test\Unit\ViewModel;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Serialize\SerializerInterface;
use QubaByte\LogsManagement\Helper\TreeBuilder;
use QubaByte\LogsManagement\ViewModel\LogsTree;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LogsTreeTest extends TestCase
{
    /**
     * @var MockObject
     */
    private MockObject $directoryList;

    /**
     * @var MockObject
     */
    private MockObject $fileIo;

    /**
     * @var MockObject
     */
    private MockObject $treeBuilder;

    /**
     * @var MockObject
     */
    private MockObject $serializer;

    /**
     * @var LogsTree
     */
    private LogsTree $logsTree;

    protected function setUp(): void
    {
        $this->directoryList = $this->createMock(DirectoryList::class);
        $this->fileIo = $this->createMock(File::class);
        $this->treeBuilder = $this->createMock(TreeBuilder::class);
        $this->serializer = $this->createMock(SerializerInterface::class);

        $this->logsTree = new LogsTree(
            $this->directoryList,
            $this->fileIo,
            $this->treeBuilder,
            $this->serializer
        );
    }

    public function testGetTreeReturnsSerializedTree(): void
    {
        $items = [
            [
                'text' => 'system.log',
                'mod_date' => '2020-01-01 00:00:00',
                'permissions' => '-rw-rw-r--',
                'owner' => 'www-data / ',
                'size' => 19986351,
                'leaf' => true,
                'is_image' => false,
                'file_type' => 'log',
            ],
            [
                'text' => 'api',
                'mod_date' => '2020-01-01 00:00:00',
                'permissions' => 'drwxr-xr-x',
                'owner' => 'www-data / ',
                'leaf' => false,
                'id' => '/var/www/html/var/log/api'
            ]
        ];
        $tree = [
            [
                'id' => 1,
                'parent' => '#',
                'text' => 'system.log',
                'icon' => 'jstree-file',
                'state' => ['opened' => 0],
                'li_attr' => [
                    'data-item-type' => 'file',
                    'data-item-path' => 'system.log',
                    'title' => 'Last modified: 2020-01-01 00:00:00\nFile size: 19.05 MB'
                ]
            ],
            [
                'id' => 2,
                'parent' => '#',
                'text' => 'api',
                'icon' => 'jstree-folder',
                'state' => ['opened' => 1],
                'li_attr' => [
                    'data-item-type' => 'dir',
                    'data-item-path' => 'api'
                ]
            ]
        ];
        $serializedTree = json_encode($tree);

        $this->directoryList->expects($this->once())
            ->method('getPath')
            ->with(DirectoryList::LOG)
            ->willReturn('/var/log');

        $this->fileIo->expects($this->once())
            ->method('cd')
            ->with('/var/log');

        $this->fileIo->expects($this->once())
            ->method('ls')
            ->with(3)
            ->willReturn($items);

        $this->treeBuilder->expects($this->once())
            ->method('buildTree')
            ->with($items, TreeBuilder::ROOT_ID)
            ->willReturn($tree);

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with($tree)
            ->willReturn($serializedTree);

        $this->assertEquals($serializedTree, $this->logsTree->getTree());
    }

    public function testGetTreeHandlesLocalizedException(): void
    {
        $items = [];
        $serializedTree = json_encode($items);

        $this->directoryList->expects($this->once())
            ->method('getPath')
            ->with(DirectoryList::LOG)
            ->willReturn('/var/log');

        $this->fileIo->expects($this->once())
            ->method('cd')
            ->with('/var/log');

        $this->fileIo->expects($this->once())
            ->method('ls')
            ->with(3)
            ->willThrowException(new LocalizedException(__('Error')));

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with($items)
            ->willReturn($serializedTree);

        $this->assertSame($serializedTree, $this->logsTree->getTree());
    }

    public function testGetItemsThrowsFileSystemException(): void
    {
        $items = [];
        $serializedTree = json_encode($items);
        $this->directoryList->expects($this->once())
            ->method('getPath')
            ->with(DirectoryList::LOG)
            ->willThrowException(new FileSystemException(__('Filesystem Error')));

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with($items)
            ->willReturn($serializedTree);

        $this->assertSame($serializedTree, $this->logsTree->getTree());
    }
}
