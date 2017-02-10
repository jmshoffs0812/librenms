CREATE TABLE IF NOT EXISTS `device_graphs` (  `device_id` int(11) NOT NULL,  `graph` varchar(32) COLLATE utf8_unicode_ci NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DROP TABLE IF EXISTS `graph_types`;
CREATE TABLE IF NOT EXISTS `graph_types` (  `graph_type` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `graph_subtype` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `graph_section` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `graph_descr` varchar(64) COLLATE utf8_unicode_ci NOT NULL,  `graph_order` int(11) NOT NULL,  KEY `graph_type` (`graph_type`),  KEY `graph_subtype` (`graph_subtype`),  KEY `graph_section` (`graph_section`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DROP TABLE `frequency`;
ALTER TABLE  `mempools` CHANGE  `mempool_index`  `mempool_index` VARCHAR( 16 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `vrfs` CHANGE `mplsVpnVrfRouteDistinguisher` `mplsVpnVrfRouteDistinguisher` varchar(26) NOT NULL;
## Change port rrds
ALTER TABLE  `perf_times` CHANGE  `duration`  `duration` DOUBLE( 8, 2 ) NOT NULL
## Extend port descriptions
ALTER TABLE ports MODIFY port_descr_circuit VARCHAR(255);
ALTER TABLE ports MODIFY port_descr_descr VARCHAR(255);
ALTER TABLE ports MODIFY port_descr_notes VARCHAR(255);
ALTER TABLE devices MODIFY community VARCHAR(255);
ALTER TABLE users MODIFY password VARCHAR(34);
ALTER TABLE sensors MODIFY sensor_descr VARCHAR(255);
ALTER TABLE `vrfs` MODIFY  `mplsVpnVrfRouteDistinguisher` VARCHAR(128);
ALTER TABLE `vrfs` MODIFY  `vrf_name` VARCHAR(128);
ALTER TABLE `ports` MODIFY  `ifDescr` VARCHAR(255);
ALTER TABLE `ports` MODIFY  `port_descr_type` VARCHAR(255);
CREATE TABLE IF NOT EXISTS `cef_switching` (  `device_id` int(11) NOT NULL,  `entPhysicalIndex` int(11) NOT NULL,  `afi` varchar(4) COLLATE utf8_unicode_ci NOT NULL,  `cef_index` int(11) NOT NULL,  `cef_path` varchar(16) COLLATE utf8_unicode_ci NOT NULL,  `drop` int(11) NOT NULL,  `punt` int(11) NOT NULL,  `punt2host` int(11) NOT NULL,  `drop_prev` int(11) NOT NULL,  `punt_prev` int(11) NOT NULL,  `punt2host_prev` int(11) NOT NULL,`updated` INT NOT NULL ,  `updated_prev` INT NOT NULL )  ENGINE=InnoDB DEFAULT CHARSET=utf8;
UPDATE sensors SET sensor_class='frequency' WHERE sensor_class='freq';
CREATE TABLE IF NOT EXISTS `ospf_areas` (  `device_id` int(11) NOT NULL,  `ospfAreaId` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfAuthType` varchar(64) COLLATE utf8_unicode_ci NOT NULL,  `ospfImportAsExtern` varchar(128) COLLATE utf8_unicode_ci NOT NULL,  `ospfSpfRuns` int(11) NOT NULL,  `ospfAreaBdrRtrCount` int(11) NOT NULL,  `ospfAsBdrRtrCount` int(11) NOT NULL,  `ospfAreaLsaCount` int(11) NOT NULL,  `ospfAreaLsaCksumSum` int(11) NOT NULL,  `ospfAreaSummary` varchar(64) COLLATE utf8_unicode_ci NOT NULL,  `ospfAreaStatus` varchar(64) COLLATE utf8_unicode_ci NOT NULL,  UNIQUE KEY `device_area` (`device_id`,`ospfAreaId`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE IF NOT EXISTS `ospf_instances` (  `device_id` int(11) NOT NULL,  `ospf_instance_id` int(11) NOT NULL,  `ospfRouterId` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfAdminStat` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfVersionNumber` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfAreaBdrRtrStatus` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfASBdrRtrStatus` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfExternLsaCount` int(11) NOT NULL,  `ospfExternLsaCksumSum` int(11) NOT NULL,  `ospfTOSSupport` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfOriginateNewLsas` int(11) NOT NULL,  `ospfRxNewLsas` int(11) NOT NULL,  `ospfExtLsdbLimit` int(11) DEFAULT NULL,  `ospfMulticastExtensions` int(11) DEFAULT NULL,  `ospfExitOverflowInterval` int(11) DEFAULT NULL,  `ospfDemandExtensions` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,  UNIQUE KEY `device_id` (`device_id`,`ospf_instance_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE IF NOT EXISTS `ospf_ports` (  `device_id` int(11) NOT NULL,  `interface_id` int(11) NOT NULL,  `ospf_port_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfIfIpAddress` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfAddressLessIf` int(11) NOT NULL,  `ospfIfAreaId` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfIfType` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,  `ospfIfAdminStat` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,  `ospfIfRtrPriority` int(11) DEFAULT NULL,  `ospfIfTransitDelay` int(11) DEFAULT NULL,  `ospfIfRetransInterval` int(11) DEFAULT NULL,  `ospfIfHelloInterval` int(11) DEFAULT NULL,  `ospfIfRtrDeadInterval` int(11) DEFAULT NULL,  `ospfIfPollInterval` int(11) DEFAULT NULL,  `ospfIfState` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,  `ospfIfDesignatedRouter` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,  `ospfIfBackupDesignatedRouter` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,  `ospfIfEvents` int(11) DEFAULT NULL,  `ospfIfAuthKey` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,  `ospfIfStatus` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,  `ospfIfMulticastForwarding` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,  `ospfIfDemand` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,  `ospfIfAuthType` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,  UNIQUE KEY `device_id` (`device_id`,`interface_id`,`ospf_port_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE IF NOT EXISTS `ospf_nbrs` (  `device_id` int(11) NOT NULL,  `interface_id` int(11) NOT NULL,  `ospf_nbr_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfNbrIpAddr` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfNbrAddressLessIndex` int(11) NOT NULL,  `ospfNbrRtrId` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfNbrOptions` int(11) NOT NULL,  `ospfNbrPriority` int(11) NOT NULL,  `ospfNbrState` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfNbrEvents` int(11) NOT NULL,  `ospfNbrLsRetransQLen` int(11) NOT NULL,  `ospfNbmaNbrStatus` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfNbmaNbrPermanence` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfNbrHelloSuppressed` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  UNIQUE KEY `device_id` (`device_id`,`ospf_nbr_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE IF NOT EXISTS `ports_stack` (`interface_id_high` INT NOT NULL ,`interface_id_low` INT NOT NULL) ENGINE = INNODB;
