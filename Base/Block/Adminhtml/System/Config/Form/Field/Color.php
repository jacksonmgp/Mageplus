<?php
/**
 * @author Mageplus
 * @copyright Copyright (c) Mageplus (https://www.mgpstore.com)
 * @package Mageplus_Base
 */

declare(strict_types=1);

namespace Mageplus\Base\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Color extends Field
{
    const FIELD_SELECTOR_ATTRIBUTE = 'data-octarine';
    const FIELD_SELECTOR_CLASS = 'octarine';

    /**
     * Add color picker
     *
     * @param AbstractElement $element
     * @return String
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->addCustomAttribute(self::FIELD_SELECTOR_ATTRIBUTE, '1');
        $element->addClass(self::FIELD_SELECTOR_CLASS);
        return $element->getElementHtml();
    }
}
