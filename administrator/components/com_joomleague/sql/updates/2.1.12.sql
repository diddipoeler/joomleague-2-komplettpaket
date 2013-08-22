ALTER TABLE  `#__joomleague_club` CHANGE  `founded`  `founded` DATE NULL DEFAULT  '0000-00-00';
UPDATE  `#__joomleague_club` SET  `founded` =  '0000-00-00' WHERE  `founded` LIKE '' OR `founded` IS NULL;
ALTER TABLE  `#__joomleague_match_event` CHANGE  `event_time`  `event_time` INT( 11 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `#__joomleague_match` ADD  `import_match_id` INT( 11 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `#__joomleague_project` ADD  `import_project_id` INT( 11 ) NOT NULL DEFAULT  '0';

--
-- Tabellenstruktur für Tabelle `#__joomleague_match_commentary`
--
CREATE TABLE IF NOT EXISTS `#__joomleague_match_commentary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `match_id` int(11) NOT NULL DEFAULT '0',
  `type` int(11) NOT NULL DEFAULT '0',
  `event_time` int(11) NOT NULL DEFAULT '0',
  `notes` text NOT NULL,
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  
  PRIMARY KEY (`id`),
  KEY `match_id` (`match_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
