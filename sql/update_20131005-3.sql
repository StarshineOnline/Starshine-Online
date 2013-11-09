-- debug
UPDATE achievement_type SET description = REPLACE(description, '"', '&quot;')
 WHERE `variable` in ('quete_kesalys', 'quete_pecheur', 'quete_ecolo');
