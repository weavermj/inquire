<?php
/**
 * @copyright	Copyright (C) 2010 Michael Richey. All rights reserved.
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
// jimport('joomla.form.formfield');
// jimport('joomla.version');

class JFormFieldBlacklist extends JFormField
{
	protected $type = 'Blacklist';
	protected $app;
	protected $db;
	protected $formfields;
	protected function getLabel() {
            return '';
        }
	protected function getInput()
	{
            $return=array();
	    if(function_exists('gmp_pow')) {
	      $return='<h3 style="float:left;clear:left;">'.JText::_('PLG_USER_DOMAINRESTRICTION_IPV46').'</h3>';
	    } else {
	      $return='<h3 style="float:left;clear:left;">'.JText::_('PLG_USER_DOMAINRESTRICTION_IPV4').'</h3>';
	    }
            return $return;
	}
        private function _getField($name) {
//             $fields = $this->form->getFieldset();
//             foreach($fields as $field) {
            foreach($this->formfields as $field) {
                if ( $field->name == 'jform[params]['.$name.']' || $field->name == 'jform[params]['.$name.'][]' ) {
                    return $field->value;
                }
            }               
        }
}
