-- phpMyAdmin SQL Dump
-- Server Version: 5.5.38
-- PHP Version: 5.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

--
-- Table structure `Login_attempts`
--

CREATE TABLE IF NOT EXISTS `Login_attempts` (
  `id` bigint(19) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(40) COLLATE utf8_general_ci NOT NULL,
  `login` varchar(50) COLLATE utf8_general_ci NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure `Sessions`
--

CREATE TABLE IF NOT EXISTS `Sessions` (
  `session_id` varchar(40) COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `ip_address` varchar(16) COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `user_agent` varchar(150) COLLATE utf8_general_ci NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure `Users`
--

CREATE TABLE IF NOT EXISTS `Users` (
  `id` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 NOT NULL,
  `activated` tinyint(1) NOT NULL DEFAULT '1',
  `banned` tinyint(1) NOT NULL DEFAULT '0',
  `ban_reason` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `new_password_key` varchar(50) COLLATE utf8_general_ci DEFAULT NULL,
  `new_password_requested` datetime DEFAULT NULL,
  `new_email` varchar(100) COLLATE utf8_general_ci DEFAULT NULL,
  `new_email_key` varchar(50) COLLATE utf8_general_ci DEFAULT NULL,
  `last_ip` varchar(40) COLLATE utf8_general_ci NOT NULL,
  `last_login` datetime NOT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure `User_autologin`
--

CREATE TABLE IF NOT EXISTS `User_autologin` (
  `key_id` char(32) COLLATE utf8_general_ci NOT NULL,
  `user_id` bigint(19) unsigned NOT NULL DEFAULT '0',
  `user_agent` varchar(150) COLLATE utf8_general_ci NOT NULL,
  `last_ip` varchar(40) COLLATE utf8_general_ci NOT NULL,
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`key_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure `User_profiles`
--

CREATE TABLE IF NOT EXISTS `User_profiles` (
  `id` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(19) unsigned NOT NULL,
  `country` varchar(20) COLLATE utf8_general_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `lang_code` varchar(2) COLLATE utf8_general_ci NOT NULL DEFAULT 'en',
  `country_code` varchar(2) COLLATE utf8_general_ci NOT NULL DEFAULT 'us',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
