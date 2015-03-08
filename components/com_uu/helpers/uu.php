<?php
/**
 * @package     UltimateUser for Joomla!
 * @author      Stéphane Bouey <stephane.bouey@faboba.com> - http://www.faboba.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright   Copyright (C) 2012-2013. All rights reserved.
 */

defined('_JEXEC') or die;

class UuSiteHelper
{

  public static function getRedirectUrl($url, $custom = 0) {
      $result = '';
      switch ($url) {
          case 'home':
              $result = JURI::base(true) . '/';
              break;
          case 'login':
              $result = 'index.php?option=com_uu&view=login';
              break;
          case 'register':
              $result = 'index.php?option=com_uu&view=registration';
              break;
          case 'custom':
              $result = 'index.php?option=com_content&view=article&id='.$custom;
              break;
          default:
              $result = JURI::base(true) . '/';
              break;
      }
      return $result;
  }
}

class UValidateHelper
{
    static public function username( $username )
    {
        // Make sure the username is at least 1 char and contain no funny char
        return (!preg_match( "/[<>\"'%;()&]/i" , $username ) && JString::strlen( $username )  > 0 );
    }

}

