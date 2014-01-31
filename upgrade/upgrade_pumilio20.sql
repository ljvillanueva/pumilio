
--
-- Table structure for table `FilesToAdd`
--

CREATE TABLE IF NOT EXISTS `FilesToAdd` (`FilesToAddID` int(11) NOT NULL AUTO_INCREMENT, `UserID` int(11) NOT NULL, `FilesPath` text COLLATE utf8_unicode_ci NOT NULL,  `StartTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,  `EndTime` timestamp NULL DEFAULT NULL, PRIMARY KEY (`FilesToAddID`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101;

-- --------------------------------------------------------

--
-- Table structure for table `FilesToAddMembers`
--

CREATE TABLE IF NOT EXISTS `FilesToAddMembers` (`ToAddMemberID` int(11) NOT NULL AUTO_INCREMENT, `FilesToAddID` int(11) NOT NULL, `FullPath` text COLLATE utf8_unicode_ci NOT NULL, `ReturnCode` int(11) NOT NULL DEFAULT '1', `OriginalFilename` varchar(250) COLLATE utf8_unicode_ci NOT NULL, `Date` date NOT NULL, `Time` time NOT NULL,  `SiteID` int(11) NOT NULL, `ColID` int(11) NOT NULL, `DirID` int(11) NOT NULL, `SensorID` int(11) NOT NULL, `ErrorCode` text COLLATE utf8_unicode_ci NOT NULL, PRIMARY KEY (`ToAddMemberID`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101;

UPDATE `PumilioSettings` SET `Value` = '21' WHERE `PumilioSettings`.`Settings` = 'db_version' LIMIT 1;

