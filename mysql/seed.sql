CREATE TABLE `trabbd`.`users` (
  `id` CHAR(36) NOT NULL,
  `name` CHAR(64) NOT NULL,
  `username` CHAR(64) NOT NULL,
  `relevancia` INT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT(`username`,`name`)
) ENGINE=MyISAM;

LOAD DATA INFILE '/var/local/users.csv' IGNORE 
INTO TABLE `trabbd`.`users`
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
LINES TERMINATED BY '\n';
-- INDEX `name_INDEX` (`name`),
  -- INDEX `username_INDEX` (`username`)
  -- 