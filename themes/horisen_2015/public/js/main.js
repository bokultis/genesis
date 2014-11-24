var menus = {
    'feedback':{
        'selector': '#feedback, .feedback-overlay',
        'btnSelector': '#feedbackToggle',
        'onClose': function(){
            $("html, body").removeClass("noscroll");
        },
        'onOpen': function(){
            $("html, body").addClass('noscroll');
        } 
    },
    'search': {
        'selector': '#search, .menu-overlay',
        'btnSelector': '#searchToggle'
    },
    'nav': {
        'selector': "nav[role='navigation'], .menu-overlay",
        'btnSelector': '#menuToggle',
        'onClose': function(){
            $("html, body").removeClass("noscroll");
        },
        'onOpen': function(){
            $("html, body").addClass('noscroll');
        }        
    }
}

function menuOpen(menu){
    //close all menus
    for(var currMenu in menus){
        if(menu != currMenu){
            menuClose(currMenu);
        }
    }
    //activate menu
    menus[menu].$el.addClass('on');
    menus[menu].$btn.addClass('on');
    if(menus[menu].onOpen){
        menus[menu].onOpen();
    }
}

function menuClose(menu){
    menus[menu].$el.removeClass('on');
    menus[menu].$btn.removeClass('on');
    if(menus[menu].onClose){
        menus[menu].onClose();
    }    
}

function menuCloseAll(){
    for(var menu in menus){
        menuClose(menu);
    }    
}

function menuToggle(menu){
    if(menus[menu].$el.hasClass('on')){
        menuClose(menu);
    }
    else{
        menuOpen(menu);
    }
}

function menuInit(){
    for(var menu in menus){
        menus[menu].$el = $(menus[menu].selector);
        menus[menu].$btn = $(menus[menu].btnSelector);
        menus[menu].$btn.data('menu',menu);
        menus[menu].$el.data('menu',menu);
        menus[menu].$btn.click(function(){
            menuToggle($(this).data('menu'));
            return false;
        });
        menus[menu].$el.click(function(e) {
            e.stopPropagation();
        });        
    }    
}

/* Jquery init */
$(document).ready(function(){
    var sliderSpeed = $('.bxslider').data('speed')? $('.bxslider').data('speed'): 5000;
    //bx-slider function
    var mainSlider  = $('.bxslider').bxSlider({
        auto: true,
        touchEnabled: true,
        pause: sliderSpeed,
        onSliderLoad: function(){
            $('.bxslider').css("visibility", "visible");
        }        
    });
    //slider might freez if resized during tranzition, so force reload
    $(window).resize(function(){
        mainSlider.reloadSlider();
    });   
    //init all menus
    menuInit();    
    //outside click close open menus
    $("body").click(function(){
        menuCloseAll();
    });
    //feedback tab
    $('.feedbackToggle').click(function(){
        menuToggle('feedback');
        return false;
    });
    //side menu - close button
    $(".menu-overlay, .main-menu-close").click(function(){
        menuToggle('nav');
    });
    //feedback menu close on blur
    $(".feedback-overlay").click(function(){
        menuToggle('feedback');
    });
    $(".menu-overlay").click(function(){
        menuToggle('search');
    });
    $(".footer-toggle").click(function(){
        $("body, body > footer, .footer-toggle").toggleClass("footer-expanded");
        //$("body").toggleClass("footer-expanded");
    });
    //feedback form
    $("#horisenFeedback").ajaxSubmit({
        fieldSelector: '[name="data\\[{field}\\]"]',
        onSubmit: function(data, errors){
            if(data.success){
                //reset form and close drawer
                $('#horisenFeedback').get(0).reset();
                //menuClose('feedback');
                //show message
                var message = data['message']? data['message']: 'Thank you';
                //alert(message);
                $(".feedbackContainer").addClass('flipped');
            }
            else{
                //alert(errors.join("\n"));
            }
        }
    });
});

//collapse elements
$(document).ready(function(){
    //add arrow
    $(".collapseContent > h3").append('<i class="fa fa-play collapseArrow"></i>');
    $(".collapseContent").each(function(){        
        $(this).data('id', Math.random().toString(36).substring(7));
        var $moreHandle = $('<a class="showMore" href="#"></a>');
        var $parentCollapse = $(this);
        $parentCollapse.append($moreHandle);
        $moreHandle.click(function(){
            //toggle status for all sibling boxes
            $(this).parents('.collapseContent').parent('.row').find('.collapseContent').each(function(){
                //check if not the originator
                if($(this).data('id') != $parentCollapse.data('id')){
                    $(this).collapsibleBox('setStatus', [!$parentCollapse.collapsibleBox('getStatus'),true]);
                }                
            });
        });        
    });
    
    $(".collapseContent").collapsibleBox({
        handleSelector: "a.showMore,h3",
        collapsibleSelector: ".collapseContentBox",
        openClass: "open",
        closedClass: "closed",
        onChange: function(el, closed){
        //console.log(el, "is closed: ", closed);
        },
        getIsClosed: function(widget){            
            //console.log("heights .collapseContent, h3, .collapseContentBox", widget.$el.height(), widget.$el.find("h3").outerHeight(true), widget.$el.find(".collapseContentBox").outerHeight(true));
            var height = widget.$el.height();
            var elementsHeight = widget.$el.find("h3").outerHeight(true) + widget.$el.find(".collapseContentBox").outerHeight(true);
            var closed = elementsHeight > height;
            return {
                "closed": closed,
                "toggable": closed 
            };
        }            
    });   
    
    //add arrow
    $(".collapsibleSection > h2").before('<i class="fa fa-play collapseArrow"></i>');
    $(".collapsibleSection > h3").before('<i class="fa fa-chevron-right collapseArrow"></i>');
    $(".collapsibleSection").collapsibleBox({
        handleSelector: ".collapseArrow,h2,h3",
        collapsibleSelector: ">div",
        openClass: "open",
        closedClass: "closed",
        getIsClosed: function(widget){
            return {
                toggable: true,
                closed: true
            }
        }        
    });
    
    $("a.group").fancybox();
        
})