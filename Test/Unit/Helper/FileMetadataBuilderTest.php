<?php

declare(strict_types=1);

namespace QubaByte\LogsManagement\Test\Unit\Helper;

use QubaByte\LogsManagement\Helper\FileMetadataBuilder;
use PHPUnit\Framework\TestCase;

class FileMetadataBuilderTest extends TestCase
{
    private FileMetadataBuilder $metadataBuilder;

    protected function setUp(): void
    {
        $this->metadataBuilder = new FileMetadataBuilder();
    }

    public function testSuccessfullyFormatMetadata(): void
    {
        $metadata = [
            'mod_date' => '2020-01-01 00:00:00',
            'size' => 1024
        ];

        $result = 'Last modified: 2020-01-01 00:00:00' . PHP_EOL . 'File size: 1.00 KB';

        $this->assertEquals($result, $this->metadataBuilder->getFormattedMetadata($metadata));
    }

    public function testMetadataIsEmpty(): void
    {
        $metadata = [];

        $this->assertEquals('', $this->metadataBuilder->getFormattedMetadata($metadata));
    }

    public function testMetadataModDateKeyIsMissing(): void
    {
        $metadata = [
            'size' => 1024
        ];

        $this->assertEquals('', $this->metadataBuilder->getFormattedMetadata($metadata));
    }

    public function testMetadataSizeKeyIsMissing(): void
    {
        $metadata = [
            'mod_date' => '2020-01-01 00:00:00'
        ];

        $this->assertEquals('', $this->metadataBuilder->getFormattedMetadata($metadata));
    }

    public function testMetadataIncorrectKey(): void
    {
        $metadata = [
            'incorrect_key' => 'incorrect_value'
        ];

        $this->assertEquals('', $this->metadataBuilder->getFormattedMetadata($metadata));
    }
}
