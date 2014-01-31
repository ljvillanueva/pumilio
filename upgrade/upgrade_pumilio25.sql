
ALTER TABLE `SoundsImages`  ADD `ImageFFT` INT NOT NULL DEFAULT '2048' AFTER `SpecMaxFreq`;

UPDATE `PumilioSettings` SET `Value` = '26' WHERE `Settings` = 'db_version' LIMIT 1;

