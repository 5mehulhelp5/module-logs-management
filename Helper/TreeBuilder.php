<?php

declare(strict_types=1);

namespace Cloudflex\LogsManagement\Helper;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Filesystem\Io\File;
use Cloudflex\LogsManagement\Service\ContentReaderInterface;

class TreeBuilder
{
    public const ROOT_ID = '#';
    protected const FILE_MODE = 0;
    protected const DIR_MODE = 1;

    /**
     * @var int Counter to generate unique IDs
     */
    private int $counter = 1;

    /**
     * @var array Variable to store the tree
     */
    private array $tree = [];

    /**
     * @param File $fileIo
     * @param FileDriver $fileDriver
     * @param FileMetadataBuilder $metadataBuilder
     */
    public function __construct(
        private readonly File $fileIo,
        private readonly FileDriver $fileDriver,
        private readonly FileMetadataBuilder $metadataBuilder
    ) {
    }

    /**
     * Build the tree
     *
     * @param array $items
     * @param string $parentId
     * @param string $path
     *
     * @return array
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function buildTree(array $items, string $parentId, string $path = ''): array
    {
        foreach ($items as $item) {
            $relativePath = $this->generateRelativePath($item, $path);

            if ($this->isFile($item)) {
                $this->processFileItem($item, $parentId, $relativePath);
            } elseif ($this->isDirectory($item)) {
                $this->processDirectoryItem($item, $parentId, $relativePath);
            }
        }

        return $this->tree;
    }

    /**
     * @param array $item
     * @param string $parentId
     * @param string $relativePath
     *
     * @return void
     */
    protected function processFileItem(array $item, string $parentId, string $relativePath): void
    {
        $metadata = [
            'mod_date' => $item['mod_date'],
            'size' => $item['size']
        ];

        $this->addToTree($item['text'], $parentId, self::FILE_MODE, $relativePath, $metadata);
    }

    /**
     * @param array $item
     * @param string $parentId
     * @param string $relativePath
     *
     * @return void
     * @throws LocalizedException
     */
    protected function processDirectoryItem(array $item, string $parentId, string $relativePath): void
    {
        $this->addToTree($item['text'], $parentId, self::DIR_MODE, $relativePath);
        $this->fileIo->cd($item['id']);
        $subItems = $this->fileIo->ls(3);
        $this->buildTree($subItems, (string)($this->counter - 1), $relativePath);
    }

    /**
     * Add a new node to the tree
     *
     * @param string $text
     * @param string $parentId
     * @param int $iconMode
     * @param string $relativePath
     * @param array $metadata
     *
     * @return void
     */
    protected function addToTree(
        string $text,
        string $parentId,
        int $iconMode,
        string $relativePath,
        array $metadata = []
    ): void {
        $node = [
            'id'     => $this->counter,
            'parent' => $parentId,
            'text'   => $text,
            'icon'   => $iconMode ? 'jstree-folder' : 'jstree-file',
            'state'  => ['opened' => $iconMode],
            'li_attr' => [
                'data-item-type' => $iconMode ? 'dir' : 'file',
                'data-item-path' => $relativePath,
                'title' => !$iconMode ? $this->metadataBuilder->getFormattedMetadata($metadata) : ''
            ]
        ];

        $this->tree[] = $node;
        $this->counter++;
    }

    /**
     * @param array $item
     * @param string $path
     *
     * @return string
     */
    private function generateRelativePath(array $item, string $path): string
    {
        return $path ? $path . '/' . $item['text'] : $item['text'];
    }

    /**
     * @param array $item
     *
     * @return bool
     */
    private function isFile(array $item): bool
    {
        return isset($item['filetype']) && $item['filetype'] === ContentReaderInterface::FILE_EXTENSION;
    }

    /**
     * @param array $item
     *
     * @return bool
     * @throws FileSystemException
     */
    private function isDirectory(array $item): bool
    {
        return isset($item['id']) && $this->fileDriver->isDirectory($item['id']);
    }
}
