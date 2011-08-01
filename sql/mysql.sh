#!/bin/bash
. common.sh
mysql --default-character-set=utf8 $conn -u $user $pass $db $*
