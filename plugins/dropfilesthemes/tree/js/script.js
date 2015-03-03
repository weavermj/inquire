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

jQuery(document).ready(function($) {
    var sourcefiles   = $("#dropfiles-template-tree-files").html();
    var sourcecategories   = $("#dropfiles-template-tree-categories").html();
    var sourcefile   = $("#dropfiles-template-tree-box").html();
    
    Handlebars.registerHelper('bytesToSize', function(bytes) {
        return bytesToSize(bytes);
    });
    
    initClickFile();
    
    $('.dropfiles-content-tree.dropfiles-content-multi a.catlink').unbind('click.cat').bind('click.cat',function(e){
        e.preventDefault();
        load($(this).parents('.dropfiles-content-tree.dropfiles-content-multi').data('category'),$(this).data('idcat'),$(this));
    });
    
    function initClickFile(){
        $('.dropfiles-content-tree.dropfiles-content .dropfile-file-link').unbind('click').click(function(e){
            e.preventDefault();
            fileid = $(this).data('id');
            catid = $(this).closest('.catlink').find("a.dropfilescategory").data('idcat');
            if(!catid){
                catid = $(this).parents(".dropfiles-content-tree").data('current');   
            }
            $.ajax({
                url: "/index.php?option=com_dropfiles&view=frontfile&format=json&id="+fileid+"&catid="+catid,
                dataType : "json"
            }).done(function(file) {
                var template = Handlebars.compile(sourcefile);
                var html = template(file);
                box = $("#dropfiles-box-tree");
                if(box.length===0){
                    $('body').append('<div id="dropfiles-box-tree" style="display: hidden;"></div>');
                    box = $("#dropfiles-box-tree");
                }
                box.empty();
                box.prepend(html);
                box.click(function(e){
                    if($(e.target).is('#dropfiles-box-tree')){
                            box.hide();
                        }
                    $('#dropfiles-box-tree').unbind('click.box-tree').bind('click.box-tree',function(e){
                        if($(e.target).is('#dropfiles-box-tree')){
                            box.hide();
                        }
                    });
                });
                $('#dropfiles-box-tree .dropfiles-close').click(function(){box.hide();});

                box.show();
                if(typeof(dropfilesColorboxInit)!=='undefined'){
                    dropfilesColorboxInit();
                }

                dropblock = box.find('.dropblock');
                
                if($(window).width() < 400){
                    dropblock.css('margin-top','0');
                    dropblock.css('margin-left','0');
                    dropblock.css('top','0');
                    dropblock.css('left','0');
                    dropblock.height($(window).height()-parseInt(dropblock.css('padding-top'),10)-parseInt(dropblock.css('padding-bottom'),10));
                    dropblock.width($(window).width()-parseInt(dropblock.css('padding-left'),10)-parseInt(dropblock.css('padding-right'),10));
                }else{
                    dropblock.css('margin-top',(-(dropblock.height()/2)-20)+'px');
                    dropblock.css('margin-left',(-(dropblock.width()/2)-20)+'px');
                    dropblock.css('height','');
                    dropblock.css('width','');
                    dropblock.css('top','');
                    dropblock.css('left','');
                }
            });
        });
    }
    function load(sourcecat,category,elem){
        ul = elem.parent().children('ul');
        if(ul.length>0){
            //close cat
            ul.slideUp(500,null,function(){
                $(this).remove();
                elem.parent().removeClass('open');
            });
            return;
        }
        
        //Get categories
        $.ajax({
            url: "/index.php?option=com_dropfiles&view=frontcategories&format=json&id="+category,
            dataType : "json"
        }).done(function(categories) {
            var template = Handlebars.compile(sourcecategories);
            var html = template(categories);
            if(categories.categories.length>0){
                elem.parents('li').append('<ul style="display:none;">'+html+'</ul>');
                $(".dropfiles-content-tree.dropfiles-content-multi[data-category="+sourcecat+"] a.catlink").unbind('click.cat').bind('click.cat',function(e){
                    e.preventDefault();
                    load($(this).parents('.dropfiles-content-tree.dropfiles-content-multi').data('category'),$(this).data('idcat'),$(this));
                    initClickFile();
                });    
            }
            
            //Get files
            $.ajax({
                url: "/index.php?option=com_dropfiles&view=frontfiles&format=json&id="+category,
                dataType : "json"
            }).done(function(content) {
                var template = Handlebars.compile(sourcefiles);
                var html = template(content);
                if(elem.parent().children('ul').length==0){
                    elem.parent().append('<ul style="display:none;">'+html+'</ul>');
                }else{
                    elem.parent().children('ul').append(html);
                }
                
                initClickFile();
                
                elem.parent().children('ul').slideDown(500,null,function(){elem.parent().addClass('open');});
            });
        });
        
    }    
});