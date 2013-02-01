ALTER TABLE `SoundsImages` ADD INDEX ( `SoundID` );
ALTER TABLE `Tags` ADD INDEX ( `Tag` );
ALTER TABLE `Tags` ADD INDEX ( `SoundID` ) ;
ALTER TABLE `Sounds` ADD INDEX ( `SourceID` ) ;
ALTER TABLE `Sounds` ADD INDEX ( `SiteID` ) ;

UPDATE `PumilioSettings` SET `Value` = '4' WHERE `PumilioSettings`.`Settings` = 'db_version' LIMIT 1 ;
