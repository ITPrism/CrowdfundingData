<?php
/**
 * @package      CrowdfundingData
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Joomla\Utilities\ArrayHelper;

// no direct access
defined('_JEXEC') or die;

/**
 * Get a list of items
 */
class CrowdfundingDataModelRecord extends JModelForm
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type    The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'Record', $prefix = 'CrowdfundingDataTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param   array   $data     An optional array of data for the form to interrogate.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  JForm|bool   A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->option . '.record', 'record', array('control' => 'jform', 'load_data' => $loadData));
        if (!$form) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @throws \Exception
     *
     * @return  mixed   The data for the form.
     * @since   1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState($this->option . '.edit.record.data', array());
        if (!$data) {
            $data = $this->getItem();
        }

        return $data;
    }

    public function getItem()
    {
        return array();
    }


    /**
     * Save data in the database
     *
     * @param array $data   The data of item
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     *
     * @return    int      Item ID
     */
    public function save($data)
    {
        $name      = ArrayHelper::getValue($data, 'name');
        $email     = ArrayHelper::getValue($data, 'email');
        $address   = ArrayHelper::getValue($data, 'address');
        $countryId = ArrayHelper::getValue($data, 'country_id');
        $projectId = ArrayHelper::getValue($data, 'project_id');
        $userId    = ArrayHelper::getValue($data, 'user_id');
        $sessionId = ArrayHelper::getValue($data, 'session_id');

        if (!$address) {
            $address = null;
        }

        $db     = $this->getDbo();

        // Check if record already exists.
        $query  = $db->getQuery(true);
        $query
            ->select('a.id')
            ->from($db->quoteName('#__cfdata_records', 'a'))
            ->where('a.project_id = ' . $db->quote($projectId));

        if ($userId > 0) {
            $query->where('a.user_id = ' . (int)$userId);
        } else {
            $query->where('a.email = ' . $db->quote($email));
        }

        $query->where('a.transaction_id = 0');

        $db->setQuery($query, 0, 1);
        $recordId = (int)$db->loadResult();

        // Load a record from the database
        $row = $this->getTable();

        // Load the existing record.
        if ($recordId > 0) {
            $row->load($recordId);
        }

        $row->set('name', $name);
        $row->set('email', $email);
        $row->set('address', $address);
        $row->set('country_id', $countryId);
        $row->set('project_id', $projectId);
        $row->set('user_id', $userId);
        $row->set('session_id', $sessionId);

        $row->store(true);

        return $row->get('id');
    }
}
