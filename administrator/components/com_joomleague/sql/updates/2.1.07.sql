-- -----------------------------------------------------
-- Table `#__joomleague_user_extra_fields_values`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `#__joomleague_user_extra_fields_values` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `field_id` INT(11) NOT NULL DEFAULT '0' ,
  `jl_id` INT(11) NOT NULL DEFAULT '0' ,    
  `fieldvalue` VARCHAR(100) NOT NULL DEFAULT '' ,
  `ordering` INT(11) NOT NULL DEFAULT '0' ,
  `checked_out` INT(11) NOT NULL DEFAULT '0' ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `modified` DATETIME NULL ,
  `modified_by` INT NULL ,
  `published` tinyint(1) NOT NULL default '1',
  PRIMARY KEY (`id`) 
  )
ENGINE = MyISAM
DEFAULT CHARSET = utf8; 