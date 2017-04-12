--
-- Setup module Air-Conditioning
--
-- Version 1.0.0
--

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
-- Constraints for table `air-conditioning_locations_modalities`
--
ALTER TABLE `air-conditioning_locations_modalities`
  ADD CONSTRAINT `air-conditioning_locations_modalities_ibfk_1` FOREIGN KEY (`fkLocation`) REFERENCES `air-conditioning_locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- --------------------------------------------------------