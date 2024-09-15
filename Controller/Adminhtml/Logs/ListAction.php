<?php

declare(strict_types=1);

namespace NetBytes\LogsExplorer\Controller\Adminhtml\Logs;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;

class ListAction extends Action implements HttpGetActionInterface
{
    /**
     * @ingeritdoc
     */
    public function execute(): void
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('NetBytes_LogsExplorer::system_index');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Logs Management'));
        $this->_view->renderLayout();
    }
}
