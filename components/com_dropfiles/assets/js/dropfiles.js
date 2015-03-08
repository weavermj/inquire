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
    if(typeof(Dropfiles)=='undefined'){
        Dropfiles ={};
        Dropfiles.can = {};
        Dropfiles.can.create=false;
        Dropfiles.can.edit=false;
        Dropfiles.can.delete=false;
        Dropfiles.maxfilesize = 10;
        Dropfiles.selection = {};
    }
    
    /**
     * Init sortable files 
     * Save order after each sort
     */
    if(Dropfiles.can.edit || (Dropfiles.can.editown && Dropfiles.author=== $('#categorieslist li.active').data('author'))){
        $('#preview').sortable({ 
            placeholder: 'highlight file',
            revert: 300,
            distance: 15,
            items: ".file",
            helper : 'clone',
            handle: ".orderingCol",
            update : function(){
                var json='';
                id_category = jQuery('input[name=id_category]').val();
                $.each($('#preview .file'),function(i,val){
                    if(json!==''){
                        json+=',';
                    }
                    json+='"'+i+'":"'+$(val).data('id-file')+'"';
                });
                json = '{'+json+'}';
                $.ajax({
                    url     :   "index.php?option=com_dropfiles&task=files.reorder&order="+encodeURIComponent(json)+"&idcat="+id_category,
                    type    :   "POST"
                });
            },
            /** Prevent firefox bug positionnement **/
            start: function (event, ui) {
                $(ui.helper).find('td').each(function(i,e){
                    $(e).css('width',$('#preview .restable thead th:nth-child('+(i+1)+')').width());
                });
                
                var userAgent = navigator.userAgent.toLowerCase();
                if( ui.helper !== "undefined" && userAgent.match(/firefox/) ){
                    ui.helper.css('position','absolute').css('margin-top', $(window).scrollTop() );
                }
            },
            beforeStop: function (event, ui) {
                var userAgent = navigator.userAgent.toLowerCase();
                if( ui.offset !== "undefined" && userAgent.match(/firefox/) ){
                    ui.helper.css('margin-top', 0);
                }
            }
        });
    }
    $('#preview').disableSelection();
    
    /* init menu actions */
    initMenu();
    
    initThemeBtn();
    
    /* Load category */
//updatepreview();
    

    /* Load nestable */
    if(Dropfiles.can.edit || (Dropfiles.can.editown && Dropfiles.author=== $('#categorieslist li.active').data('author'))){
        $('.nested').nestable().on('change', function(event, e){
            pk = $(e).data('id-category');
            if($(e).prev('li').length===0){
                position = 'first-child';
                if($(e).parents('li').length===0){
                    //root
                    ref = 0;
                }else{
                    ref = $(e).parents('li').data('id-category');
                }
            }else{
                position = 'after';
                ref = $(e).prev('li').data('id-category');
            }
            $.ajax({
                url     :   "index.php?option=com_dropfiles&task=categories.order&pk="+pk+"&position="+position+"&ref="+ref,
                type    :   "POST"
            }).done(function(data){
                result = jQuery.parseJSON(data);
                if(result.response===true){
    //                console.log(result.datas);
                }else{
                    bootbox.alert(result.response);
                }
            });
        });
        if(Dropfiles.collapse===true){
            $('.nested').nestable('collapseAll');
        }
    }
    
    //Check what is loaded via editor
    if(typeof(gcaninsert)!=='undefined' && gcaninsert===true){
        if(typeof(window.parent.tinyMCE)!=='undefined'){
            content = window.parent.tinyMCE.get(e_name).selection.getContent();
            imgparent = window.parent.tinyMCE.get(e_name).selection.getNode().parentNode;
            exp = '<img.*data\-dropfilesfile="([0-9a-zA-Z_]+)".*?>';
            file = content.match(exp);
            exp = '<img.*data\-dropfilescategory="([0-9]+)".*?>';
            category = content.match(exp);
            exp = '<img.*data\-dropfilesfilecategory="([0-9]+)".*?>';
            filecategory = content.match(exp);
            Dropfiles.selection = new Array();
            Dropfiles.selection.content = content;

            if(file!==null && filecategory!==null){
                if(file!==null){
                    elem = $(content).filter('img[data-dropfilesfile='+file[1]+']');
                    Dropfiles.selection.selection = elem;
                    Dropfiles.selection.file = file[1];
                }
                if(filecategory!==null){
                    Dropfiles.selection.category = filecategory[1];
                    $('#categorieslist li').removeClass('active');
                    $('#categorieslist li[data-id-category="'+filecategory[1]+'"]').addClass('active');
                    $('input[name=id_category]').val(filecategory[1]);
                    updatepreview(filecategory[1],file[1]);
                }
            }else if(category!==null){
                Dropfiles.selection.category = category[1];
                $('#categorieslist li').removeClass('active');
                $('#categorieslist li[data-id-category="'+category[1]+'"]').addClass('active');
                $('input[name=id_category]').val(category[1]);
                updatepreview(category[1]);
                loadGalleryParams();
            }else{
                updatepreview();
                loadGalleryParams();
            }
        }
    }else{
        /* Load gallery */
        updatepreview();
    }
    
    /* Init version dropbox */
    initDropboxVersion($('#fileversion'));
    $('#upload_button_version').on('click',function(){
        $('#upload_input_version').trigger('click');
        return false;
    });

    /* Init File import */
    if(Dropfiles.can.config){
        $('#jao').jaofiletree({ 
            script  : 'index.php?option=com_dropfiles&task=connector.listdir&tmpl=component',
            usecheckboxes : 'files',
            showroot : '/'
        });
    }
    $('#importFilesBtn').click(function(){
        id_category = $('input[name=id_category]').val();
        var files= '';
        $($('#jao').jaofiletree('getchecked')).each(function(){files+='&files[]='+this.file;});
        if(files===''){
            return;
        }
        $.ajax({
            url     :   "index.php?option=com_dropfiles&task=files.import&"+$('#categoryToken').attr('name') + "=1&id_category="+id_category,
            type    : 'GET',
            data    :   files
        }).done(function(data){
            result = jQuery.parseJSON(data);
            if(result.response===true){
                bootbox.alert(result.datas.nb+Joomla.JText._('COM_DROPFILES_JS_X_FILES_IMPORTED', ' files imported'));
                updatepreview(id_category);
            }else{
                if(typeof(result.datas)!=='undefined' && result.datas=='noerror'){
                    
                }else{
                    bootbox.alert(result.response);
                }
            }
        });
        return false;
    });
    $('#selectAllImportFiles').click(function(){
        $('#filesimport input[type="checkbox"]').attr('checked', true);
    });
    $('#unselectAllImportFiles').click(function(){
        $('#filesimport input[type="checkbox"]').attr('checked', false);
    });
    
    /** Check new version **/
    $.getJSON( "index.php?option=com_dropfiles&task=update.check", function(data) {
        if(data!==false){
            $('#updateGroup').show().find('span.versionNumber').html(data);
        }
    });
    
    $('#hideUpdateBtn').click(function(e){
        e.preventDefault();
        var today = new Date(), expires = new Date();
        expires.setTime(today.getTime() + (7*24*60*60*1000));
        document.cookie = "com_dropfiles_noCheckUpdates =true; expires=" + expires.toGMTString();
        $('#updateGroup').hide();
    });

    function showCategory(){
        $('.fileblock').fadeOut(function(){$('.categoryblock').fadeIn();});
        $('#insertfile').fadeOut(function(){$('#insertcategory').fadeIn();});
        
    }

    function showFile(e){
//        $('#singleimage').attr('src',$(e).attr('src'));
        $('.categoryblock').fadeOut(function(){$('.fileblock').fadeIn();});
        $('#insertcategory').fadeOut(function(){$('#insertfile').fadeIn();});
    }

    
    /**
     * Reload a category preview
     * @param id_category
     * @param id_file
     */
    function updatepreview(id_category,id_file,order,order_dir){
        $('#preview').contents().remove();
        if(typeof(id_category)==="undefined" || id_category===null){
                id_category = $('#categorieslist li.active').data('id-category');
            if(typeof(id_category)==='undefined'){
                $('#insertcategory').hide();
                return; 
            }
            $('input[name=id_category]').val(id_category);
        }else{
            $('#preview')
        }
        loading('#wpreview');
        url = "index.php?option=com_dropfiles&view=files&format=raw&id_category="+id_category;
        if(typeof(order)==='string'){
            url = url + '&orderCol='+order;
        }        
        if(order_dir==='asc'){
            url = url + '&orderDir=desc';
        }else if(order_dir==='desc'){
            url = url + '&orderDir=asc';
        }
        $.ajax({
            url     :   url,
            type    :   "POST"
        }).done(function(data){
            $('#preview').html($(data));
            if(Dropfiles.can.edit || (Dropfiles.can.editown && Dropfiles.author=== $('#categorieslist li.active').data('author'))){
                $('<div id="dropbox"><span class="message">'+Joomla.JText._('COM_DROPFILES_JS_DROP_FILES_HERE', 'Drop files here to upload')+'.<i> '+Joomla.JText._('COM_DROPFILES_JS_USE_UPLOAD_BUTTON', 'Or use the button below')+'</i></span><input class="hide" type="file" id="upload_input" multiple=""><a href="" id="upload_button" class="btn btn-large btn-primary">'+Joomla.JText._('COM_DROPFILES_JS_SELECT_FILES', 'Select files')+'</a></div><div class="clr"></div>').appendTo('#preview');
            }
            $('#preview .restable').restable({
                    type : 'hideCols',
                    priority : {0:'persistent' , 1:3, 2:'persistent' , 3:1 , 8:'persistent'}
                });
            if(Dropfiles.can.edit || (Dropfiles.can.editown && Dropfiles.author=== $('#categorieslist li.active').data('author'))){
                if($('#preview .currentOrderingCol').data('ordering')==='ordering'){
                    $('#preview').sortable('enable');
                    $('#preview').sortable('refresh');
                }else{
                    $('#preview').sortable('disable');
                }
            }
            initDeleteBtn();


            if(Dropfiles.can.edit || (Dropfiles.can.editown && Dropfiles.author=== $('#categorieslist li.active').data('author'))){
                initUploadBtn();
            }
                        
            /** Init ordering **/
            $('#preview .restable thead a').click(function(e){
                e.preventDefault();
                updatepreview(null,null,$(this).data('ordering'),$(this).data('direction'));
                if($(this).data('direction')==='asc'){
                    direction = 'desc';
                }else{
                    direction = 'asc';
                }
                $('#jform_params_ordering option[value="'+$(this).data('ordering')+'"]').attr('selected','selected').parent().animate({'background-color':'#ACFFCD'});
                $('#jform_params_orderingdir option[value="'+direction+'"]').attr('selected','selected').parent().animate({'background-color':'#ACFFCD'});
            });
            
            /** Init files **/
            $(document).unbind('click.window').bind('click.window',function(e){
                if($(e.target).is('#rightcol') || 
                    $(e.target).parents('#rightcol').length>0 ||
                    $(e.target).parents('#rightcol').length>0 || 
                    $(e.target).is('.modal-backdrop') || 
                    $(e.target).parents('.bootbox.modal').length>0
                    ){
                    return;
                }
                $('#preview .file').removeClass('selected');
                showCategory();
            });        

            $('#preview .file').unbind('click').click(function(e){
               iselected = $(this).find('tr.selected').length;
               $('#preview .file.selected').removeClass('selected');
    //            //Allow multiselect
    //            if (!e.ctrlKey){
    //                $('#preview .file.selected').removeClass('selected');
    //            }
               if(iselected===0){
                    $(this).addClass('selected');
               }

               if($('#preview .file.selected').length>0){
                   if(Dropfiles.can.edit || (Dropfiles.can.editown && Dropfiles.author=== $('#categorieslist li.active').data('author'))){
                        loadFileParams();
                        loadVersions();
                    }
                   showFile(this);
               }else{
                    showCategory();
               }
               e.stopPropagation();
            });

            
            
            $('#wpreview').unbind();
            initDropbox($('#wpreview'));

            theme = $('input[name=theme]').val();
            
            if($('.themesblock #themeselect option[value="'+theme+'"]').length===0){                
                $('.themesblock #themeselect option[value="default"]').attr('selected','selected');
            }else{
                $('.themesblock #themeselect option[value="'+theme+'"]').attr('selected','selected');
            }

            if(typeof(id_file)!=="undefined" && id_file!==null){
                $('#preview .file[data-id-file='+id_file+']').trigger('click');
            }else{
                showCategory();
                if(typeof(order)==='undefined'){
                    loadGalleryParams();
                }
            }

            rloading('#wpreview');
        });
        initEditBtn();
        initDeleteBtn();
      
    }

    function initDeleteBtn(){
        $('.actions .trash').unbind('click').click(function(e){
                    that = this;
                    bootbox.confirm(Joomla.JText._('COM_DROPFILES_JS_ARE_YOU_SURE', 'Are you sure')+'?',function(result){
                        if(result===true){
                            //Delete file
                            id_file = $(that).parents('.file').data('id-file');
                            id_category = $('input[name=id_category]').val();
                            $.ajax({
                                url     :   "index.php?option=com_dropfiles&task=files.delete&id_file="+id_file+"&id_cat="+id_category,
                                type    :   "POST"
                            }).done(function(data){
                                result = jQuery.parseJSON(data);
                                if(result===true){
                                    $(that).parents('.file').fadeOut(500, function() {$(this).remove();});
                                }
                            });
                        }
                    }); 
                    return false;
                });
    }
    
     
    /**
     * Init the file edit btn
     */
    function initEditBtn(){
        $('.wbtn a.edit').unbind('click').click(function(e){  
            that = this;
            id_file = $(that).parents('.wimg').find('img.img').data('id-file');
            $.ajax({
                url     :   "index.php?option=com_dropfiles&view=file&format=raw&id="+id_file,
                type    :   "POST"
            }).done(function(data){
                bootbox.dialog(data,[{'label':Joomla.JText._('COM_DROPFILES_JS_SAVE', 'Save'),'class':'btn-success','callback':function(){
                    var p = '';
                    $('#file-form .dropfilesinput').each(function(index){
                        p = p + $(this).attr('name')+ '=' + $(this).attr('value') + '&';
                    });
                    $.ajax({
                            url     :   $('#file-form').attr('action'),
                            type    :   'POST',
                            data    :   p
                    }).done(function(data){
//                        console.log(data);
                    });
                }},{'label':Joomla.JText._('COM_DROPFILES_JS_CANCEL', 'Cancel'),'class':'btn-warning'}],{header:Joomla.JText._('COM_DROPFILES_JS_IMAGE_PARAMETERS', 'Image parameters')});
//                result = jQuery.parseJSON(data);
//                if(result==true){
//                    $(that).parents('.wimg').fadeOut(500, function() {$(this).remove();})
//                }
            });
            return false;
        });
    }

    function initOrdering(){
        if(!Dropfiles.can.edit && !(Dropfiles.can.editown && Dropfiles.author=== $('#categorieslist li.active').data('author'))){
            return;
        }
    }

    function loadGalleryParams(){
        if(!Dropfiles.can.edit && !(Dropfiles.can.editown && Dropfiles.author=== $('#categorieslist li.active').data('author'))){
            return;
        }
        id_category = $('input[name=id_category]').val();
        showCategory();
        loading('#galleryparams');
        
        $.ajax({
            url     :   "index.php?option=com_dropfiles&task=category.edit&layout=form&id="+id_category
        }).done(function(data){
            $('#galleryparams').html(data);
//            rloading($('.dropfilesparams'));
            $('#galleryparams .dropfilesparams button[type="submit"]').click(function(e){
                e.preventDefault();
                id_category = $('input[name=id_category]').val();
                $.ajax({
                    url     :   "index.php?option=com_dropfiles&task=category.setparams&id="+id_category,
                    type    :   "POST",
                    data    :   $('#galleryparams .dropfilesparams [name*="jform"], #galleryparams .dropfilesparams input')
                }).done(function(data){
//                    console.log(data);
                    result = jQuery.parseJSON(data);
                    if(result.response===true){
                        updatepreview();                        
                    }else{
                        bootbox.alert(result.response);
                    }
//                    loadGalleryParams();
                });                
                return false;
            });
            rloading('#galleryparams');
        });
    }

    function initThemeBtn(){
        $('#themeselect').change(function(){
            id_category = $('input[name=id_category]').val();
            $.ajax({
                url     :   'index.php?option=com_dropfiles&task=config.setTheme&theme='+$('#themeselect option:selected').val()+'&id='+id_category,
                type    :   'POST'
            }).done(function(data){
                result = jQuery.parseJSON(data);
                if(result===true){
                    loadGalleryParams();
                }
            });
        });
    }

    function loadFileParams(){
        id_category = $('input[name=id_category]').val();
        id_file = jQuery('.file.selected').data('id-file');
        loading('#rightcol');
        $.ajax({
            url     :   "index.php?option=com_dropfiles&task=file.edit&layout=form&id="+id_file+"&catid="+id_category
        }).done(function(data){
            $('#fileparams').html(data);
//            rloading($('.dropfilesparams'));
            $('#fileparams .dropfilesparams button[type="submit"]').click(function(e){
                e.preventDefault();
                id_file = jQuery('.file.selected').data('id-file');
//                type = jQuery('.file div.selected').parent().data('type');
                id_category = $('input[name=id_category]').val();

                $.ajax({
                    url     :   "index.php?option=com_dropfiles&task=file.save&id="+id_file+"&catid="+id_category,
                    type    :   "POST",
                    data    :   $('#fileparams .dropfilesparams [name*="jform"], #fileparams .dropfilesparams input')
                }).done(function(data){
                    result = jQuery.parseJSON(data);
                    if(result.response===true){
                        loadFileParams();
                    }else{
                        bootbox.alert(result.response);
                    }
                    loadFileParams();
                    updatepreview(null,id_file);
                });                
                return false;
            });
            rloading('#rightcol');
        });
    }

    function loadVersions(){
        id_category = $('input[name=id_category]').val();
        id_file = jQuery('.file.selected').data('id-file');
        loading('#fileversion');
        $.ajax({
            url     :   "index.php?option=com_dropfiles&view=files&layout=versions&format=raw&id_file="+id_file+"&id_category="+id_category
        }).done(function(data){
            $('#versions_content').html(data);
            $('#versions_content a.trash').click(function(){
                that = this;
                bootbox.confirm(Joomla.JText._('COM_DROPFILES_JS_ARE_YOU_SURE', 'Are you sure')+'?',function(result){
                    if(result===true){
                        id = $(that).data('id');
                        $.ajax({
                            url     :   "index.php?option=com_dropfiles&task=file.deleteVersion&id="+id+"&id_file="+id_file+"&catid="+id_category,
                            type    :   "POST"
                        }).done(function(data){
                            result = jQuery.parseJSON(data);
                            if(result.response===true){
                                $(that).parents('tr').remove();
                            }else{
                                bootbox.alert(result.response);
                            }
                        });
                    }
                });
                return false;
            });
            rloading('#fileversion');
        });
    }

    function initUploadBtn(){
        $('#upload_button').on('click',function(){
            $('#upload_input').trigger('click');
            return false;
        });
    }

    /**
     * Click on new category btn
     */
    $('#newcategory a:not(.dropdown-toggle)').on('click',function(e){
        e.preventDefault();
        if($(this).hasClass('googleCat')){
            type = 'googledrive';
        }else{
            type = 'joomla';
        }
        $.ajax({
            url     :   "index.php?option=com_dropfiles&task=category.addCategory&type="+type,
            type    : 'POST',
            data    :   $('#categoryToken').attr('name') + '=1'
        }).done(function(data){
            try {
                result = jQuery.parseJSON(data);
            }catch(err){
                bootbox.alert('<div>'+data+'</div>');
            }
            if(result.response===true){
                if(type==='googledrive'){
                    icon='<i class="google-drive-icon"></i> ';
                }else{
                    icon = '';
                }
                link = ''+
                        '<li class="dd-item dd3-item" data-id-category="'+result.datas.id_category+'" data-author="'+Dropfiles.author+'">'+
                            '<div class="dd-handle dd3-handle"></div>'+
                            '<div class="dd-content dd3-content">'+
                                '<a class="edit"><i class="icon-edit"></i></a>'+
                                '<a class="trash"><i class="icon-trash"></i></a>'+
                                '<a href="" class="t">'+
                                    icon +
                                    '<span class="title">'+result.datas.name + '</span>' +
                                '</a>'+
                            '</div>';
                $(link).appendTo('#categorieslist');
                initMenu();
                $('#mycategories #categorieslist li[data-id-category='+result.datas.id_category+'] .dd-content').click();
                $('#insertcategory').show();
            }else{
                bootbox.alert(result.response);
            }
        });        
    });

    /**
     * Init the dropbox 
     **/    
    function initDropbox(dropbox){
        dropbox.filedrop({
                paramname:'pic',
                fallback_id:'upload_input',
                maxfiles: 30,
                maxfilesize:  Dropfiles.maxfilesize,
                queuefiles: 2,
                data: {
                    id_category : function(){
                        return $('input[name=id_category]').val(); 
                    }
                },
                url: 'index.php?option=com_dropfiles&task=files.upload',

                uploadFinished:function(i,file,response){
                    if(typeof(response)==='string'){
                            bootbox.alert('<div>'+response+'</div>');
                    }
                    if(response.response===true){
                        $.data(file).addClass('done');
                        $.data(file).find('img').data('id-file', response.datas.id_file);
                    }else{
                        bootbox.alert(response.response);
                        $.data(file).remove();
                    }
                },

                error: function(err, file) {
                        switch(err) {
                                case 'BrowserNotSupported':
                                        bootbox.alert(Joomla.JText._('COM_DROPFILES_JS_BROWSER_NOT_SUPPORT_HTML5', 'Your browser does not support HTML5 file uploads!'));
                                        break;
                                case 'TooManyFiles':
                                        bootbox.alert(Joomla.JText._('COM_DROPFILES_JS_TOO_ANY_FILES','Too many files')+'!');
                                        break;
                                case 'FileTooLarge':
                                        bootbox.alert(file.name+' '+Joomla.JText._('COM_DROPFILES_JS_FILE_TOO_LARGE', 'is too large')+'!');
                                        break;
                                default:
                                        break;
                        }
                },

                // Called before each upload is started
                beforeEach: function(file){
                },

                uploadStarted:function(i, file, len){
                        var preview = $('<tr class="file well uploadplaceholder"><td colspan="100">'+
                                            Joomla.JText._('COM_DROPFILES_JS_WAIT_UPLOADING','Too many files')+'<span class="dl-loader"></span>'+
                                        '</td></tr>');

                        var reader = new FileReader();

                        preview.appendTo('#preview .table');
                        $('#preview table tbody').prepend(preview);

                        $.data(file,preview);
                },

                progressUpdated: function(i, file, progress) {
                        $.data(file).find('.progress .bar').width(progress+'%');
                },
                
                afterAll: function(){
                    $('#preview .progress').delay(300).fadeIn(300).hide(300, function(){
                      $(this).remove();
                    });
                    $('#preview .uploaded').delay(300).fadeIn(300).hide(300, function(){
                      $(this).remove();
                    });
                    $('#preview .file').delay(1200).show(1200,function(){
                        $(this).removeClass('done placeholder');
                    });
                    updatepreview();        
//                    initDeleteBtn();
//                    initEditBtn();

                },
                rename : function(name){
                    function fetchAscii(obj){
                            var convertedObj = '';
                            for(i = 0; i < obj.length; i++)
                            { 
                                  var asciiChar = obj.charCodeAt(i);
                                  convertedObj += '&#' + asciiChar + ';';
                            } 
                            return convertedObj;
                    }
                    return fetchAscii(name);
                }
        });
    }

    /**
     * Init the dropbox 
     **/    
    function initDropboxVersion(dropbox){
        dropbox.filedrop({
                paramname:'pic',
                fallback_id:'upload_input_version',
                maxfiles: 1,
                maxfilesize: Dropfiles.maxfilesize,
                queuefiles: 1,
                data: {
                    id_file : function(){
                        return $('.file.selected').data('id-file');
                    },
                    id_category : function(){
                        return $('input[name=id_category]').val();
                    }
                },
                url: 'index.php?option=com_dropfiles&task=files.version',

                uploadFinished:function(i,file,response){
                    if(response.response===true){
                    
                    }else{
                        bootbox.alert(response.response);
//                        $.data(file).remove();
                        $('#dropbox_version .progress').addClass('hide');
                        $('#dropbox_version .upload').removeClass('hide');
                    }
                },

                error: function(err, file) {
                        switch(err) {
                                case 'BrowserNotSupported':
                                        bootbox.alert(Joomla.JText._('COM_DROPFILES_JS_BROWSER_NOT_SUPPORT_HTML5', 'Your browser does not support HTML5 file uploads!'));
                                        break;
                                case 'TooManyFiles':
                                        bootbox.alert(Joomla.JText._('COM_DROPFILES_JS_TOO_ANY_FILES','Too many files')+'!');
                                        break;
                                case 'FileTooLarge':
                                        bootbox.alert(file.name+' '+Joomla.JText._('COM_DROPFILES_JS_FILE_TOO_LARGE', 'is too large')+'!');
                                        break;
                                default:
                                        break;
                        }
                },

                // Called before each upload is started
                beforeEach: function(file){
//                        if(!file.type.match(/^image\//)){
//                                bootbox.alert(Joomla.JText._('COM_DROPFILES_JS_ONLY_IMAGE_ALLOWED','Only images are allowed')+'!');
//                                return false;
//                        }
                },

                uploadStarted:function(i, file, len){
//                        var preview = $('<div class="file well uploadplaceholder">'+
//                                            '<span class="uploaded"></span>'+
//                                            '<div class="progress progress-striped active">'+
//                                                '<div class="bar"></div>'+
//                                            '</div>'+
//                                        '</div>');

//                        var reader = new FileReader();

                        // Reading the file as a DataURL. When finished,
                        // this will trigger the onload function above:
//                        reader.readAsDataURL(file);

//                        preview.appendTo('#preview .table');
//                        $('#dropbox').before(preview);

                        // Associating a preview container
                        // with the file, using jQuery's $.data():
                        $('#dropbox_version .upload').addClass('hide');
                        $('#dropbox_version .progress').removeClass('hide');
//                        $.data(file,preview);
                },

                progressUpdated: function(i, file, progress) {
                        $('#dropbox_version .progress .bar').width(progress+'%');
                },
                
                afterAll: function(){
//                    $('#preview .progress').delay(300).fadeIn(300).hide(300, function(){
//                      $(this).remove();
//                    });
//                    $('#preview .uploaded').delay(300).fadeIn(300).hide(300, function(){
//                      $(this).remove();
//                    });
//                    $('#preview .file').delay(1200).show(1200,function(){
//                        $(this).removeClass('done placeholder');
//                    });
                        $('#dropbox_version .progress').addClass('hide');
                        $('#dropbox_version .upload').removeClass('hide');
                        id_file = $('.file.selected').data('id-file');
                        updatepreview(null,id_file);
//                    initDeleteBtn();
//                    initEditBtn();

                },
                rename : function(name){
                    ext = name.substr(name.lastIndexOf('.'),name.length);
                    name = name.substr(0, name.lastIndexOf('.'));
                    var pattern_accent = new Array("é", "è", "ê", "ë", "ç", "à", "â", "ä", "î", "ï", "ù", "ô", "ó", "ö"); 
                    var pattern_replace_accent = new Array("e", "e", "e", "e", "c", "a", "a", "a", "i", "i", "u", "o", "o", "o");
                    name = preg_replace (pattern_accent, pattern_replace_accent,name);
                    
                    name = name.replace(/\s+/gi, '-');
                    name = name.replace(/[^a-zA-Z0-9\-]/gi, '');                    
                    return name+ext;
                }
        });
    }

    
        /* Title edition */
    function initMenu(){
        /**
        * Click on delete category btn
        */
       $('#categorieslist .dd-content .trash').unbind('click').on('click',function(){
           id_category = $(this).closest('li').data('id-category');
           bootbox.confirm(Joomla.JText._('COM_DROPFILES_JS_WANT_DELETE_CATEGORY','Do you really want to delete "')+$(this).parent().find('.title').text()+'"?', function(result) {
               if(result===true){
                   $.ajax({
                       url     :   "index.php?option=com_dropfiles&task=categories.delete&id_category="+id_category,
                       type    :   'POST',
                       data    :   $('#categoryToken').attr('name') + '=1'
                   }).done(function(data){
                       result = jQuery.parseJSON(data);
                       if(result.response===true){
                           $('#mycategories #categorieslist li[data-id-category='+id_category+']').remove();
                           $('#preview').contents().remove();
                           first = $('#mycategories #categorieslist li dd-content').first();
                           if(first.length>0){
                               first.click();
                           }else{
                               $('#insertcategory').hide();
                           }
                       }else{
                           bootbox.alert(result.response);
                       }
                   });
               }
           });
           return false;
       });
       
        /* Set the active category on menu click */
        $('#categorieslist .dd-content').unbind('click').click(function(e){
            id_category = $(this).parent().data('id-category');
            $('input[name=id_category]').val(id_category);
            updatepreview(id_category);
            $('#categorieslist li').removeClass('active');
            $(this).parent().addClass('active');
            return false;
        });
        
        $('#categorieslist .dd-content a.edit').unbind().click(function(e){
            e.stopPropagation();
            $this = this;
            link = $(this).parent().find('a span.title');
            oldTitle = link.text();
            $(link).attr('contentEditable',true);
            $(link).addClass('editable');
            $(link).selectText();

            $('#categorieslist a span.editable').bind('click.mm',hstop);  //let's click on the editable object
            $(link).bind('keypress.mm',hpress); //let's press enter to validate new title'
            $('*').not($(link)).bind('click.mm',houtside);

            function unbindall(){
                $('#categorieslist a span').unbind('click.mm',hstop);  //let's click on the editable object
                $(link).unbind('keypress.mm',hpress); //let's press enter to validate new title'
                $('*').not($(link)).unbind('click.mm',houtside);
            }

            //Validation       
            function hstop(event){
                event.stopPropagation();
                return false;
            }

            //Press enter
            function hpress(e){
                if ( e.which == 13 ) {
                    e.preventDefault();
                    unbindall();
                    updateTitle($(link).text());
                    $(link).removeAttr('contentEditable');
                    $(link).removeClass('editable');
                }
            }

            //click outside
            function houtside(e){
                unbindall();
                updateTitle($(link).text());
                $(link).removeAttr('contentEditable');
                $(link).removeClass('editable');
            }


            function updateTitle(title){
                id_category = $(link).parents('li').data('id-category');
                if(title!==''){
                    $.ajax({
                        url     :   "index.php?option=com_dropfiles&task=category.setTitle&id_category="+id_category+'&title='+title,
                        type    :   "POST"
                    }).done(function(data){
                        result = jQuery.parseJSON(data);
                        if(result===true){
                            return true;
                        }
                        $(link).text(oldTitle);
                        return false;
                    });
                }else{
                    $(link).text(oldTitle);
                    return false;
                }
                $(link).parent().css('white-space','normal');
                setTimeout(function(){
                    $(link).parent().css('white-space','');
                  }, 200);

            }
        });
    }
    
    (function(){
        $('#patchHtaccess').click(function(){
            $.ajax({
                url :   'index.php?option=com_dropfiles&view=patch&tmpl=component&format=raw'
            }).done(function(data){
                bootbox.alert(data);
            });
        });
    })();
    
    function loading(e){
        $(e).addClass('dploadingcontainer');
        $(e).append('<div class="dploading"></div>');
    }
    function rloading(e){
        $(e).removeClass('dploadingcontainer');
        $(e).find('div.dploading').remove();
    }
});

/**
* Insert the current category into a content editor
*/
function insertCategory(){
    id_category = jQuery('input[name=id_category]').val();
    dir = decodeURIComponent(getUrlVar('path'));
    code = '<img src="'+dir+'/components/com_dropfiles/assets/images/t.gif"'+
                'data-dropfilescategory="'+id_category+'"'+
                'style="background: url('+dir+'/components/com_dropfiles/assets/images/folder_download.png) no-repeat scroll center center #D6D6D6;'+
                'border: 2px dashed #888888;'+
                'height: 200px;'+
                'border-radius: 10px;'+
                'width: 99%;" data-category="'+id_category+'" />';
    return code;
}

/**
* Insert the current file into a content editor
*/
function insertFile(){
    id_file = jQuery('.file.selected').data('id-file');
    id_category = jQuery('input[name=id_category]').val();
    dir = decodeURIComponent(getUrlVar('path'));
    code = '<img src="'+dir+'/components/com_dropfiles/assets/images/t.gif"'+
                'data-dropfilesfile="'+id_file+'"'+
                'data-dropfilesfilecategory="'+id_category+'"'+
                'style="background: url('+dir+'/components/com_dropfiles/assets/images/file_download.png) no-repeat scroll center center #D6D6D6;'+
                'border: 2px dashed #888888;'+
                'height: 100px;'+
                'border-radius: 10px;'+
                'width: 99%;" data-file="'+id_file+'" />';
    return code;
}

//From http://jquery-howto.blogspot.fr/2009/09/get-url-parameters-values-with-jquery.html
function getUrlVars()
{
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

function getUrlVar(v){
    if(typeof(getUrlVars()[v])!=="undefined"){
        return getUrlVars()[v];
    }
    return null;
}

function preg_replace (array_pattern, array_pattern_replace, my_string) {var new_string = String (my_string);for (i=0; i<array_pattern.length; i++) {var reg_exp= RegExp(array_pattern[i], "gi");var val_to_replace = array_pattern_replace[i];new_string = new_string.replace (reg_exp, val_to_replace);}return new_string;}

//https://gist.github.com/ncr/399624
jQuery.fn.single_double_click = function(single_click_callback, double_click_callback, timeout) {
  return this.each(function(){
    var clicks = 0, self = this;
    jQuery(this).click(function(event){
      clicks++;
      if (clicks == 1) {
        setTimeout(function(){
          if(clicks == 1) {
            single_click_callback.call(self, event);
          } else {
            double_click_callback.call(self, event);
          }
          clicks = 0;
        }, timeout || 300);
      }
    });
  });
}
