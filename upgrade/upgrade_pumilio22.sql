
ALTER TABLE `Kml` CHANGE `KmlDefault` `KmlDefault` ENUM('0','1','2') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0';

UPDATE `PumilioSettings` SET `Value` = '23' WHERE `PumilioSettings`.`Settings` = 'db_version' LIMIT 1;

