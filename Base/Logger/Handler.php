<?php
/**
 * @author Mageplus
 * @copyright Copyright (c) Mageplus (https://www.mgpstore.com)
 * @package Mageplus_Base
 */

declare(strict_types=1);

namespace Mageplus\Base\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger as MonoLogger;

class Handler extends Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = MonoLogger::DEBUG;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/mageplus.log';
}
