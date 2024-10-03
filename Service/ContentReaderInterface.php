<?php

declare(strict_types=1);

namespace NetBytes\LogsManagement\Service;

use Magento\Framework\Exception\FileSystemException;

interface ContentReaderInterface
{
    const string FILE_EXTENSION = 'log';

    /**
     * Read the content of a file
     *
     * @param string $path
     *
     * @return string
     * @throws FileSystemException
     */
    public function read(string $path): string;
}
