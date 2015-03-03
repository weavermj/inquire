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
    var sourcefiles   = $("#dropfiles-template-ggd-files").html();
    var sourcecategories   = $("#dropfiles-template-ggd-categories").html();
    var sourcefile   = $("#dropfiles-template-ggd-box").html();
    
    Handlebars.registerHelper('bytesToSize', function(bytes) {
        return bytesToSize(bytes);
    });
    
    Handlebars.registerHelper('isGGExt', function(ext,options) {
        if(dropfilesGVExt.indexOf(ext)>=0){
            return options.fn(this);
        }
    });
    
    Handlebars.registerHelper('encodeURI', function(uri) {
        res = encodeURIComponent(uri);
        return res;
    });

    initClickFile();
    
    $('.dropfiles-content-ggd.dropfiles-content-multi .catlink').click(function(e){
        e.preventDefault();
        load($(this).parents('.dropfiles-content-ggd.dropfiles-content-multi').data('category'),$(this).data('idcat'));
    });
    
    function initClickFile(){
        $('.dropfiles-content-ggd.dropfiles-content .dropfile-file-link').unbind('click').click(function(e){
            e.preventDefault();
            fileid = $(this).data('id')
            catid = $(this).parents(".dropfiles-content-ggd").data('current');
            $.ajax({
                url: "/index.php?option=com_dropfiles&view=frontfile&format=json&id="+fileid+"&catid="+catid,
                dataType : "json"
            }).done(function(file) {
                var template = Handlebars.compile(sourcefile);
                var html = template(file);
                box = $("#dropfiles-box-ggd");
                if(box.length===0){
                    $('body').append('<div id="dropfiles-box-ggd" style="display: hidden;"></div>');
                    box = $("#dropfiles-box-ggd");
                }
                box.empty();
                box.prepend(html);
                box.click(function(e){
                    if($(e.target).is('#dropfiles-box-ggd')){
                        box.hide();
                    }
                    $('#dropfiles-box-ggd').unbind('click.box').bind('click.box',function(e){
                        if($(e.target).is('#dropfiles-box-ggd')){
                            box.hide();
                        }
                    });
                });
                $('#dropfiles-box-ggd .dropfiles-close').click(function(){box.hide();});
                if(typeof(dropfilesColorboxInit)!=='undefined'){
                    dropfilesColorboxInit();
                }
                box.show();

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
    function load(sourcecat,category){
        $(".dropfiles-content-ggd.dropfiles-content-multi[data-category="+sourcecat+"]").empty();
        
        //Get categories
        $.ajax({
            url: "/index.php?option=com_dropfiles&view=frontcategories&format=json&id="+category,
            dataType : "json"
        }).done(function(categories) {
            var template = Handlebars.compile(sourcecategories);
            var html = template(categories);
            $(".dropfiles-content-ggd.dropfiles-content-multi[data-category="+sourcecat+"]").data('current',category);
            $(".dropfiles-content-ggd.dropfiles-content-multi[data-category="+sourcecat+"]").prepend(html);
            $(".dropfiles-content-ggd.dropfiles-content-multi[data-category="+sourcecat+"] .catlink").click(function(e){
                e.preventDefault();
                load($(this).parents(".dropfiles-content-ggd.dropfiles-content-multi").data('category'),$(this).data('idcat'));
                initClickFile();
            });
        });
        
        //Get files
        $.ajax({
            url: "/index.php?option=com_dropfiles&view=frontfiles&format=json&id="+category,
            dataType : "json"
        }).done(function(content) {
            var template = Handlebars.compile(sourcefiles);
            var html = template(content);
            $(".dropfiles-content-ggd.dropfiles-content-multi[data-category="+sourcecat+"]").append(html);
            initClickFile();
            if(typeof(dropfilesColorboxInit)!=='undefined'){
                dropfilesColorboxInit();
            }
        });

    }    
});