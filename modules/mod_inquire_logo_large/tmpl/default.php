<?php
// No direct access
defined('_JEXEC') or die; ?>
<?php
echo $templatePath;
if($isHomepage) { ?>
    <img class="main-logo" src="logo.png">
    <?php
    echo $logo;
} else {
    echo 'not homepage';
}

?>
