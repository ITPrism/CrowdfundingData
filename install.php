<?php
/**
 * @package      CrowdfundingData
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Script file of the component
 */
class pkg_crowdfundingdataInstallerScript
{
    /**
     * Method to install the component.
     *
     * @param $parent
     *
     * @return void
     */
    public function install($parent)
    {
    }

    /**
     * Method to uninstall the component.
     *
     * @param $parent
     *
     * @return void
     */
    public function uninstall($parent)
    {
    }

    /**
     * Method to update the component.
     *
     * @param $parent
     *
     * @return void
     */
    public function update($parent)
    {
    }

    /**
     * Method to run before an install/update/uninstall method
     *
     * @param $type
     * @param $parent
     *
     * @return void
     */
    public function preflight($type, $parent)
    {
    }

    /**
     * Method to run after an install/update/uninstall method
     *
     * @param $type
     * @param $parent
     *
     * @return void
     */
    public function postflight($type, $parent)
    {
        if (!defined('CROWDFUNDINGDATA_PATH_COMPONENT_ADMINISTRATOR')) {
            define('CROWDFUNDINGDATA_PATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_crowdfundingdata');
        }

        jimport('Prism.init');
        jimport('Crowdfundingdata.init');

        // Register Component helpers
        JLoader::register('CrowdfundingDataInstallHelper', CROWDFUNDINGDATA_PATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'install.php');

        // Start table with the information
        CrowdfundingDataInstallHelper::startTable();

        // Requirements
        CrowdfundingDataInstallHelper::addRowHeading(JText::_('COM_CROWDFUNDINGDATA_MINIMUM_REQUIREMENTS'));

        // Display result about verification for GD library
        $title = JText::_('COM_CROWDFUNDINGDATA_GD_LIBRARY');
        $info  = '';
        if (!extension_loaded('gd') and !function_exists('gd_info')) {
            $result = array('type' => 'important', 'text' => JText::_('COM_CROWDFUNDINGDATA_WARNING'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JON'));
        }
        CrowdfundingDataInstallHelper::addRow($title, $result, $info);

        // Display result about verification for cURL library
        $title = JText::_('COM_CROWDFUNDINGDATA_CURL_LIBRARY');
        $info  = '';
        if (!extension_loaded('curl')) {
            $info   = JText::_('COM_CROWDFUNDINGDATA_CURL_INFO');
            $result = array('type' => 'important', 'text' => JText::_('JOFF'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JON'));
        }
        CrowdfundingDataInstallHelper::addRow($title, $result, $info);

        // Display result about verification Magic Quotes
        $title = JText::_('COM_CROWDFUNDINGDATA_MAGIC_QUOTES');
        $info  = '';
        if (get_magic_quotes_gpc()) {
            $info   = JText::_('COM_CROWDFUNDINGDATA_MAGIC_QUOTES_INFO');
            $result = array('type' => 'important', 'text' => JText::_('JON'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JOFF'));
        }
        CrowdfundingDataInstallHelper::addRow($title, $result, $info);

        // Display result about verification FileInfo
        $title = JText::_('COM_CROWDFUNDINGDATA_FILEINFO');
        $info  = '';
        if (!function_exists('finfo_open')) {
            $info   = JText::_('COM_CROWDFUNDINGDATA_FILEINFO_INFO');
            $result = array('type' => 'important', 'text' => JText::_('JOFF'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JON'));
        }
        CrowdfundingDataInstallHelper::addRow($title, $result, $info);

        // Display result about verification PHP Intl
        $title = JText::_('COM_CROWDFUNDINGDATA_PHPINTL');
        $info  = '';
        if (!extension_loaded('intl')) {
            $info   = JText::_('COM_CROWDFUNDINGDATA_PHPINTL_INFO');
            $result = array('type' => 'important', 'text' => JText::_('JOFF'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JON'));
        }
        CrowdfundingDataInstallHelper::addRow($title, $result, $info);
        
        // Display result about verification PHP version.
        $title = JText::_('COM_CROWDFUNDINGDATA_PHP_VERSION');
        $info  = '';
        if (version_compare(PHP_VERSION, '5.5.0') < 0) {
            $result = array('type' => 'important', 'text' => JText::_('COM_CROWDFUNDINGDATA_WARNING'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JYES'));
        }
        CrowdfundingDataInstallHelper::addRow($title, $result, $info);

        // Display result about MySQL Version.
        $title = JText::_('COM_CROWDFUNDINGDATA_MYSQL_VERSION');
        $info  = '';
        $dbVersion = JFactory::getDbo()->getVersion();
        if (version_compare($dbVersion, '5.5.3', '<')) {
            $result = array('type' => 'important', 'text' => JText::_('COM_CROWDFUNDINGDATA_WARNING'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JYES'));
        }
        CrowdfundingDataInstallHelper::addRow($title, $result, $info);

        // Display result about verification of installed Prism Library
        $info  = '';
        if (!class_exists('Prism\\Version')) {
            $title  = JText::_('COM_CROWDFUNDINGDATA_PRISM_LIBRARY');
            $info   = JText::_('COM_CROWDFUNDINGDATA_PRISM_LIBRARY_DOWNLOAD');
            $result = array('type' => 'important', 'text' => JText::_('JNO'));
        } else {
            $prismVersion   = new Prism\Version();
            $text           = JText::sprintf('COM_CROWDFUNDINGDATA_CURRENT_V_S', $prismVersion->getShortVersion());

            if (class_exists('Crowdfundingdata\\Version')) {
                $componentVersion = new Crowdfundingdata\Version();
                $title            = JText::sprintf('COM_CROWDFUNDINGDATA_PRISM_LIBRARY_S', $componentVersion->requiredPrismVersion);

                if (version_compare($prismVersion->getShortVersion(), $componentVersion->requiredPrismVersion, '<')) {
                    $info   = JText::_('COM_CROWDFUNDINGDATA_PRISM_LIBRARY_DOWNLOAD');
                    $result = array('type' => 'warning', 'text' => $text);
                }

            } else {
                $title  = JText::_('COM_CROWDFUNDINGDATA_PRISM_LIBRARY');
                $result = array('type' => 'success', 'text' => $text);
            }
        }
        CrowdfundingDataInstallHelper::addRow($title, $result, $info);

        // Installed extensions

        CrowdfundingDataInstallHelper::addRowHeading(JText::_('COM_CROWDFUNDINGDATA_INSTALLED_EXTENSIONS'));

        // Crowdfunding Library
        $result = array('type' => 'success', 'text' => JText::_('COM_CROWDFUNDINGDATA_INSTALLED'));
        CrowdfundingDataInstallHelper::addRow(JText::_('COM_CROWDFUNDINGDATA_CROWDFUNDINGDATA_LIBRARY'), $result, JText::_('COM_CROWDFUNDINGDATA_LIBRARY'));

        // Plugins

        // Crowdfunding Payment - Data
        $result = array('type' => 'success', 'text' => JText::_('COM_CROWDFUNDINGDATA_INSTALLED'));
        CrowdfundingDataInstallHelper::addRow(JText::_('COM_CROWDFUNDINGDATA_CROWDFUNDINGPAYMENT_DATA'), $result, JText::_('COM_CROWDFUNDINGDATA_PLUGIN'));

        // End table
        CrowdfundingDataInstallHelper::endTable();

        echo JText::sprintf('COM_CROWDFUNDINGDATA_MESSAGE_REVIEW_SAVE_SETTINGS', JRoute::_('index.php?option=com_crowdfundingdata'));

        if (!class_exists('Prism\\Version')) {
            echo JText::_('COM_CROWDFUNDINGDATA_MESSAGE_INSTALL_PRISM_LIBRARY');
        } else {
            if (class_exists('Crowdfundingdata\\Version')) {
                $prismVersion     = new Prism\Version();
                $componentVersion = new Crowdfundingdata\Version();
                if (version_compare($prismVersion->getShortVersion(), $componentVersion->requiredPrismVersion, '<')) {
                    echo JText::_('COM_CROWDFUNDINGDATA_MESSAGE_INSTALL_PRISM_LIBRARY');
                }
            }
        }
    }
}
