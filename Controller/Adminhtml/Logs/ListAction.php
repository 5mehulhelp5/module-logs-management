<?php

declare(strict_types=1);

namespace NetBytes\LogsManagement\Controller\Adminhtml\Logs;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

class ListAction extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'NetBytes_LogsManagement::index';

    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return Page
     */
    public function execute(): Page
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('NetBytes_LogsManagement::system_index');
        $resultPage->addBreadcrumb(__('Logs Management'), __('Logs Management'));
        $resultPage->getConfig()->getTitle()->prepend(__('Logs Management'));

        return $resultPage;
    }
}
