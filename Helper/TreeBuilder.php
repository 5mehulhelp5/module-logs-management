<?php

declare(strict_types=1);

namespace NetBytes\LogsManagement\Helper;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Filesystem\Io\File;
use NetBytes\LogsManagement\Service\ContentReaderInterface;

class TreeBuilder
{
    public const ROOT_ID = '#';
    protected const FILE_MODE = 0;
    protected const DIR_MODE = 1;
    protected const NEW_LINE_CHAR = PHP_EOL;

    /**
     * @var int Counter to generate unique IDs
     */
    private int $counter = 1;
    /**
     * @var array Variable to store the tree
     */
    private array $tree = [];
    /**
     * @var File
     */
    private File $fileIo;
    /**
     * @var FileDriver
     */
    private FileDriver $fileDriver;

    /**
     * @param File $fileIo
     * @param FileDriver $fileDriver
     */
    public function __construct(
        File $fileIo,
        FileDriver $fileDriver
    ) {
        $this->fileIo = $fileIo;
        $this->fileDriver = $fileDriver;
    }

    /**
     * Build the tree
     *
     * @param array $items
     * @param string $parentId
     * @param string $path
     *
     * @return array
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
                'title' => !$iconMode ? $this->getFormattedMetadata($metadata) : ''
            ]
        ];

        $this->tree[] = $node;
        $this->counter++;
    }

    /**
     * @param array $metadata
     *
     * @return string
     */
    private function getFormattedMetadata(array $metadata): string
    {
        if (empty($metadata) || !isset($metadata['mod_date'], $metadata['size'])) {
            return '';
        }

        return implode(self::NEW_LINE_CHAR, [
            __('Last modified: %1', $metadata['mod_date']),
            __('File size: %1', $this->getFormattedBytes($metadata['size']))
        ]);
    }

    /**
     * @param int $bytes
     *
     * @return string
     */
    private function getFormattedBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            $size = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $size = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $size = number_format($bytes / 1024, 2) . ' KB';
        } else {
            $size = $bytes . ' B';
        }

        return $size;
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
