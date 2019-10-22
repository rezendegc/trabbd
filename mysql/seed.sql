CREATE TABLE `trabbd`.`users` (
  `id` CHAR(36) NOT NULL,
  `name` CHAR(64) NOT NULL,
  `username` CHAR(32) NOT NULL,
  `relevancia` INT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `username_UNIQUE` (`username` ASC),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC));

LOAD DATA INFILE '/var/local/users.csv' IGNORE 
INTO TABLE `trabbd`.`users`
FIELDS TERMINATED BY ',' 
LINES TERMINATED BY '\n';