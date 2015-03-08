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
    //load media tables
    $('.dropfiles-content-table.dropfiles-content .mediaTable').mediaTable();
    
    var source   = $("#dropfiles-template-table").html();
    
    Handlebars.registerHelper('bytesToSize', function(bytes) {
        return bytesToSize(bytes);
    });
    
    function initClick(){
        $('.dropfiles-content-table.dropfiles-content-multi .catlink').click(function(e){
            e.preventDefault();
            load($(this).parents('.dropfiles-content-table.dropfiles-content-multi').data('category'),$(this).data('idcat'));
        });
    }
    
    initClick();
    
    function load(sourcecat,category){
        $(".dropfiles-content-table.dropfiles-content-multi[data-category="+sourcecat+"] table tbody").empty();
        
        //Get categories
        $.ajax({
            url: "/index.php?option=com_dropfiles&view=frontcategories&format=json&id="+category,
            dataType : "json"
        }).done(function(categories) {
            //Get files
            $.ajax({
                url: "/index.php?option=com_dropfiles&view=frontfiles&format=json&id="+category,
                dataType : "json"
            }).done(function(content) {
                $.extend(content,categories);
                var template = Handlebars.compile(source);
                var html = template(content);
                $(".dropfiles-content-table.dropfiles-content-multi[data-category="+sourcecat+"] table tbody").append(html);
                $(".dropfiles-content-table.dropfiles-content-multi[data-category="+sourcecat+"] table tbody").trigger('change');
                $(".dropfiles-content-table.dropfiles-content-multi[data-category="+sourcecat+"] .mediaTableMenu").find('input').trigger('change');
                initClick();
                if(typeof(dropfilesColorboxInit)!=='undefined'){
                    dropfilesColorboxInit();
                }
            });
        });
    }    
});