<?php

declare(strict_types=1);

namespace Cloudflex\LogsManagement\Test\Unit\Controller\Adminhtml\Logs;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Cloudflex\LogsManagement\Controller\Adminhtml\Logs\View;
use Cloudflex\LogsManagement\Service\ContentReaderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    /**
     * @var MockObject
     */
    private MockObject $resultJsonFactoryMock;

    /**
     * @var MockObject
     */
    private MockObject $jsonMock;

    /**
     * @var MockObject
     */
    private MockObject $requestMock;

    /**
     * @var MockObject
     */
    private MockObject $contentReaderMock;

    /**
     * @var View
     */
    private View $viewController;

    protected function setUp(): void
    {
        $this->resultJsonFactoryMock = $this->createMock(JsonFactory::class);
        $this->jsonMock = $this->createMock(Json::class);
        $this->requestMock = $this->createMock(RequestInterface::class);
        $this->contentReaderMock = $this->createMock(ContentReaderInterface::class);
        $contextMock = $this->createMock(Context::class);

        $this->viewController = new View(
            $contextMock,
            $this->resultJsonFactoryMock,
            $this->requestMock,
            $this->contentReaderMock
        );
    }

    public function testSuccessfullyReturnsJson(): void
    {
        $path = 'debug.log';
        $content = '[2024-05-20T19:20:25.014117+00:00] main.INFO: Broken reference: the \'catalog.compare.sidebar';

        $this->resultJsonFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->jsonMock);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('path', '')
            ->willReturn($path);

        $this->contentReaderMock->expects($this->once())
            ->method('read')
            ->with($path)
            ->willReturn($content);

        $this->jsonMock->expects($this->once())
            ->method('setData')
            ->with([
                'error' => false,
                'message' => null,
                'content' => $content
            ])->willReturnSelf();

        $this->assertSame($this->jsonMock, $this->viewController->execute());
    }

    public function testPathParamIsMissing(): void
    {
        $path = '';
        $exception = new FileSystemException(__('Invalid file extension.'));

        $this->resultJsonFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->jsonMock);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('path', '')
            ->willReturn($path);

        $this->contentReaderMock->expects($this->once())
            ->method('read')
            ->with($path)
            ->willThrowException($exception);

        $this->jsonMock->expects($this->once())
            ->method('setData')
            ->with([
                'error' => true,
                'message' => $exception->getMessage(),
                'content' => null
            ])->willReturnSelf();

        $this->assertSame($this->jsonMock, $this->viewController->execute());
    }

    public function testFileNotExists(): void
    {
        $path = 'not_exists.log';
        $exception = new FileSystemException(__('The file was not found.'));

        $this->resultJsonFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->jsonMock);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('path', '')
            ->willReturn($path);

        $this->contentReaderMock->expects($this->once())
            ->method('read')
            ->with($path)
            ->willThrowException($exception);

        $this->jsonMock->expects($this->once())
            ->method('setData')
            ->with([
                'error' => true,
                'message' => $exception->getMessage(),
                'content' => null
            ])->willReturnSelf();

        $this->assertSame($this->jsonMock, $this->viewController->execute());
    }

    public function testLocalizedExceptionThrows(): void
    {
        $path = 'not_exists.log';
        $exception = new LocalizedException(__('Example message.'));

        $this->resultJsonFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->jsonMock);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('path', '')
            ->willReturn($path);

        $this->contentReaderMock->expects($this->once())
            ->method('read')
            ->with($path)
            ->willThrowException($exception);

        $this->jsonMock->expects($this->once())
            ->method('setData')
            ->with([
                'error' => true,
                'message' => $exception->getMessage(),
                'content' => null
            ])->willReturnSelf();

        $this->assertSame($this->jsonMock, $this->viewController->execute());
    }
}
