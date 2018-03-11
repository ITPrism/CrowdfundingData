<?php
/**
 * @package      CrowdfundingData
 * @subpackage   Plugins
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Load the script that initializes the select element with banks.
$doc->addScript('plugins/crowdfundingpayment/data/js/script.js?v=' . rawurlencode($this->version));
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?php echo JText::_('PLG_CROWDFUNDINGPAYMENT_DATA_INFORMATION_ABOUT_YOU');?>
            </div>
            <div class="panel-body">
                <form action="<?php echo JRoute::_('index.php?option=com_crowdfundingdata');?>" method="post" id="js-cfdata-form">
                    <div class="form-group">
                        <?php echo $this->form->getLabel('name'); ?>
                        <?php echo $this->form->getInput('name'); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->form->getLabel('email'); ?>
                        <?php echo $this->form->getInput('email'); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->form->getLabel('address'); ?>
                        <?php echo $this->form->getInput('address'); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->form->getLabel('country_id'); ?>
                        <?php echo $this->form->getInput('country_id'); ?>
                    </div>

                    <div class="alert alert-warning" id="js-cfdata-alert" style="display: none;">
                        <span class="fa fa-warning"></span>
                        <span id="js-cfdata-alert-text"></span>
                    </div>

                    <button type="submit" class="btn btn-primary" id="js-cfdata-btn-submit">
                        <span class="fa fa-check-circle"></span>
                        <?php echo JText::_('PLG_CROWDFUNDINGPAYMENT_DATA_SUBMIT'); ?>
                    </button>
                    <span class="fa fa-spinner fa-spin" id="js-cfdata-ajax-loading" style="display: none;" aria-hidden="true"></span>

                    <a href="<?php echo $nextStepParams->link; ?>" class="btn btn-success" id="js-continue-btn" role="button" style="display: none;">
                        <span class="fa fa-chevron-right"></span>
                        <?php echo JText::_('PLG_CROWDFUNDINGPAYMENT_DATA_CONTINUE_NEXT_STEP'); ?>
                    </a>

                    <?php echo $this->form->getInput('project_id'); ?>

                    <input type="hidden" name="task" value="record.save" />
                    <input type="hidden" name="format" value="raw" />
                    <?php echo JHtml::_('form.token'); ?>
                </form>
            </div>
        </div>
    </div>
</div>