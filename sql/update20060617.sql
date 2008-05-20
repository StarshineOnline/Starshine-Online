CREATE TABLE `log_connexion` (                         
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`id_joueur` INT UNSIGNED NOT NULL ,                    
`time` INT UNSIGNED NOT NULL ,                         
`ip` VARCHAR( 50 ) NOT NULL ,                          
`message` TEXT NOT NULL                                
);

UPDATE `quete` SET `nom` = 'Le ragoût de lapin',
`description` = 'Mon fournisseur habituel est souffrant aujourd''hui, ça vous dirait pas de vous faire quelques pièces facilement ? <span class="small">*le tavernier vous observe en grommelant *</span> ...Moué, je vous enverrais bien me chercher quelques steack de guépard mais je dois vous avouer que m''êtes plus utile vivant... Enfin bon, ma spécialité, c''est le ragout de Lapin, essayer de m''en ramener une quinzaine que je puisse préparer le prochain repas de tous ces affamés et je vous filerai un petit quelque chose en récompense.' WHERE `id` =1 LIMIT 1 ;
ALTER TABLE `quete` ADD `fournisseur` VARCHAR( 50 ) NOT NULL AFTER `description` ;
UPDATE `quete` SET `nom` = 'L''invasion des belettes',
`description` = 'Je vous jure ! Plein mon poullailler que j''en ai ! des vrais saloperies, et la garde qui me rigole au nez quand je leur en parle, il faut que quelqu''un fasse quelque chose ... C''est pas un truc bien héroïque mais si vous m''en débarasser, je saurais être génereux .. et faites gaffes, ces sales bestioles mordent ...',
`fournisseur` = 'taverne' WHERE `id` =5 LIMIT 1 ;
INSERT INTO `quete` ( `id` , `nom` , `description` , `fournisseur` , `objectif` , `exp` , `honneur` , `star` , `repeat` , `niveau_requis` , `honneur_requis` , `star_royaume` , `lvl_joueur` )
VALUES (
NULL , 'Le sérum', 'Tiens vous voila, justement je vous cherchais, notre guérisseur est a court d''ingrédients pour préparer ses baumes, et je me disais qu''un gars comme vous toujours a la recherche d''aventure pourrait être interessé... Ce qu''il lui faut surtout ce sont des glandes de scorpions pour préparer des anti-poisons, si vous vous sentez le courage, ramenez en 5, il n''est pas ingrat vous verrez', 'magasin', 'a:1:{i:0;O:8:"stdClass":3:{s:5:"cible";s:2:"M8";s:6:"nombre";i:5;s:6:"requis";s:0:"";}}', '300', '10', '50', '0', '0', '0', '25', '1'
), (
NULL , 'Chasse aux Pingouins', 'De la graisse de pingouin voila ce qu''il me faudrait... Un bail que je suis pas parti a la chasse pour en ramener... mais avec tout le boulot que j''ai ici, j''ai plus vraiment le temps <span class="small">( soupirs )</span>, des fois je me dis que je ferais mieux de tout plaquer... Enfin bon je disais que la graisse de Pingouin c''est l''idéal pour la cuisine ... et justement je me disais que vous pourriez aller chasser quelque uns de ces oiseaux pour moi, histoire d''améliorer la bouffe du coin... Evidemment je vous paierai !', 'magasin', 'a:1:{i:0;O:8:"stdClass":3:{s:5:"cible";s:3:"M10";s:6:"nombre";i:10;s:6:"requis";s:0:"";}}', '500', '10', '50', '0', '0', '0', '25', '1'
);
INSERT INTO `quete` ( `id` , `nom` , `description` , `fournisseur` , `objectif` , `exp` , `honneur` , `star` , `repeat` , `niveau_requis` , `honneur_requis` , `star_royaume` , `lvl_joueur` )
VALUES (
NULL , 'Je vous donne du monsieur', 'Oué c''est sur vous avez réussi, on peux dire que je m''attendais pas a ce que vous progressiez aussi vite, on parle de vous en haut lieu... Mais croyez moi vous avez pas fini d''apprendre, je veux dire vous êtes fort et tout, mais est ce que vous vous êtes déja mesuré a un Griffon ??? parce que ça c''est le vrai défi... un Griffon mon gars, ya pas plus dangereux et majestueux aux alentours... Mais là si vous réussissez ce coup là, je vous donne du monsieur c''est sur !', 'ecole_combat', 'a:1:{i:0;O:8:"stdClass":3:{s:5:"cible";s:3:"M15";s:6:"nombre";i:1;s:6:"requis";s:0:"";}}', '3000', '2000', '1500', '0', '10', '0', '1000', '14'
);