<?php
/**
 * @package      CrowdfundingData
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Crowdfunding\Container\Helper\Money as MoneyHelper;
use Crowdfunding\Currency\Gateway\JoomlaGateway as JoomlaCurrencyDatabaseGateway;

// no direct access
defined('_JEXEC') or die;

class CrowdfundingDataViewRecords extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $state;

    protected $items;
    protected $pagination;

    protected $option;
    protected $listOrder;
    protected $listDirn;
    protected $saveOrder;
    protected $sortFields;

    protected $saveOrderingUrl;
    
    protected $currencies;
    protected $currency;
    protected $moneyFormatter;

    protected $sidebar;
    
    public function display($tpl = null)
    {
        $this->option     = JFactory::getApplication()->input->get('option');

        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        // Get currencies
        $currencyCodes= array();
        foreach ($this->items as $item) {
            $currencyCodes[] = $item->txn_currency;
        }
        
        $currencyCodes = array_filter(array_unique($currencyCodes));
        if (count($currencyCodes) > 0) {
            $databaseRequest     = new \Prism\Database\Request\Request();
            $databaseRequest->addSpecificCondition('codes', new \Prism\Database\Request\Condition(['column' => 'code', 'value' => $currencyCodes]));
            
            $mapper              = new \Crowdfunding\Currency\Mapper(new JoomlaCurrencyDatabaseGateway(JFactory::getDbo()));
            $repository          = new Crowdfunding\Currency\Repository($mapper);
            $this->currencies    = $repository->fetchCollection($databaseRequest);
        }

        $container              = Prism\Container::getContainer();
        $language               = \JFactory::getLanguage();
        $locale                 = $language->getTag();
        
        $crowdfundingParams     = JComponentHelper::getParams('com_crowdfunding');

        $gateway                = new Crowdfunding\Currency\Gateway\JoomlaGateway(JFactory::getDbo());

        $moneyHelper            = new MoneyHelper($container);
        $this->moneyFormatter   = $moneyHelper->getFormatter($locale, (int)$crowdfundingParams->get('fraction_digits', 2));
        $this->currency         = $moneyHelper->getCurrency($crowdfundingParams->get('project_currency'), $gateway);

        // Add submenu
        CrowdfundingDataHelper::addSubmenu($this->getName());

        // Prepare sorting data
        $this->prepareSorting();

        // Prepare actions
        $this->addToolbar();
        $this->addSidebar();
        $this->setDocument();

        parent::display($tpl);
    }

    /**
     * Prepare sortable fields, sort values and filters.
     */
    protected function prepareSorting()
    {
        // Prepare filters
        $this->listOrder = $this->escape($this->state->get('list.ordering'));
        $this->listDirn  = $this->escape($this->state->get('list.direction'));
        $this->saveOrder = (strcmp($this->listOrder, 'a.ordering') === 0);

        if ($this->saveOrder) {
            $this->saveOrderingUrl = 'index.php?option=' . $this->option . '&task=' . $this->getName() . '.saveOrderAjax&format=raw';
            JHtml::_('sortablelist.sortable', $this->getName() . 'List', 'adminForm', strtolower($this->listDirn), $this->saveOrderingUrl);
        }

        $this->sortFields = array(
            'a.name'             => JText::_('COM_CROWDFUNDINGDATA_NAME'),
            'b.title'            => JText::_('COM_CROWDFUNDINGDATA_PROJECT'),
            'c.txn_id'           => JText::_('COM_CROWDFUNDINGDATA_TRANSACTION_ID'),
            'c.txn_amount'       => JText::_('COM_CROWDFUNDINGDATA_AMOUNT'),
            'd.name'             => JText::_('COM_CROWDFUNDINGDATA_COUNTRY'),
            'a.id'               => JText::_('JGRID_HEADING_ID')
        );
    }

    /**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar()
    {
        JHtmlSidebar::setAction('index.php?option=' . $this->option . '&view=' . $this->getName());

        $txnStatesOptions = array(
            JHtml::_('select.option', 1, JText::_('COM_CROWDFUNDINGDATA_COMPLETED')),
            JHtml::_('select.option', 0, JText::_('COM_CROWDFUNDINGDATA_NOT_COMPLETED')),
        );

        JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'),
            'filter_transaction_state',
            JHtml::_('select.options', $txnStatesOptions, 'value', 'text', $this->state->get('filter.transaction_state'), true)
        );

        $this->sidebar = JHtmlSidebar::render();
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        // Set toolbar items for the page
        JToolbarHelper::title(JText::_('COM_CROWDFUNDINGDATA_RECORDS_MANAGER'));
        JToolbarHelper::editList('record.edit');
        JToolbarHelper::custom('records.view', 'eye', '', JText::_('COM_CROWDFUNDINGDATA_VIEW'));
        JToolbarHelper::divider();
        JToolbarHelper::deleteList(JText::_('COM_CROWDFUNDINGDATA_DELETE_ITEMS_QUESTION'), 'records.delete');
        JToolbarHelper::divider();
        JToolbarHelper::custom('records.backToDashboard', 'dashboard', '', JText::_('COM_CROWDFUNDINGDATA_DASHBOARD'), false);
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_CROWDFUNDINGDATA_RECORDS_MANAGER'));

        // Scripts
        JHtml::_('behavior.multiselect');

        JHtml::_('bootstrap.tooltip');
        JHtml::_('formbehavior.chosen', 'select');

        JHtml::_('Prism.ui.joomlaList');
    }
}
