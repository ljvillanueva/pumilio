INSERT INTO `Sites` (`SiteName`) VALUES ('None');

ALTER TABLE `PumilioSettings` CHANGE `Value` `Value` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

UPDATE `PumilioSettings` SET `Value` = '11' WHERE `PumilioSettings`.`Settings` = 'db_version' LIMIT 1;
