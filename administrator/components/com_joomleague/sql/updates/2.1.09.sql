ALTER TABLE  `#__joomleague_club` CHANGE  `founded_year`  `founded_year` VARCHAR( 4 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '0000';
ALTER TABLE  `#__joomleague_club` CHANGE  `dissolved_year`  `dissolved_year` VARCHAR( 4 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '0000';
UPDATE  `#__joomleague_club` SET  `founded_year` =  '0000' WHERE  `founded_year` LIKE '' OR `founded_year` IS NULL;
UPDATE  `#__joomleague_club` SET  `dissolved_year` =  '0000' WHERE  `dissolved_year` LIKE '' OR `dissolved_year` IS NULL;