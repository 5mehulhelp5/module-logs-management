<?php

declare(strict_types=1);

namespace NetBytes\LogsManagement\Controller\Adminhtml\Logs;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use NetBytes\LogsManagement\Service\ContentReaderInterface;

class View extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'NetBytes_LogsManagement::index';

    /**
     * @var ContentReaderInterface
     */
    private ContentReaderInterface $contentReader;

    /**
     * @param Context $context
     * @param ContentReaderInterface $contentReader
     */
    public function __construct(
        Context $context,
        ContentReaderInterface $contentReader
    ) {
        parent::__construct($context);
        $this->contentReader = $contentReader;
    }

    /**
     * @ingeritDoc
     */
    public function execute(): ResultInterface
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $path = $this->_request->getParam('path');

        try {
            $content = $this->contentReader->read($path);
            $resultJson->setData([
                'error' => false,
                'message' => null,
                'content' => $content
            ]);
        } catch (FileSystemException|LocalizedException $e) {
            $resultJson->setData([
                'error' => true,
                'message' => $e->getMessage(),
                'content' => null
            ]);
        }

        return $resultJson;
    }
}
