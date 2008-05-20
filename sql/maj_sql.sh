#!/bin/bash

# Ce script permet de mettre à jour la base de données d'un serveur de dev
# pour les tables statiques (competences, sorts, items ...)

username="bastounet"
host="www.starshine-online.com"
sql_log="-u root"
bdd="starshine"

if [ -f maj_sql.local ] ; then
    # Permet de changer la configuration dans un fichier non ajouté dans le svn
    source maj_sql.local
fi

# Clefs ssh
if [ -x /usr/bin/ssh-agent ]; then
    if [ -z $SSH_AUTH_SOCK ]; then
	echo starting agent
	eval `/usr/bin/ssh-agent`
    else
	KEEP_AGENT=1
    fi
    for i in identity id_rsa id_dsa; do
	if [ -f $HOME/.ssh/$i ]; then
	    ssh-add -l | grep $i > /dev/null || ssh-add $HOME/.ssh/$i
	fi
    done
fi

ssh $username@$host ./get_maj
scp $username@$host:maj.sql .
iconv -f ISO-8859-1 -t UTF-8 maj.sql | sed s/=latin1/=utf8/g > maj_utf8.sql || exit 1
mysql $sql_log $bdd -e 'source maj_utf8.sql'
rm maj.sql maj_utf8.sql

echo done

if [ -x /usr/bin/ssh-agent ]; then
    if [ -z $KEEP_AGENT ]; then
	echo killing agent
	kill $SSH_AGENT_PID
    fi
fi

# Voici le contenu de get_maj :
# #!/bin/bash
# mysqldump -u starshine -pilove50 starshine comp_combat comp_jeu accessoire arme armure classe classe_comp_permet classe_permet classe_requis gemme monstre objet quete recette sort_combat sort_jeu taverne > maj.sql
# echo maj.sql done
