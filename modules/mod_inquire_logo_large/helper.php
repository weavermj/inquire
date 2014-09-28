<?php
/**
 * Helper class for Inquire Logo Large module
 *
 * @package    Inquire
 * @subpackage Modules
 */
class modInquireLogoLarge
{
    public static function isHomepage ()
    {
        $doc = JFactory::getDocument();
        if($doc->getTitle() === 'Welcome') {
            return true;
        }
        return false;
    }
}
?>
