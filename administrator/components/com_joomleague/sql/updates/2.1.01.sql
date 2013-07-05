-- -----------------------------------------------------
-- Table `#__joomleague_prediction_groups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `#__joomleague_prediction_groups` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(75) NOT NULL DEFAULT '' ,
  `alias` VARCHAR(75) NOT NULL DEFAULT '' ,
  `extended` TEXT NULL ,
  `ordering` INT(11) NOT NULL DEFAULT '0' ,
  `checked_out` INT(11) NOT NULL DEFAULT '0' ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `modified` DATETIME NULL ,
  `modified_by` INT NULL ,
  `extendeduser` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `name` (`name` ASC) )
ENGINE = MyISAM
DEFAULT CHARSET = utf8;

ALTER TABLE  `#__joomleague_prediction_member` ADD  `group_id` INT( 11 ) NOT NULL DEFAULT  '0'