CREATE TABLE `log_connexion` (                         
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`id_joueur` INT UNSIGNED NOT NULL ,                    
`time` INT UNSIGNED NOT NULL ,                         
`ip` VARCHAR( 50 ) NOT NULL ,                          
`message` TEXT NOT NULL                                
);

UPDATE `quete` SET `nom` = 'Le rago�t de lapin',
`description` = 'Mon fournisseur habituel est souffrant aujourd''hui, �a vous dirait pas de vous faire quelques pi�ces facilement ? <span class="small">*le tavernier vous observe en grommelant *</span> ...Mou�, je vous enverrais bien me chercher quelques steack de gu�pard mais je dois vous avouer que m''�tes plus utile vivant... Enfin bon, ma sp�cialit�, c''est le ragout de Lapin, essayer de m''en ramener une quinzaine que je puisse pr�parer le prochain repas de tous ces affam�s et je vous filerai un petit quelque chose en r�compense.' WHERE `id` =1 LIMIT 1 ;
ALTER TABLE `quete` ADD `fournisseur` VARCHAR( 50 ) NOT NULL AFTER `description` ;
UPDATE `quete` SET `nom` = 'L''invasion des belettes',
`description` = 'Je vous jure ! Plein mon poullailler que j''en ai ! des vrais saloperies, et la garde qui me rigole au nez quand je leur en parle, il faut que quelqu''un fasse quelque chose ... C''est pas un truc bien h�ro�que mais si vous m''en d�barasser, je saurais �tre g�nereux .. et faites gaffes, ces sales bestioles mordent ...',
`fournisseur` = 'taverne' WHERE `id` =5 LIMIT 1 ;
INSERT INTO `quete` ( `id` , `nom` , `description` , `fournisseur` , `objectif` , `exp` , `honneur` , `star` , `repeat` , `niveau_requis` , `honneur_requis` , `star_royaume` , `lvl_joueur` )
VALUES (
NULL , 'Le s�rum', 'Tiens vous voila, justement je vous cherchais, notre gu�risseur est a court d''ingr�dients pour pr�parer ses baumes, et je me disais qu''un gars comme vous toujours a la recherche d''aventure pourrait �tre interess�... Ce qu''il lui faut surtout ce sont des glandes de scorpions pour pr�parer des anti-poisons, si vous vous sentez le courage, ramenez en 5, il n''est pas ingrat vous verrez', 'magasin', 'a:1:{i:0;O:8:"stdClass":3:{s:5:"cible";s:2:"M8";s:6:"nombre";i:5;s:6:"requis";s:0:"";}}', '300', '10', '50', '0', '0', '0', '25', '1'
), (
NULL , 'Chasse aux Pingouins', 'De la graisse de pingouin voila ce qu''il me faudrait... Un bail que je suis pas parti a la chasse pour en ramener... mais avec tout le boulot que j''ai ici, j''ai plus vraiment le temps <span class="small">( soupirs )</span>, des fois je me dis que je ferais mieux de tout plaquer... Enfin bon je disais que la graisse de Pingouin c''est l''id�al pour la cuisine ... et justement je me disais que vous pourriez aller chasser quelque uns de ces oiseaux pour moi, histoire d''am�liorer la bouffe du coin... Evidemment je vous paierai !', 'magasin', 'a:1:{i:0;O:8:"stdClass":3:{s:5:"cible";s:3:"M10";s:6:"nombre";i:10;s:6:"requis";s:0:"";}}', '500', '10', '50', '0', '0', '0', '25', '1'
);
INSERT INTO `quete` ( `id` , `nom` , `description` , `fournisseur` , `objectif` , `exp` , `honneur` , `star` , `repeat` , `niveau_requis` , `honneur_requis` , `star_royaume` , `lvl_joueur` )
VALUES (
NULL , 'Je vous donne du monsieur', 'Ou� c''est sur vous avez r�ussi, on peux dire que je m''attendais pas a ce que vous progressiez aussi vite, on parle de vous en haut lieu... Mais croyez moi vous avez pas fini d''apprendre, je veux dire vous �tes fort et tout, mais est ce que vous vous �tes d�ja mesur� a un Griffon ??? parce que �a c''est le vrai d�fi... un Griffon mon gars, ya pas plus dangereux et majestueux aux alentours... Mais l� si vous r�ussissez ce coup l�, je vous donne du monsieur c''est sur !', 'ecole_combat', 'a:1:{i:0;O:8:"stdClass":3:{s:5:"cible";s:3:"M15";s:6:"nombre";i:1;s:6:"requis";s:0:"";}}', '3000', '2000', '1500', '0', '10', '0', '1000', '14'
);