INSERT INTO `cms_page_type` (`id`, `code`, `name`, `description`, `module`, `data`) VALUES
(20, 'api', 'API Page', 'API Page', 'cms', '{\r\n"delete":{\r\n"link":"cms/admin/page-delete" } }');


UPDATE `application` SET `settings` = '{\r\n  "hosting_quota": 524288000,\r\n  "theme": "horisen_2015",\r\n  "captcha": {\r\n    "fontName": "font4.ttf",\r\n    "wordLen": "3",\r\n    "timeout": "300",\r\n    "width": "150",\r\n    "height": "40",\r\n    "dotNoiseLevel": "20",\r\n    "lineNoiseLevel": "2"\r\n  },\r\n  "ga": {\r\n    "email": "nis@horisen.com",\r\n    "password": "h0r1sens0lut10ns",\r\n    "account_id": "58280383",\r\n    "tracking_id": "UA-30607857-1"\r\n  },\r\n  "default_upload": {\r\n    "default_extensions": [\r\n      "pjpeg",\r\n      "jpeg",\r\n      "jpg",\r\n      "png",\r\n      "x-png",\r\n      "gif",\r\n      "pdf"\r\n    ],\r\n    "default_mimetypes": [\r\n      "image/pjpeg",\r\n      "image/jpeg",\r\n      "image/jpg",\r\n      "image/png",\r\n      "image/x-png",\r\n      "image/gif",\r\n      "application/pdf"\r\n    ]\r\n  },\r\n  "page_types": {\r\n    "20": {\r\n      "template": "templates/api_page.phtml"\r\n    }\r\n  }\r\n}' WHERE `application`.`id` = 1;

UPDATE  `cms_page` SET  `type_id` =  '20' WHERE  `cms_page`.`id` =45;


INSERT INTO upgrade_db_log (
    file,
    branch,
    username,
    revision,
    repos_dt,
    insert_dt) 
values (substring_index(trim(both '$' from '$HeadURL: svn+sshp://radisa.no-ip.info/repos/web/apps/cms/branches/horisen_2015/docs/db/scripts/updb0020-api_pages.sql $'),'/',-1),
    'horisen_2015',
    trim(TRAILING '$' from substring('$Author: milan $',locate(':','$Author: milan $')+1)),
    trim(TRAILING '$' from substring('$Revision: 25157 $',locate(':','$Revision: 25157 $')+1)),
    trim(TRAILING '$' from substring('$Date: 2014-11-18 19:54:29 +0100 (уто, 18 нов 2014) $',locate(':','$Date: 2014-11-18 19:54:29 +0100 (уто, 18 нов 2014) $')+1)),
    now());

