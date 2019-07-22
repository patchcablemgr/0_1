-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 14, 2018 at 06:52 AM
-- Server version: 5.7.20-0ubuntu0.17.10.1
-- PHP Version: 7.1.11-0ubuntu0.17.10.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `app_04ac4517a101a77dd0f22859b0a25839`
--

-- --------------------------------------------------------

--
-- Table structure for table `env_tree`
--

CREATE TABLE `env_tree` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT 'New Node',
  `parent` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `size` int(11) NOT NULL DEFAULT '42',
  `floorplan_img` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `env_tree`
--

INSERT INTO `env_tree` (`id`, `name`, `parent`, `type`, `size`) VALUES
(1, 'Location', '#', 'location', 42),
(2, 'Sub-Location', '1', 'location', 42),
(3, 'Pod', '2', 'pod', 42),
(4, 'Cab1', '3', 'cabinet', 42),
(5, 'Cab2', '3', 'cabinet', 42),
(6, 'Cab3', '3', 'cabinet', 42);

-- --------------------------------------------------------

--
-- Table structure for table `table_address`
--

CREATE TABLE `table_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `defaultAddress` tinyint(1) DEFAULT '0',
  `displayName` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `street1` varchar(255) NOT NULL,
  `street2` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `zip` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `table_cabinet_adj`
--

CREATE TABLE `table_cabinet_adj` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `left_cabinet_id` int(11) DEFAULT NULL,
  `right_cabinet_id` int(11) DEFAULT NULL,
  `entrance_ru` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `table_cabinet_adj`
--

INSERT INTO `table_cabinet_adj` (`id`, `left_cabinet_id`, `right_cabinet_id`, `entrance_ru`) VALUES
(1, 4, 5, 0),
(2, 5, 6, 0);

-- --------------------------------------------------------

--
-- Table structure for table `table_cable_path`
--

CREATE TABLE `table_cable_path` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cabinet_a_id` int(11) NOT NULL,
  `cabinet_b_id` int(11) NOT NULL DEFAULT '0',
  `distance` int(11) NOT NULL DEFAULT '1',
  `path_entrance_ru` int(11) NOT NULL,
  `notes` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `table_floorplan_object`
--

CREATE TABLE `table_floorplan_object` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `env_tree_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `position_top` int(11) NOT NULL,
  `position_left` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `table_floorplan_object_peer`
--

CREATE TABLE `table_floorplan_object_peer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_id` int(11) NOT NULL,
  `peer_id` int(11) NOT NULL,
  `peer_face` int(11) NOT NULL,
  `peer_depth` int(11) NOT NULL,
  `peer_port` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `table_inventory`
--

CREATE TABLE `table_inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `a_id` int(11) NOT NULL DEFAULT '0',
  `a_code39` varchar(255) NOT NULL DEFAULT '0',
  `a_connector` int(11) NOT NULL DEFAULT '0',
  `a_object_id` int(11) NOT NULL DEFAULT '0',
  `a_port_id` int(11) NOT NULL DEFAULT '0',
  `a_object_face` int(11) NOT NULL DEFAULT '0',
  `a_object_depth` int(11) NOT NULL DEFAULT '0',
  `b_id` int(11) NOT NULL DEFAULT '0',
  `b_code39` varchar(255) NOT NULL DEFAULT '0',
  `b_connector` int(11) NOT NULL DEFAULT '0',
  `b_object_id` int(11) NOT NULL DEFAULT '0',
  `b_port_id` int(11) NOT NULL DEFAULT '0',
  `b_object_face` int(11) NOT NULL DEFAULT '0',
  `b_object_depth` int(11) NOT NULL DEFAULT '0',
  `mediaType` int(11) DEFAULT '0',
  `length` int(11) NOT NULL DEFAULT '1',
  `color` int(11) DEFAULT '0',
  `editable` tinyint(1) NOT NULL DEFAULT '1',
  `order_id` int(11) DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `table_object`
--

CREATE TABLE `table_object` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `env_tree_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT 'New_Object',
  `template_id` int(11) NOT NULL,
  `RU` int(11) DEFAULT NULL,
  `cabinet_front` int(11) DEFAULT NULL,
  `cabinet_back` int(11) DEFAULT '0',
  `parent_id` int(11) DEFAULT NULL,
  `parent_face` int(11) DEFAULT '0',
  `parent_depth` int(11) DEFAULT NULL,
  `insertSlotX` int(11) DEFAULT NULL,
  `insertSlotY` int(11) DEFAULT NULL,
  `position_top` int(11) DEFAULT NULL,
  `position_left` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Table structure for table `table_object_category`
--

CREATE TABLE `table_object_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `defaultOption` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `table_object_category`
--

INSERT INTO `table_object_category` (`id`, `name`, `color`, `defaultOption`) VALUES
(1, 'Switch', '#d581d6', 0),
(2, 'Router', '#d6819f', 0),
(3, 'Server', '#d68d8d', 0),
(4, 'Module', '#e59881', 0),
(5, 'Linecard', '#81d6a1', 0),
(6, 'Patch_Panel', '#a9a9a9', 1),
(7, 'Cable_Mgmt', '#d3d3d3', 0),
(8, 'Enclosure', '#95d681', 0),
(9, 'MM_Fiber_Insert', '#81d6ce', 0),
(10, 'SM_Fiber_Insert', '#d6d678', 0);

-- --------------------------------------------------------

--
-- Table structure for table `table_object_compatibility`
--

CREATE TABLE `table_object_compatibility` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL,
  `side` tinyint(4) DEFAULT '0',
  `depth` int(11) DEFAULT '0',
  `portLayoutX` int(11) DEFAULT NULL,
  `portLayoutY` int(11) DEFAULT NULL,
  `portTotal` int(11) DEFAULT NULL,
  `encLayoutX` int(11) DEFAULT NULL,
  `encLayoutY` int(11) DEFAULT NULL,
  `templateType` varchar(255) NOT NULL,
  `partitionType` varchar(255) DEFAULT NULL,
  `partitionFunction` varchar(255) DEFAULT NULL,
  `portOrientation` int(11) DEFAULT NULL,
  `portType` int(11) DEFAULT NULL,
  `mediaType` int(11) DEFAULT NULL,
  `mediaCategory` varchar(255) DEFAULT NULL,
  `mediaCategoryType` int(11) DEFAULT NULL,
  `direction` varchar(255) DEFAULT NULL,
  `flex` varchar(255) DEFAULT NULL,
  `hUnits` int(11) DEFAULT NULL,
  `vUnits` int(11) DEFAULT NULL,
  `portNameFormat` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `table_object_compatibility`
--

INSERT INTO `table_object_compatibility` (`id`, `template_id`, `side`, `depth`, `portLayoutX`, `portLayoutY`, `portTotal`, `encLayoutX`, `encLayoutY`, `templateType`, `partitionType`, `partitionFunction`, `portOrientation`, `portType`, `mediaType`, `mediaCategory`, `mediaCategoryType`, `direction`, `flex`, `hUnits`, `vUnits`, `portNameFormat`) VALUES
(1, 11, 0, 0, 24, 1, 24, NULL, NULL, 'Standard', 'Connectable', 'Passive', 1, 1, 1, '1', 1, 'column', '0', 10, 2, '[{\"type\":\"static\",\"value\":\"Port\",\"count\":0,\"order\":0},{\"type\":\"series\",\"value\":[\"a\",\"b\",\"c\"],\"count\":3,\"order\":1}]'),
(2, 12, 0, 0, 24, 2, 48, NULL, NULL, 'Standard', 'Connectable', 'Passive', 1, 1, 1, '1', 1, 'column', '0', 10, 4, '[{\"type\":\"static\",\"value\":\"Port\",\"count\":0,\"order\":0},{\"type\":\"static\",\"value\":\"-\",\"count\":0,\"order\":0},{\"type\":\"incremental\",\"value\":\"1\",\"count\":48,\"order\":1},{\"type\":\"series\",\"value\":[\"a\",\"b\",\"c\"],\"count\":3,\"order\":2}]'),
(3, 4, 0, 0, 24, 2, 48, NULL, NULL, 'Standard', 'Connectable', 'Passive', 1, 1, 1, '1', 1, 'column', '0', 10, 4, '[{\"type\":\"static\",\"value\":\"Port\",\"count\":0,\"order\":0},{\"type\":\"incremental\",\"value\":\"1\",\"count\":0,\"order\":1}]'),
(4, 5, 0, 0, 1, 6, 6, NULL, NULL, 'Insert', 'Connectable', 'Passive', 2, 2, 6, '2', 2, 'row', '0', 10, 8, '[{\"type\":\"static\",\"value\":\"Port\",\"count\":0,\"order\":0},{\"type\":\"incremental\",\"value\":\"1\",\"count\":0,\"order\":1}]'),
(5, 6, 0, 0, 1, 6, 6, NULL, NULL, 'Insert', 'Connectable', 'Passive', 2, 2, 5, '4', 2, 'row', '0', 10, 8, '[{\"type\":\"static\",\"value\":\"Port\",\"count\":0,\"order\":0},{\"type\":\"incremental\",\"value\":\"1\",\"count\":0,\"order\":1}]'),
(6, 7, 0, 0, 4, 1, 4, NULL, NULL, 'Insert', 'Connectable', 'Endpoint', 1, 4, 8, '5', 4, 'row', '0', 2, 2, '[{\"type\":\"incremental\",\"value\":\"1\",\"count\":0,\"order\":1}]'),
(7, 8, 0, 1, 6, 2, 12, NULL, NULL, 'Standard', 'Connectable', 'Endpoint', 2, 1, 8, '5', 1, 'column', '0.2', 2, 2, '[{\"type\":\"static\",\"value\":\"G1/0/\",\"count\":0,\"order\":0},{\"type\":\"incremental\",\"value\":\"1\",\"count\":0,\"order\":1}]'),
(8, 8, 0, 2, 6, 2, 12, NULL, NULL, 'Standard', 'Connectable', 'Endpoint', 2, 1, 8, '5', 1, 'column', '0.2', 2, 2, '[{\"type\":\"static\",\"value\":\"G1/0/\",\"count\":0,\"order\":0},{\"type\":\"incremental\",\"value\":\"13\",\"count\":0,\"order\":1}]'),
(9, 8, 0, 3, 6, 2, 12, NULL, NULL, 'Standard', 'Connectable', 'Endpoint', 2, 1, 8, '5', 1, 'column', '0.2', 2, 2, '[{\"type\":\"static\",\"value\":\"G1/0/\",\"count\":0,\"order\":0},{\"type\":\"incremental\",\"value\":\"25\",\"count\":0,\"order\":1}]'),
(10, 8, 0, 4, 6, 2, 12, NULL, NULL, 'Standard', 'Connectable', 'Endpoint', 2, 1, 8, '5', 1, 'column', '0.2', 2, 2, '[{\"type\":\"static\",\"value\":\"G1/0/\",\"count\":0,\"order\":0},{\"type\":\"incremental\",\"value\":\"37\",\"count\":0,\"order\":1}]'),
(11, 8, 0, 5, NULL, NULL, 0, 1, 1, 'Standard', 'Enclosure', 'Endpoint', NULL, NULL, 8, '5', NULL, 'column', '0.2', 2, 2, 'null'),
(12, 8, 1, 2, 1, 1, 1, NULL, NULL, 'Standard', 'Connectable', 'Endpoint', 1, 1, 8, '5', 1, 'row', '0.5', 1, 1, '[{\"type\":\"static\",\"value\":\"Con\",\"count\":0,\"order\":0},{\"type\":\"incremental\",\"value\":\"1\",\"count\":0,\"order\":1}]'),
(13, 8, 1, 3, 1, 1, 1, NULL, NULL, 'Standard', 'Connectable', 'Endpoint', 1, 1, 8, '5', 1, 'row', '0.5', 1, 1, '[{\"type\":\"static\",\"value\":\"Mgmt\",\"count\":0,\"order\":0},{\"type\":\"incremental\",\"value\":\"1\",\"count\":0,\"order\":1}]'),
(14, 9, 0, 0, NULL, NULL, 0, 12, 1, 'Standard', 'Enclosure', 'Passive', NULL, NULL, NULL, NULL, NULL, 'column', '0', 10, 8, 'null'),
(15, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, 'walljack', 'Connectable', 'Passive', NULL, 1, 8, '1', 1, NULL, NULL, NULL, NULL, NULL),
(16, 2, 0, 0, 1, 1, 1, NULL, NULL, 'wap', 'Connectable', 'Endpoint', NULL, 1, 8, '1', 1, NULL, NULL, NULL, NULL, '[{\"type\":\"static\",\"value\":\"NIC\",\"count\":0,\"order\":0},{\"type\":\"incremental\",\"value\":\"1\",\"count\":0,\"order\":1}]'),
(17, 3, 0, 0, 1, 1, 1, NULL, NULL, 'device', 'Connectable', 'Endpoint', NULL, 1, 8, '1', 1, NULL, NULL, NULL, NULL, '[{\"type\":\"static\",\"value\":\"NIC\",\"count\":0,\"order\":0},{\"type\":\"incremental\",\"value\":\"1\",\"count\":0,\"order\":1}]');


-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- Table structure for table `table_object_peer`
--

CREATE TABLE `table_object_peer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `a_id` int(11) DEFAULT NULL,
  `a_face` int(11) DEFAULT NULL,
  `a_depth` int(11) DEFAULT NULL,
  `a_port` int(11) DEFAULT NULL,
  `a_endpoint` tinyint(1) NOT NULL,
  `b_id` int(11) DEFAULT NULL,
  `b_face` int(11) DEFAULT NULL,
  `b_depth` int(11) DEFAULT NULL,
  `b_port` int(11) DEFAULT NULL,
  `b_endpoint` tinyint(1) NOT NULL,
  `floorplan_peer` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `table_object_templates`
--

CREATE TABLE `table_object_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `templateName` varchar(255) NOT NULL,
  `templateCategory_id` int(11) DEFAULT NULL,
  `templateType` varchar(255) NOT NULL,
  `templateRUSize` int(11) DEFAULT NULL,
  `templateFunction` varchar(255) NOT NULL,
  `templateMountConfig` tinyint(1) DEFAULT NULL,
  `templateEncLayoutX` int(11) DEFAULT NULL,
  `templateEncLayoutY` int(11) DEFAULT NULL,
  `templateHUnits` int(11) DEFAULT NULL,
  `templateVUnits` int(11) DEFAULT NULL,
  `templatePartitionData` text DEFAULT NULL,
  `frontImage` varchar(45) DEFAULT NULL,
  `rearImage` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `table_object_templates`
--

INSERT INTO `table_object_templates` (`id`, `templateName`, `templateCategory_id`, `templateType`, `templateRUSize`, `templateFunction`, `templateMountConfig`, `templateEncLayoutX`, `templateEncLayoutY`, `templateHUnits`, `templateVUnits`, `templatePartitionData`, `frontImage`, `rearImage`) VALUES
(1, 'Walljack', NULL, 'walljack', NULL, 'Passive', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'WAP', NULL, 'wap', NULL, 'Endpoint', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'Device', NULL, 'device', NULL, 'Endpoint', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, '48p_RJ45_Cat6', 6, 'Standard', 2, 'Passive', 0, NULL, NULL, NULL, NULL, '[[{\"portLayoutX\":\"24\",\"portLayoutY\":\"2\",\"encLayoutX\":1,\"encLayoutY\":1,\"partitionType\":\"Connectable\",\"portOrientation\":1,\"portType\":1,\"mediaType\":1,\"direction\":\"column\",\"vunits\":4,\"hunits\":10,\"flex\":\"0\",\"portNameFormat\":[{\"type\":\"static\",\"value\":\"Port\",\"count\":0,\"order\":0},{\"type\":\"incremental\",\"value\":\"1\",\"count\":0,\"order\":1}]}]]', '105137c8ec469aecd387cef6ef807dc0.jpg', NULL),
(5, '6p_LC_OM4', 9, 'Insert', 4, 'Passive', NULL, 12, 1, 10, 8, '[[{\"portLayoutX\":\"1\",\"portLayoutY\":\"6\",\"encLayoutX\":1,\"encLayoutY\":1,\"partitionType\":\"Connectable\",\"portOrientation\":\"2\",\"portType\":\"2\",\"mediaType\":\"6\",\"direction\":\"row\",\"vunits\":8,\"hunits\":10,\"flex\":\"0\",\"portNameFormat\":[{\"type\":\"static\",\"value\":\"Port\",\"count\":0,\"order\":0},{\"type\":\"incremental\",\"value\":\"1\",\"count\":0,\"order\":1}]}]]', 'f782af8c83a096a188ba333d0039aaf7.jpg', NULL),
(6, '6p_LC_OS1', 10, 'Insert', 4, 'Passive', NULL, 12, 1, 10, 8, '[[{\"portLayoutX\":\"1\",\"portLayoutY\":\"6\",\"encLayoutX\":1,\"encLayoutY\":1,\"partitionType\":\"Connectable\",\"portOrientation\":\"2\",\"portType\":\"2\",\"mediaType\":\"5\",\"direction\":\"row\",\"vunits\":8,\"hunits\":10,\"flex\":\"0\",\"portNameFormat\":[{\"type\":\"static\",\"value\":\"Port\",\"count\":0,\"order\":0},{\"type\":\"incremental\",\"value\":\"1\",\"count\":0,\"order\":1}]}]]', '86257b172c9a706aa704abfdad4de53c.jpg', NULL),
(7, 'C3850-NM-4-10G', 4, 'Insert', 1, 'Endpoint', NULL, 1, 1, 2, 2, '[[{\"portLayoutX\":\"4\",\"portLayoutY\":\"1\",\"encLayoutX\":1,\"encLayoutY\":1,\"partitionType\":\"Connectable\",\"portOrientation\":1,\"portType\":\"4\",\"mediaType\":1,\"direction\":\"row\",\"vunits\":2,\"hunits\":2,\"flex\":\"0\",\"portNameFormat\":[{\"type\":\"incremental\",\"value\":\"1\",\"count\":0,\"order\":1}]}]]', '7ae4f0a4040a7fe8f3a823b167952f26.jpg', NULL),
(8, 'C3850_48p', 1, 'Standard', 1, 'Endpoint', 1, NULL, NULL, NULL, NULL, '[[{\"portLayoutX\":0,\"portLayoutY\":0,\"encLayoutX\":1,\"encLayoutY\":1,\"partitionType\":\"Generic\",\"portPrefix\":\"Port\",\"portNumber\":1,\"portOrientation\":1,\"portType\":1,\"mediaType\":1,\"direction\":\"row\",\"vunits\":2,\"hunits\":10,\"flex\":\"0\",\"children\":[{\"portLayoutX\":\"6\",\"portLayoutY\":\"2\",\"encLayoutX\":1,\"encLayoutY\":1,\"partitionType\":\"Connectable\",\"portOrientation\":\"2\",\"portType\":1,\"mediaType\":1,\"direction\":\"column\",\"vunits\":2,\"hunits\":2,\"flex\":\"0.2\",\"portNameFormat\":[{\"type\":\"static\",\"value\":\"G1/0/\",\"count\":0,\"order\":0},{\"type\":\"incremental\",\"value\":\"1\",\"count\":0,\"order\":1}]},{\"portLayoutX\":\"6\",\"portLayoutY\":\"2\",\"encLayoutX\":1,\"encLayoutY\":1,\"partitionType\":\"Connectable\",\"portOrientation\":\"2\",\"portType\":1,\"mediaType\":1,\"direction\":\"column\",\"vunits\":2,\"hunits\":2,\"flex\":\"0.2\",\"portNameFormat\":[{\"type\":\"static\",\"value\":\"G1/0/\",\"count\":0,\"order\":0},{\"type\":\"incremental\",\"value\":\"13\",\"count\":0,\"order\":1}]},{\"portLayoutX\":\"6\",\"portLayoutY\":\"2\",\"encLayoutX\":1,\"encLayoutY\":1,\"partitionType\":\"Connectable\",\"portOrientation\":\"2\",\"portType\":1,\"mediaType\":1,\"direction\":\"column\",\"vunits\":2,\"hunits\":2,\"flex\":\"0.2\",\"portNameFormat\":[{\"type\":\"static\",\"value\":\"G1/0/\",\"count\":0,\"order\":0},{\"type\":\"incremental\",\"value\":\"25\",\"count\":0,\"order\":1}]},{\"portLayoutX\":\"6\",\"portLayoutY\":\"2\",\"encLayoutX\":1,\"encLayoutY\":1,\"partitionType\":\"Connectable\",\"portOrientation\":\"2\",\"portType\":1,\"mediaType\":1,\"direction\":\"column\",\"vunits\":2,\"hunits\":2,\"flex\":\"0.2\",\"portNameFormat\":[{\"type\":\"static\",\"value\":\"G1/0/\",\"count\":0,\"order\":0},{\"type\":\"incremental\",\"value\":\"37\",\"count\":0,\"order\":1}]},{\"portLayoutX\":0,\"portLayoutY\":0,\"encLayoutX\":1,\"encLayoutY\":1,\"partitionType\":\"Enclosure\",\"portPrefix\":\"Port\",\"portNumber\":1,\"portOrientation\":1,\"portType\":1,\"mediaType\":1,\"direction\":\"column\",\"vunits\":2,\"hunits\":2,\"flex\":\"0.2\"}]}],[{\"portLayoutX\":0,\"portLayoutY\":0,\"encLayoutX\":1,\"encLayoutY\":1,\"partitionType\":\"Generic\",\"portPrefix\":\"Port\",\"portNumber\":1,\"portOrientation\":1,\"portType\":1,\"mediaType\":1,\"direction\":\"row\",\"vunits\":2,\"hunits\":10,\"flex\":\"0\",\"children\":[{\"portLayoutX\":0,\"portLayoutY\":0,\"encLayoutX\":1,\"encLayoutY\":1,\"partitionType\":\"Generic\",\"portPrefix\":\"Port\",\"portNumber\":1,\"portOrientation\":1,\"portType\":1,\"mediaType\":1,\"direction\":\"column\",\"vunits\":2,\"hunits\":1,\"flex\":\"0.1\",\"children\":[{\"portLayoutX\":\"1\",\"portLayoutY\":\"1\",\"encLayoutX\":1,\"encLayoutY\":1,\"partitionType\":\"Connectable\",\"portOrientation\":1,\"portType\":1,\"mediaType\":1,\"direction\":\"row\",\"vunits\":1,\"hunits\":1,\"flex\":\"0.5\",\"portNameFormat\":[{\"type\":\"static\",\"value\":\"Con\",\"count\":0,\"order\":0},{\"type\":\"incremental\",\"value\":\"1\",\"count\":0,\"order\":1}]},{\"portLayoutX\":\"1\",\"portLayoutY\":\"1\",\"encLayoutX\":1,\"encLayoutY\":1,\"partitionType\":\"Connectable\",\"portOrientation\":1,\"portType\":1,\"mediaType\":1,\"direction\":\"row\",\"vunits\":1,\"hunits\":1,\"flex\":\"0.5\",\"portNameFormat\":[{\"type\":\"static\",\"value\":\"Mgmt\",\"count\":0,\"order\":0},{\"type\":\"incremental\",\"value\":\"1\",\"count\":0,\"order\":1}]}]}]}]]', 'ab97c853ba94a8ebb9d950f6867601de.jpg', '93c68d70070b0ccb5645bd6bfd53c89b.jpg'),
(9, 'Fiber_Enclosure', 8, 'Standard', 4, 'Passive', 0, NULL, NULL, NULL, NULL, '[[{\"portLayoutX\":0,\"portLayoutY\":0,\"encLayoutX\":\"12\",\"encLayoutY\":\"1\",\"partitionType\":\"Enclosure\",\"portPrefix\":\"Port\",\"portNumber\":1,\"portOrientation\":1,\"portType\":1,\"mediaType\":1,\"direction\":\"column\",\"vunits\":8,\"hunits\":10,\"flex\":\"0\"}]]', NULL, NULL),
(10, '1RU_Cable_Mgmt', 7, 'Standard', 1, 'Passive', 0, NULL, NULL, NULL, NULL, '[[{\"portLayoutX\":0,\"portLayoutY\":0,\"encLayoutX\":1,\"encLayoutY\":1,\"partitionType\":\"Generic\",\"portPrefix\":\"Port\",\"portNumber\":1,\"portOrientation\":1,\"portType\":1,\"mediaType\":1,\"direction\":\"column\",\"vunits\":2,\"hunits\":10,\"flex\":\"0\"}]]', 'c4e7eb2d860f17b94042199509ca4b28.jpg', NULL),
(11, '24P_RJ45_Cat5E', 6, 'Standard', 1, 'Passive', 0, NULL, NULL, NULL, NULL, '[[{\"portLayoutX\":\"24\",\"portLayoutY\":\"1\",\"encLayoutX\":1,\"encLayoutY\":1,\"partitionType\":\"Connectable\",\"portOrientation\":1,\"portType\":1,\"mediaType\":1,\"direction\":\"column\",\"vunits\":2,\"hunits\":10,\"flex\":\"0\",\"portNameFormat\":[{\"type\":\"static\",\"value\":\"Port\",\"count\":0,\"order\":0},{\"type\":\"series\",\"value\":[\"a\",\"b\",\"c\"],\"count\":3,\"order\":1}]}]]', '47618b55d38fcaf9ad73be0ead312d68.jpg', NULL),
(12, '48p_RJ45_Cat5e', 6, 'Standard', 2, 'Passive', 0, NULL, NULL, NULL, NULL, '[[{\"portLayoutX\":\"24\",\"portLayoutY\":\"2\",\"encLayoutX\":1,\"encLayoutY\":1,\"partitionType\":\"Connectable\",\"portOrientation\":1,\"portType\":1,\"mediaType\":1,\"direction\":\"column\",\"vunits\":4,\"hunits\":10,\"flex\":\"0\",\"portNameFormat\":[{\"type\":\"static\",\"value\":\"Port\",\"count\":0,\"order\":0},{\"type\":\"static\",\"value\":\"-\",\"count\":0,\"order\":0},{\"type\":\"incremental\",\"value\":\"1\",\"count\":48,\"order\":1},{\"type\":\"series\",\"value\":[\"a\",\"b\",\"c\"],\"count\":3,\"order\":2}]}]]', '0e1b15f076088230505a14c5a6e010c0.jpg', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `table_organization_data`
--

CREATE TABLE `table_organization_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `sub_level` int(11) NOT NULL,
  `cust_id` varchar(255) DEFAULT NULL,
  `sub_id` varchar(255) DEFAULT NULL,
  `expiration` int(11) DEFAULT '0',
  `status` varchar(255) NOT NULL,
  `created` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

INSERT INTO `table_organization_data` (`id`, `name`, `sub_level`, `cust_id`, `sub_id`, `expiration`, `status`, `created`) VALUES
(1, 'Acme, Inc.', 1, '', '', 0, 'Live', 0);

--
-- Table structure for table `table_populated_port`
--

CREATE TABLE `table_populated_port` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_id` int(11) NOT NULL,
  `object_face` int(11) NOT NULL,
  `object_depth` int(11) NOT NULL,
  `port_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `table_user_messages`
--

CREATE TABLE `table_user_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to_id` int(11) NOT NULL,
  `from_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `subject` varchar(255) NOT NULL,
  `message` varchar(1000) NOT NULL,
  `viewed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `table_inventory`
--
ALTER TABLE `table_inventory`
  ADD KEY `a_id` (`a_id`),
  ADD KEY `b_id` (`b_id`);


/*!40101 SET CHARACTER_SET_CLIENT='utf8mb4' */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
