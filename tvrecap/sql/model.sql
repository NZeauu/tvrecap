-- DROP TABLE IF EXISTS
DROP TABLE IF EXISTS `Seen_List`;
DROP TABLE IF EXISTS `Accounts`;
DROP TABLE IF EXISTS `Films`;
DROP TABLE IF EXISTS `Episodes`;
DROP TABLE IF EXISTS `Séries`;
DROP TABLE IF EXISTS `Covers`;

-- CREATE DATABASE
CREATE TABLE `Covers`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(255) NOT NULL,
    `image` MEDIUMBLOB NOT NULL
);
ALTER TABLE
    `Covers` ADD INDEX `covers_id_index`(`id`);
CREATE TABLE `Episodes`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(255) NOT NULL,
    `num_ep` BIGINT NOT NULL,
    `duree` BIGINT NOT NULL,
    `saison` BIGINT NOT NULL,
    `serie_id` BIGINT UNSIGNED NOT NULL
);
ALTER TABLE
    `Episodes` ADD INDEX `episodes_id_index`(`id`);
ALTER TABLE 
    `Episodes` ADD CONSTRAINT `unique_episode_season` UNIQUE (`num_ep`, `saison`, `serie_id`);
CREATE TABLE `Séries`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(255) NOT NULL,
    `nb_saisons` INT NOT NULL,
    `date_sortie` YEAR NOT NULL,
    `categorie` VARCHAR(255) NOT NULL,
    `synopsis` VARCHAR(1000) NOT NULL,
    `actors` VARCHAR(255) NOT NULL,
    `realisator` VARCHAR(255) NOT NULL,
    `image_id` BIGINT UNSIGNED NOT NULL
);
ALTER TABLE
    `Séries` ADD INDEX `séries_id_index`(`id`);
ALTER TABLE 
    `Séries` ADD CONSTRAINT `unique_serie_entered` UNIQUE (`nom`, `nb_saisons`, `image_id`);
CREATE TABLE `Films`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(255) NOT NULL,
    `image_id` BIGINT UNSIGNED NOT NULL,
    `duree` BIGINT NOT NULL,
    `date_sortie` YEAR NOT NULL,
    `categorie` VARCHAR(255) NOT NULL,
    `synopsis` VARCHAR(1000) NOT NULL,
    `actors` VARCHAR(255) NOT NULL,
    `realisator` VARCHAR(255) NOT NULL
);
ALTER TABLE
    `Films` ADD INDEX `films_id_index`(`id`);
ALTER TABLE 
    `Films` ADD CONSTRAINT `unique_film_entered` UNIQUE (`nom`, `image_id`, `date_sortie`);
CREATE TABLE `Accounts`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL
    `administrator` BOOLEAN NOT NULL DEFAULT FALSE
);
ALTER TABLE
    `Accounts` ADD INDEX `accounts_id_index`(`id`);
ALTER TABLE 
    `Accounts` ADD CONSTRAINT `unique_user` UNIQUE (`username`, `email`);
ALTER TABLE 
    `Accounts` ADD UNIQUE(`administrator`);
CREATE TABLE `Seen_List`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `type` VARCHAR(255) NOT NULL COMMENT 'movie/serie',
    `movie_id` BIGINT UNSIGNED,
    `episode_id` BIGINT UNSIGNED
);
ALTER TABLE
    `Seen_List` ADD INDEX `seen_list_id_index`(`id`);
ALTER TABLE 
    `Seen_List` ADD CONSTRAINT `unique_movie_user_entry` UNIQUE (`user_id`, `type`, `movie_id`);
ALTER TABLE 
    `Seen_List` ADD CONSTRAINT `unique_episode_user_entry` UNIQUE (`user_id`, `type`, `episode_id`);
ALTER TABLE
    `Seen_List` ADD CONSTRAINT `seen_list_user_id_foreign` FOREIGN KEY(`user_id`) REFERENCES `Accounts`(`id`);
ALTER TABLE
    `Seen_List` ADD CONSTRAINT `seen_list_episode_id_foreign` FOREIGN KEY(`episode_id`) REFERENCES `Episodes`(`id`);
ALTER TABLE
    `Séries` ADD CONSTRAINT `séries_image_id_foreign` FOREIGN KEY(`image_id`) REFERENCES `Covers`(`id`);
ALTER TABLE
    `Films` ADD CONSTRAINT `films_image_id_foreign` FOREIGN KEY(`image_id`) REFERENCES `Covers`(`id`);
ALTER TABLE
    `Episodes` ADD CONSTRAINT `episodes_serie_id_foreign` FOREIGN KEY(`serie_id`) REFERENCES `Séries`(`id`);
ALTER TABLE
    `Seen_List` ADD CONSTRAINT `seen_list_movie_id_foreign` FOREIGN KEY(`movie_id`) REFERENCES `Films`(`id`);