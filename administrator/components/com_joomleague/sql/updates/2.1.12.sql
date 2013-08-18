ALTER TABLE  `#__joomleague_club` CHANGE  `founded`  `founded` DATE NULL DEFAULT  '0000-00-00';
UPDATE  `#__joomleague_club` SET  `founded` =  '0000-00-00' WHERE  `founded` LIKE '' OR `founded` IS NULL;

--
-- Tabellenstruktur für Tabelle `#__joomleague_match_commentary`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_match_commentary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `match_id` int(11) NOT NULL DEFAULT '0',
  `type` int(11) NOT NULL DEFAULT '0',
  `time` varchar(20) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `match_id` (`match_id`),
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
