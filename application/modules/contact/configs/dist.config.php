<?php

return array(
    'email' => array(
        'sender'    => array(
            "transport" => "smtp",
            "parameters" => array(
                "server" => "mail.horisen.com",
                "auth" => "login",
                "username" => "fbapp@horisen.biz",
                "password" => "Fbh0r1sen*9",
                "port" => "587"
            )                   
        ),
        "from_email" => "fbapp@horisen.biz",
        "from_name" => "HORISEN CMS",
        "reply_email" => "info@horisen.com",
        "to_emails" => array(
            array(
                "name" => "Milan",
                "email" => "milanr@horisen.com"
            )
        )                
    ),    
    'forms' => array(
        'contact' => array(
            'template'  => null,
            'email' => array(
                "confirmation_email" => "yes"
            ),
            'db' => array(
                'save'  => true
            ),
            'fields'    => array(
                'first_name'    => array(
                    'type'      => 'name',
                    'name'      => 'First name',
                    'placeholder'=> 'Your first name',
                    'required'  => true,
                    'maxlength' => 50,
                    'css_class' => 'form-group half-row'
                ),
                'last_name'     => array(
                    'type'      => 'name',
                    'name'      => 'Last name',
                    'placeholder'=> 'Your last name',
                    'required'  => true,
                    'maxlength' => 50,
                    'css_class' => 'form-group half-row right-half'
                ), 
                'company'     => array(
                    'type'      => 'name',
                    'name'      => 'Company',
                    'placeholder'=> 'Your company name',
                    'required'  => true,
                    'maxlength' => 50,
                    'css_class' => 'form-group'
                ),                 
                'email'         => array(
                    'name'      => 'Email',
                    'placeholder'=> 'e.g. your.name@example.com',
                    'type'      => 'email',
                    'required'  => true,
                    'maxlength' => 255,
                    'css_class' => 'form-group'
                ),
                'phone'         => array(
                    'type'      => 'phone',
                    'name'      => 'Phone',
                    'placeholder'=> 'e.g. +41 21 1234567',                    
                    'required'  => false,
                    'maxlength' => 50,
                    'css_class' => 'form-group half-row'
                ),
                'mobile'         => array(
                    'type'      => 'phone',
                    'name'      => 'Mobile',
                    'placeholder'=> 'e.g. +41 78 1234567',                    
                    'required'  => false,
                    'maxlength' => 50,
                    'css_class' => 'form-group half-row right-half'
                ),
                'zip'           => array(
                    'type'      => 'zip',
                    'name'      => 'Zip',              
                    'placeholder'=> 'e.g. 9400',
                    'required'  => false,
                    'maxlength' => 50,
                    'css_class' => 'form-group half-row field-new-line'
                ),
                'city'          => array(
                    'type'      => 'name',
                    'name'      => 'City',                    
                    'placeholder'=> 'e.g. Rorschach',
                    'required'  => false,
                    'maxlength' => 50,
                    'css_class' => 'form-group half-row right-half'
                ),
                'country'       => array(
                    'type'      => 'country',
                    'name'      => 'Country',
                    'placeholder'=> 'Choose your country',                    
                    'params'    => array(
                        'ip_country_detection' => true,
                        'selected_only' => false,
                        'selected_countries' => array('CH'),                        
                    ),
                    'css_class' => 'form-group'
                ),                
                'message'       => array(
                    'type'      => 'message',
                    'name'      => 'Message',                    
                    'placeholder'=> 'Your message',
                    'required'  => true,
                    'maxlength' => 500,
                    'css_class' => 'form-group messageBox'
                ),
                'honepot'       => array(
                    'type'      => 'honeypot'
                )
            )
        )
    )   
);
