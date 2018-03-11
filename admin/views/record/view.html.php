<?php
/**
 * @package      CrowdfundingData
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Crowdfunding\Container\MoneyHelper;

// no direct access
defined('_JEXEC') or die;

class CrowdfundingDataViewRecord extends JViewLegacy
{

    /**
     * @var JDocumentHtml
     */
    public $document;

    protected $state;
    protected $item;
    protected $form;

    protected $documentTitle;
    protected $option;

    protected $currency;
    protected $moneyFormatter;
    protected $layout;

    public function display($tpl = null)
    {
        $this->option = JFactory::getApplication()->input->get('option');
        
        $this->state = $this->get('State');
        $this->item  = $this->get('Item');
        $this->form  = $this->get('Form');

        $this->layout = $this->getLayout();

        if (strcmp($this->layout, 'edit') !== 0) {
            $container              = Prism\Container::getContainer();

            $crowdfundingParams     = JComponentHelper::getParams('com_crowdfunding');
            $this->currency         = MoneyHelper::getCurrency($container, $crowdfundingParams);
            $this->moneyFormatter   = MoneyHelper::getMoneyFormatter($container, $crowdfundingParams);
        }

        // Prepare actions, behaviors, scripts and document
        $this->addToolbar();
        $this->setDocument();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);

        if (strcmp($this->layout, 'edit') !== 0) {
            $this->documentTitle = JText::_('COM_CROWDFUNDINGDATA_VIEW_RECORD');
        } else {
            $this->documentTitle = JText::_('COM_CROWDFUNDINGDATA_EDIT_RECORD');

            JToolbarHelper::apply('record.apply');
            JToolbarHelper::save('record.save');
        }

        JToolbarHelper::cancel('record.cancel', 'JTOOLBAR_CANCEL');

        JToolbarHelper::title($this->documentTitle);
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle($this->documentTitle);

        // Scripts
        JHtml::_('behavior.formvalidation');
        JHtml::_('behavior.tooltip');

        JHtml::_('formbehavior.chosen', 'select');

        $this->document->addScript('../media/' . $this->option . '/js/admin/' . JString::strtolower($this->getName()) . '.js');
    }
}
