<?php

declare(strict_types=1);

namespace Cloudflex\LogsManagement\Test\Unit\Helper;

use Magento\Framework\Exception\FileSystemException;
use \Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Filesystem\Io\File;
use Cloudflex\LogsManagement\Helper\FileMetadataBuilder;
use Cloudflex\LogsManagement\Helper\TreeBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TreeBuilderTest extends TestCase
{
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
    private MockObject $metadataBuilderMock;

    /**
     * @var TreeBuilder
     */
    private TreeBuilder $treeBuilder;

    protected function setUp(): void
    {
        $this->fileMock = $this->createMock(File::class);
        $this->fileDriverMock = $this->createMock(FileDriver::class);
        $this->metadataBuilderMock = $this->createMock(FileMetadataBuilder::class);

        $this->treeBuilder = new TreeBuilder(
            $this->fileMock,
            $this->fileDriverMock,
            $this->metadataBuilderMock
        );
    }

    public function testSuccessfullyBuildTree(): void
    {
        $subItems = [
            [
                'text' => 'exception.log',
                'mod_date' => '2020-01-01 00:00:00',
                'permissions' => '-rw-rw-r--',
                'owner' => 'www-data / ',
                'size' => 52876,
                'leaf' => true,
                'is_image' => false,
                'filetype' => 'log'
            ]
        ];

        $items = [
            [
                'text' => 'debug.log',
                'mod_date' => '2020-01-01 00:00:00',
                'permissions' => '-rw-rw-r--',
                'owner' => 'www-data / ',
                'size' => 24311234,
                'leaf' => true,
                'is_image' => false,
                'filetype' => 'log'
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

        $parentId = '#';

        $userCallCount = 0;
        $this->metadataBuilderMock->expects($this->exactly(2))
            ->method('getFormattedMetadata')
            ->willReturnCallback(function () use (&$userCallCount) {
                $userCallCount++;
                return match ($userCallCount) {
                    1 => 'Last modified: 2020-01-01 00:00:00\\nFile size: 23.26 MB',
                    2 => 'Last modified: 2020-01-01 00:00:00\\nFile size: 51.64 KB',
                    default => '',
                };
            });

        $this->fileDriverMock->expects($this->once())
            ->method('isDirectory')
            ->with('/var/www/html/var/log/api')
            ->willReturn(true);

        $this->fileMock->expects($this->once())
            ->method('ls')
            ->with(3)
            ->willReturn($subItems);

        $result = [
            [
                'id' => 1,
                'parent' => '#',
                'text' => 'debug.log',
                'icon' => 'jstree-file',
                'state' => [
                    'opened' => 0
                ],
                'li_attr' => [
                    'data-item-type' => 'file',
                    'data-item-path' => 'debug.log',
                    'title' => 'Last modified: 2020-01-01 00:00:00\\nFile size: 23.26 MB'
                ]
            ],
            [
                'id' => 2,
                'parent' => '#',
                'text' => 'api',
                'icon' => 'jstree-folder',
                'state' => [
                    'opened' => 1
                ],
                'li_attr' => [
                    'data-item-type' => 'dir',
                    'data-item-path' => 'api',
                    'title' => ''
                ]
            ],
            [
                'id' => 3,
                'parent' => '2',
                'text' => 'exception.log',
                'icon' => 'jstree-file',
                'state' => [
                    'opened' => 0
                ],
                'li_attr' => [
                    'data-item-type' => 'file',
                    'data-item-path' => 'api/exception.log',
                    'title' => 'Last modified: 2020-01-01 00:00:00\\nFile size: 51.64 KB'
                ]
            ]
        ];

        $this->assertEquals($result, $this->treeBuilder->buildTree($items, $parentId));
    }

    public function testItemsIsEmpty(): void
    {
        $items = [];
        $parent = '#';
        $this->assertEquals([], $this->treeBuilder->buildTree($items, $parent));
    }
}
