<?php

declare(strict_types=1);

namespace NetBytes\LogsExplorer\Service;

interface ContentReaderInterface
{
    const string FILE_EXTENSION = 'log';

    /**
     * Read the content of a file
     *
     * @param string $path
     *
     * @return string
     */
    public function read(string $path): string;
}
