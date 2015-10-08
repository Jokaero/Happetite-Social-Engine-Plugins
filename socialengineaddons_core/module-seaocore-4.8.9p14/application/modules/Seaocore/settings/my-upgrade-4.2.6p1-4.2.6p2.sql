DROP TABLE IF EXISTS `engine4_seaocore_locationitems`;
CREATE TABLE IF NOT EXISTS `engine4_seaocore_locationitems` (
  `locationitem_id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_type` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `resource_id` int(11) NOT NULL,
  `location` text COLLATE utf8_unicode_ci,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `formatted_address` text COLLATE utf8_unicode_ci,
  `country` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zipcode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zoom` int(11) NOT NULL,
  PRIMARY KEY (`locationitem_id`),
  UNIQUE KEY `resource_id` (`resource_id`,`resource_type`),
  KEY `resource_type` (`resource_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;