<?php

declare(strict_types=1);

namespace Cloudflex\LogsManagement\Test\Unit\Controller\Adminhtml\Logs;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use Magento\Framework\View\Result\PageFactory;
use Cloudflex\LogsManagement\Controller\Adminhtml\Logs\ListAction;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ListActionTest extends TestCase
{
    /**
     * @var MockObject
     */
    private MockObject $resultPageFactoryMock;

    /**
     * @var MockObject
     */
    private MockObject $pageMock;

    /**
     * @var MockObject
     */
    private MockObject $pageConfigMock;

    /**
     * @var MockObject
     */
    private MockObject $titleMock;

    /**
     * @var ListAction
     */
    private ListAction $listActionController;

    protected function setUp(): void
    {
        $this->resultPageFactoryMock = $this->createMock(PageFactory::class);
        $this->pageMock = $this->createMock(Page::class);
        $this->pageConfigMock = $this->createMock(Config::class);
        $this->titleMock = $this->createMock(Title::class);
        $contextMock = $this->createMock(Context::class);

        $this->listActionController = new ListAction(
            $contextMock,
            $this->resultPageFactoryMock
        );
    }

    public function testListAction(): void
    {
        $this->resultPageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->pageMock);

        $this->pageMock->expects($this->once())
            ->method('setActiveMenu')
            ->with('Cloudflex_LogsManagement::system_index')
            ->willReturnSelf();

        $this->pageMock->expects($this->once())
            ->method('addBreadcrumb')
            ->with(__('Logs Management'), __('Logs Management'))
            ->willReturnSelf();

        $this->pageMock->expects($this->once())
            ->method('getConfig')
            ->willReturn($this->pageConfigMock);

        $this->pageConfigMock->expects($this->once())
            ->method('getTitle')
            ->willReturn($this->titleMock);

        $this->titleMock->expects($this->once())
            ->method('prepend')
            ->with(__('Logs Management'))
            ->willReturn($this->pageMock);

        $this->assertSame($this->pageMock, $this->listActionController->execute());
    }
}
