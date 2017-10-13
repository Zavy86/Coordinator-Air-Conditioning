--
-- Setup module Air-Conditioning
--
-- Version 0.0.1
--

-- --------------------------------------------------------

SET time_zone = "+00:00";
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

--
-- Table structure for table `air-conditioning_locations`
--

CREATE TABLE IF NOT EXISTS `air-conditioning_locations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `addTimestamp` int(11) unsigned NOT NULL,
  `addFkUser` int(11) unsigned NOT NULL,
  `updTimestamp` int(11) unsigned DEFAULT NULL,
  `updFkUser` int(11) unsigned DEFAULT NULL,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `air-conditioning_locations_zones`
--

CREATE TABLE IF NOT EXISTS `air-conditioning_locations_zones` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fkLocation` int(11) unsigned NOT NULL,
  `order` int(11) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `token` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `heater_relay` int(11) unsigned DEFAULT NULL,
  `cooler_relay` int(11) unsigned DEFAULT NULL,
  `dehumidifier_relay` int(11) unsigned DEFAULT NULL,
  `humidifier_relay` int(11) unsigned DEFAULT NULL,
  `plannings` text COLLATE utf8_unicode_ci NOT NULL,
  `addTimestamp` int(11) unsigned NOT NULL,
  `addFkUser` int(11) unsigned NOT NULL,
  `updTimestamp` int(11) unsigned DEFAULT NULL,
  `updFkUser` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkLocation` (`fkLocation`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `air-conditioning_locations_zones_detections`
--

CREATE TABLE IF NOT EXISTS `air-conditioning_locations_zones_detections` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fkZone` int(11) unsigned NOT NULL,
  `timestamp` int(11) unsigned NOT NULL,
  `temperature` double NOT NULL,
  `humidity` double unsigned NOT NULL,
  `heater_status` tinyint(1) unsigned NOT NULL COMMENT '0 off, 1 on',
  `cooler_status` tinyint(1) unsigned NOT NULL COMMENT '0 off, 1 on',
  `humidifier_status` tinyint(1) unsigned NOT NULL COMMENT '0 off, 1 on',
  `dehumidifier_status` tinyint(1) unsigned NOT NULL COMMENT '0 off, 1 on',
  PRIMARY KEY (`id`),
  KEY `fkZone` (`fkZone`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `air-conditioning_locations_modalities`
--

CREATE TABLE IF NOT EXISTS `air-conditioning_locations_modalities` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fkLocation` int(11) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `color` varchar(7) COLLATE utf8_unicode_ci NOT NULL,
  `temperature` double NOT NULL,
  `addTimestamp` int(11) unsigned NOT NULL,
  `addFkUser` int(11) unsigned NOT NULL,
  `updTimestamp` int(11) unsigned DEFAULT NULL,
  `updFkUser` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkLocation` (`fkLocation`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Constraints for dumped tables
--

--
-- Constraints for table `air-conditioning_locations_zones`
--
ALTER TABLE `air-conditioning_locations_zones`
  ADD CONSTRAINT `air-conditioning_locations_zones_ibfk_1` FOREIGN KEY (`fkLocation`) REFERENCES `air-conditioning_locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `air-conditioning_locations_zones_detections`
--
ALTER TABLE `air-conditioning_locations_zones_detections`
  ADD CONSTRAINT `air-conditioning_locations_zones_detections_ibfk_1` FOREIGN KEY (`fkZone`) REFERENCES `air-conditioning_locations_zones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `air-conditioning_locations_modalities`
--
ALTER TABLE `air-conditioning_locations_modalities`
  ADD CONSTRAINT `air-conditioning_locations_modalities_ibfk_1` FOREIGN KEY (`fkLocation`) REFERENCES `air-conditioning_locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- --------------------------------------------------------
