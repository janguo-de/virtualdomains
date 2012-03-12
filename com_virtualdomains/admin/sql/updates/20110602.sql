ALTER TABLE #__menu ADD domain CHAR( 255 ) NOT NULL AFTER `language`;
ALTER TABLE #__menu  ADD INDEX `idx_domain` ( `domain` ) ; 
UPDATE #__virtualdomain as vd, #__menu as m SET m.domain = vd.domain WHERE m.id = vd.menuid;