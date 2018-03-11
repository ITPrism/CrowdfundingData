<?php
/**
* @package      CrowdfundingData
* @subpackage   Library
* @author       Todor Iliev
* @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
* @license      GNU General Public License version 3 or later; see LICENSE.txt
*/

defined('JPATH_PLATFORM') or die;

if (!defined('CROWDFUNDINGDATA_PATH_COMPONENT_ADMINISTRATOR')) {
    define('CROWDFUNDINGDATA_PATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/com_crowdfundingdata');
}

if (!defined('CROWDFUNDINGDATA_PATH_COMPONENT_SITE')) {
    define('CROWDFUNDINGDATA_PATH_COMPONENT_SITE', JPATH_SITE . '/components/com_crowdfundingdata');
}

if (!defined('CROWDFUNDINGDATA_PATH_LIBRARY')) {
    define('CROWDFUNDINGDATA_PATH_LIBRARY', JPATH_LIBRARIES . '/Crowdfundingdata');
}

JLoader::registerNamespace('Crowdfundingdata', JPATH_LIBRARIES);

// Register helpers
JLoader::register('CrowdfundingDataHelper', CROWDFUNDINGDATA_PATH_COMPONENT_ADMINISTRATOR . '/helpers/crowdfundingdata.php');

// Register HTML helpers
JHtml::addIncludePath(CROWDFUNDINGDATA_PATH_COMPONENT_SITE . '/helpers/html');
JLoader::register('JHtmlString', JPATH_LIBRARIES . '/joomla/html/html/string.php');
