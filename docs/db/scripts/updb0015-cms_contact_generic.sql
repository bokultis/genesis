UPDATE `cms_route` SET `path` = 'contact/generic/index',`params` = 'form_id/contact' WHERE `cms_route`.`id` = 39;
UPDATE `cms_route` SET `path` = 'contact/generic/index',`params` = 'form_id/contact' WHERE `cms_route`.`id` = 57;

UPDATE `cms_menu_item` SET `path` = 'contact/generic/index',`params` = 'form_id/contact' WHERE `cms_menu_item`.`id` = 36;

ALTER TABLE  `contact` ADD  `company` VARCHAR( 100 ) NULL DEFAULT NULL AFTER  `last_name`;

INSERT INTO upgrade_db_log (
    file,
    branch,
    username,
    revision,
    repos_dt,
    insert_dt) 
values (substring_index(trim(both '$' from '$HeadURL: svn+sshp://radisa.no-ip.info/repos/web/apps/cms/branches/horisen_2015/docs/db/scripts/updb0015-cms_contact_generic.sql $'),'/',-1),
    'horisen_2015',
    trim(TRAILING '$' from substring('$Author: milan $',locate(':','$Author: milan $')+1)),
    trim(TRAILING '$' from substring('$Revision: 25079 $',locate(':','$Revision: 25079 $')+1)),
    trim(TRAILING '$' from substring('$Date: 2014-11-13 13:43:07 +0100 (чет, 13 нов 2014) $',locate(':','$Date: 2014-11-13 13:43:07 +0100 (чет, 13 нов 2014) $')+1)),
    now());

