
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

UPDATE `PumilioSettings` SET `Value` = '20' WHERE `PumilioSettings`.`Settings` = 'db_version' LIMIT 1;

