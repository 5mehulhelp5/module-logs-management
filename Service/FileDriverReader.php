<?php

declare(strict_types=1);

namespace NetBytes\LogsExplorer\Service;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\Exception\ExtensionFileException;

readonly class FileDriverReader implements ContentReaderInterface
{
    /**
     * @param Filesystem $filesystem
     * @param File $fileDriver
     */
    public function __construct(
        private Filesystem $filesystem,
        private File $fileDriver
    ) {
    }

    /**
     * @param string $path
     *
     * @return string
     * @throws ExtensionFileException
     * @throws FileNotFoundException
     * @throws FileSystemException
     */
    public function read(string $path): string
    {
        if (!$this->validateExtension($path)) {
            throw new ExtensionFileException('Invalid extension for file: ' . $path);
        }

        $sanitizedPath = $this->sanitizePath($path);
        $logDir = $this->filesystem->getDirectoryRead(DirectoryList::LOG);
        $filePath = $logDir->getAbsolutePath($sanitizedPath);

        if (!$this->fileDriver->isExists($filePath)) {
            throw new FileNotFoundException('File not found: ' . $filePath);
        }

        return $this->fileDriver->fileGetContents($filePath);
    }

    /**
     * Check if extension is valid
     *
     * @param string $path
     *
     * @return bool
     */
    protected function validateExtension(string $path): bool
    {
        $fileExtension = pathinfo($path, PATHINFO_EXTENSION);

        if ($fileExtension === ContentReaderInterface::FILE_EXTENSION) {
            return true;
        }

        return false;
    }

    /**
     * Sanitize path
     *
     * @param string $path
     *
     * @return string
     */
    protected function sanitizePath(string $path): string
    {
        $sanitizedPath = preg_replace('/(\.\.\/|\.\/|~|#)/', '', $path);

        return filter_var($sanitizedPath, FILTER_SANITIZE_URL);
    }
}
