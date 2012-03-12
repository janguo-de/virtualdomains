CREATE TABLE IF NOT EXISTS `#__virtualdomain_menu` (
  `menu_id` int(11) NOT NULL,
  `domain` varchar(200) NOT NULL,
  PRIMARY KEY (`menu_id`,`domain`),
  KEY `fk_jos_menu_has_virtualdomain_virtualdomain1` (`domain`)
);

ALTER TABLE #__virtualdomain_menu ADD home TINYINT( 1 ) NOT NULL AFTER `domain`;
ALTER TABLE #__virtualdomain_menu ADD viewlevel int(11) NOT NULL AFTER `template`;