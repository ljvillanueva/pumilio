ALTER TABLE `Queue` CHANGE `Status` `Status` ENUM( '0', '1', '2', '3', '4', '5' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '0 is not done, 1 is taken, 2 is done, 3 is error, 4 is hold, 5 is not found 404 error';

UPDATE `PumilioSettings` SET `Value` = '12' WHERE `PumilioSettings`.`Settings` = 'db_version' LIMIT 1;
