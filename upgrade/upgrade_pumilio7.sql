ALTER TABLE `Sounds`  ADD `MD5_hash` CHAR(32) NULL COMMENT 'MD5 hash of the file, to verify that the file has not been changed.' AFTER `FileSize`;


ALTER TABLE `Sounds` CHANGE `PhysicalArchiveID` `PhysicalArchiveID` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL, CHANGE `PhysicalArchiveType` `PhysicalArchiveType` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL, CHANGE `PhysicalArchiveSoundID` `PhysicalArchiveSoundID` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;


--
-- Table structure for table `ProcessLog`
--

CREATE TABLE IF NOT EXISTS `ProcessLog` (
  `LogID` int(11) NOT NULL AUTO_INCREMENT,
  `QueueID` int(11) NOT NULL,
  `Computer` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `TimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `FileLog` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`LogID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101 ;


--
-- Table structure for table `Queue`
--

CREATE TABLE IF NOT EXISTS `Queue` (
  `QueueID` int(11) NOT NULL AUTO_INCREMENT,
  `JobID` int(11) NOT NULL,
  `SoundID` int(11) DEFAULT NULL,
  `ScriptID` int(11) NOT NULL DEFAULT '104',
  `Priority` enum('0','1','2','3') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `Status` enum('0','1','2','3','4') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '0 is not done, 1 is taken, 2 is done, 3 is error, 4 is hold',
  `LastChange` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ClaimedDate` timestamp NULL DEFAULT NULL,
  `ProcessDoneDate` timestamp NULL DEFAULT NULL,
  `ComputerDone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Name of computer that completed the task',
  PRIMARY KEY (`QueueID`),
  KEY `Status` (`Status`),
  KEY `Priority` (`Priority`),
  KEY `ProcessDoneComputer` (`ComputerDone`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101 ;

--
-- Table structure for table `Scripts`
--

CREATE TABLE IF NOT EXISTS `Scripts` (
  `ScriptID` int(11) NOT NULL AUTO_INCREMENT,
  `ScriptName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ScriptPurpose` text COLLATE utf8_unicode_ci NOT NULL,
  `Language` enum('R','Python') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'R',
  `Script` text COLLATE utf8_unicode_ci NOT NULL,
  `ScriptVersion` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `TimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ScriptID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101 ;


CREATE TABLE IF NOT EXISTS `QueueJobs` (
  `JobID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `JobName` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`JobID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101 ;

ALTER TABLE `SoundsImages`  ADD `ColorPalette` INT NOT NULL DEFAULT '1' COMMENT 'The color palette used for the images. Same value as svt.py';

UPDATE `PumilioSettings` SET `Value` = '8' WHERE `PumilioSettings`.`Settings` = 'db_version' LIMIT 1 ;
