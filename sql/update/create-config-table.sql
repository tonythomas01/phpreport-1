--
-- New table for application configuration: config
--
-- The table will only have one row. To ensure that, there is a primary key row
-- which is set to 1 by default and at the same time has a restriction to check
-- the only posible value of that row is 1.
-- See: http://archives.postgresql.org/pgsql-general/2004-08/msg01569.php
--
-- Rows:
-- * id: primary key, one-row restriction.
-- * version: contains the version number to ease automated upgrades
--

create table config (
  id                        INT PRIMARY KEY NOT NULL DEFAULT(1) CHECK (id = 1),
  version                   varchar(20)
) ;

--
-- Insert first row of the config table. From now on, only UPDATE statements
-- should be used on this table.
-- Set version number to 2.1
--

INSERT INTO config(version) VALUES ('2.1');
