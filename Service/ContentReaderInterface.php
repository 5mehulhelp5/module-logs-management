<?php

declare(strict_types=1);

namespace Cloudflex\LogsManagement\Service;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;

interface ContentReaderInterface
{
    public const FILE_EXTENSION = 'log';

    /**
     * Read the content of a file
     *
     * @param string $path
     *
     * @return string
     * @throws FileSystemException|LocalizedException
     */
    public function read(string $path): string;
}
