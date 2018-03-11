<?php
/**
 * @package      CrowdfundingData
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Data controller class.
 *
 * @package        CrowdfundingData
 * @subpackage     Component
 * @since          1.6
 */
class CrowdfundingDataControllerRecord extends JControllerForm
{
    public function save($key = null, $urlVar = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $response = new Prism\Response\Json();

        $data   = $this->input->post->get('jform', array(), 'array');
        $itemId = Joomla\Utilities\ArrayHelper::getValue($data, 'project_id', 0, 'int');

        $container = Prism\Container::getContainer();

        $containerHelper = new Crowdfunding\Container\Helper();
        $project         = $containerHelper->fetchProject($container, $itemId);

        // Prepare return URL to discover page.
        $returnUrl = JRoute::_(CrowdfundingHelperRoute::getDiscoverRoute());

        if (!$project->getId()) {
            $response
                ->setTitle(JText::_('COM_CROWDFUNDINGDATA_FAIL'))
                ->setText(JText::_('COM_CROWDFUNDINGDATA_INVALID_ITEM'))
                ->setRedirectUrl($returnUrl)
                ->failure();

            echo $response;
            $app->close();
        }

        // Prepare return URL to backing page.
        $returnUrl = JRoute::_(CrowdfundingHelperRoute::getBackingRoute($project->getSlug(), $project->getCatSlug()));

        $model = $this->getModel();
        /** @var $model CrowdfundingDataModelRecord */

        $form = $model->getForm($data, false);
        /** @var $form JForm */

        if (!$form) {
            throw new Exception(JText::_('COM_CROWDFUNDINGDATA_ERROR_FORM_CANNOT_BE_LOADED'));
        }

        // Validate the form data
        $validData = $model->validate($form, $data);

        // Check for errors
        if (!$validData) {
            $errors_ = $form->getErrors();
            $errors  = array();
            /** @var $error RuntimeException */

            foreach ($errors_ as $error) {
                $errors[] = $error->getMessage();
            }

            // Send response to the browser
            $response
                ->setTitle(JText::_('COM_CROWDFUNDINGDATA_FAIL'))
                ->setText(implode("\n", $errors))
                ->setRedirectUrl($returnUrl)
                ->failure();

            echo $response;
            $app->close();
        }

        // Get the payment session object and session ID.
        $paymentSessionContext    = Crowdfunding\Constants::PAYMENT_SESSION_CONTEXT . $project->getId();
        $paymentSessionLocal      = $app->getUserState($paymentSessionContext);

        try {
            if (is_array($validData)) {
                $validData['session_id'] = $paymentSessionLocal->session_id;
                $validData['user_id']    = (int)JFactory::getUser()->get('id');
            }

            $model->save($validData);

        } catch (Exception $e) {
            $response
                ->setTitle(JText::_('COM_CROWDFUNDINGDATA_FAIL'))
                ->setText(JText::_('COM_CROWDFUNDINGDATA_ERROR_SYSTEM'))
                ->setRedirectUrl($returnUrl)
                ->failure();

            echo $response;
            $app->close();
        }

        $response
            ->setTitle(JText::_('COM_CROWDFUNDINGDATA_SUCCESS'))
            ->setText(JText::_('COM_CROWDFUNDINGDATA_DATA_SAVED_SUCCESSFULLY'))
            ->success();

        echo $response;
        $app->close();
    }
}
