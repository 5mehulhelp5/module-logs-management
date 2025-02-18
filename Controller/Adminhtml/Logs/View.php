<?php

declare(strict_types=1);

namespace Cloudflex\LogsManagement\Controller\Adminhtml\Logs;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Cloudflex\LogsManagement\Service\ContentReaderInterface;

class View extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Cloudflex_LogsManagement::index';

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param RequestInterface $request
     * @param ContentReaderInterface $contentReader
     */
    public function __construct(
        Context $context,
        private readonly JsonFactory $resultJsonFactory,
        private readonly RequestInterface $request,
        private readonly ContentReaderInterface $contentReader
    ) {
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $resultJson = $this->resultJsonFactory->create();
        $path = $this->request->getParam('path', '');

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
