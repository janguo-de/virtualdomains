CREATE TABLE IF NOT EXISTS `#__virtualdomain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(200) NOT NULL,
  `home` tinyint(1) NOT NULL,
  `menuid` int(11) NOT NULL,
  `template` varchar(100) NOT NULL,
  `template_style_id` int(11) NOT NULL,
  `viewlevel` int(11) NOT NULL,
  `params` text NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
);

INSERT INTO `#__virtualdomain` (`id`, `domain`, `home`, `menuid`, `template`, `template_style_id`, `viewlevel`, `params`, `published`, `checked_out`, `checked_out_time`, `ordering`) VALUES
(1, 'replace-with-your-default-domain', 1, 0, '', 0, 0, '', 1, 0, '0000-00-00 00:00:00', 1);


CREATE TABLE IF NOT EXISTS `#__virtualdomain_params` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `action` set('none','globals','request') NOT NULL,
  `home` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `#__virtualdomain_menu` (
  `menu_id` int(11) NOT NULL,
  `domain` varchar(200) NOT NULL,
  PRIMARY KEY (`menu_id`,`domain`),
  KEY `fk_jos_menu_has_virtualdomain_virtualdomain1` (`domain`)
);
