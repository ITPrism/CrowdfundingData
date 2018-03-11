<?php
/**
 * @package         CrowdfundingData
 * @subpackage      Plugins
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/licenses/gpl-3.0.en.html GNU/GPL
 */

use Joomla\Registry\Registry;

// no direct access
defined('_JEXEC') or die;

jimport('Crowdfunding.init');
jimport('Crowdfundingdata.init');

/**
 * Crowdfunding Data Plugin
 *
 * @package        CrowdfundingData
 * @subpackage     Plugins
 */
class plgCrowdfundingPaymentData extends Crowdfunding\Payment\Plugin
{
    protected $autoloadLanguage = true;

    /**
     * @var JApplicationSite
     */
    protected $app;

    protected $form;

    protected $name;
    protected $version    = '2.3';

    protected $itemId = 0;

    /**
     * @var Registry
     */
    public $params;

    /**
     * This method prepares a payment gateway - buttons, forms,...
     * That gateway will be displayed on the summary page as a payment option.
     *
     * @param string    $context This string gives information about that where it has been executed the trigger.
     * @param stdClass  $item    A project data.
     * @param stdClass  $nextStepParams Parameters of the next step (task, layout, link).
     * @param Registry  $params  The parameters of the component
     *
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     *
     * @return null|string
     */
    public function onPreparePaymentStep($context, $item, $nextStepParams, $params)
    {
        if (strcmp('com_crowdfunding.payment.step.data', $context) !== 0) {
            return null;
        }

        if ($this->app->isAdmin()) {
            return null;
        }

        $doc = JFactory::getDocument();
        /**  @var $doc JDocumentHtml */

        // Check document type
        $docType = $doc->getType();
        if (strcmp('html', $docType) !== 0) {
            return null;
        }

        // Load language file of the component.
        $language = JFactory::getLanguage();
        $language->load('com_crowdfundingdata', CROWDFUNDINGDATA_PATH_COMPONENT_SITE);

        // Get payment session.
        $paymentSessionContext    = Crowdfunding\Constants::PAYMENT_SESSION_CONTEXT . $item->id;
        $paymentSessionLocal      = $this->app->getUserState($paymentSessionContext);

        $paymentSessionRemote  = $this->getPaymentSession(array(
            'session_id' => $paymentSessionLocal->session_id
        ));

        if (empty($paymentSessionLocal->step1) or !$paymentSessionRemote->getId()) {
            $path = JPath::clean(JPluginHelper::getLayoutPath('crowdfundingpayment', 'data', 'error'));

            // Render error layout.
            ob_start();
            include $path;
            return ob_get_clean();
        }

        // Check for duplication of session ID.
        $this->prepareSessionId($paymentSessionLocal, $paymentSessionRemote);

        // Load the form.
        JForm::addFormPath(CROWDFUNDINGDATA_PATH_COMPONENT_SITE . '/models/forms');
        JForm::addFieldPath(CROWDFUNDINGDATA_PATH_COMPONENT_SITE . '/models/fields');

        $form = JForm::getInstance('com_crowdfundingdata.record', 'record', array('control' => 'jform', 'load_data' => false));

        // Prepare default name of a user.
        $user    = JFactory::getUser();
        if ($user->get('id')) {
            $form->setValue('name', null, $user->get('name'));
        }

        // Set item id to the form.
        $form->setValue('project_id', null, $item->id);

        $this->form = $form;

        // Load jQuery
        JHtml::_('jquery.framework');

        // Include Chosen
        if ($this->params->get('enable_chosen', 0)) {
            JHtml::_('formbehavior.chosen', '#jform_country_id');
        }

        // Get the path for the layout file
        $path = JPath::clean(JPluginHelper::getLayoutPath('crowdfundingpayment', 'data'));

        // Render the form.
        ob_start();
        include $path;
        $html = ob_get_clean();

        return $html;
    }

    /**
     * Check for duplication of session ID.
     * If the session ID exists, generate new one.
     *
     * @param JData $paymentSessionLocal
     * @param Crowdfunding\Payment\Session $paymentSessionRemote
     *
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    protected function prepareSessionId($paymentSessionLocal, $paymentSessionRemote)
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('COUNT(*)')
            ->from($db->quoteName('#__cfdata_records', 'a'))
            ->where('a.session_id = ' . $db->quote($paymentSessionLocal->session_id));

        $db->setQuery($query, 0, 1);
        $result = (int)$db->loadResult();

        // Remove the record that contains this session ID.
        if ($result > 0 and $paymentSessionRemote->getId()) {
            $query = $db->getQuery(true);
            $query
                ->delete($db->quoteName('#__cfdata_records'))
                ->where($db->quoteName('session_id') .'='. $db->quote($paymentSessionLocal->session_id));

            $db->setQuery($query);
            $db->execute();
        }
    }

    /**
     * This method is executed after complete payment.
     * It is used to be stored the transaction ID and the investor ID in data record.
     *
     * @param string $context
     * @param stdClass $paymentResult  Object that contains Transaction, Reward, Project and PaymentSession.
     * @param Joomla\Registry\Registry $params Component parameters
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function onAfterPaymentNotify($context, $paymentResult, $params)
    {
        if (!preg_match('/com_crowdfunding\.(notify|payments)/', $context)) {
            return;
        }

        if ($this->app->isAdmin()) {
            return;
        }

        // Check document type
        $docType = JFactory::getDocument()->getType();
        if (!in_array($docType, array('raw', 'html'), true)) {
            return;
        }

        $transaction = $paymentResult->transaction;
        /** @var Crowdfunding\Transaction\Transaction $transaction */

        $paymentSessionRemote = $paymentResult->paymentSession;
        /** @var Crowdfunding\Payment\Session $paymentSessionRemote */

        // Load record data from database.
        $keys = array(
            'session_id' => $paymentSessionRemote->getSessionId()
        );

        $record = new Crowdfundingdata\Record(JFactory::getDbo());
        $record->load($keys);

        if (!$record->getId()) {
            return;
        }

        // Set transaction ID.
        if ($transaction->getId() and (int)$transaction->getId() > 0) {
            $record->setTransactionId($transaction->getId());
        }

        // Set user ID.
        if ($transaction->getInvestorId() and (int)$transaction->getInvestorId() > 0) {
            $record->setUserId($transaction->getInvestorId());
        }

        $record->store();
    }

    /**
     * Return information about a step on the payment wizard.
     *
     * @param string $context
     * @param stdClass $item
     * @param string $layout
     *
     * @return null|array
     */
    public function onPrepareWizardSteps($context, $item, $layout)
    {
        if (strcmp('com_crowdfunding.payment.wizard', $context) !== 0) {
            return null;
        }

        if ($this->app->isAdmin()) {
            return null;
        }

        $doc = JFactory::getDocument();
        /**  @var $doc JDocumentHtml */

        // Check document type
        $docType = $doc->getType();
        if (strcmp('html', $docType) !== 0) {
            return null;
        }

        $showToRegistered = (bool)$this->params->get('show_registered', Prism\Constants::NO);
        $userId           = (int)JFactory::getUser()->get('id');
        if ($userId > 0 and !$showToRegistered) {
            return null;
        }

        // Create an object that will contain the data during the payment process.
        $paymentSessionContext = Crowdfunding\Constants::PAYMENT_SESSION_CONTEXT.$item->id;
        $paymentSessionLocal   = $this->app->getUserState($paymentSessionContext);

        $isActive = true;
        $step1    = (bool)$paymentSessionLocal->step1;
        if (!$step1 or strcmp('share', $layout) === 0) {
            $isActive = false;
        }

        return array(
            'title'   => JText::_('PLG_CROWDFUNDINGPAYMENT_DATA_STEP_TITLE'),
            'context' => 'data',
            'show_to_registered' => $showToRegistered,
            'is_active' => $isActive
        );
    }
}
