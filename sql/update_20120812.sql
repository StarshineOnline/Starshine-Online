UPDATE sort_combat SET duree = effet2 * 3600 WHERE type LIKE 'debuff_antirez';
UPDATE sort_combat SET duree = effet2 * 86400 WHERE type LIKE 'encombrement_psy';
UPDATE sort_combat SET duree = effet * 60 WHERE type LIKE 'debuff_enracinement';
UPDATE sort_combat SET duree = 2678400 WHERE type LIKE 'nostalgie_karn';