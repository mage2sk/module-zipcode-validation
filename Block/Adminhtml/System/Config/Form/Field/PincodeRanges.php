<?php
/**
 * Panth_ZipcodeValidation
 *
 * Dynamic rows field for managing PIN/ZIP code validation ranges
 * Includes custom pagination (10 rows per page)
 *
 * @category  Panth
 * @package   Panth_ZipcodeValidation
 * @author    Panth
 * @copyright Copyright (c) 2025 Panth
 */

namespace Panth\ZipcodeValidation\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Panth\ZipcodeValidation\Block\Adminhtml\System\Config\Form\Field\Column\CountryColumn;

class PincodeRanges extends AbstractFieldArray
{
    /**
     * @var CountryColumn
     */
    protected $countryRenderer;

    /**
     * Rows per page for pagination
     */
    const ROWS_PER_PAGE = 10;

    /**
     * Prefix for row IDs to ensure valid CSS selectors
     *
     * @var string
     */
    protected $_idPrefix = 'row_';

    /**
     * Prepare rendering the new field by adding all the needed columns
     */
    protected function _prepareToRender()
    {
        $this->addColumn('country_id', [
            'label' => __('Country'),
            'renderer' => $this->getCountryRenderer(),
            'class' => 'required-entry'
        ]);

        $this->addColumn('state_code', [
            'label' => __('State/Region Code'),
            'class' => 'input-text'
        ]);

        $this->addColumn('state_name', [
            'label' => __('State/Region Name'),
            'class' => 'input-text required-entry'
        ]);

        $this->addColumn('pincode_start', [
            'label' => __('PIN/ZIP Start'),
            'class' => 'input-text required-entry validate-digits'
        ]);

        $this->addColumn('pincode_end', [
            'label' => __('PIN/ZIP End'),
            'class' => 'input-text required-entry validate-digits'
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Range');
    }

    /**
     * Get unique row ID with prefix
     *
     * @param int $rowId
     * @return string
     */
    protected function _getRowId($rowId)
    {
        return $this->_idPrefix . $rowId;
    }

    /**
     * Get array rows - Override to prefix all row IDs
     * This is critical to fix the CSS selector error in the JavaScript template
     *
     * @return array
     */
    public function getArrayRows()
    {
        $rows = parent::getArrayRows();

        // Prefix all numeric row IDs to ensure valid CSS selectors
        foreach ($rows as $rowId => $row) {
            $currentId = $row->getData('_id');
            if ($currentId !== null && is_numeric($currentId)) {
                $row->setData('_id', $this->_getRowId($currentId));
            }
        }

        return $rows;
    }

    /**
     * Render array row
     * Override to fix row ID to include prefix
     *
     * @param DataObject $row
     * @return string
     */
    protected function _renderRow(DataObject $row)
    {
        $rowId = $row->getData('_id');
        if ($rowId !== null && is_numeric($rowId)) {
            $row->setData('_id', $this->_getRowId($rowId));
        }
        return parent::_renderRow($row);
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        $country = $row->getCountryId();
        if ($country !== null) {
            $options['option_' . $this->getCountryRenderer()->calcOptionHash($country)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * Get country column renderer
     *
     * @return CountryColumn
     * @throws LocalizedException
     */
    protected function getCountryRenderer()
    {
        if (!$this->countryRenderer) {
            $this->countryRenderer = $this->getLayout()->createBlock(
                CountryColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->countryRenderer;
    }

    /**
     * Render array cell for prototypeJS template and fix numeric row IDs
     *
     * @param string $columnName
     * @return string
     * @throws \Exception
     */
    protected function _toHtml()
    {
        $html = parent::_toHtml();

        // Fix numeric row IDs to prevent CSS selector errors
        // This is critical: CSS selectors cannot start with digits

        // Pattern 1: Fix tr id attributes: id="0" -> id="row_0"
        $html = preg_replace('/\sid="(\d+)"/', ' id="row_$1"', $html);
        $html = preg_replace("/\sid='(\d+)'/", " id='row_\$1'", $html);

        // Pattern 2: Fix tr# in JavaScript selectors: 'tr#0' -> 'tr#row_0'
        $html = preg_replace('/(tr#)(\d+)/', '$1row_$2', $html);

        // Pattern 3: Fix ID selectors in quotes: '#0' -> '#row_0'
        $html = preg_replace('/(["\'])#(\d+)(["\'])/', '$1#row_$2$3', $html);

        // Pattern 4: Fix template variables: <%- _id %> usage in selectors
        // Replace patterns like 'tr#<%- _id %>' with 'tr#row_<%- _id %>'
        $html = preg_replace('/(tr#)(<%[-=]\s*_id\s*%>)/', '$1row_$2', $html);
        $html = preg_replace('/(["\'])#(<%[-=]\s*_id\s*%>)/', '$1#row_$2', $html);

        // Pattern 5: Fix any remaining #digit patterns in JavaScript context
        $html = preg_replace('/(["\'\(])#(\d+)(["\'\)])/', '$1#row_$2$3', $html);

        // Add pagination controls and JavaScript
        $elementId = $this->getElement()->getId();
        $rowsPerPage = self::ROWS_PER_PAGE;

        $paginationHtml = <<<HTML
<div id="{$elementId}_search_box" class="pincode-search-box" style="margin-bottom: 10px; margin-top: -5px;">
    <input type="text" id="{$elementId}_search" placeholder="Search by country, state, code, PIN range..." class="admin__control-text" style="width: 100%; max-width: 400px; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 13px;" />
</div>
<div id="{$elementId}_pagination_controls" class="pincode-pagination-controls" style="margin-top: 15px; display: none;">
    <div class="pagination-info" style="float: left; line-height: 32px;">
        <span id="{$elementId}_page_info"></span>
    </div>
    <div class="pagination-buttons" style="float: right;">
        <button type="button" class="action-default" id="{$elementId}_prev_page" style="margin-right: 5px;">
            <span>Previous</span>
        </button>
        <span id="{$elementId}_page_numbers" style="margin: 0 10px;"></span>
        <button type="button" class="action-default" id="{$elementId}_next_page">
            <span>Next</span>
        </button>
    </div>
    <div style="clear: both;"></div>
</div>

<script>
require(['jquery', 'domReady!'], function($) {
    var elementId = '{$elementId}';
    var rowsPerPage = {$rowsPerPage};
    var currentPage = 1;
    var totalPages = 1;
    var searchTerm = '';

    function getVisibleRows() {
        var table = $('#' + elementId);
        var tbody = table.find('tbody');
        var rows = tbody.find('tr');
        if (!searchTerm) return rows;
        return rows.filter(function() {
            var text = $(this).text().toLowerCase();
            return text.indexOf(searchTerm) !== -1;
        });
    }

    function initPagination() {
        var allRows = $('#' + elementId).find('tbody tr');
        var rows = getVisibleRows();
        var totalRows = rows.length;

        // Hide non-matching rows when searching
        if (searchTerm) {
            allRows.hide();
            rows.show();
            $('#' + elementId + '_pagination_controls').hide();
            $('#' + elementId + '_page_info').text(totalRows + ' result(s) found');
            $('#' + elementId + '_pagination_controls').show();
            $('#' + elementId + '_prev_page').hide();
            $('#' + elementId + '_next_page').hide();
            $('#' + elementId + '_page_numbers').empty();
            return;
        }

        // Normal pagination
        $('#' + elementId + '_prev_page').show();
        $('#' + elementId + '_next_page').show();
        if (totalRows <= rowsPerPage) {
            $('#' + elementId + '_pagination_controls').hide();
            rows.show();
            return;
        }

        $('#' + elementId + '_pagination_controls').show();
        totalPages = Math.ceil(totalRows / rowsPerPage);
        if (currentPage > totalPages) currentPage = totalPages;

        showPage(currentPage);
        updatePaginationControls();
    }

    function showPage(page) {
        var rows = getVisibleRows();
        currentPage = page;
        rows.hide();
        var start = (page - 1) * rowsPerPage;
        var end = start + rowsPerPage;
        rows.slice(start, end).show();
        updatePaginationControls();
    }

    function updatePaginationControls() {
        var totalRows = getVisibleRows().length;
        var start = (currentPage - 1) * rowsPerPage + 1;
        var end = Math.min(currentPage * rowsPerPage, totalRows);

        $('#' + elementId + '_page_info').text('Showing ' + start + ' to ' + end + ' of ' + totalRows + ' entries');

        $('#' + elementId + '_prev_page').prop('disabled', currentPage === 1).toggleClass('disabled', currentPage === 1);
        $('#' + elementId + '_next_page').prop('disabled', currentPage === totalPages).toggleClass('disabled', currentPage === totalPages);

        var pageNumbersHtml = '';
        var maxVisiblePages = 5;
        var startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        var endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
        if (endPage - startPage < maxVisiblePages - 1) startPage = Math.max(1, endPage - maxVisiblePages + 1);

        for (var i = startPage; i <= endPage; i++) {
            if (i === currentPage) {
                pageNumbersHtml += '<span class="page-number active" style="margin: 0 3px; padding: 5px 10px; background: #0D9488; color: white; border-radius: 3px; cursor: pointer;">' + i + '</span>';
            } else {
                pageNumbersHtml += '<span class="page-number" data-page="' + i + '" style="margin: 0 3px; padding: 5px 10px; background: #f0f0f0; border-radius: 3px; cursor: pointer;">' + i + '</span>';
            }
        }
        $('#' + elementId + '_page_numbers').html(pageNumbersHtml);
    }

    // Search handler
    var searchTimer;
    $('#' + elementId + '_search').on('input', function() {
        clearTimeout(searchTimer);
        var input = $(this);
        searchTimer = setTimeout(function() {
            searchTerm = input.val().toLowerCase().trim();
            currentPage = 1;
            initPagination();
        }, 300);
    });

    // Pagination event handlers
    $('#' + elementId + '_prev_page').on('click', function() { if (currentPage > 1) showPage(currentPage - 1); });
    $('#' + elementId + '_next_page').on('click', function() { if (currentPage < totalPages) showPage(currentPage + 1); });
    $(document).on('click', '#' + elementId + '_page_numbers .page-number', function() {
        var page = parseInt($(this).data('page'));
        if (page && page !== currentPage) showPage(page);
    });

    // MutationObserver for row changes
    var observer = new MutationObserver(function() { searchTerm = ''; $('#' + elementId + '_search').val(''); initPagination(); });
    var table = document.getElementById(elementId);
    if (table) {
        var tbody = table.querySelector('tbody');
        if (tbody) observer.observe(tbody, { childList: true, subtree: false });
    }

    setTimeout(function() { initPagination(); }, 500);
});
</script>

<style>
.pincode-search-box { margin-bottom: 10px; }
.pincode-pagination-controls { padding: 10px 15px; background: #f9f9f9; border: 1px solid #ddd; border-top: none; }
.pincode-pagination-controls .pagination-info { font-size: 13px; color: #333; }
.pincode-pagination-controls button.disabled { opacity: 0.5; cursor: not-allowed; }
.pincode-pagination-controls .page-number:hover:not(.active) { background: #e0e0e0 !important; }
</style>
HTML;

        return $html . $paginationHtml;
    }
}
