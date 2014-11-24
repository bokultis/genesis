DELETE FROM `cms_page_type` WHERE `cms_page_type`.`id` = 2;
DELETE FROM `cms_page_type` WHERE `cms_page_type`.`id` = 13;

INSERT INTO upgrade_db_log (
    file,
    branch,
    username,
    revision,
    repos_dt,
    insert_dt) 
values (substring_index(trim(both '$' from '$HeadURL: svn+sshp://radisa.no-ip.info/repos/web/apps/cms/branches/horisen_2015/docs/db/scripts/updb0007-page_types.sql $'),'/',-1),
    'horisen_2015',
    trim(TRAILING '$' from substring('$Author: milan $',locate(':','$Author: milan $')+1)),
    trim(TRAILING '$' from substring('$Revision: 24895 $',locate(':','$Revision: 24895 $')+1)),
    trim(TRAILING '$' from substring('$Date: 2014-11-03 17:01:22 +0100 (пон, 03 нов 2014) $',locate(':','$Date: 2014-11-03 17:01:22 +0100 (пон, 03 нов 2014) $')+1)),
    now());

