<?php

declare(strict_types=1);

namespace NetBytes\LogsManagement\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Shell;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Filesystem\Io\File;

class TailReader implements ContentReaderInterface
{
    public const LINES_NUMBER_CONFIG = 'system/logs_management/lines_number';

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;
    /**
     * @var File
     */
    private File $file;
    /**
     * @var FileDriver
     */
    private FileDriver $fileDriver;
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $config;
    /**
     * @var Shell
     */
    private Shell $shell;

    /**
     * @param Filesystem $filesystem
     * @param File $file
     * @param FileDriver $fileDriver
     * @param ScopeConfigInterface $config
     * @param Shell $shell
     */
    public function __construct(
        Filesystem $filesystem,
        File $file,
        FileDriver $fileDriver,
        ScopeConfigInterface $config,
        Shell $shell
    ) {
        $this->config = $config;
        $this->fileDriver = $fileDriver;
        $this->filesystem = $filesystem;
        $this->file = $file;
        $this->shell = $shell;
    }

    /**
     * Read the content of a file
     *
     * @param string $path
     *
     * @return string
     * @throws FileSystemException|LocalizedException
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

        $arguments = ['-n', $this->getNumberOfLines(), $filePath];

        return $this->shell->execute('tail %s %s %s', $arguments);
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
        $pathInfo = $this->file->getPathInfo($path);

        if (isset($pathInfo['extension']) && $pathInfo['extension'] === ContentReaderInterface::FILE_EXTENSION) {
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
     * @return string
     */
    protected function getNumberOfLines(): string
    {
        return $this->config->getValue(self::LINES_NUMBER_CONFIG);
    }
}
