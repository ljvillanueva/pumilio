
ALTER TABLE `Sounds` CHANGE `DerivedSound` `DerivedSound` ENUM('0','1') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Is this soundfile derived from another in the database?';

UPDATE Sounds SET `OtherSoundID` = NULL WHERE `OtherSoundID` = '0';

UPDATE `PumilioSettings` SET `Value` = '25' WHERE `Settings` = 'db_version' LIMIT 1;

