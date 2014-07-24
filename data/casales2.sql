-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 07, 2013 at 07:19 PM
-- Server version: 5.6.12-log
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `casales2`
--
CREATE DATABASE IF NOT EXISTS `casales2` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `casales2`;

-- --------------------------------------------------------

--
-- Table structure for table `crm_account`
--

CREATE TABLE IF NOT EXISTS `crm_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_bin NOT NULL,
  `account_type` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `do_not_call` tinyint(4) DEFAULT '0',
  `do_not_mail` tinyint(4) DEFAULT '0',
  `do_not_email` tinyint(4) DEFAULT '0',
  `notes` longtext COLLATE utf8_bin,
  `website` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `group_id` int(10) unsigned DEFAULT NULL,
  `source` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `referral_id` int(10) unsigned DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  `last_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crm_account_ibfk_3` (`group_id`),
  KEY `crm_account_ibfk_4` (`source`),
  KEY `crm_account_ibfk_5` (`referral_id`),
  KEY `crm_account_ibfk_2` (`account_type`),
  KEY `crm_account_idx_1` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=949 ;

-- --------------------------------------------------------

--
-- Table structure for table `crm_activity`
--

CREATE TABLE IF NOT EXISTS `crm_activity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `discriminator` varchar(30) COLLATE utf8_bin NOT NULL,
  `account_id` int(10) unsigned DEFAULT NULL,
  `contact_id` int(10) unsigned DEFAULT NULL,
  `lead_id` int(10) unsigned DEFAULT NULL,
  `opportunity_id` int(10) unsigned DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_bin NOT NULL,
  `scheduled_start` datetime DEFAULT NULL,
  `scheduled_end` datetime DEFAULT NULL,
  `actual_start` datetime DEFAULT NULL,
  `actual_end` datetime DEFAULT NULL,
  `state` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `status` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `priority` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `telephone_id` int(10) unsigned DEFAULT NULL,
  `address_id` int(10) unsigned DEFAULT NULL,
  `direction` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `original_start_date` datetime DEFAULT NULL,
  `left_voice_mail` tinyint(1) DEFAULT '0',
  `percent_complete` tinyint(4) DEFAULT NULL,
  `location` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `notes` mediumtext COLLATE utf8_bin,
  `long_notes` longtext COLLATE utf8_bin,
  `creation_date` datetime DEFAULT NULL,
  `last_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crm_activity_idx1` (`discriminator`),
  KEY `crm_activity_idx2` (`scheduled_start`),
  KEY `crm_activity_idx3` (`scheduled_end`),
  KEY `crm_activity_idx4` (`actual_end`),
  KEY `crm_activity_idx5` (`state`),
  KEY `crm_activity_idx6` (`status`),
  KEY `crm_activity_idx7` (`priority`),
  KEY `crm_activity_idx8` (`percent_complete`),
  KEY `crm_activity_fk1` (`account_id`),
  KEY `crm_activity_fk2` (`contact_id`),
  KEY `crm_activity_fk3` (`opportunity_id`),
  KEY `crm_activity_fk4` (`telephone_id`),
  KEY `crm_activity_fk5` (`address_id`),
  KEY `crm_activity_fk6` (`lead_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Table structure for table `crm_address`
--

CREATE TABLE IF NOT EXISTS `crm_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(20) COLLATE utf8_bin NOT NULL,
  `account_id` int(10) unsigned DEFAULT NULL,
  `contact_id` int(10) unsigned DEFAULT NULL,
  `lead_id` int(10) unsigned DEFAULT NULL,
  `address1` varchar(64) COLLATE utf8_bin NOT NULL,
  `address2` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `address3` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `city` varchar(64) COLLATE utf8_bin NOT NULL,
  `state_id` int(10) unsigned DEFAULT NULL,
  `postal_code` varchar(12) COLLATE utf8_bin DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  `last_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crm_address_ibfk_1` (`account_id`),
  KEY `crm_address_ibfk_2` (`contact_id`),
  KEY `crm_address_ibfk_3` (`state_id`),
  KEY `crm_address_ibfk_4` (`type`),
  KEY `crm_address_fk5` (`lead_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=571 ;

-- --------------------------------------------------------

--
-- Table structure for table `crm_contact`
--

CREATE TABLE IF NOT EXISTS `crm_contact` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned DEFAULT NULL,
  `salutation` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `prefix` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `first_name` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `middle_name` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `last_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `suffix` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `display_name` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `nickname` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `sort_name` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `job_title` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `gender` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `email1` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `email2` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `do_not_call` tinyint(4) DEFAULT '0',
  `do_not_mail` tinyint(4) DEFAULT '0',
  `do_not_email` tinyint(4) DEFAULT '0',
  `description` longblob,
  `is_primary_contact` tinyint(4) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `assistant_name` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `interests` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  `last_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crm_contact_ibfk_1` (`account_id`),
  KEY `crm_contact_idx_1` (`last_name`),
  KEY `crm_contact_idx_2` (`sort_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2617 ;

-- --------------------------------------------------------

--
-- Table structure for table `crm_group`
--

CREATE TABLE IF NOT EXISTS `crm_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `description` varchar(64) COLLATE utf8_bin NOT NULL,
  `creation_date` datetime DEFAULT NULL,
  `last_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crm_group_ibfk_1` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Table structure for table `crm_lead`
--

CREATE TABLE IF NOT EXISTS `crm_lead` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned DEFAULT NULL,
  `company_name` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `confirm_interest` tinyint(1) DEFAULT '0',
  `contact_id` int(10) unsigned DEFAULT NULL,
  `decision_maker` tinyint(1) DEFAULT '0',
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `do_not_email` tinyint(1) DEFAULT '0',
  `do_not_mail` tinyint(1) DEFAULT '0',
  `do_not_phone` tinyint(1) DEFAULT '0',
  `email1` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `email2` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `estimated_close_date` date DEFAULT NULL,
  `estimated_value` int(10) unsigned DEFAULT NULL,
  `evaluate_fit` tinyint(1) DEFAULT '0',
  `first_name` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `full_name` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `initial_contact` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `job_title` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `last_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `lead_quality` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `lead_source` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `middle_name` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `need` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `opportunity_id` int(10) unsigned DEFAULT NULL,
  `prefix` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `priority` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `purchase_process` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `purchase_timeframe` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `qualification_comments` mediumtext COLLATE utf8_bin,
  `revenue` int(10) unsigned DEFAULT NULL,
  `sales_stage` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `sales_stage_code` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `salutation` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `schedule_followup_prospect` datetime DEFAULT NULL,
  `schedule_followup_qualify` datetime DEFAULT NULL,
  `state` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `status` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `suffix` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  `last_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crm_lead_idx1` (`last_name`),
  KEY `crm_lead_idx2` (`first_name`),
  KEY `crm_lead_idx3` (`full_name`),
  KEY `crm_lead_idx4` (`company_name`),
  KEY `crm_lead_idx5` (`do_not_email`),
  KEY `crm_lead_idx6` (`do_not_mail`),
  KEY `crm_lead_idx7` (`do_not_phone`),
  KEY `crm_lead_idx8` (`estimated_close_date`),
  KEY `crm_lead_idx9` (`priority`),
  KEY `crm_lead_idx10` (`state`),
  KEY `crm_lead_idx11` (`status`),
  KEY `crm_lead_fk1` (`account_id`),
  KEY `crm_lead_fk2` (`contact_id`),
  KEY `crm_lead_fk3` (`opportunity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `crm_opportunity`
--

CREATE TABLE IF NOT EXISTS `crm_opportunity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned NOT NULL,
  `contact_id` int(10) unsigned NOT NULL,
  `description` varchar(64) COLLATE utf8_bin NOT NULL,
  `initial_contact` varchar(30) COLLATE utf8_bin NOT NULL,
  `opportunity_rating` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `status` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `stage_id` int(10) unsigned DEFAULT NULL,
  `date_opened` datetime DEFAULT NULL,
  `lead_date` datetime DEFAULT NULL,
  `sales_potential` bigint(20) DEFAULT NULL,
  `close_probability` bigint(20) DEFAULT NULL,
  `sales_amount` decimal(17,2) DEFAULT NULL,
  `actual_amount` decimal(17,2) DEFAULT NULL,
  `estimated_close_date` datetime DEFAULT NULL,
  `est_close_date_probability` bigint(20) DEFAULT NULL,
  `actual_close_date` datetime DEFAULT NULL,
  `is_interested` tinyint(4) DEFAULT NULL,
  `sent_brochure` tinyint(4) DEFAULT NULL,
  `is_win` tinyint(4) DEFAULT NULL,
  `is_closed` tinyint(4) DEFAULT NULL,
  `is_dormant` tinyint(4) DEFAULT NULL,
  `needs` text COLLATE utf8_bin,
  `pain_points` text COLLATE utf8_bin,
  `notes` longtext COLLATE utf8_bin,
  `creation_date` datetime DEFAULT NULL,
  `last_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crm_opportunity_ibfk_1` (`account_id`),
  KEY `crm_opportunity_ibfk_2` (`contact_id`),
  KEY `crm_opportunity_ibfk_4` (`initial_contact`),
  KEY `crm_opportunity_idx_1` (`date_opened`),
  KEY `crm_opportunity_idx_2` (`close_probability`),
  KEY `crm_opportunity_idx_3` (`opportunity_rating`),
  KEY `crm_opportunity_idx_4` (`status`),
  KEY `crm_opportunity_idx_5` (`stage_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `crm_region`
--

CREATE TABLE IF NOT EXISTS `crm_region` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_bin NOT NULL,
  `abbreviation` varchar(4) COLLATE utf8_bin NOT NULL,
  `creation_date` datetime DEFAULT NULL,
  `last_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=60 ;

-- --------------------------------------------------------

--
-- Table structure for table `crm_stage`
--

CREATE TABLE IF NOT EXISTS `crm_stage` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_bin NOT NULL,
  `sequence` tinyint(4) unsigned DEFAULT NULL,
  `notes` text COLLATE utf8_bin,
  `creation_date` datetime DEFAULT NULL,
  `last_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `crm_telephone`
--

CREATE TABLE IF NOT EXISTS `crm_telephone` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `account_id` int(10) unsigned DEFAULT NULL,
  `contact_id` int(10) unsigned DEFAULT NULL,
  `lead_id` int(10) unsigned DEFAULT NULL,
  `phone` varchar(32) COLLATE utf8_bin NOT NULL,
  `is_primary` tinyint(4) DEFAULT '0',
  `creation_date` datetime DEFAULT NULL,
  `last_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crm_telephone_ibfk_1` (`account_id`),
  KEY `crm_telephone_ibfk_2` (`contact_id`),
  KEY `crm_telephone_ibfk_3` (`type`),
  KEY `crm_telephone_fk4` (`lead_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1466 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `crm_account`
--
ALTER TABLE `crm_account`
  ADD CONSTRAINT `crm_account_ibfk_3` FOREIGN KEY (`group_id`) REFERENCES `crm_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `crm_account_ibfk_5` FOREIGN KEY (`referral_id`) REFERENCES `crm_account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `crm_activity`
--
ALTER TABLE `crm_activity`
  ADD CONSTRAINT `crm_activity_ibfk_6` FOREIGN KEY (`lead_id`) REFERENCES `crm_lead` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `crm_activity_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `crm_account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `crm_activity_ibfk_2` FOREIGN KEY (`contact_id`) REFERENCES `crm_contact` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `crm_activity_ibfk_3` FOREIGN KEY (`opportunity_id`) REFERENCES `crm_opportunity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `crm_activity_ibfk_4` FOREIGN KEY (`telephone_id`) REFERENCES `crm_telephone` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `crm_activity_ibfk_5` FOREIGN KEY (`address_id`) REFERENCES `crm_address` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `crm_address`
--
ALTER TABLE `crm_address`
  ADD CONSTRAINT `crm_address_ibfk_4` FOREIGN KEY (`lead_id`) REFERENCES `crm_lead` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `crm_address_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `crm_account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `crm_address_ibfk_2` FOREIGN KEY (`contact_id`) REFERENCES `crm_contact` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `crm_address_ibfk_3` FOREIGN KEY (`state_id`) REFERENCES `crm_region` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `crm_contact`
--
ALTER TABLE `crm_contact`
  ADD CONSTRAINT `crm_contact_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `crm_account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `crm_group`
--
ALTER TABLE `crm_group`
  ADD CONSTRAINT `crm_group_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `crm_group` (`id`);

--
-- Constraints for table `crm_lead`
--
ALTER TABLE `crm_lead`
  ADD CONSTRAINT `crm_lead_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `crm_account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `crm_lead_ibfk_2` FOREIGN KEY (`contact_id`) REFERENCES `crm_contact` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `crm_lead_ibfk_3` FOREIGN KEY (`opportunity_id`) REFERENCES `crm_opportunity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `crm_opportunity`
--
ALTER TABLE `crm_opportunity`
  ADD CONSTRAINT `crm_opportunity_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `crm_account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `crm_opportunity_ibfk_2` FOREIGN KEY (`contact_id`) REFERENCES `crm_contact` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `crm_opportunity_ibfk_6` FOREIGN KEY (`stage_id`) REFERENCES `crm_stage` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `crm_telephone`
--
ALTER TABLE `crm_telephone`
  ADD CONSTRAINT `crm_telephone_ibfk_3` FOREIGN KEY (`lead_id`) REFERENCES `crm_lead` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `crm_telephone_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `crm_account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `crm_telephone_ibfk_2` FOREIGN KEY (`contact_id`) REFERENCES `crm_contact` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
