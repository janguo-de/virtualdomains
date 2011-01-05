
CREATE TABLE IF NOT EXISTS #__virtualdomain_params (
  id int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `action` set('none','globals','request') NOT NULL,
  home tinyint(1) NOT NULL,
  label varchar(30) NOT NULL,
  `type` varchar(20) NOT NULL,
  PRIMARY KEY (id)
) 
