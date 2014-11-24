ALTER TABLE `auth_user` ADD COLUMN `attempt_login` int  NOT NULL DEFAULT 0 AFTER `logged`;
ALTER TABLE `auth_user` ADD COLUMN `attempt_login_dt` datetime  NOT NULL AFTER `attempt_login`;

CREATE TABLE  `auth_user_history_password` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,  
  `password` VARCHAR(32) NOT NULL,
  `last_changed_date_password` datetime,  
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

UPDATE `auth_user` SET `changed_password_dt` = NOW();

INSERT INTO upgrade_db_log (
    file,
    branch,
    username,
    revision,
    repos_dt,
    insert_dt) 
values (substring_index(trim(both '$' from '$HeadURL: svn+sshp://radisa.no-ip.info/repos/web/apps/cms/branches/horisen_2015/docs/db/scripts/updb0006-auth_update.sql $'),'/',-1),
    'horisen_2015',
    trim(TRAILING '$' from substring('$Author: milan $',locate(':','$Author: milan $')+1)),
    trim(TRAILING '$' from substring('$Revision: 24895 $',locate(':','$Revision: 24895 $')+1)),
    trim(TRAILING '$' from substring('$Date: 2014-11-03 17:01:22 +0100 (пон, 03 нов 2014) $',locate(':','$Date: 2014-11-03 17:01:22 +0100 (пон, 03 нов 2014) $')+1)),
    now());

