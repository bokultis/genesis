INSERT INTO upgrade_db_log (
    file,
    branch,
    username,
    revision,
    repos_dt,
    insert_dt) 
values (substring_index(trim(both '$' from '$HeadURL: svn+sshp://radisa.no-ip.info/repos/web/apps/cms/branches/horisen_2015/docs/db/scripts/updbXXXX.tpl.sql $'),'/',-1),
    'trunk',
    trim(TRAILING '$' from substring('$Author: milan $',locate(':','$Author: milan $')+1)),
    trim(TRAILING '$' from substring('$Revision: 24892 $',locate(':','$Revision: 24892 $')+1)),
    trim(TRAILING '$' from substring('$Date: 2014-11-03 16:52:23 +0100 (пон, 03 нов 2014) $',locate(':','$Date: 2014-11-03 16:52:23 +0100 (пон, 03 нов 2014) $')+1)),
    now());
