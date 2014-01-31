ALTER TABLE  `Sounds` ADD  `DirID` INT NOT NULL AFTER  `ColID`;

UPDATE `PumilioSettings` SET `Value` = '17' WHERE `PumilioSettings`.`Settings` = 'db_version' LIMIT 1;

