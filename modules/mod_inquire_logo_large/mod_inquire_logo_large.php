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
require( JModuleHelper::getLayoutPath('mod_inquire_logo_large'));
?>
