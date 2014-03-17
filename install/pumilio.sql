-- phpMyAdmin SQL Dump
-- version 3.2.2.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 04, 2010 at 11:46 AM
-- Server version: 5.1.37
-- PHP Version: 5.2.10-2ubuntu6.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `pumilio`
--

-- --------------------------------------------------------

--
-- Table structure for table `Cookies`
--

CREATE TABLE IF NOT EXISTS `Cookies` (
  `user_id` int(11) NOT NULL,
  `cookie` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`cookie`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `Cookies`
--


-- --------------------------------------------------------

--
-- Table structure for table `Equipment`
--

CREATE TABLE IF NOT EXISTS `Equipment` (
  `EquipmentID` int(11) NOT NULL AUTO_INCREMENT,
  `Recorder` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `Microphone` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `Notes` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`EquipmentID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101 ;

--
-- Dumping data for table `Equipment`
--


-- --------------------------------------------------------

--
-- Table structure for table `PumilioSettings`
--

CREATE TABLE IF NOT EXISTS `PumilioSettings` (
  `Settings` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Value` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Settings`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `PumilioSettings`
--

-- --------------------------------------------------------

--
-- Table structure for table `Sites`
--

CREATE TABLE IF NOT EXISTS `Sites` (
  `SiteID` int(11) NOT NULL AUTO_INCREMENT,
  `SiteName` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `SiteLat` double DEFAULT NULL,
  `SiteLon` double DEFAULT NULL,
  `WeatherSiteID` int(11) DEFAULT NULL,
  PRIMARY KEY (`SiteID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101 ;

--
-- Dumping data for table `Sites`
--


-- --------------------------------------------------------

--
-- Table structure for table `Sounds`
--

CREATE TABLE IF NOT EXISTS `Sounds` (
  `SoundID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID of the sound',
  `PhysicalArchiveID` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `PhysicalArchiveType` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `PhysicalArchiveSoundID` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `OtherSoundID` int(11) DEFAULT NULL,
  `SourceID` int(11) NOT NULL COMMENT 'ID of the source of the sound',
  `SoundName` varchar(160) COLLATE utf8_unicode_ci NOT NULL,
  `OriginalFilename` varchar(150) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Original name of the file or track number from the source',
  `FileSize` int(11) DEFAULT NULL COMMENT 'Size of the file, in bytes',
  `MP3Filename` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Date` date DEFAULT NULL COMMENT 'Date of the recording',
  `Time` time DEFAULT NULL COMMENT 'Time of the recording',
  `SamplingRate` int(11) NOT NULL DEFAULT '44100',
  `BitRate` int(11) NOT NULL DEFAULT '16',
  `Channels` enum('1','2','4') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `Duration` float NOT NULL,
  `SoundFormat` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `SiteID` int(11) DEFAULT NULL,
  `Location` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Latitude` float DEFAULT NULL,
  `Longitude` float DEFAULT NULL,
  `EquipmentID` int(11) DEFAULT NULL,
  `Notes` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`SoundID`),
  KEY `SourceID` (`SourceID`),
  KEY `SiteID` (`SiteID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101 ;

--
-- Dumping data for table `Sounds`
--


-- --------------------------------------------------------

--
-- Table structure for table `SoundsImages`
--

CREATE TABLE IF NOT EXISTS `SoundsImages` (
  `SoundsImagesID` int(11) NOT NULL AUTO_INCREMENT,
  `SoundID` int(11) NOT NULL,
  `ImageFile` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `ImageType` enum('spectrogram','waveform') COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`SoundsImagesID`),
  KEY `SoundID` (`SoundID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101 ;

--
-- Dumping data for table `SoundsImages`
--


-- --------------------------------------------------------

--
-- Table structure for table `SoundsMarks`
--

CREATE TABLE IF NOT EXISTS `SoundsMarks` (
  `marks_ID` int(11) NOT NULL AUTO_INCREMENT,
  `SoundID` int(11) NOT NULL,
  `time_min` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `time_max` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `freq_min` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `freq_max` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `mark_tag` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `fft_size` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `UserID` int(11) NOT NULL,
  PRIMARY KEY (`marks_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101 ;

--
-- Dumping data for table `SoundsMarks`
--


-- --------------------------------------------------------

--
-- Table structure for table `Sources`
--

CREATE TABLE IF NOT EXISTS `Sources` (
  `SourceID` int(11) NOT NULL AUTO_INCREMENT,
  `Author` varchar(80) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Author of the source',
  `FilesSource` enum('Field Recording','Book with CD','Automated Audio Logger','Audio CD','CD-ROM','DVD','Tape','Internet','Donation','Other') COLLATE utf8_unicode_ci NOT NULL COMMENT 'Type of source',
  `SourceName` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name to display of the source',
  `SourceFullCitation` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Citation in format cientific format or full URL',
  `MiscURL` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Miscelaneous URL, like page of the author',
  `Notes` text COLLATE utf8_unicode_ci COMMENT 'Miscelaneous notes of this source',
  PRIMARY KEY (`SourceID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Sources of sounds, like CDs, DVDs, Websites or libraries' AUTO_INCREMENT=101 ;

--
-- Dumping data for table `Sources`
--


-- --------------------------------------------------------

--
-- Table structure for table `Tags`
--

CREATE TABLE IF NOT EXISTS `Tags` (
  `TagID` int(11) NOT NULL AUTO_INCREMENT,
  `SoundID` int(11) NOT NULL,
  `Tag` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`TagID`),
  KEY `Tag` (`Tag`),
  KEY `SoundID` (`SoundID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101 ;

--
-- Dumping data for table `Tags`
--


-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE IF NOT EXISTS `Users` (
  `UserID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of the user',
  `UserName` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `UserFullname` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `UserEmail` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `UserPassword` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `UserRole` enum('user','admin') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'user',
  `UserActive` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  PRIMARY KEY (`UserID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101 ;

--
-- Dumping data for table `Users`
--


-- --------------------------------------------------------

--
-- Table structure for table `WeatherData`
--

CREATE TABLE IF NOT EXISTS `WeatherData` (
  `WeatherDataID` int(11) NOT NULL AUTO_INCREMENT,
  `WeatherSiteID` int(11) NOT NULL,
  `WeatherDate` date NOT NULL,
  `WeatherTime` time NOT NULL,
  `Temperature` float DEFAULT NULL,
  `Precipitation` float DEFAULT NULL,
  `RelativeHumidity` float DEFAULT NULL,
  `DewPoint` float DEFAULT NULL,
  `WindSpeed` float DEFAULT NULL,
  `WindDirection` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `LightIntensity` float DEFAULT NULL,
  `BarometricPressure` float DEFAULT NULL,
  PRIMARY KEY (`WeatherDataID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101 ;

--
-- Dumping data for table `WeatherData`
--


-- --------------------------------------------------------

--
-- Table structure for table `WeatherSites`
--

CREATE TABLE IF NOT EXISTS `WeatherSites` (
  `WeatherSiteID` int(11) NOT NULL AUTO_INCREMENT,
  `WeatherSiteName` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `WeatherSiteLat` double NOT NULL,
  `WeatherSiteLon` double NOT NULL,
  `WeatherSiteElev` int(11) NOT NULL,
  `WeatherSiteSource` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`WeatherSiteID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101 ;

--
-- Dumping data for table `WeatherSites`
--

-- --------------------------------------------------------

--
-- Table structure for table `Samples`
--
CREATE TABLE IF NOT EXISTS `Samples` (
`SampleID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`SampleName` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
`SampleNotes` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101 ;

-- --------------------------------------------------------

--
-- Table structure for table `SampleMembers`
--
CREATE TABLE `SampleMembers` (
`SampleMembersID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`SampleID` INT NOT NULL ,
`SoundID` INT NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101 ;


ALTER TABLE `SoundsImages` CHANGE `ImageType` `ImageType` ENUM( 'spectrogram', 'waveform', 'spectrogram-small', 'waveform-small', 'spectrogram-large', 'waveform-large' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

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


CREATE TABLE IF NOT EXISTS `SitesPhotos` (
  `SitesPhotoID` int(11) NOT NULL AUTO_INCREMENT,
  `SiteID` int(11) NOT NULL,
  `PhotoFilename` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `ViewDegrees` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `UserID` int(11) NOT NULL,
  `PhotoNotes` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`SitesPhotoID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101 ;

ALTER TABLE `SitesPhotos`  ADD `PhotoDate` DATETIME NOT NULL AFTER `ViewDegrees`;

ALTER TABLE `Queue` CHANGE `ComputerDone` `ComputerDone` VARCHAR( 80 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Name of computer that completed the task';

INSERT INTO `Sites` (`SiteName`) VALUES ('None');

ALTER TABLE `PumilioSettings` CHANGE `Value` `Value` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE `Queue` CHANGE `Status` `Status` ENUM( '0', '1', '2', '3', '4', '5' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '0 is not done, 1 is taken, 2 is done, 3 is error, 4 is hold, 5 is not found 404 error';

CREATE TABLE `Kml` (`KmlID` INT NOT NULL AUTO_INCREMENT ,`KmlName` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`KmlNotes` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`KmlURL` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,PRIMARY KEY ( `KmlID` )) ENGINE = MYISAM DEFAULT CHARSET  = utf8 COLLATE  = utf8_unicode_ci ;

ALTER TABLE `Sounds` CHANGE `SourceID` `ColID` INT( 11 ) NOT NULL COMMENT 'ID of the collection to which the sound is assigned to';

CREATE  TABLE  `Collections` (  `SourceID` int( 11  )  NOT  NULL  AUTO_INCREMENT , `Author` varchar( 80  )  COLLATE utf8_unicode_ci NOT  NULL  COMMENT  'Author of the source', `FilesSource` enum(  'Field Recording',  'Book with CD',  'Automated Audio Logger',  'Audio CD',  'CD-ROM',  'DVD',  'Tape',  'Internet',  'Donation',  'Other'  )  COLLATE utf8_unicode_ci NOT  NULL  COMMENT  'Type of source', `SourceName` varchar( 100  )  COLLATE utf8_unicode_ci NOT  NULL  COMMENT  'Name to display of the source', `SourceFullCitation` varchar( 255  )  COLLATE utf8_unicode_ci  DEFAULT NULL  COMMENT  'Citation in format cientific format or full URL', `MiscURL` varchar( 250  )  COLLATE utf8_unicode_ci  DEFAULT NULL  COMMENT  'Miscelaneous URL, like page of the author', `Notes` text COLLATE utf8_unicode_ci COMMENT  'Miscelaneous notes of this source', PRIMARY  KEY (  `SourceID`  )  ) ENGINE  =  MyISAM  DEFAULT CHARSET  = utf8 COLLATE  = utf8_unicode_ci COMMENT  =  'Collections of sounds, like CDs, DVDs, Websites or libraries';

INSERT INTO `Collections` SELECT * FROM `Sources`;

DROP TABLE `Sources`;

ALTER TABLE `Collections` CHANGE `SourceID` `ColID` INT(11) NOT NULL AUTO_INCREMENT, CHANGE `Author` `Author` VARCHAR(80) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'Author of the collection', CHANGE `FilesSource` `FilesSource` ENUM('Field Recording','Book with CD','Automated Audio Logger','Audio CD','CD-ROM','DVD','Tape','Internet','Donation','Other') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'Type of collection', CHANGE `SourceName` `CollectionName` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name to display of the collection', CHANGE `SourceFullCitation` `CollectionFullCitation` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Citation in format cientific format or full URL', CHANGE `Notes` `Notes` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Miscelaneous notes of this collection';

ALTER TABLE `SoundsImages`  ADD `SpecMaxFreq` INT NULL;

ALTER TABLE `Kml` ADD `KmlDefault` ENUM( '0', '1' ) NOT NULL DEFAULT '0' AFTER `KmlURL`;

ALTER TABLE `Tags` CHANGE `Tag` `Tag` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

CREATE TABLE IF NOT EXISTS `Tokens` (  `TokenID` varchar(150) COLLATE utf8_unicode_ci NOT NULL,  `UserID` int(11) NOT NULL,  `soundfile_format` varchar(10) COLLATE utf8_unicode_ci NOT NULL,  `soundfile_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,  `soundfile_duration` float NOT NULL,  `soundfile_samplingrate` int(11) NOT NULL,  `soundfile_samplingrateoriginal` int(11) DEFAULT NULL,  `soundfile_id` int(11) NOT NULL,  `no_channels` int(11) NOT NULL,  `frequency_max` int(11) DEFAULT NULL,  `frequency_min` int(11) DEFAULT NULL,  `soundfile_wav` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,  `from_db` enum('FALSE','TRUE') COLLATE utf8_unicode_ci DEFAULT NULL,  `random_cookie` varchar(50) COLLATE utf8_unicode_ci NOT NULL,  UNIQUE KEY `TokenID` (`TokenID`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE  `Sounds` ADD  `DirID` INT NOT NULL AFTER  `ColID`;

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

CREATE TABLE IF NOT EXISTS `QualityFlags` (`QualityFlagID` float NOT NULL, `QualityFlag` varchar(40) COLLATE utf8_unicode_ci NOT NULL, PRIMARY KEY (`QualityFlagID`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `QualityFlags` (`QualityFlagID`, `QualityFlag`) VALUES (0, 'Unknown'), (1, 'File checked and OK');

ALTER TABLE `Sounds` CHANGE `QualityFlag` `QualityFlagID` FLOAT NOT NULL DEFAULT '0' COMMENT 'Quality of the file';

INSERT INTO `PumilioSettings` (`Settings`, `Value`) VALUES ('default_qf', 0);

ALTER TABLE `Tokens`  ADD `timestamp` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

update `Tokens` set `timestamp` = NOW() WHERE `timestamp`='0000-00-00 00:00:00';

CREATE TABLE IF NOT EXISTS `CheckAuxfiles` (`SoundID` int(11) NOT NULL, KEY `SoundID` (`SoundID`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


ALTER TABLE `Sounds` ADD INDEX (`Duration`);
ALTER TABLE `Sounds` ADD INDEX (`ColID`);
ALTER TABLE `Sounds` ADD INDEX (`SoundStatus`);
ALTER TABLE `Sounds` ADD INDEX (`QualityFlagID`);
ALTER TABLE `Sounds` ADD INDEX (`Date`);
ALTER TABLE `Sounds` ADD INDEX (`Time`);
ALTER TABLE `Sounds` ADD INDEX (`SamplingRate`);
ALTER TABLE `Sounds` ADD INDEX (`Channels`);
ALTER TABLE `Sounds` ADD INDEX (`SensorID`);
ALTER TABLE `Sounds` ADD INDEX (`SoundFormat`);

INSERT INTO `Sensors` (`SensorID`, `Recorder`) VALUES ('1', 'None');

--
-- Table structure for table `FilesToAdd`
--

CREATE TABLE IF NOT EXISTS `FilesToAdd` (`FilesToAddID` int(11) NOT NULL AUTO_INCREMENT, `UserID` int(11) NOT NULL, `FilesPath` text COLLATE utf8_unicode_ci NOT NULL,  `StartTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,  `EndTime` timestamp NULL DEFAULT NULL, PRIMARY KEY (`FilesToAddID`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101;


CREATE TABLE IF NOT EXISTS `FilesToAddMembers` (`ToAddMemberID` int(11) NOT NULL AUTO_INCREMENT, `FilesToAddID` int(11) NOT NULL, `FullPath` text COLLATE utf8_unicode_ci NOT NULL, `ReturnCode` int(11) NOT NULL DEFAULT '1', `OriginalFilename` varchar(250) COLLATE utf8_unicode_ci NOT NULL, `Date` date NOT NULL, `Time` time NOT NULL,  `SiteID` int(11) NOT NULL, `ColID` int(11) NOT NULL, `DirID` int(11) NOT NULL, `SensorID` int(11) NOT NULL, `ErrorCode` text COLLATE utf8_unicode_ci NOT NULL, PRIMARY KEY (`ToAddMemberID`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101;

CREATE TABLE IF NOT EXISTS `SoundsStatsResults` (`SoundsStatsID` int(11) NOT NULL AUTO_INCREMENT, `SoundID` int(11) NOT NULL, `Stat` varchar(50) COLLATE utf8_unicode_ci NOT NULL, `StatValue` double DEFAULT NULL, PRIMARY KEY (`SoundsStatsID`), KEY `SoundID` (`SoundID`), KEY `Stat` (`Stat`), KEY `StatValue` (`StatValue`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101;

ALTER TABLE `Cookies`  ADD `hostname` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `Cookies`  ADD `TimeStamp` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL;

ALTER TABLE `Sounds`  ADD `SoundStats` SMALLINT NOT NULL DEFAULT '0' COMMENT '0 is not done, 1 is in progress, 2 is done' AFTER `Notes`,  ADD INDEX (`SoundStats`);

ALTER TABLE `FilesToAddMembers`  ADD `TimeStamp` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

UPDATE `FilesToAddMembers` SET `TimeStamp`=NOW();

CREATE TABLE IF NOT EXISTS `SoundsStats` (`SoundsStatsID` int(11) NOT NULL AUTO_INCREMENT, `SoundID` int(11) NOT NULL, `StatToCalculate` varchar(250) COLLATE utf8_unicode_ci NOT NULL, `ReturnCode` int(11) NOT NULL DEFAULT '1', `ErrorCode` text COLLATE utf8_unicode_ci NOT NULL, `TimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (`SoundsStatsID`), KEY `StatToCalculate` (`StatToCalculate`), KEY `SoundID` (`SoundID`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101;

ALTER TABLE `Kml` CHANGE `KmlDefault` `KmlDefault` ENUM('0','1','2') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0';

ALTER TABLE `Sites`  ADD `SiteURL` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT 'URL for more info on the site, can be relative to the installation.' AFTER `WeatherSiteID`;

ALTER TABLE `SoundsImages`  ADD `ImageCreator` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'svt' COMMENT 'Which program created the images.' AFTER `SpecMaxFreq`;

ALTER TABLE `SoundsImages` CHANGE `ColorPalette` `ColorPalette` INT(11) NOT NULL DEFAULT '1' COMMENT 'The color palette used for the images. For either svt.py or SoX';

ALTER TABLE `Sounds` CHANGE `DerivedSound` `DerivedSound` ENUM('0','1') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Is this soundfile derived from another in the database?';

UPDATE Sounds SET `OtherSoundID` = NULL WHERE `OtherSoundID` = '0';

ALTER TABLE `SoundsImages`  ADD `ImageFFT` INT NOT NULL DEFAULT '2048' AFTER `SpecMaxFreq`;

INSERT INTO `PumilioSettings` (`Settings`, `Value`) VALUES ('use_tags', '1');

REPLACE INTO `PumilioSettings` (`Settings`, `Value`) VALUES ('db_version', '26');

