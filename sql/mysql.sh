#!/bin/sh
. common.sh
mysql --default-character-set=utf8 $conn -u $user $pass $db $*
