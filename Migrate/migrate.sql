SET CHARACTER SET utf8mb4;

SET foreign_key_checks = 0;
TRUNCATE TABLE `actions`;
TRUNCATE TABLE `contacts`;
TRUNCATE TABLE `devices`;
TRUNCATE TABLE `groups`;
TRUNCATE TABLE `languages`;
TRUNCATE TABLE `plugins`;
TRUNCATE TABLE `quota`;
TRUNCATE TABLE `received`;
TRUNCATE TABLE `scheduled`;
TRUNCATE TABLE `sent`;
TRUNCATE TABLE `webhooks`;
TRUNCATE TABLE `settings`;

INSERT INTO `settings` (`name`, `value`) VALUES
('site_name', 'Zender'),
('site_desc', 'Your awesome website!'),
('purchase_code', ''),
('default_lang',  '1'),
('registrations', '1'),
('mail_function', '2'),
('site_mail', 'noreply@yourdomain.com'),
('smtp_host', ''),
('smtp_port', ''),
('smtp_username', ''),
('smtp_password', ''),
('paypal_email', ''),
('recaptcha_key', ''),
('recaptcha_secret', ''),
('package_name', 'com.zender.gateway'),
('app_name', 'Zender Gateway'),
('app_desc', 'The awesome app!'),
('app_color', '#003853'),
('protocol', '1'),
('paypal_test', '1'),
('theme_background', '#003853'),
('theme_highlight', '#ffffff'),
('mollie_key', ''),
('currency', 'USD'),
('providers', 'paypal,mollie,bank'),
('livechat', '2'),
('analytics_key', ''),
('tawk_id', ''),
('sent_limit', '10'),
('received_limit', '10'),
('message_min', '5'),
('message_max', '0'),
('message_mark', 'Sent by Zender'),
('smtp_secure', '1'),
('facebook_id', ''),
('facebook_secret', ''),
('google_id', ''),
('google_secret', ''),
('vk_id', ''),
('vk_secret', ''),
('social_auth', '2'),
('social_platforms', 'facebook,google,vk'),
('homepage', '1'),
('apk_version', '1'),
('freemodel', '1'),
('reset_mode', '1'),
('theme_spinner', '#ffffff'),
('admin_api', '0'),
('app_icon_remote', ''),
('app_splash_remote', ''),
('app_logo_remote', ''),
('app_loginlogo_remote', ''),
('app_js', ''),
('app_css', ''),
('app_layout', ''),
('build_email', 'validemail@gmail.com'),
('bank_template', 'Hi <strong>{{user.name}}</strong>!\r\nPlease pay <strong>{{order.price}}</strong> in the bank address below:\r\n<code>\r\nBank: BDO UNIBANK, INC.\r\nSwift: BNORPHMM\r\n</code>\r\n1. After paying, please send us the order details with the following format:\r\n<code>\r\nOrder Type: <strong>Package</strong> or <strong>Credits</strong>\r\nOrder Name: <strong>Package name</strong> or <strong>Credits</strong>\r\nAmount: <strong>Month duration</strong> or <strong>Credit amount</strong>\r\n</code>\r\nThen attached the receipt image. Please use your registered email ({{user.email}}) for sending this message.\r\n\r\n2. After we confirm the payment, we will apply your package subscription or credits.\r\n\r\nThank you!'),
('auth_redirect', '1'),
('default_country', 'PH'),
('default_timezone', 'asia/manila'),
('mailing_triggers', '0'),
('recaptcha', '2'),
('mailing_address', 'nogats07@gmail.com'),
('wa_server', ''),
('wa_port', '7001'),
('partner_commission', '10'),
('partner_minimum', '50');

ALTER TABLE `actions`
ADD `source` tinyint(4) NOT NULL AFTER `type`,
ADD `link` text COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `event`,
DROP `devices`,
CHANGE `keywords` `keywords` text COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `name`,
DROP `link`;

ALTER TABLE `contacts`
CHANGE `gid` `groups` tinytext NOT NULL AFTER `uid`,
CHANGE `phone` `phone` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `groups`,
CHANGE `name` `name` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `phone`;

ALTER TABLE `deleted`
CHANGE `uid` `uid` int(11) NOT NULL AFTER `id`,
CHANGE `rid` `rid` int(11) NOT NULL AFTER `uid`;

ALTER TABLE `devices`
CHANGE `did` `did` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `uid`,
CHANGE `name` `name` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `did`,
CHANGE `version` `version` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `name`,
CHANGE `manufacturer` `manufacturer` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `version`,
CHANGE `random_min` `random_min` int NOT NULL AFTER `random_send`,
CHANGE `random_max` `random_max` int NOT NULL AFTER `random_min`,
ADD `limit_status` tinyint(4) NOT NULL AFTER `random_max`,
ADD `limit_interval` tinyint(4) NOT NULL AFTER `limit_status`,
ADD `limit_number` int NOT NULL AFTER `limit_interval`,
ADD `packages` text NOT NULL AFTER `limit_number`,
ADD `receive_sms` tinyint NOT NULL AFTER `packages`,
ADD `global_priority` tinyint(4) NOT NULL AFTER `global_device`,
ADD `global_slots` tinytext NOT NULL AFTER `global_priority`,
ADD `country` tinytext NOT NULL AFTER `global_slots`,
ADD `rate` float NOT NULL AFTER `country`,
ADD `online_id` tinytext NOT NULL AFTER `rate`,
ADD `online_status` tinyint NOT NULL AFTER `online_id`,
DROP `allowed_packages`;

ALTER TABLE `groups`
CHANGE `name` `name` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `uid`;

ALTER TABLE `keys`
CHANGE `key` `secret` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `uid`,
CHANGE `name` `name` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `secret`,
DROP `devices`;

ALTER TABLE `languages`
ADD `order` int NOT NULL AFTER `iso`,
DROP `translations`,
CHANGE `name` `name` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `order`;

INSERT INTO `languages` (`rtl`, `iso`, `order`, `name`) VALUES
('2', 'US', '1', 'English');

ALTER TABLE `packages`
ADD `ussd_limit` int(11) NOT NULL AFTER `receive_limit`,
ADD `notification_limit` int(11) NOT NULL AFTER `ussd_limit`,
ADD `action_limit` int(11) NOT NULL AFTER `webhook_limit`,
ADD `scheduled_limit` int NOT NULL AFTER `action_limit`,
ADD `wa_send_limit` int NOT NULL AFTER `scheduled_limit`,
ADD `wa_receive_limit` int NOT NULL AFTER `wa_send_limit`,
ADD `wa_account_limit` int NOT NULL AFTER `wa_receive_limit`,
CHANGE `name` `name` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `wa_account_limit`;

ALTER TABLE `pages`
CHANGE `roles` `roles` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `id`,
CHANGE `slug` `slug` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `roles`,
CHANGE `name` `name` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `logged`;

ALTER TABLE `quota`
ADD `wa_sent` int(11) NOT NULL,
ADD `wa_received` int(11) NOT NULL AFTER `wa_sent`,
ADD `ussd` int(11) NOT NULL AFTER `wa_received`,
ADD `notifications` int NOT NULL AFTER `ussd`;

ALTER TABLE `received`
CHANGE `did` `did` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `uid`,
ADD `slot` tinyint NOT NULL AFTER `did`,
CHANGE `phone` `phone` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `slot`,
CHANGE `message` `message` text COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `phone`;

ALTER TABLE `roles`
CHANGE `name` `name` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `id`,
CHANGE `permissions` `permissions` text COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `name`;

ALTER TABLE `scheduled`
CHANGE `did` `did` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `uid`,
ADD `mode` tinyint(4) NOT NULL AFTER `sim`,
ADD `gateway` int NOT NULL AFTER `mode`,
CHANGE `groups` `groups` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `gateway`,
CHANGE `name` `name` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `groups`,
CHANGE `numbers` `numbers` text COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `name`,
CHANGE `message` `message` text COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `numbers`,
CHANGE `last_send` `last_send` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `repeat`,
CHANGE `send_date` `send_date` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `last_send`;

ALTER TABLE `sent`
ADD `cid` int(11) NOT NULL AFTER `id`,
CHANGE `did` `did` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `uid`,
ADD `gateway` int NOT NULL AFTER `did`,
CHANGE `api` `api` tinyint(4) NOT NULL AFTER `gateway`,
CHANGE `sim` `sim` tinyint(4) NOT NULL AFTER `api`,
ADD `mode` tinyint(4) NOT NULL AFTER `sim`,
CHANGE `priority` `priority` tinyint(4) NOT NULL AFTER `mode`,
CHANGE `phone` `phone` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `priority`,
CHANGE `message` `message` mediumtext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `phone`,
CHANGE `status` `status` tinyint(4) NOT NULL AFTER `message`,
ADD `status_code` tinytext NOT NULL AFTER `status`;

ALTER TABLE `templates`
CHANGE `name` `name` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `uid`;

ALTER TABLE `transactions`
ADD `type` tinyint NOT NULL AFTER `pid`,
CHANGE `currency` `currency` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `price`,
CHANGE `provider` `provider` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `duration`;

ALTER TABLE `users`
CHANGE `email` `email` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `role`,
CHANGE `password` `password` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `email`,
ADD `credits` float NOT NULL AFTER `password`,
ADD `earnings` float NOT NULL AFTER `credits`,
CHANGE `name` `name` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `earnings`,
ADD `alertsound` tinyint NOT NULL AFTER `providers`,
DROP `receive_branded`,
CHANGE `timezone` `timezone` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `suspended`,
ADD `formatting` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `timezone`,
ADD `partner` tinyint NOT NULL AFTER `formatting`,
ADD `confirmed` tinyint NOT NULL AFTER `partner`;

ALTER TABLE `vouchers`
CHANGE `code` `code` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `package`,
CHANGE `name` `name` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `code`;

ALTER TABLE `webhooks`
ADD `events` tinytext NOT NULL AFTER `uid`,
CHANGE `secret` `secret` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `events`,
CHANGE `url` `url` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `secret`,
CHANGE `name` `name` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `url`,
DROP `devices`;

ALTER TABLE `widgets`
CHANGE `size` `size` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `type`,
CHANGE `position` `position` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `size`,
CHANGE `icon` `icon` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `position`,
CHANGE `name` `name` tinytext COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `icon`;

UPDATE `users` SET `confirmed` = 1, `partner` = 2;

CREATE TABLE `campaigns` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `uid` int NOT NULL,
  `did` tinytext NOT NULL,
  `gateway` int NOT NULL,
  `mode` tinyint NOT NULL,
  `status` tinyint NOT NULL,
  `name` tinytext NOT NULL,
  `contacts` int NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) COLLATE 'utf8mb4_unicode_ci';

CREATE TABLE `commissions` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `pid` int NOT NULL,
  `sid` int NOT NULL,
  `mid` int NOT NULL,
  `did` tinytext NOT NULL,
  `original_amount` float NOT NULL,
  `commission_amount` float NOT NULL,
  `currency` tinytext NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) COLLATE 'utf8mb4_unicode_ci';

CREATE TABLE `events` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `uid` int NOT NULL,
  `type` tinyint NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) COLLATE 'utf8mb4_unicode_ci';

CREATE TABLE `gateways` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` tinytext NOT NULL,
  `callback` tinyint NOT NULL,
  `callback_id` tinytext NOT NULL,
  `pricing` longtext NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) COLLATE 'utf8mb4_unicode_ci';

CREATE TABLE `marketing` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `type` tinyint NOT NULL,
  `users` text NOT NULL,
  `roles` tinytext NOT NULL,
  `title` text NOT NULL,
  `content` longtext NOT NULL,
  `image` tinytext NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) COLLATE 'utf8mb4_unicode_ci';

CREATE TABLE `notifications` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `uid` int NOT NULL,
  `did` tinytext NOT NULL,
  `package` tinytext NOT NULL,
  `title` tinytext NOT NULL,
  `text` text NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) COLLATE 'utf8mb4_unicode_ci';

CREATE TABLE `payouts` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `uid` int NOT NULL,
  `amount` int NOT NULL,
  `currency` tinytext NOT NULL,
  `provider` tinytext NOT NULL,
  `address` tinytext NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) COLLATE 'utf8mb4_unicode_ci';

CREATE TABLE `shorteners` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` tinytext NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) COLLATE 'utf8mb4_unicode_ci';

CREATE TABLE `unsubscribed` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `uid` int NOT NULL,
  `phone` tinytext NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) COLLATE 'utf8mb4_unicode_ci';

CREATE TABLE `ussd` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `uid` int NOT NULL,
  `did` tinytext NOT NULL,
  `sim` tinyint NOT NULL,
  `code` tinytext NOT NULL,
  `response` text NOT NULL,
  `status` tinyint NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) COLLATE 'utf8mb4_unicode_ci';

CREATE TABLE `utilities` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `uid` int NOT NULL,
  `type` tinyint NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) COLLATE 'utf8mb4_unicode_ci';

CREATE TABLE `visitors` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `country` tinytext NOT NULL,
  `browser` tinytext NOT NULL,
  `os` tinytext NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) COLLATE 'utf8mb4_unicode_ci';

CREATE TABLE `wa_accounts` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `uid` int NOT NULL,
  `wid` tinytext NOT NULL,
  `unique` text NOT NULL,
  `receive_chats` tinyint NOT NULL,
  `random_send` tinyint NOT NULL,
  `random_min` int NOT NULL,
  `random_max` int NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) COLLATE 'utf8mb4_unicode_ci';

CREATE TABLE `wa_campaigns` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `uid` int NOT NULL,
  `wid` tinytext NOT NULL,
  `type` tinytext NOT NULL,
  `status` int NOT NULL,
  `name` tinytext NOT NULL,
  `contacts` int NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) COLLATE 'utf8mb4_unicode_ci';

CREATE TABLE `wa_groups` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `uid` int NOT NULL,
  `wid` tinytext NOT NULL,
  `gid` tinytext NOT NULL,
  `name` tinytext NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) COLLATE 'utf8mb4_unicode_ci';

CREATE TABLE `wa_received` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `uid` int NOT NULL,
  `wid` tinytext NOT NULL,
  `unique` text NOT NULL,
  `phone` tinytext NOT NULL,
  `message` text NOT NULL,
  `receive_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) COLLATE 'utf8mb4_unicode_ci';

CREATE TABLE `wa_scheduled` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `uid` int NOT NULL,
  `wid` tinytext NOT NULL,
  `unique` text NOT NULL,
  `repeat` tinyint NOT NULL,
  `groups` tinytext NOT NULL,
  `name` tinytext NOT NULL,
  `numbers` text NOT NULL,
  `message` text NOT NULL,
  `last_send` tinytext NOT NULL,
  `send_date` tinytext NOT NULL
) COLLATE 'utf8mb4_unicode_ci';

CREATE TABLE `wa_sent` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `cid` int NOT NULL,
  `uid` int NOT NULL,
  `wid` tinytext NOT NULL,
  `unique` text NOT NULL,
  `phone` tinytext NOT NULL,
  `message` text NOT NULL,
  `status` tinyint NOT NULL,
  `api` tinyint NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) COLLATE 'utf8mb4_unicode_ci';

ALTER TABLE `scheduled`
CHANGE `repeat` `repeat` int NOT NULL AFTER `message`;

ALTER TABLE `wa_scheduled`
CHANGE `repeat` `repeat` int NOT NULL AFTER `unique`;