<?php

declare(strict_types=1);

namespace Cloudflex\LogsManagement\Helper;

class FileMetadataBuilder
{
    protected const NEW_LINE_CHAR = PHP_EOL;
    private const BYTES_PER_KB = 1024;
    private const BYTES_PER_MB = 1048576;
    private const BYTES_PER_GB = 1073741824;

    /**
     * Get the formatted metadata
     *
     * @param array $metadata
     *
     * @return string
     */
    public function getFormattedMetadata(array $metadata): string
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
     * Get the formatted bytes
     *
     * @param int $bytes
     *
     * @return string
     */
    private function getFormattedBytes(int $bytes): string
    {
        if ($bytes >= self::BYTES_PER_GB) {
            $size = number_format($bytes / self::BYTES_PER_GB, 2) . ' GB';
        } elseif ($bytes >= self::BYTES_PER_MB) {
            $size = number_format($bytes / self::BYTES_PER_MB, 2) . ' MB';
        } elseif ($bytes >= self::BYTES_PER_KB) {
            $size = number_format($bytes / self::BYTES_PER_KB, 2) . ' KB';
        } else {
            $size = $bytes . ' B';
        }

        return $size;
    }
}
