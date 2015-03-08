<?php
/**
 * @package     UltimateUser for Joomla!
 * @author      Stéphane Bouey <stephane.bouey@faboba.com> - http://www.faboba.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright   Copyright (C) 2012-2013. All rights reserved.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_ROOT .'/components/com_uu/libraries/parameter.php');
class CustomField
{
    protected $fieldId = null;
    protected $params = null;
    protected $options = null;


    public function __construct($fieldId=null){
        if ($fieldId!==null) {
            $this->load($fieldId);

            if ($this->hasOptions()){
                $this->options = $this->loadOptions();
            }
        }
    }

    public function load($fieldId){
        if ($fieldId!==null) {
            $this->fieldId = $fieldId;
            $db		= JFactory::getDBO();
            $query	= 'SELECT * FROM '.$db->quoteName('#__uu_fields')
                . ' WHERE '.$db->quoteName('id').'='.$db->quote($this->fieldId);
            $db->setQuery($query);
            if($db->getErrorNum()) {
                JError::raiseError( 500, $db->stderr());
            }
            $field	= $db->loadObject();
            $this->params = new UuParameter($field->params);
        }
    }

    public function validLength( $value )
    {
        if(isset($this->params)){
            $max_char = $this->params->get('max_char');
            $min_char = $this->params->get('min_char');
            $len = strlen($value);
            if($min_char && $len < $min_char ){
                return false;
            }
            if($max_char && $len > $max_char ){
                return false;
            }
        }
        return true;
    }
    public function getStyle(){
        if(isset($this->params)){
            $style = $this->params->get('style');
            return $style;
        }
        return '';
    }

    public function loadOptions() {
        $db		= JFactory::getDBO();
        $query	= 'SELECT * FROM '.$db->quoteName('#__uu_fields_values')
            . ' WHERE '.$db->quoteName('id_field').'='.$db->quote($this->fieldId);
        $db->setQuery($query);
        if($db->getErrorNum()) {
            JError::raiseError( 500, $db->stderr());
        }
        return $db->loadObjectList();
    }


}
