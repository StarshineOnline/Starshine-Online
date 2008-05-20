#!/bin/sh
user=`cat ../connect.php | grep \'user\' | cut -d\" -f4`
db=`cat ../connect.php | grep \'db\' | cut -d\" -f4`
password=`cat ../connect.php | grep \'pass\' | cut -d\" -f4`
host=`cat ../connect.php | grep \'host\' | cut -d\" -f4`
if [ ! -z $password ]; then
    pass="-p$password"
fi
if [ ! -z $host ]; then
    conn="-h $host"
fi
echo dump database
mysqldump $conn -u $user $pass --default-character-set=latin1 -c  --insert-ignore --skip-set-charset $db > dump.sql || exit 1
echo convert dump file
iconv -f ISO-8859-1 -t UTF-8 dump.sql | sed s/=latin1/=utf8/g > dump_utf8.sql || exit 1
echo drop database
mysql $conn -u $user $pass --execute="DROP DATABASE $db" || exit 1
echo create database
mysql $conn -u $user $pass --execute="CREATE DATABASE $db CHARACTER SET utf8 COLLATE utf8_general_ci"
echo import database
mysql $conn -u $user $pass --max_allowed_packet=16M --default-character-set=utf8 $db < dump_utf8.sql
echo done
