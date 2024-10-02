<?php

declare(strict_types=1);

namespace NetBytes\LogsExplorer\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File;

readonly class ContentReader
{
    /**
     * @param DirectoryList $directoryList
     * @param File $driverFile
     */
    public function __construct(
        private DirectoryList $directoryList,
        private File $driverFile
    ) {
    }

    public function read(string $relativePath): string
    {
        $basePath = $this->directoryList->getPath(DirectoryList::LOG);

        $absolutePath = $basePath . '/' . $relativePath;

        $content = $this->driverFile->fileGetContents($absolutePath);

        return $content;
    }
}
