<?php
// No direct access
defined('_JEXEC') or die; ?>
<?php
if($isHomepage) { ?>
    <img class="main-logo" src="./images/logo.png">
    <?php
    echo $logo;
} else {
    echo 'not homepage';
}

?>
