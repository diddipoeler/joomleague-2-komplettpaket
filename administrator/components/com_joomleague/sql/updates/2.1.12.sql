ALTER TABLE  `#__joomleague_club` CHANGE  `founded`  `founded` DATE NULL DEFAULT  '0000-00-00';
UPDATE  `#__joomleague_club` SET  `founded` =  '0000-00-00' WHERE  `founded` LIKE '' OR `founded` IS NULL;
