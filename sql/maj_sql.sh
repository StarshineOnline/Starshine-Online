#!/bin/bash

# Ce script permet de mettre à jour la base de données d'un serveur de dev
# pour les tables statiques (competences, sorts, items ...)

username="bastien"
host="www.starshine-online.com"

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

ssh $username@$host ./dumpstatic.sh stdout > dumpstatic.sql

USER=`cat ../connect.php | grep \'user\' | cut -d\" -f4`
BASE=`cat ../connect.php | grep \'db\' | cut -d\" -f4`
PASSWORD=`cat ../connect.php | grep \'pass\' | cut -d\" -f4`
HOST=`cat ../connect.php | grep \'host\' | cut -d\" -f4`

if [ ! -z $PASSWORD ]; then
    pass="-p$PASSWORD"
fi
if [ ! -z $HOST ]; then
    conn="-h $HOST"
fi

mysql -u $USER $pass $conn $BASE -e 'source dumpstatic.sql'
mysql -u $USER $pass $conn $BASE <<EOF
update map m, map_static s SET
m.info = s.info, m.decor = s.decor, m.type = s.type
WHERE m.x = s.x AND m.y = s.y ;

insert ignore into map(royaume,x,y,id,info,decor,type)
select 0, x,y,id,info,decor,type from map_static ;

drop table map_static ;
EOF

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
# cat maj.sql
