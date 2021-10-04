CREATE DATABASE IF NOT EXISTS `quiz`;

USE `quiz`;

CREATE TABLE IF NOT EXISTS `users` (
 `user_id`  INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 `username` VARCHAR(40) NOT NULL,
 `email`    VARCHAR(128) NOT NULL,
 `password` VARCHAR(80) NOT NULL,
 `birthday` DATE NOT NULL,
 `gender`   ENUM('Female', 'Male', 'Other') NOT NULL,
 `photo`    VARCHAR(100) NOT NULL DEFAULT 'anon.png',
 `role_id`  INT(11) UNSIGNED NOT NULL,
 `active`   TINYINT(1) NOT NULL DEFAULT '1',
 `views`    INT(11) UNSIGNED NOT NULL DEFAULT '0',
 `created`  DATETIME NOT NULL,
 `modified` DATETIME NOT NULL,
 PRIMARY KEY (`user_id`),
 UNIQUE KEY `username` (`username`),
 UNIQUE KEY `email` (`email`),
 KEY `role_id` (`role_id`),
 KEY `active` (`active`) 
) ENGINE=InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `roles`(
 `role_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 `role`    VARCHAR(25)      NOT NULL,
 PRIMEY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT IGNORE INTO `roles` (`role_id`, `role`) VALUES
(1, 'admin'),
(2, 'member'),
(3, 'guest');

CREATE TABLE IF NOT EXISTS `forgot` (
  `user_id` INT(11) UNSIGNED NOT NULL,
  `token`   VARCHAR(40) NOT NULL,
  PRIMARY KEY (`user_id`, `token`)
) ENGINE=InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;