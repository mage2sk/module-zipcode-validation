<?php
/**
 * Panth_ZipcodeValidation
 *
 * @category  Panth
 * @package   Panth_ZipcodeValidation
 * @author    Panth
 * @copyright Copyright (c) 2025 Panth
 */

namespace Panth\ZipcodeValidation\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ImportExport extends Field
{
    /**
     * @var string
     */
    protected $_template = 'Panth_ZipcodeValidation::system/config/import_export.phtml';

    /**
     * Remove scope label
     *
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * Get import URL
     *
     * @return string
     */
    public function getImportUrl()
    {
        return $this->getUrl('zipcodevalidation/import/index');
    }

    /**
     * Get export URL
     *
     * @return string
     */
    public function getExportUrl()
    {
        return $this->getUrl('zipcodevalidation/export/index');
    }

    /**
     * Get sample download URL
     *
     * @return string
     */
    public function getSampleUrl()
    {
        return $this->getUrl('zipcodevalidation/sample/index');
    }
}
