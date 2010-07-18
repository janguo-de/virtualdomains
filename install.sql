CREATE TABLE IF NOT EXISTS `#__virtualdomain` (
  `id` int(11) NOT NULL auto_increment,
  `domain` varchar(200) NOT NULL,
  `menuid` int(11) NOT NULL,
  `catid` int(11) NOT NULL,
  `template` varchar(100) NOT NULL,
  `Team_ID` int(11) NOT NULL,
  `params` text NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`));