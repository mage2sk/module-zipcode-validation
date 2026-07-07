<?php
namespace Panth\ZipcodeValidation\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ImportExport extends Field
{
    protected $_template = 'Panth_ZipcodeValidation::system/config/import_export.phtml';

    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    public function getImportUrl()
    {
        return $this->getUrl('zipcodevalidation/import/index');
    }

    public function getExportUrl()
    {
        return $this->getUrl('zipcodevalidation/export/index');
    }

    public function getSampleUrl()
    {
        return $this->getUrl('zipcodevalidation/sample/index');
    }
}
