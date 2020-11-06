-- MySQL dump 10.13  Distrib 5.7.32, for Linux (x86_64)
--
-- Host: localhost    Database: rngapi
-- ------------------------------------------------------
-- Server version	5.7.32-0ubuntu0.18.04.1

--
-- Table structure for table `auth_tokens`
--

DROP TABLE IF EXISTS `auth_tokens`;
CREATE TABLE `auth_tokens` (
  `token` varchar(32) NOT NULL,
  `created_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `numbers`
--

DROP TABLE IF EXISTS `numbers`;
CREATE TABLE `numbers` (
  `auth_token_id` varchar(32) NOT NULL,
  `generation_id` varchar(32) NOT NULL,
  `number` int(11) NOT NULL,
  PRIMARY KEY (`generation_id`),
  KEY `auth_token_id` (`auth_token_id`),
  CONSTRAINT `numbers_ibfk_1` FOREIGN KEY (`auth_token_id`) REFERENCES `auth_tokens` (`token`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dump completed on 2020-11-06  2:32:10