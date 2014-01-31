ALTER TABLE `Kml` ADD `KmlDefault` ENUM( '0', '1' ) NOT NULL DEFAULT '0' AFTER `KmlURL`;

ALTER TABLE `Tags` CHANGE `Tag` `Tag` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

UPDATE `PumilioSettings` SET `Value` = '15' WHERE `PumilioSettings`.`Settings` = 'db_version' LIMIT 1;

