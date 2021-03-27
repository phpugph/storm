CREATE TABLE IF NOT EXISTS `address` (
	`address_id` int(10) unsigned NOT NULL COMMENT 'Database Generated',
	`address_label` varchar(255) DEFAULT NULL COMMENT 'eg. My Home, My Work ect.',
	`address_street` varchar(255) NOT NULL,
	`address_neighborhod` varchar(255) DEFAULT NULL COMMENT 'Not all addresses have a neighborhood',
	`address_city` varchar(255) NOT NULL,
	`address_state` varchar(255) DEFAULT NULL COMMENT 'Not all addresses have a state',
	`address_region` varchar(255) DEFAULT NULL COMMENT 'Not all addresses have a region',
	`address_country` varchar(255) NOT NULL COMMENT 'eg. PH, US etc.',
	`address_postal` varchar(255) NOT NULL COMMENT 'Postal zip code',
	`address_landmarks` varchar(255) DEFAULT NULL COMMENT 'Informal landmarks',
	`address_latitude` float(10,10) NOT NULL DEFAULT '0.0000000000' COMMENT 'Application Generated',
	`address_longitude` float(10,10) NOT NULL DEFAULT '0.0000000000' COMMENT 'Application Generated',
	`address_public` int(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Can it be publicly listed ?',
	`address_active` int(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Do not delete rows',
	`address_type` varchar(255) DEFAULT NULL COMMENT 'General usage type',
	`address_flag` int(1) unsigned NOT NULL DEFAULT '0' COMMENT 'General usage flag',
	`address_created` datetime NOT NULL COMMENT 'System Generated',
	`address_updated` datetime NOT NULL COMMENT 'System Generated'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

ALTER TABLE `address`
	ADD PRIMARY KEY (`address_id`), 
	ADD KEY `address_city` (`address_city`), 
	ADD KEY `address_state` (`address_state`), 
	ADD KEY `address_country` (`address_country`), 
	ADD KEY `address_postal` (`address_postal`), 
	ADD KEY `address_latitude` (`address_latitude`), 
	ADD KEY `address_longitude` (`address_longitude`), 
	ADD KEY `address_public` (`address_public`), 
	ADD KEY `address_active` (`address_active`), 
	ADD KEY `address_type` (`address_type`), 
	ADD KEY `address_flag` (`address_flag`), 
	ADD KEY `address_created` (`address_created`), 
	ADD KEY `address_updated` (`address_updated`);

ALTER TABLE `address` MODIFY `address_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Database Generated';