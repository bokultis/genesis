UPDATE  `application` SET  `twitter_settings` =  '{}',
`og_settings` =  '{}' WHERE  `application`.`id` =1;

DELETE FROM `auth_privilege` WHERE id = 2;
DELETE FROM `auth_resource` WHERE id = 2;
DELETE FROM `auth_role` WHERE `auth_role`.`id` = 3;
DELETE FROM `auth_role` WHERE `auth_role`.`id` = 4;
DELETE FROM `auth_acl` WHERE `auth_acl`.`id` = 2;
DROP TABLE `cms_bgchanger`, `cms_content_user`;
DELETE FROM `module` WHERE `id` = 70;

INSERT INTO upgrade_db_log (
    file,
    branch,
    username,
    revision,
    repos_dt,
    insert_dt) 
values (substring_index(trim(both '$' from '$HeadURL: svn+sshp://radisa.no-ip.info/repos/web/apps/cms/branches/horisen_2015/docs/db/scripts/updb0004-setup.sql $'),'/',-1),
    'horisen_2015',
    trim(TRAILING '$' from substring('$Author: milan $',locate(':','$Author: milan $')+1)),
    trim(TRAILING '$' from substring('$Revision: 24863 $',locate(':','$Revision: 24863 $')+1)),
    trim(TRAILING '$' from substring('$Date: 2014-10-31 14:00:45 +0100 (пет, 31 окт 2014) $',locate(':','$Date: 2014-10-31 14:00:45 +0100 (пет, 31 окт 2014) $')+1)),
    now());
