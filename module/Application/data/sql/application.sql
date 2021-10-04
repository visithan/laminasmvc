CREATE TABLE IF NOT EXISTS `quizzes`(
 `quiz_id`     INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 `user_id`     INT(11) UNSIGNED NOT NULL,
 `category_id` INT(11) UNSIGNED NOT NULL,
 `title`       VARCHAR(100)     NOT NULL,
 `question`    TEXT             NOT NULL,
 `status`      TINYINT(1)       NOT NULL,
 `views`       INT(11) UNSIGNED NOT NULL DEFAULT '0',
 `total`       INT(11) UNSIGNED NOT NULL DEFAULT '0',
 `timeout`     DATETIME         NOT NULL,
 `created`     DATETIME         NOT NULL,
 PRIMARY KEY(`quiz_id`),
 KEY `user_id` (`user_id`),
 KEY `category_id` (`category_id`),
 KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `answers`(
 `answer_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 `quiz_id`   INT(11) UNSIGNED NOT NULL,
 `answer`    TEXT             NOT NULL,
 `tally`     INT(11) UNSIGNED NOT NULL DEFAULT '0',
 PRIMARY KEY(`answer_id`),
  KEY `quiz_id` (`quiz_id`)
)ENGINE=InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tallies`(
 `quiz_id`   INT(11) UNSIGNED NOT NULL,
 `answer_id` INT(11) UNSIGNED NOT NULL,
 `user_id`   INT(11) UNSIGNED NOT NULL,
 `created`   DATETIME         NOT NULL,
 PRIMARY KEY(`quiz_id`, `user_id`)
)ENGINE=InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `categories`(
 `category_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 `category`    VARCHAR(100)     NOT NULL,
 PRIMARY KEY(`category_id`),
 KEY `category` (`category`)
)ENGINE=InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT IGNORE INTO `categories` (`category_id`, `category`) VALUES
(1, 'Health'),
(2, 'Politics'),
(3, 'Science'),
(4, 'Sports'),
(5, 'World');
