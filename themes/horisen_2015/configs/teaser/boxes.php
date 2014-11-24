<?php


return array(
    "slider-main" => array(
        "code" => "slider-main",
        "name" => "Main Slider",
        "template" => "slider-main.phtml",
        "icon"  => '/themes/horisen_2015/images/teaser/previews/slider-main.jpg',
        "type" => "slider",
        "elements" => array("images", "link", "speed"),
        "custom_item_elements"  => array(
            "css_id"   => array(
                "label" => "CSS ID",
                "type"  => "text"
            ),
            "css_text"   => array(
                "label" => "Custom CSS",
                "type"  => "textarea"
            ),              
        ),        
        "params" => array(
            "images_dims" => array(1920, 370, 1366, 540, 1366, 540, 960, 350, 960, 350, 768, 210, 768, 210, 480, 180, 480, 180),
            'images_section'    => 'slider'
        )
    ),
    "intro-slider" => array(
        "code" => "intro-slider",
        "name" => "Intro Slider",
        "template" => "intro-slider.phtml",
        "icon"  => '/themes/horisen_2015/images/teaser/previews/intro-slider.jpg',
        "type" => "slider",
        "elements" => array("subtitle", "images", "link", "speed"),
        "custom_item_elements"  => array(
            "button_text"   => array(
                "label" => "Button text",
                "type"  => "text"
            ),
            "css_id"   => array(
                "label" => "CSS ID",
                "type"  => "text"
            ),
            "css_text"   => array(
                "label" => "Custom CSS",
                "type"  => "textarea"
            ),              
        ),
        "params" => array(
            'images'    => array(
                'img_p'  => array(
                    "name" => "Image for portrait orientation",
                    "media_query" => "(orientation: portrait)",
                    "options" => array(
                        "minwidth" => 1920,
                        "maxwidth" => 1920,
                        "minheight" => 1920,
                        "maxheight" => 1920
                    )
                ),
                'img_l'  => array(
                    "name" => "Image for landscape orientation",
                    "media_query" => "(orientation: landscape)",
                    "options" => array(
                        "minwidth" => 1920,
                        "maxwidth" => 1920,
                        "minheight" => 1920,
                        "maxheight" => 1920
                    )
                ),
                'img_1920'  => array(
                    "name" => "Image for big screen viewport width 1920+",
                    "media_query" => "(min-width: 1921px)",
                    "options" => array(
                        "minwidth" => 2560,
                        "maxwidth" => 2560,
                        "minheight" => 1600,
                        "maxheight" => 1600
                    )
                )
            )
        )
    ),
    "teaser1" => array(
        "code" => "teaser1",
        "name" => "Teaser1",
        "template" => "teaser.phtml",
        "icon"  => '/themes/horisen_2015/images/teaser/previews/teaser1.jpg',
        "type" => "teaser",
        "elements" => array(
            "subtitle",
            "images",
            "link",
            "text"
        ),
        "params" => array(
            "images_dims" => array(333, 333, 960, 576, 333, 333, 333, 333, 480, 287, 480, 287, 640, 370, 320, 185)
        )
    ),
    "teaser2" => array(
        "code" => "teaser2",
        "name" => "Teaser2",
        "template" => "teaser.phtml",
        "icon"  => '/themes/horisen_2015/images/teaser/previews/teaser2.jpg',
        "type" => "teaser",
        "elements" => array(
            "subtitle",
            "images",
            "link",
            "text"
        ),
        "params" => array(
            "images_dims" => array(333, 333, 960, 576, 333, 333, 333, 333, 480, 287, 480, 287, 640, 370, 320, 185)
        )
    ),
    "teaser3" => array(
        "code" => "teaser3",
        "name" => "Teaser3",
        "template" => "teaser.phtml",
        "icon"  => '/themes/horisen_2015/images/teaser/previews/teaser3.jpg',
        "type" => "teaser",
        "elements" => array(
            "subtitle",
            "images",
            "link",
            "text"
        ),
        "params" => array(
            "images_dims" => array(333, 333, 960, 576, 333, 333, 333, 333, 480, 287, 480, 287, 640, 370, 320, 185)
        )
    ),
    "teaser4" => array(
        "code" => "teaser4",
        "name" => "Teaser4",
        "template" => "teaser.phtml",
        "icon"  => '/themes/horisen_2015/images/teaser/previews/teaser4.jpg',
        "type" => "teaser",
        "elements" => array(
            "subtitle",
            "images",
            "link",
            "text"
        ),
        "params" => array(
            "images_dims" => array(333, 333, 960, 576, 333, 333, 333, 333, 480, 287, 480, 287, 640, 370, 320, 185)
        )
    )
);