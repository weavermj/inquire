<?php
/**
 *
 * Inquire Logo Large Module Entry Point
 *
 * @package    Inquire
 * @subpackage Modules
 */

// no direct access
defined('_JEXEC') or die;
// Include the syndicate functions only once
require_once( dirname(__FILE__) . '/helper.php' );

$logo = modInquireLogoLarge::getLogo($params);
$isHomepage = modInquireLogoLarge::isHomepage();
$tempatePath = $this->baseurl.'/templates'.$this->template;

require( JModuleHelper::getLayoutPath('mod_inquire_logo_large'));
?>
