<?php
/**
 * Helper class for Inquire Logo Large module
 *
 * @package    Inquire
 * @subpackage Modules
 */
class modInquireLogoLarge
{
    /**
    * Retrieves the logo url
    *
    * @param array $params An object containing the module parameters
    * @access public
    */
    public static function getLogo( $params )
    {
        return 'logo path here';
    }

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
