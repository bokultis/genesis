DELETE FROM  `translate_language` WHERE  `translate_language`.`id` =4;
DELETE FROM  `translate_language` WHERE  `translate_language`.`id` =5;

UPDATE  `translate_language` SET  `default` =  'no' WHERE  `translate_language`.`id` =1;
UPDATE  `translate_language` SET  `default` =  'yes' WHERE  `translate_language`.`id` =2;

TRUNCATE TABLE  `translate`;

INSERT INTO upgrade_db_log (
    file,
    branch,
    username,
    revision,
    repos_dt,
    insert_dt) 
values (substring_index(trim(both '$' from '$HeadURL: svn+sshp://radisa.no-ip.info/repos/web/apps/cms/branches/horisen_2015/docs/db/scripts/updb0003-trans_changes.sql $'),'/',-1),
    'horisen_2015',
    trim(TRAILING '$' from substring('$Author: milan $',locate(':','$Author: milan $')+1)),
    trim(TRAILING '$' from substring('$Revision: 24843 $',locate(':','$Revision: 24843 $')+1)),
    trim(TRAILING '$' from substring('$Date: 2014-10-30 14:11:39 +0100 (чет, 30 окт 2014) $',locate(':','$Date: 2014-10-30 14:11:39 +0100 (чет, 30 окт 2014) $')+1)),
    now());
