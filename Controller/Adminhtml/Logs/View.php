<?php

declare(strict_types=1);

namespace NetBytes\LogsManagement\Controller\Adminhtml\Logs;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use NetBytes\LogsManagement\Service\ContentReaderInterface;

class View extends Action implements HttpGetActionInterface
{
    const string ADMIN_RESOURCE = 'NetBytes_LogsManagement::index';

    /**
     * @param Context $context
     * @param ContentReaderInterface $contentReader
     */
    public function __construct(
        private readonly Context $context,
        private readonly ContentReaderInterface $contentReader
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

        try {
            $content = $this->contentReader->read($path);
            $resultJson->setData([
                'error' => false,
                'message' => null,
                'content' => $content
            ]);
        } catch (FileSystemException $e) {
            $resultJson->setData([
                'error' => true,
                'message' => $e->getMessage(),
                'content' => null
            ]);
        }

        return $resultJson;
    }
}
