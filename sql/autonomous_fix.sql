ALTER TABLE `autonomous` CHANGE `starttime` `starttime` VARCHAR( 6 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '-1,-1'
ALTER TABLE `autonomous` ADD `name` varchar(255)