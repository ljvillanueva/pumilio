ALTER TABLE `SoundsImages` CHANGE `ImageType` `ImageType` ENUM( 'spectrogram', 'waveform', 'spectrogram-small', 'waveform-small', 'spectrogram-large', 'waveform-large' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

UPDATE `PumilioSettings` SET `Value` = '7' WHERE `PumilioSettings`.`Settings` = 'db_version' LIMIT 1 ;

