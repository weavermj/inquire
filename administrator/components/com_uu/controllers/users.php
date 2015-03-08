<?php
/**
 * @package     UltimateUser for Joomla!
 * @author      Stéphane Bouey <stephane.bouey@faboba.com> - http://www.faboba.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright   Copyright (C) 2012-2013. All rights reserved.
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Users list controller class.
 */
class UuControllerUsers extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'user', $prefix = 'UuModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
    
    
	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function saveOrderAjax()
	{
		// Get the input
		$input = JFactory::getApplication()->input;
		$pks = $input->post->get('cid', array(), 'array');
		$order = $input->post->get('order', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}

    function sync(){
        // Get the model
        $model = $this->getModel('users');
        $model->SyncUsers();
        $this->setRedirect(JRoute::_('index.php?option=com_uu&view=users'));
    }

    function export()
    {
        $input = JFactory::getApplication()->input;
        $format = $input->getString( 'format', 'csv');
        /**
         * TODO: Currently it only supports CSV export. In the future we may want to support other types as well
         **/
        switch( $format )
        {
            case 'csv':
            default:
                $this->exportCSV();
                break;
        }

    }

    private function exportCSV()
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-disposition: attachment; filename="ultimate_user_list.csv"');

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        //get all fieldcode
        $query->select('f.fieldcode');
        $query->from('#__uu_fields AS f');
        $query->where('core = 0');
        $query->where('type != '.$db->quote('group'));
        $db->setQuery($query);
        $fields = $db->loadObjectList();

        //get all users
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('u.*,uu.*');
        $query->from('#__users AS u');
        $query->leftJoin('#__uu_users as uu ON uu.user_id = u.id ');
        $query->order('u.id ASC');

        $db->setQuery($query);
        $list = $db->loadObjectList();

        //print header
        echo 'id,name,username,email,register_date,lastvisite_date';
        foreach ($fields as $field){
            echo ','.$field->fieldcode;
        }
        echo "\r\n";

        //print data
        foreach ($list as $item )
        {
            $user = JFactory::getUser($item->id);

            echo $user->id . ',' . $user->name . ',' . $user->username . ',' . $user->email . ',' . $user->registerDate . ',' . $user->lastvisitDate;
            foreach ($fields as $field) {
                $fieldcode = $field->fieldcode;
                echo ','.$item->$fieldcode;
            }
            echo "\r\n";
        }
        exit;
    }
    
}