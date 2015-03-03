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

?>
<div id="mycategories">
    <div id="updateGroup" class="btn-group btn-block hide">
        <a id="updateBtn" class="btn btn-warning" href="http://www.joomunited.com/my-account" target="_blank" style="width: 60%;"><i class="icon-info"></i> <?php echo JText::_('COM_DROPFILES_CTRL_UPDATE_AVAILABLE'); ?> <span class="versionNumber"></span></a>
        <a id="hideUpdateBtn" class="btn btn-warning" href=""><i class="icon-cancel"></i> <?php echo JText::_('COM_DROPFILES_CTRL_UPDATE_HIDE'); ?></a>
    </div>
    <?php if($this->canDo->get('core.create')): ?>
    <div id="newcategory" class="btn-group btn-categories <?php echo $this->params->get('google_credentials','')?'':'centpc' ?>">
        <a class="btn btn-default" href=""><i class="icon-plus"></i> <?php echo JText::_('COM_DROPFILES_LAYOUT_DROPFILES_NEW_CATEGORY'); ?></a>
        <?php if($this->params->get('google_credentials','')): ?>
        <a class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
        <ul class="dropdown-menu pull-right">            
            <li><a href="#" class="googleCat"><i class="google-drive-icon"></i> <?php echo JText::_('COM_DROPFILES_LAYOUT_DROPFILES_NEW_GOOGLEDRIVE'); ?></a></li>            
        </ul>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <div class="nested dd">
        <ol id="categorieslist" class="dd-list nav bs-docs-sidenav2 ">
            <?php
            $content = '';
            if(!empty($this->categories)){
                $previouslevel = 1;
                for ($index = 0; $index < count($this->categories); $index++) {
                    if($index+1!=count($this->categories)){
                        $nextlevel = $this->categories[$index+1]->level;
                    }else{
                        $nextlevel = 0;
                    }
                    $content .= openItem($this->categories[$index],$index,$this->canDo);
                    if($nextlevel>$this->categories[$index]->level){
                        $content .= openlist($this->categories[$index]);
                    }elseif($nextlevel==$this->categories[$index]->level){
                        $content .= closeItem($this->categories[$index]);
                    }else{
                        $c = '';
                        $c .= closeItem($this->categories[$index]);
                        $c .= closeList($this->categories[$index]);
                        $content .= str_repeat($c,$this->categories[$index]->level-$nextlevel);
                    }
                    $previouslevel = $this->categories[$index]->level;                    
                }
            } 
            echo $content;
            ?>
        </ol>
        <input type="hidden" id="categoryToken" name="<?php echo JSession::getFormToken(); ?>" /> 
    </div>
</div>


<?php  
function openItem($category,$key,$canDo){
    $icon = '';
    if($category->type==='googledrive'){
        $icon = '<i class="google-drive-icon"></i> ';
    }
    $return = '<li class="dd-item dd3-item '.($key?'':'active').'" data-id-category="'.$category->id.'" data-author="'.$category->created_user_id.'">
        <div class="dd-handle dd3-handle"></div>
        <div class="dd-content dd3-content">';
        if($canDo->get('core.edit') || $canDo->get('core.edit.own')){
            $return .= '<a class="edit"><i class="icon-edit"></i></a>'; 
        }
        if($canDo->get('core.delete')){
            $return .= '<a class="trash"><i class="icon-trash"></i></a>';
        }
        $return .=    '<a href="" class="t">'.$icon.'
                <span class="title">'.$category->title.'</span>
            </a>
        </div>';
        return $return;
}

function closeItem($category){
    return '</li>';
}

function itemContent($category){
    return '<div class="dd-handle dd3-handle"></div>
    <div class="dd-content dd3-content"
        <i class="icon-chevron-right"></i>
        <a class="edit"><i class="icon-edit"></i></a>
        <a class="trash"><i class="icon-trash"></i></a>
        <a href="" class="t">
            <span class="title">'.$category->title.'</span>
        </a>
    </div>';
}

function openlist($category){
    return '<ol class="dd-list">';
}

function closelist($category){
    return '</ol>';
}