
CREATE TABLE IF NOT EXISTS `CheckAuxfiles` (`SoundID` int(11) NOT NULL, KEY `SoundID` (`SoundID`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

UPDATE `PumilioSettings` SET `Value` = '19' WHERE `PumilioSettings`.`Settings` = 'db_version' LIMIT 1;

