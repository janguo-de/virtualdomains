CREATE TABLE IF NOT EXISTS `#__virtualdomain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(200) NOT NULL,
  `menuid` int(11) NOT NULL,
  `template` varchar(100) NOT NULL,
  `viewlevel` int(11) NOT NULL,
  `params` text NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `#__virtualdomain_params` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `action` set('none','globals','request') NOT NULL,
  `home` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
);

