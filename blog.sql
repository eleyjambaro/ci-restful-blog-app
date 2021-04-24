--
-- Table structure for table `users`
--

CREATE TABLE `blog`.`users` (
`id` SERIAL NOT NULL,
`first_name` VARCHAR(50) NOT NULL,
`last_name` VARCHAR(50) NOT NULL,
`username` VARCHAR(50) NOT NULL,
`password` VARCHAR(255) NOT NULL,
`email` VARCHAR(150) NOT NULL,
`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
`updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
`is_admin` BOOLEAN NOT NULL DEFAULT FALSE,
PRIMARY KEY (`id`),
UNIQUE `unique_username` (`username`(50)),
UNIQUE `unique_email` (`username`(50)),
) ENGINE = InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `posts`
--

CREATE TABLE `blog`.`posts` (
`id` SERIAL NOT NULL,
`author_id` BIGINT NOT NULL,
`title` VARCHAR(150) NULL DEFAULT NULL,
`content` VARCHAR(10000) NOT NULL,
`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
`updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`id`),
INDEX `index_author_id` (`author_id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;