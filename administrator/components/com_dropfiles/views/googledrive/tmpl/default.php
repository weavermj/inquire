<?php
/** 
 * Dropfiles
 * 
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@joomunited.com *
 * @package Dropfiles
 * @copyright Copyright (C) 2013 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @copyright Copyright (C) 2013 Damien Barrère (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die;

$params = JComponentHelper::getParams('com_dropfiles');
$google = new dropfilesGoogle();

if(!$params->get('google_client_id') || !$params->get('google_client_secret')){ ?>
        <?php JText::printf('COM_DROPFILES_GOOGLEDRIVE_CONNECT_PART1_1'); ?>
        
        <?php JText::printf('COM_DROPFILES_GOOGLEDRIVE_CONNECT_PART1_2',JURI::root(), JURI::root().'administrator/index.php?option=com_dropfiles&task=googledrive.authenticate'); ?>

        <?php if(dropfilesBase::isJoomla25()){
            JText::printf('COM_DROPFILES_GOOGLEDRIVE_CONNECT_PART1_3','href="index.php?option=com_config&view=component&component=com_dropfiles&path=&tmpl=component"');
        }else{
            JText::printf('COM_DROPFILES_GOOGLEDRIVE_CONNECT_PART1_3','href="#" onclick="window.parent.open(\'index.php?option=com_config&view=component&component=com_dropfiles&path=\',\'_self\');return false;"');        
        } ?>
    </p>    
<?php }elseif(!$google->checkAuth()){
    $url = $google->getAuthorisationUrl(); 
    ?>
    <p><?php echo JText::_('COM_DROPFILES_GOOGLEDRIVE_CONNECT_PART2'); ?></p>
    <p><a id="ggconnect" class="btn btn-primary btn-large" href="#" onclick="window.open('<?php echo $url; ?>','foo','width=600,height=600');return false;"><img src="<?php echo JURI::root(); ?>/components/com_dropfiles/assets/images/drive-icon-colored.png" alt="" /> <?php echo JText::_('COM_DROPFILES_GOOGLEDRIVE_CONNECT_PART2_CONNECT'); ?></a></p>
<?php }else{ ?>
    <?php echo JText::_('COM_DROPFILES_GOOGLEDRIVE_CONNECT_PART3'); ?>
    <a class="btn btn-primary" href="index.php?option=com_dropfiles&task=googledrive.logout"><?php echo JText::_('COM_DROPFILES_GOOGLEDRIVE_CONNECT_PART3_DISCONNECT'); ?></a>
<?php } ?>
        




