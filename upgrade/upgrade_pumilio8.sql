
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

UPDATE `PumilioSettings` SET `Value` = '9' WHERE `PumilioSettings`.`Settings` = 'db_version' LIMIT 1 ;
