    function getEditUrl(id, setId, language){
        return sprintf('/%s/cms/admin-category/category-edit/id/%d/set_id/%d/language/%s',CURR_LANG, id, setId, language);
    }
    
    function getCategoryEditUrl(){
        return sprintf('/%s/cms/admin-category/edit-main-category/',CURR_LANG);
    }
    function editDialog(id){
        ajaxForm.dialog(getEditUrl(id, $("#categorySetFilter").val(), $("#langFilter").val()),{
            onContent: function(dialog){
                if(!$('#data-data-show_pic').attr("checked")){
                    $('.show_picture').css('display', 'none');
                }
                $('#data-data-show_pic').change(function() {
                    if($('#data-data-show_pic').attr("checked")) {
                        $('.show_picture').css('display', '');
                    } else {
                        $('.show_picture').css('display', 'none');
                    }
                });
                var fileInput = dialog.find("#data\\[data\\]\\[picture\\]");
                $(fileInput).filebrowserdialog({
                    "initPath": $(fileInput).val(),
                    "fileWebRoot":window.fileWebRoot,
                    "extensions":"gif,png,jpg,jpeg"
                });
                populateCategoryItem(categoryItemData, $('#parent_selected').val() );

                $("#data\\[name\\]").blur(function(){
                    if($(this).val() != '' && $("#data\\[url_id\\]").val() == ''){
                        $.post("/" + CURR_LANG + "/cms/admin-category/url-id",{"name":$(this).val(),"lang":$("#langFilter").val()},
                        function(data){
                            $("#data\\[url_id\\]").val(data.url_id);
                        });
                    }
                });
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
    
    function populateCategoryItem(categoryItemData, selected){
        var thisId = $("#data\\[id\\]").val();
        var sel = $("#data\\[parent_id\\]");
        sel.empty();
        sel.append('<option value="0">' + 'No Parent ' + '</option>');
        if(categoryItemData){
            for (var i=0; i< categoryItemData.length; i++) {
                if(thisId == "" || thisId != categoryItemData[i].id){
                    var sele = (categoryItemData[i].id == selected)? 'selected="selected"' : '';
                    sel.append('<option value="' + categoryItemData[i].id + '"'+ sele +' style="padding-left:'+ categoryItemData[i].level * 15 +'px">' + categoryItemData[i].name + '</option>');
                }
            }
        }
    }
    
    function populateCategory(categoryFilterData, selected){
        var sel = $("#categorySetFilter");
        sel.empty();
        for (var i=0; i< categoryFilterData.length; i++) {
          var sele = (categoryFilterData[i].id == selected)? 'selected="selected"' : '';
          sel.append('<option value="' + categoryFilterData[i].id + '"'+ sele +'>' + categoryFilterData[i].description + '</option>');
        }
    }

    function categorySetExist(){
        var sel = $("#categorySetFilter");
        if(sel.val() != ""){
            return true;
        }else{
            return false;
        }
    }
    
    function editMainCategory(){
        $.ajax({
            url: getCategoryEditUrl(),
            type: "POST",
            data: $("#categoryForm").serialize(),
            success: function(res){
                if(res.success){
                    populateCategory(res.set, res.selected)
                    $("#inputNewCategory").hide();
                    $("#newCategory").show();
                    updateList('categorySetFilter', res.selected);
                    $.flashMessenger(res.message,{clsName:"ok"});
                }else{
                    var errors = {};
                    ajaxForm.parseErrors('data',null,res['message'],errors);
                    for(var field in errors){
                        var errorUl = '<ul class="error">';
                        for(var i = 0; i < errors[field].length; i++){
                            errorUl += '<li>' + errors[field][i] + '</li>';
                        }
                        errorUl += '</ul>';
                        $(ajaxForm.jqId(field)).parent().append(errorUl);
                    }
                }
            }
        });
    }
    //init select filter
    $(document).ready(function(){
        initList('/' + CURR_LANG + '/cms/admin-category/category-list/?categorySetFilter='+$("#categorySetFilter").val(),{},function(me,data){
            $("#listContainer").find("a.edit").click(function(){
                editDialog($(this).data('id'));
                return false;
            })
        });
        $("#newCategory").click(function(){
            $("#newCategory").hide();
            $("#inputNewCategory").show();
            return false;
        })
        $("#cancelCategory").click(function(){
            $("#inputNewCategory").hide();
            $("#newCategory").show();
            $(".error").remove();
            return false;
        })
        $("#saveCategory").click(function(){
            $(".error").remove();
            editMainCategory();
            return false;
        })
        $("#categorySetFilter").change(function(){
            updateList('categorySetFilter',$(this).val());
        });
        $("#langFilter").change(function(){
            updateList('language',$(this).val());
        });
        $(".add").click(function(){
            if( categorySetExist() ){
                editDialog(null);
                return false;
            }else{
                alert(_("Please select Category Set."));
                return false;
            }
        });
    });
    

