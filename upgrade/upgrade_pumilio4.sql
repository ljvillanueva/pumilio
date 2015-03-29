ALTER TABLE `Sites` CHANGE `SiteLat` `SiteLat` DOUBLE NULL DEFAULT NULL ,CHANGE `SiteLon` `SiteLon` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `Sounds` CHANGE `Latitude` `Latitude` DOUBLE NULL DEFAULT NULL ,CHANGE `Longitude` `Longitude` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `Sounds` ADD `FileSize` INT NULL COMMENT 'Size of the file, in bytes' AFTER `OriginalFilename`;

UPDATE `PumilioSettings` SET `Value` = '5' WHERE `PumilioSettings`.`Settings` = 'db_version' LIMIT 1 ;
