--
-- Tabellenstruktur für Tabelle `#__joomleague_version_history`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_version_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `text` text,
  `version` VARCHAR(255) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

TRUNCATE TABLE  `#__joomleague_version_history`;

INSERT INTO `#__joomleague_version_history` (`id`, `date`, `text`, `version`) VALUES
(NULL, '2013-02-14', 'COM_JOOMLEAGUE_DB_UPDATE_2013_02_14', '2.0.48'),
(NULL, '2013-02-13', 'COM_JOOMLEAGUE_DB_UPDATE_2013_02_13', '2.0.47');

update #__joomleague_version set major='2', minor='0', build='48', revision='-diddipoeler', version='a', file='joomleague' where file='joomleague'