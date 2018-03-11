<?php
/**
 * @package      CrowdfundingData
 * @subpackage   Plugins
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
/**
 * @var stdClass $item
 */
?>
<div class="alert alert-warning">
    <span class="fa fa-exclamation-triangle"></span>
    <?php echo JText::_('PLG_CROWDFUNDINGPAYMENT_ERROR_SESSION_EXPIRED'); ?>
</div>
<a href="<?php echo JRoute::_(CrowdfundingHelperRoute::getBackingRoute($item->slug, $item->catslug)); ?>" class="btn btn-primary" >
    <span class="fa fa-chevron-left"></span>
    <?php echo JText::_('PLG_CROWDFUNDINGPAYMENT_BUTTON_STEP_ONE'); ?>
</a>