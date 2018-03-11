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
 * @var $currencies \Crowdfunding\Currency\Currencies
 * @var $currency_  \Crowdfunding\Currency\Currency
 * @var $currency   \Crowdfunding\Currency\Currency
 */
?>
<?php foreach ($this->items as $i => $item) {
    $amount = '---';
    if ($item->txn_currency) {
        $currencies = $this->currencies->toArray();

        $currency = new Prism\Money\Currency();
        foreach ($currencies as $currency_) {
            if (strcmp($item->txn_currency, $currency_->getCode()) === 0) {
                $currency = $currency_;
                break;
            }
        }

        $amount = $this->moneyFormatter->formatCurrency(new Prism\Money\Money($item->txn_amount, $currency));
    }?>
    <tr class="row<?php echo $i % 2; ?>">
        <td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td class="has-context">
            <a href="<?php echo JRoute::_('index.php?option=com_crowdfundingdata&view=record&layout=edit&id=' . $item->id); ?>"><?php echo $this->escape($item->name); ?></a>
            <a href="<?php echo JRoute::_('index.php?option=com_crowdfundingdata&view=record&id=' . $item->id); ?>" class="btn btn-mini">
                <i class="icon icon-eye"></i>
            </a>
            <?php if (!empty($item->user_id)) { ?>
            <a href="<?php echo JRoute::_('index.php?option=com_crowdfunding&view=users&filter_search=id:' . $item->user_id); ?>" class="btn btn-mini hasTooltip" title="<?php echo JText::_('COM_CROWDFUNDINGDATA_ADDITIONAL_INFORMATION'); ?>">
                <i class="icon-user"></i>
            </a>
            <?php } ?>

            <?php if (!empty($item->email)) { ?>
            <div class="small hidden-phone">
                <?php echo JText::sprintf('COM_CROWDFUNDINGDATA_EMAIL_S', $item->email); ?>
            </div>
            <?php } ?>
        </td>
        <td>
            <a href="<?php echo JRoute::_('index.php?option=com_crowdfunding&view=projects&filter_search=id:' . $item->project_id); ?>">
                <?php echo $this->escape($item->project); ?>
            </a>
        </td>
        <td>
            <?php echo $amount; ?>
        </td>
        <td class="nowrap hidden-phone">
            <?php echo $item->country; ?>
        </td>
        <td class="hidden-phone">
            <?php echo JHtml::_('crowdfunding.date', $item->record_date, JText::_('DATE_FORMAT_LC2')); ?>
        </td>
        <td class="hidden-phone">
            <a href="<?php echo JRoute::_('index.php?option=com_crowdfunding&view=transactions&filter_search=id:' . $item->transaction_id); ?>">
                <?php echo $this->escape($item->txn_id); ?>
            </a>
        </td>
        <td class="nowrap hidden-phone">
            <?php echo $this->escape($item->txn_status); ?>
        </td>
        <td class="center hidden-phone">
            <?php echo $item->id;?>
        </td>
    </tr>
<?php }?>
