
CREATE TABLE IF NOT EXISTS `PumilioLog` (
  `LogID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `LogType` int(11) NOT NULL,
  `SoundID` int(11) NOT NULL,
  `LogText` text COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`LogID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101;

ALTER TABLE `Sounds` DROP  `PhysicalArchiveID` , DROP  `PhysicalArchiveType` , DROP  `PhysicalArchiveSoundID`;

ALTER TABLE `Sounds`  ADD `SoundStatus` SMALLINT NOT NULL DEFAULT '0' COMMENT 'Status of this record, 0 is as inserted, 1 is modified, 9 is deleted' AFTER `SoundID`;

ALTER TABLE `Equipment` CHANGE `EquipmentID` `SensorID` INT(11) NOT NULL AUTO_INCREMENT;

RENAME TABLE `Equipment` TO  `Sensors`;

ALTER TABLE  `Sounds` CHANGE  `EquipmentID`  `SensorID` INT( 11 ) NULL DEFAULT NULL;

INSERT INTO `Sensors` (`SensorID`, `Recorder`, `Microphone`, `Notes`) VALUES (NULL, 'Wildlife Acoustics SM1', '', ''), (NULL, 'Wildlife Acoustics SM2', '', '');

ALTER TABLE `Sites`  ADD `SiteElevation` DOUBLE NULL DEFAULT NULL AFTER `SiteLon`;

ALTER TABLE `Sites`  ADD `SiteNotes` TEXT NULL DEFAULT NULL AFTER `SiteName`;

ALTER TABLE `Sounds`  ADD `DerivedSound` ENUM('0','1') NOT NULL DEFAULT '0' COMMENT 'Is this soundfile derived from another in the database?' AFTER `OtherSoundID`,  ADD `DerivedFromSoundID` INT NULL DEFAULT NULL COMMENT 'The SoundID of the file from which this row is derived' AFTER `DerivedSound`,  ADD INDEX (`DerivedSound`, `DerivedFromSoundID`);

ALTER TABLE `Sounds`  ADD `QualityFlag` SMALLINT NOT NULL DEFAULT '0' COMMENT 'Quality flag of the file' DEFAULT '0' AFTER `SoundStatus`;

ALTER TABLE `Sounds` CHANGE `MP3Filename` `AudioPreviewFilename` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `Sounds`  ADD `AudioPreviewFormat` VARCHAR(5) NOT NULL DEFAULT 'mp3' AFTER `AudioPreviewFilename`;

ALTER TABLE `Sounds` DROP `Location`, DROP `Latitude`, DROP `Longitude`;

CREATE TABLE IF NOT EXISTS `QualityFlags` (`QualityFlagID` float NOT NULL, `QualityFlag` varchar(40) COLLATE utf8_unicode_ci NOT NULL, PRIMARY KEY (`QualityFlagID`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `QualityFlags` (`QualityFlagID`, `QualityFlag`) VALUES (0, 'Unknown'), (1, 'File checked and OK');

ALTER TABLE `Sounds` CHANGE `QualityFlag` `QualityFlagID` FLOAT NOT NULL DEFAULT '0' COMMENT 'Quality of the file';

INSERT INTO `PumilioSettings` (`Settings`, `Value`) VALUES ('default_qf', 0);

ALTER TABLE `Tokens`  ADD `timestamp` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

update `Tokens` set `timestamp` = NOW() WHERE `timestamp`='0000-00-00 00:00:00';

UPDATE `PumilioSettings` SET `Value` = '18' WHERE `PumilioSettings`.`Settings` = 'db_version' LIMIT 1;

