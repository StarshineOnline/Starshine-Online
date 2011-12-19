-- table de gestion des maj de table
CREATE TABLE `db_auto_maj` (
`loaded` VARCHAR( 250 ) NOT NULL
) ENGINE = MYISAM COMMENT = 'cette table enregistre les scripts de mise à jour qui sont passés';

-- insert : this sql
insert into db_auto_maj values ('update_20111217.sql');
-- insert : old sqls
insert into db_auto_maj values ('update_20111205.sql'),
       ('update_20111209.sql'), ('update_20111214.sql');

-- create index
ALTER TABLE `db_auto_maj` ADD PRIMARY KEY ( `loaded` ) ;
