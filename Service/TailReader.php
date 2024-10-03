<?php

declare(strict_types=1);

namespace NetBytes\LogsManagement\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;

class TailReader implements ContentReaderInterface
{
    const string LINES_NUMBER_CONFIG = 'system/logs_management/lines_number';

    /**
     * @param Filesystem $filesystem
     * @param File $fileDriver
     * @param ScopeConfigInterface $config
     */
    public function __construct(
        private Filesystem $filesystem,
        private File $fileDriver,
        private ScopeConfigInterface $config
    ) {
    }

    /**
     * Read the content of a file
     *
     * @param string $path
     *
     * @return string
     * @throws FileSystemException
     */
    public function read(string $path): string
    {
        if (!$this->validateExtension($path)) {
            throw new FileSystemException(__('Invalid file extension.'));
        }

        $sanitizedPath = $this->sanitizePath($path);
        $logDir = $this->filesystem->getDirectoryRead(DirectoryList::LOG);
        $filePath = $logDir->getAbsolutePath($sanitizedPath);

        if (!$this->fileDriver->isExists($filePath)) {
            throw new FileSystemException(__('The file was not found.'));
        }

        $command = sprintf('tail -n %d %s', $this->getNumberOfLines(), escapeshellarg($filePath));
        $output = shell_exec($command);

        if (!is_string($output)) {
            throw new FileSystemException(__('The file could not be read.'));
        }

        return $output;
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

    /**
     * Get number of lines to read
     *
     * @return int
     */
    protected function getNumberOfLines(): int
    {
        return (int)$this->config->getValue(self::LINES_NUMBER_CONFIG);
    }
}
