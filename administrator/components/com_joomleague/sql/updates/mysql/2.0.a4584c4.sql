ALTER TABLE `#__joomleague_club` CHANGE `founded` `founded` DATE NULL DEFAULT NULL;
ALTER TABLE `#__joomleague_club` ADD `dissolved` DATE NULL DEFAULT NULL AFTER `founded`;
ALTER TABLE `#__joomleague_sports_type` CHANGE `name` `name` VARCHAR( 255 );
ALTER TABLE `#__joomleague_project` CHANGE `sports_type_id` `sports_type_id` INT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE `#__joomleague_eventtype` CHANGE `sports_type_id` `sports_type_id` INT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE `#__joomleague_position` CHANGE `sports_type_id` `sports_type_id` INT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE `#__joomleague_statistic` CHANGE `sports_type_id` `sports_type_id` INT( 1 ) NOT NULL DEFAULT '1';