<?php

return array(
    array(
        'id' => 'home',
        'label' => 'Home',
        'title' => 'Home',
        'module' => 'admin',
        'controller' => 'index',
        'action' => 'index',
        'order' => 10,
        'class' => 'home',
        'iconClass' => 'dash -home',
        'pages' => array(
        )
    ),
    array(
        'id' => 'configuration',
        'label' => 'Configuration',
        'title' => 'Configuration',
        'module' => 'admin',
        'controller' => 'application',
        'action' => 'edit',
        'order' => 20,
        'class' => 'configuration',
        'iconClass' => 'dash -configuration',
        'resource'  => 'admin',
        'privilege' => 'master',
        'pages' => array(
            array(
                'id' => 'contact-config',
                'label' => 'Contact',
                'title' => 'Contact',
                'module' => 'contact',
                'controller' => 'admin',
                'action' => 'config',
                'params' => array('type' => 'contact'),
                'order' => 10,
                'class' => 'contact',
                'resource'  => 'admin',
                'privilege' => 'master',
                'iconClass' => 'dash -submenu_bullet'
            ),
            
            array(
                'id' => 'cms-config',
                'label' => 'CMS',
                'title' => 'CMS',
                'module' => 'cms',
                'controller' => 'admin-config',
                'action' => 'config',
                'order' => 30,
                'class' => 'cms',
                'resource'  => 'admin',
                'privilege' => 'master',
                'iconClass' => 'dash -submenu_bullet'
            ),
             
        )
    ),
    array(
        'id' => 'menu-background',
        'label' => 'Backgrounds',
        'title' => 'Backgrounds',
        'module' => 'bgchanger',
        'controller' => 'admin-menu',
        'action' => 'menu',
        'order' => 31,
        'class' => 'backgrounds',
        'iconClass' => 'dash -backgrounds',
    ),
    array(
        'id' => 'pages',
        'label' => 'Content',
        'title' => 'Content',
        'module' => 'cms',
        'controller' => 'admin',
        'params' => array('type_code'=>'page'),
        'action' => 'page',
        'order' => 30,
        'class' => 'pages',
        'iconClass' => 'dash -pages',
        'pages' => array(
             array(
                'id' => 'pages',
                'label' => 'Pages',
                'title' => 'Pages',
                'module' => 'cms',
                'controller' => 'admin',
                'params' => array('type_code'=>'page'),
                'action' => 'page',                
                'order' => 1,
                'class' => 'pages',
                 'iconClass' => 'dash -submenu_bullet'
            ),     
            array(
                'id' => 'content_blocks',
                'label' => 'Content blocks',
                'title' => 'Content blocks',
                'module' => 'cms',
                'controller' => 'admin',
                'action' => 'page',
                'params' => array('type_code'=>'contentblock'),
                'order' => 80,
                'class' => 'pages',
                'iconClass' => 'dash -submenu_bullet'
            )
        )
    ),
    array(
        'id' => 'menu',
        'label' => 'Menus',
        'title' => 'Menus',
        'module' => 'cms',
        'controller' => 'admin-menu',
        'action' => 'menu',
        'order' => 40,
        'class' => 'menu',
        'iconClass' => 'dash -menu',
        'resource'  => 'admin',
        'privilege' => 'master',
        'pages' => array(
            array(
            'id' => 'route',
            'label' => 'Route',
            'title' => 'Route',
            'module' => 'cms',
            'controller' => 'admin-route',
            'action' => 'index',
            'order' => 10,
            'class' => 'menu',
            'resource'  => 'admin',
            'privilege' => 'master',
            'iconClass' => 'dash -submenu_bullet'
            ),
             array(
            'id' => 'redirect',
            'label' => 'Redirection',
            'title' => 'Redirection',
            'module' => 'cms',
            'controller' => 'admin-redirect',
            'action' => 'index',
            'order' => 10,
            'class' => 'menu',
            'resource'  => 'admin',
            'privilege' => 'master',
            'iconClass' => 'dash -submenu_bullet'
        )
        )
    ),
    array(
        'id' => 'user_roles',
        'label' => 'Users',
        'title' => 'Users',
        'module' => 'auth',
        'controller' => 'admin-user',
        'action' => 'user',
        'order' => 70,
        'class' => 'users',
        'iconClass' => 'dash -users',
        'resource'  => 'admin',
        'privilege' => 'master',
        'pages' => array(
              array(
                'id' => 'users',
                'label' => 'Users',
                'title' => 'Users',
                'module' => 'auth',
                'controller' => 'admin-user',
                'action' => 'user',
                'order' => 10,
                'class' => 'users',
                'resource'  => 'admin',
                'privilege' => 'master',
                'iconClass' => 'dash -submenu_bullet'
            ),
            array(
                'id' => 'roles',
                'label' => 'Roles',
                'title' => 'Roles',
                'module' => 'auth',
                'controller' => 'admin-role',
                'action' => 'role',
                'order' => 20,
                'class' => 'roles',
                'resource'  => 'admin',
                'privilege' => 'master',
                'iconClass' => 'dash -submenu_bullet'
            )
        )
    ),
   
    array(
        'id' => 'contact',
        'label' => 'Contact',
        'title' => 'Contact',
        'module' => 'contact',
        'controller' => 'admin',
        'action' => 'contact',
        'order' => 90,
        'class' => 'menu',
        'iconClass' => 'dash -contact',
        'pages' => array(
        )
    ),
  
    array(
        'id' => 'filemanager',
        'label' => 'File Manager',
        'title' => 'File Manager',
        'module' => 'admin',
        'controller' => 'index',
        'action' => 'filemanager',
        'order' => 110,
        'class' => 'filemanager',
        'iconClass' => 'dash -filemanager',
        'pages' => array(
        )
    ),
    
    array(
        'id' => 'translation',
        'label' => 'Translation',
        'title' => 'Translation',
        'module' => 'translation',
        'controller' => 'admin',
        'action' => 'index',
        'order' => 120,
        'class' => 'translation',
        'iconClass' => 'dash -translation',
        'pages' => array(
              array(
                'id' => 'menu',
                'label' => 'Menu',
                'title' => 'Menu',
                'module' => 'translation',
                'controller' => 'admin-menu',
                'action' => 'index',
                'order' => 10,
                'class' => 'menu',
                'iconClass' => 'dash -submenu_bullet'
            ),
            array(
                'id' => 'type',
                'label' => 'Type',
                'title' => 'Type',
                'module' => 'translation',
                'controller' => 'admin-type',
                'action' => 'index',
                'order' => 20,
                'class' => 'type',
                'iconClass' => 'dash -submenu_bullet'
            ),
            array(
                'id' => 'lang',
                'label' => 'Language',
                'title' => 'Language',
                'module' => 'translation',
                'controller' => 'admin-lang',
                'action' => 'index',
                'order' => 30,
                'class' => 'lang',
                'resource'  => 'admin',
                'privilege' => 'master',
                'iconClass' => 'dash -submenu_bullet'
            )
        )
    ),
    array(
        'id' => 'teaser',
        'label' => 'Teaser',
        'title' => 'Teaser',
        'module' => 'teaser',
        'controller' => 'admin-teaser',
        'action' => 'index',
        'order' => 50,
        'class' => 'slider',
        'iconClass' => 'dash -teasers',
        'pages' => array()
    )    
);
