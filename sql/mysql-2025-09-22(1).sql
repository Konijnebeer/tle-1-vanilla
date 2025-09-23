CREATE TABLE `users`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL,
    `username` VARCHAR(50) NOT NULL,
    `phone_number` VARCHAR(50) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
ALTER TABLE
    `users` ADD UNIQUE `users_email_unique`(`email`);
ALTER TABLE
    `users` ADD UNIQUE `users_username_unique`(`username`);
ALTER TABLE
    `users` ADD UNIQUE `users_phone_number_unique`(`phone_number`);
CREATE TABLE `posts`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `group_id` BIGINT UNSIGNED NOT NULL,
    `image_id` CHAR(36) NULL,
    `text_content` TEXT NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
ALTER TABLE
    `posts` ADD INDEX `posts_user_id_index`(`user_id`);
ALTER TABLE
    `posts` ADD INDEX `posts_image_id_index`(`image_id`);
ALTER TABLE
    `posts` ADD INDEX `posts_group_id_index`(`group_id`);
CREATE TABLE `images`(
    `id` CHAR(36) NOT NULL,
    `path` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `extension` ENUM('jpg', 'png', 'webp', 'gif') NOT NULL,
    `posted` BOOLEAN NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(`id`)
);
CREATE TABLE `groups`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `discription` VARCHAR(255) NOT NULL,
    `theme` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
ALTER TABLE
    `groups` ADD INDEX `groups_user_id_index`(`user_id`);
CREATE TABLE `user_group`(
    `user_id` BIGINT UNSIGNED NOT NULL,
    `group_id` BIGINT UNSIGNED NOT NULL
);
ALTER TABLE
    `user_group` ADD INDEX `user_group_user_id_index`(`user_id`);
ALTER TABLE
    `user_group` ADD INDEX `user_group_group_id_index`(`group_id`);
CREATE TABLE `roles`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `type` ENUM('') NOT NULL,
    `logo` VARCHAR(255) NOT NULL
);
CREATE TABLE `role_user`(
    `role_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE
    `role_user` ADD INDEX `role_user_role_id_index`(`role_id`);
ALTER TABLE
    `role_user` ADD INDEX `role_user_user_id_index`(`user_id`);
ALTER TABLE
    `role_user` ADD CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY(`role_id`) REFERENCES `roles`(`id`);
ALTER TABLE
    `user_group` ADD CONSTRAINT `user_group_user_id_foreign` FOREIGN KEY(`user_id`) REFERENCES `users`(`id`);
ALTER TABLE
    `groups` ADD CONSTRAINT `groups_user_id_foreign` FOREIGN KEY(`user_id`) REFERENCES `users`(`id`);
ALTER TABLE
    `posts` ADD CONSTRAINT `posts_image_id_foreign` FOREIGN KEY(`image_id`) REFERENCES `images`(`id`);
ALTER TABLE
    `posts` ADD CONSTRAINT `posts_user_id_foreign` FOREIGN KEY(`user_id`) REFERENCES `users`(`id`);
ALTER TABLE
    `posts` ADD CONSTRAINT `posts_group_id_foreign` FOREIGN KEY(`group_id`) REFERENCES `groups`(`id`);
ALTER TABLE
    `user_group` ADD CONSTRAINT `user_group_group_id_foreign` FOREIGN KEY(`group_id`) REFERENCES `groups`(`id`);
ALTER TABLE
    `role_user` ADD CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY(`user_id`) REFERENCES `users`(`id`);