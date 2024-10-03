<?php

declare(strict_types=1);

namespace NetBytes\LogsManagement\Controller\Adminhtml\Logs;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;

class ListAction extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'NetBytes_LogsManagement::index';

    /**
     * @ingeritDoc
     */
    public function execute(): ResultInterface
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('NetBytes_LogsManagement::system_index');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Logs Management'));

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
