CREATE TABLE `Kml` (`KmlID` INT NOT NULL AUTO_INCREMENT ,`KmlName` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`KmlNotes` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`KmlURL` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,PRIMARY KEY ( `KmlID` )) ENGINE = MYISAM DEFAULT CHARSET  = utf8 COLLATE  = utf8_unicode_ci ;

UPDATE `PumilioSettings` SET `Value` = '13' WHERE `PumilioSettings`.`Settings` = 'db_version' LIMIT 1;

