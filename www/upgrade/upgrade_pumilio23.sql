
ALTER TABLE `Sites`  ADD `SiteURL` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT 'URL for more info on the site, can be relative to the installation.' AFTER `WeatherSiteID`;

ALTER TABLE `SoundsImages`  ADD `ImageCreator` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'svt' COMMENT 'Which program created the images.' AFTER `SpecMaxFreq`;

ALTER TABLE `SoundsImages` CHANGE `ColorPalette` `ColorPalette` INT(11) NOT NULL DEFAULT '1' COMMENT 'The color palette used for the images. For either svt.py or SoX';

UPDATE `PumilioSettings` SET `Value` = '24' WHERE `Settings` = 'db_version' LIMIT 1;

