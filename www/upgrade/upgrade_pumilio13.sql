ALTER TABLE `Sounds` CHANGE `SourceID` `ColID` INT( 11 ) NOT NULL COMMENT 'ID of the collection to which the sound is assigned to';

CREATE  TABLE  `Collections` (  `SourceID` int( 11  )  NOT  NULL  AUTO_INCREMENT , `Author` varchar( 80  )  COLLATE utf8_unicode_ci NOT  NULL  COMMENT  'Author of the source', `FilesSource` enum(  'Field Recording',  'Book with CD',  'Automated Audio Logger',  'Audio CD',  'CD-ROM',  'DVD',  'Tape',  'Internet',  'Donation',  'Other'  )  COLLATE utf8_unicode_ci NOT  NULL  COMMENT  'Type of source', `SourceName` varchar( 100  )  COLLATE utf8_unicode_ci NOT  NULL  COMMENT  'Name to display of the source', `SourceFullCitation` varchar( 255  )  COLLATE utf8_unicode_ci  DEFAULT NULL  COMMENT  'Citation in format cientific format or full URL', `MiscURL` varchar( 250  )  COLLATE utf8_unicode_ci  DEFAULT NULL  COMMENT  'Miscelaneous URL, like page of the author', `Notes` text COLLATE utf8_unicode_ci COMMENT  'Miscelaneous notes of this source', PRIMARY  KEY (  `SourceID`  )  ) ENGINE  =  MyISAM  DEFAULT CHARSET  = utf8 COLLATE  = utf8_unicode_ci COMMENT  =  'Collections of sounds, like CDs, DVDs, Websites or libraries';

INSERT INTO `Collections` SELECT * FROM `Sources`;

DROP TABLE `Sources`;

ALTER TABLE `Collections` CHANGE `SourceID` `ColID` INT(11) NOT NULL AUTO_INCREMENT, CHANGE `Author` `Author` VARCHAR(80) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'Author of the collection', CHANGE `FilesSource` `FilesSource` ENUM('Field Recording','Book with CD','Automated Audio Logger','Audio CD','CD-ROM','DVD','Tape','Internet','Donation','Other') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'Type of collection', CHANGE `SourceName` `CollectionName` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name to display of the collection', CHANGE `SourceFullCitation` `CollectionFullCitation` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Citation in format cientific format or full URL', CHANGE `Notes` `Notes` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Miscelaneous notes of this collection';

ALTER TABLE `SoundsImages`  ADD `SpecMaxFreq` INT NULL;

UPDATE `PumilioSettings` SET `Value` = '14' WHERE `PumilioSettings`.`Settings` = 'db_version' LIMIT 1;

