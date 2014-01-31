
CREATE TABLE IF NOT EXISTS `SoundsStatsResults` (`SoundsStatsID` int(11) NOT NULL AUTO_INCREMENT, `SoundID` int(11) NOT NULL, `Stat` varchar(50) COLLATE utf8_unicode_ci NOT NULL, `StatValue` double DEFAULT NULL, PRIMARY KEY (`SoundsStatsID`), KEY `SoundID` (`SoundID`), KEY `Stat` (`Stat`), KEY `StatValue` (`StatValue`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101;

ALTER TABLE `Cookies`  ADD `hostname` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `Cookies`  ADD `TimeStamp` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL;

ALTER TABLE `Sounds`  ADD `SoundStats` SMALLINT NOT NULL DEFAULT '0' COMMENT '0 is not done, 1 is in progress, 2 is done' AFTER `Notes`,  ADD INDEX (`SoundStats`);

ALTER TABLE `FilesToAddMembers`  ADD `TimeStamp` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

UPDATE `FilesToAddMembers` SET `TimeStamp`=NOW();

CREATE TABLE IF NOT EXISTS `SoundsStats` (`SoundsStatsID` int(11) NOT NULL AUTO_INCREMENT, `SoundID` int(11) NOT NULL, `StatToCalculate` varchar(250) COLLATE utf8_unicode_ci NOT NULL, `ReturnCode` int(11) NOT NULL DEFAULT '1', `ErrorCode` text COLLATE utf8_unicode_ci NOT NULL, `TimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (`SoundsStatsID`), KEY `StatToCalculate` (`StatToCalculate`), KEY `SoundID` (`SoundID`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101;

UPDATE `PumilioSettings` SET `Value` = '22' WHERE `PumilioSettings`.`Settings` = 'db_version' LIMIT 1;

