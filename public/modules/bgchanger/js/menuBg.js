    function getEditUrl(id,lang,menu){
        if(!lang || lang == null || lang == undefined){
            return sprintf('/%s/bgchanger/admin-menu/menu-edit/id/%d/langFilter/%s/menuFilter/%s',CURR_LANG,id,CURR_LANG,menu);
        }
        return sprintf('/%s/bgchanger/admin-menu/menu-edit/id/%d/langFilter/%s/menuFilter/%s',CURR_LANG,id,lang,menu);
    }
    
    //update list view when one param is changed
    function updateList(paramName,paramValue){
        var params = $("#listContainer").data('params');
        params[paramName] = paramValue;
        $("#listContainer").hfbList({
            'params': params
        });
    }
    
    function editDialog(id, parent_selected){
            ajaxForm.dialog(getEditUrl(id,$("#langFilter").val(),$("#menuFilter").val()),{
                onContent: function(dialog){
                    parent_selected = (parent_selected != '')?parent_selected:$('#parent_selected').val();
                    populateMenuItem(menuItemData, parent_selected, id);
                    
                    //var boxCode = dialog.find('#data\\[box_code\\]').val();
                    
                    //additional images                
                    renderImages(dialog.find('#images'));
                    
                    //upload ZIP package
                    $("#imagePackage").filebrowserdialog({
                        "fileWebRoot": fileWebRoot,
                        "activeModule": "bgchanger",
                        "extensions": "zip",
                        "onSelect": function(zipFile){
                            $.post("/" + CURR_LANG + "/bgchanger/admin-menu/unzip", {
                                'zip_file': zipFile
                            },function( result ) {
                                if(!result.success){
                                    var message = (result.message)? result.message: 'Unknown error';
                                    $.flashMessenger(message,{
                                        autoClose:false,
                                        modal:true,
                                        clsName:"err"
                                    });                                
                                    return false;
                                }
                                //set image paths
                                for(var imagePath in result.images){
                                    for(var i = 0; i < result.images[imagePath].length; i++){
                                        var imageKey  = result.images[imagePath][i];
                                        
                                        dialog.find('#data\\[content\\]\\[' + imageKey + '\\]').val(imagePath);
                                    }                                
                                }

                            });                        
                        }
                    })
   
                },
                onClose: function(success){
                    //firebug fix
                    if(success){
                        updateList();
                    }
                },
                width: 'auto',
                height: 'auto'
            });
    }
    
    function populateMenuItem(menuItemData, selected, id){
        var sel = $("#data\\[parent_id\\]");
        sel.empty();
        sel.append('<option value="0">' + 'No Parent ' + '</option>');
        for (var i=0; i< menuItemData.length; i++) {
            var sele = (menuItemData[i].id == selected)? ' selected="selected" ' : '';
            var styleSelfMenu = ''; 
            var selfMenu = '';
            if(menuItemData[i].id == id){
                styleSelfMenu = 'font-style: italic;';
                selfMenu = ' disabled="disabled" ';
            }
            sel.append('<option value="' + menuItemData[i].id + '"'+ sele + selfMenu + ' style="'+styleSelfMenu+'padding-left:'+ menuItemData[i].level * 15 +'px">' + menuItemData[i].name + '</option>');
        }
    }
    
    function populateMenu(menuFilterData, selected){
        var sel = $("#menuFilter");
        sel.empty();
        for (var i=0; i< menuFilterData.length; i++) {
          var sele = (menuFilterData[i].code == selected)? 'selected="selected"' : '';
          sel.append('<option value="' + menuFilterData[i].code + '"'+ sele +'>' + menuFilterData[i].name + '</option>');
        }
    }
    
    
    
    //render all defined images input boxes
    function renderImages(container){
        container.find(".customImage").remove();
        if(!settings['bgchanger']){
            return;
        }

        var imagesHtml = $.fn.hfbList.tmpl('images_tpl',{
            "images": settings['bgchanger']['params']['images'],
            "data"  : itemData
        });
        container.html(imagesHtml);
        container.find(".backgroundImage").each(function(){
            var options = {
                "fileWebRoot": fileWebRoot,
                "activeModule": "bgchanger"
            };
            var customImageData = settings['bgchanger']['params']['images'][$(this).data("id")];
            var imgOptions = (customImageData['options'])? customImageData['options']: {};
            options = $.extend({}, options, imgOptions);
            $(this).imagebrowserdialog(options);
        });
    }
    
    //init select filter
    $(document).ready(function(){
        initList(listUrl,{},function(me,data){
            $("#listContainer").find("a.edit").click(function(){
                editDialog($(this).data('id'), '');
                return false;
            })
            $("#listContainer").find("a.add").click(function(){
                editDialog('',$(this).data('id'));
                return false;
            })
        });
        $("#langFilter").change(function(){
            updateList('langFilter',$(this).val());
        });
        $("#menuFilter").change(function(){
            updateList('menuFilter',$(this).val());
        });
        $(".add").click(function(){
            editDialog(null);
            return false;
        });
    });