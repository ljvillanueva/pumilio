UPDATE `PumilioSettings` SET `Value` = '6' WHERE `PumilioSettings`.`Settings` = 'db_version' LIMIT 1 ;
-- --------------------------------------------------------

--
-- Table structure for table `Samples`
--
CREATE TABLE IF NOT EXISTS `Samples` (
`SampleID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`SampleName` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
`SampleNotes` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101 ;

-- --------------------------------------------------------

--
-- Table structure for table `SampleMembers`
--
CREATE TABLE `SampleMembers` (
`SampleMembersID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`SampleID` INT NOT NULL ,
`SoundID` INT NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=101 ;

