CREATE TABLE IF NOT EXISTS `#__virtualdomain_menu` (
  `menu_id` int(11) NOT NULL,
  `domain` varchar(200) NOT NULL,
  PRIMARY KEY (`menu_id`,`domain`),
  KEY `idx_domain` (`domain`)
);