<?php

declare(strict_types=1);

namespace NetBytes\LogsExplorer\Helper;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Io\File;

class TreeBuilder
{
    public const string ROOT_ID = '#';
    protected const string LOG_EXT = 'log';
    protected const int FILE_MODE = 0;
    protected const int DIR_MODE = 1;

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
     */
    public function __construct(private readonly File $fileIo)
    {
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
            $relativePath = $path ? $path . '/' . $item['text'] : $item['text'];
            if (isset($item['filetype']) && $item['filetype'] === self::LOG_EXT) {
                $this->addToTree($item['text'], $parentId, self::FILE_MODE, $relativePath);
            } elseif (isset($item['id']) && is_dir($item['id'])) {
                $this->addToTree($item['text'], $parentId, self::DIR_MODE, $relativePath);
                $this->fileIo->cd($item['id']);
                $subItems = $this->fileIo->ls(3);
                $this->buildTree($subItems, (string)($this->counter - 1), $relativePath);
            }
        }

        return $this->tree;
    }

    /**
     * Add a new node to the tree
     *
     * @param string $text
     * @param string $parentId
     * @param int $iconMode
     * @param string $relativePath
     *
     * @return void
     */
    protected function addToTree(
        string $text,
        string $parentId,
        int $iconMode,
        string $relativePath
    ): void {
        $node = [
            'id'     => $this->counter,
            'parent' => $parentId,
            'text'   => $text,
            'icon'   => $iconMode ? 'jstree-folder' : 'jstree-file',
            'state'  => ['opened' => $iconMode],
            'li_attr' => ['data-item-type' => $iconMode ? 'dir' : 'file', 'data-item-path' => $relativePath],
        ];

        $this->tree[] = $node;
        $this->counter++;
    }
}
