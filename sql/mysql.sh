#!/bin/sh
user=`cat ../connect.php | grep \'user\' | cut -d\" -f4`
db=`cat ../connect.php | grep \'db\' | cut -d\" -f4`
password=`cat ../connect.php | grep \'pass\' | cut -d\" -f4`
host=`cat ../connect.php | grep \'host\' | cut -d\" -f4`
if [ -f ../connect.local.php ]; then
    user=`cat ../connect.local.php | grep \'user\' | cut -d\" -f4`
    db=`cat ../connect.local.php | grep \'db\' | cut -d\" -f4`
    password=`cat ../connect.local.php | grep \'pass\' | cut -d\" -f4`
    host=`cat ../connect.local.php | grep \'host\' | cut -d\" -f4`
fi
if [ ! -z $password ]; then
    pass="-p$password"
fi
if [ ! -z $host ]; then
    conn="-h $host"
fi
mysql --default-character-set=utf8 $conn -u $user $pass $db $*
