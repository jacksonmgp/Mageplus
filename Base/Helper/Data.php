<?php
/**
 * @author Mageplus
 * @copyright Copyright (c) Mageplus (https://www.mgpstore.com)
 * @package Mageplus_Base
 */

declare(strict_types=1);

namespace Mageplus\Base\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Escaper;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Module\Manager;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;
use Mageplus\Base\Logger\Logger;

class Data extends AbstractHelper
{
    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var WriterInterface
     */
    protected WriterInterface $configWriter;

    /**
     * @var CurlFactory
     */
    protected CurlFactory $curlFactory;

    /**
     * @var JsonHelper
     */
    protected JsonHelper $jsonHelper;

    /**
     * @var Json
     */
    protected Json $jsonSerializer;

    /**
     * @var Manager
     */
    protected Manager $moduleManager;

    /**
     * @var Logger
     */
    protected Logger $logger;

    /**
     * @var Http
     */
    protected Http $request;

    /**
     * @var HttpContext
     */
    protected HttpContext $httpContext;

    /**
     * @var Escaper
     */
    protected Escaper $escaper;

    /**
     * Data constructor.
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param WriterInterface $configWriter
     * @param CurlFactory $curlFactory
     * @param JsonHelper $jsonHelper
     * @param Json $jsonSerializer
     * @param Http $request
     * @param Manager $moduleManager
     * @param Logger $logger
     * @param HttpContext $httpContext
     * @param Escaper $escaper
     * @param Context $context
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface  $scopeConfig,
        WriterInterface       $configWriter,
        CurlFactory           $curlFactory,
        JsonHelper            $jsonHelper,
        Json                  $jsonSerializer,
        Http                  $request,
        Manager               $moduleManager,
        Logger                $logger,
        HttpContext           $httpContext,
        Escaper               $escaper,
        Context               $context
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        $this->curlFactory = $curlFactory;
        $this->jsonHelper = $jsonHelper;
        $this->jsonSerializer = $jsonSerializer;
        $this->request = $request;
        $this->moduleManager = $moduleManager;
        $this->logger = $logger;
        $this->httpContext = $httpContext;
        $this->escaper = $escaper;
        parent::__construct($context);
    }

    /**
     * @return Escaper
     */
    public function escaper(): Escaper
    {
        return $this->escaper;
    }


    public function isCustomerLoggedIn()
    {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    /**
     * @return StoreManagerInterface
     */
    public function getStoreManager(): StoreManagerInterface
    {
        return $this->storeManager;
    }

    /**
     * @param $node
     */
    public function getStoreConfig($node)
    {
        return $this->scopeConfig->getValue($node, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $node
     * @param $value
     */
    public function saveConfig($node, $value)
    {
        $this->configWriter->save($node, $value);
    }

    /**
     * @param $data
     * @return string
     */
    public function jsonEncode($data): string
    {
        return $this->jsonHelper->jsonEncode($data);
    }

    /**
     * @param $data
     */
    public function jsonDecode($data)
    {
        return $this->jsonHelper->jsonDecode($data);
    }

    /**
     * @param $data
     * @return string
     */
    public function jsonSerialize($data): string
    {
        return $this->jsonSerializer->serialize($data);
    }

    /**
     * @param $data
     */
    public function jsonUnSerialize($data)
    {
        return $this->jsonSerializer->unserialize($data);
    }

    /**
     * @param $module
     * @return bool
     */
    public function _isModuleEnable($module): bool
    {
        if ($this->moduleManager->isOutputEnabled($module) && $this->moduleManager->isEnabled($module)) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getFullActionName(): string
    {
        return $this->request->getFullActionName();
    }

    /* Check current page is homepage or not */
    /**
     * @return bool
     */
    public function isHomepage(): bool
    {
        if ($this->request->getFullActionName() == 'cms_index_index') {
            return true;
        }
        return false;
    }

    /**
     * @param $value
     * @return string
     */
    public function getUrl($value): string
    {
        return $this->_getUrl($value, ['_secure' => true]);
    }

    /**
     * @return Logger
     */
    public function logger(): Logger
    {
        return $this->logger;
    }

    /**
     * @param $path
     * @param $message
     */
    public function directLogger($path, $message)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . $path);
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($message);
    }

    /**
     * @param array $post_param
     * @param $action
     * @param $method
     * @return string
     */
    public function curl(array $post_param, $action, $method =\Laminas\Http\Request::METHOD_GET): string
    {
        $dataEncode  = $this->jsonEncode($post_param);

        $httpAdapter = $this->curlFactory->create();

        $httpAdapter->write(
            $method,
            $action,
            '1.1',
            ["Content-Type:application/json"],
            $dataEncode
        );

        $result = $httpAdapter->read();
        $body = $this->extractBody($result);
        return $body;
    }

    /**
     * Extract the body from a response string
     *
     * @param string $response_str
     * @return string
     */
    public function extractBody($response_str)
    {
        $parts = preg_split('|(?:\r\n){2}|m', $response_str, 2);
        if (isset($parts[1])) {
            return $parts[1];
        }
        return '';
    }
}
