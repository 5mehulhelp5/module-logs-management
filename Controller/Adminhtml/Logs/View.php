<?php

declare(strict_types=1);

namespace NetBytes\LogsExplorer\Controller\Adminhtml\Logs;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use NetBytes\LogsExplorer\Helper\ContentReader;

class View extends Action implements HttpGetActionInterface
{
    /**
     * @param Context $context
     * @param ContentReader $contentReader
     */
    public function __construct(
        private readonly Context $context,
        private readonly ContentReader $contentReader
    ) {
        parent::__construct($context);
    }

    /**
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $path = $this->_request->getParam('path');

        $content = $this->contentReader->read($path);

        $resultJson->setData($content);

        return $resultJson;
    }
}
