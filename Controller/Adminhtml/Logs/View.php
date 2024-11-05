<?php

declare(strict_types=1);

namespace NetBytes\LogsManagement\Controller\Adminhtml\Logs;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use NetBytes\LogsManagement\Service\ContentReaderInterface;

class View extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'NetBytes_LogsManagement::index';

    /**
     * @var JsonFactory
     */
    private JsonFactory $resultJsonFactory;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var ContentReaderInterface
     */
    private ContentReaderInterface $contentReader;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param RequestInterface $request
     * @param ContentReaderInterface $contentReader
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        RequestInterface $request,
        ContentReaderInterface $contentReader
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->contentReader = $contentReader;
        $this->request = $request;
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
