-- MySQL dump 10.13  Distrib 5.6.33, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: wildlife_video
-- ------------------------------------------------------
-- Server version	5.6.33-0ubuntu0.14.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `classifications`
--

DROP TABLE IF EXISTS `classifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `classifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `video_id` int(11) NOT NULL,
  `video_segment_id` int(11) NOT NULL,
  `probability` float DEFAULT '0',
  `type` enum('AVERAGE_WINDOW','SURF','SIFT') NOT NULL,
  `detection` enum('MOTION','PRESENCE','ABSENCE','CHICKS','LEAVE','RETURN','DEFENSE','PREDATOR') NOT NULL,
  `species_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `tag` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `video_id` (`video_id`,`video_segment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=137420 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `computed_events`
--

DROP TABLE IF EXISTS `computed_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `computed_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `algorithm_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `version_id` int(11) NOT NULL,
  `start_time_s` double NOT NULL,
  `end_time_s` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `computed_events_ibfk_1` (`algorithm_id`),
  KEY `video_id` (`video_id`),
  KEY `version_id` (`version_id`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `computed_events_ibfk_1` FOREIGN KEY (`algorithm_id`) REFERENCES `event_algorithms` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31520629 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `corrupted_images`
--

DROP TABLE IF EXISTS `corrupted_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `corrupted_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `archive_filename` varchar(256) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `archive_filename` (`archive_filename`)
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event_algorithms`
--

DROP TABLE IF EXISTS `event_algorithms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_algorithms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `main_version_id` int(11) DEFAULT NULL,
  `beta_version_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `expert_observations`
--

DROP TABLE IF EXISTS `expert_observations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expert_observations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_type` enum('foraging - on nest','foraging - off nest','self directed - preen','self directed - scratch','self directed - shake','territorial - chase','territorial - crouch','territorial - submissive','parent care - brood','parent care - cool shade','parent care - nest exchange','parent care - nest exchange','parent care - eggshell removal','parent care - feeding young','chick behavior - walking','chick behavior - foraging','chick behavior - submissive','chick behavior - running','volunteer training','unspecified','territorial - predator','territorial - other animal','territorial - nest defense','nest success','chick presence','camera interaction - attack','camera interaction - inspection','camera interaction - observation','parent behavior - not in frame','parent behavior - in frame','parent behavior - on nest','parent behavior - walking','parent behavior - flying','parent care - fail parental feed','parent care - success parental feed','parent care - unknown parental feed') DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `start_time` time DEFAULT NULL,
  `comments` varchar(512) DEFAULT NULL,
  `video_id` int(11) NOT NULL,
  `end_time` time DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4891 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `goldbadge`
--

DROP TABLE IF EXISTS `goldbadge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goldbadge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `u_id` int(11) DEFAULT NULL,
  `gold_participation` varchar(255) DEFAULT NULL,
  `species_rank` varchar(1000) DEFAULT NULL,
  `hours_week` varchar(16) DEFAULT NULL,
  `hours_sitting` varchar(16) DEFAULT NULL,
  `video_speed` varchar(16) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `other_activities` varchar(255) DEFAULT NULL,
  `interesting` varchar(16) DEFAULT NULL,
  `int_elaboration` varchar(255) DEFAULT NULL,
  `learned` varchar(255) DEFAULT NULL,
  `experience` varchar(255) DEFAULT NULL,
  `motivation` varchar(255) DEFAULT NULL,
  `other_species` varchar(255) DEFAULT NULL,
  `recommendation` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `image_observation_boxes`
--

DROP TABLE IF EXISTS `image_observation_boxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `image_observation_boxes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `image_observation_id` int(10) unsigned NOT NULL,
  `species_id` int(11) NOT NULL,
  `x` int(10) unsigned NOT NULL,
  `y` int(10) unsigned NOT NULL,
  `width` int(10) unsigned NOT NULL,
  `height` int(10) unsigned NOT NULL,
  `on_nest` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `image_observation_id` (`image_observation_id`),
  KEY `species_id` (`species_id`),
  CONSTRAINT `image_observation_boxes_ibfk_1` FOREIGN KEY (`image_observation_id`) REFERENCES `image_observations` (`id`),
  CONSTRAINT `image_observation_boxes_ibfk_2` FOREIGN KEY (`species_id`) REFERENCES `species_lookup` (`species_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7677 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `image_observation_comments`
--

DROP TABLE IF EXISTS `image_observation_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `image_observation_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `image_observation_id` int(10) unsigned NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `image_observation_id` (`image_observation_id`),
  CONSTRAINT `image_observation_comments_ibfk_1` FOREIGN KEY (`image_observation_id`) REFERENCES `image_observations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `image_observations`
--

DROP TABLE IF EXISTS `image_observations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `image_observations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `image_id` int(11) DEFAULT NULL,
  `nothing_here` tinyint(1) DEFAULT '0',
  `submit_time` datetime NOT NULL,
  `duration` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_image` (`image_id`),
  CONSTRAINT `fk_image` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8071 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `image_project_lookup`
--

DROP TABLE IF EXISTS `image_project_lookup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `image_project_lookup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `image_project_lookup_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project_lookup` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL,
  `temp` tinyint(4) DEFAULT NULL,
  `archive_filename` varchar(256) NOT NULL,
  `watermarked` tinyint(1) DEFAULT '0',
  `watermarked_filename` varchar(256) DEFAULT NULL,
  `camera_id` varchar(40) NOT NULL,
  `species` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `md5_hash` varchar(64) DEFAULT NULL,
  `size` int(32) DEFAULT NULL,
  `views` int(11) DEFAULT '0',
  `needed_views` int(11) DEFAULT '3',
  `verified` tinyint(1) DEFAULT '0',
  `project_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `archive_filename` (`archive_filename`),
  UNIQUE KEY `watermarked_filename` (`watermarked_filename`)
) ENGINE=InnoDB AUTO_INCREMENT=4023274 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `images_queue`
--

DROP TABLE IF EXISTS `images_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `images_queue` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `image_id` int(11) NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  `species` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `image_id` (`image_id`),
  CONSTRAINT `images_queue_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4846 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `initial_observations`
--

DROP TABLE IF EXISTS `initial_observations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `initial_observations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `comments` varchar(512) DEFAULT NULL,
  `video_id` int(11) NOT NULL,
  `species_id` int(11) NOT NULL,
  `tags` varchar(512) DEFAULT NULL,
  `location_id` int(11) NOT NULL,
  `expert` tinyint(1) NOT NULL,
  `start_time_s` double NOT NULL DEFAULT '-1',
  `end_time_s` double NOT NULL DEFAULT '-1',
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('UNVALIDATED','VALID','INVALID') NOT NULL DEFAULT 'UNVALIDATED',
  `auto_generated` tinyint(1) NOT NULL DEFAULT '0',
  `report_status` enum('UNREPORTED','REPORTED','RESPONDED') NOT NULL DEFAULT 'UNREPORTED',
  `report_comments` varchar(1024) DEFAULT NULL,
  `response_comments` varchar(1024) DEFAULT NULL,
  `reporter_id` int(11) DEFAULT NULL,
  `responder_id` int(11) DEFAULT NULL,
  `reporter_name` varchar(128) DEFAULT NULL,
  `responder_name` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `video_id` (`video_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=85371 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location` varchar(999) DEFAULT NULL,
  `long_name` varchar(128) DEFAULT NULL,
  `year` int(11) NOT NULL DEFAULT '2012',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `merged_observations`
--

DROP TABLE IF EXISTS `merged_observations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `merged_observations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bird_leave` tinyint(4) DEFAULT NULL,
  `bird_return` tinyint(4) DEFAULT NULL,
  `bird_presence` tinyint(4) DEFAULT NULL,
  `bird_absence` tinyint(4) DEFAULT NULL,
  `predator_presence` tinyint(4) DEFAULT NULL,
  `nest_defense` tinyint(4) DEFAULT NULL,
  `nest_success` tinyint(4) DEFAULT NULL,
  `interesting` tinyint(4) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `video_segment_id` int(11) NOT NULL,
  `insert_time` datetime DEFAULT NULL,
  `comments` varchar(512) DEFAULT NULL,
  `status` enum('UNVALIDATED','INVALID','VALID','CANONICAL','EXPERT','INCONCLUSIVE') DEFAULT 'UNVALIDATED',
  `corrupt` tinyint(1) DEFAULT NULL,
  `too_dark` tinyint(1) DEFAULT NULL,
  `chick_presence` tinyint(4) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `species_id` int(11) DEFAULT NULL,
  `video_issue` int(2) DEFAULT NULL,
  `awarded_credit` float NOT NULL DEFAULT '0',
  `accuracy_rating` float NOT NULL DEFAULT '0',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`video_segment_id`),
  KEY `video_segment_id` (`video_segment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=202347 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mosaic_images`
--

DROP TABLE IF EXISTS `mosaic_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mosaic_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` datetime DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  `latitude` float(7,4) DEFAULT '0.0000',
  `longitude` float(7,4) DEFAULT '0.0000',
  `yaw` float(7,4) DEFAULT '0.0000',
  `pitch` float(7,4) DEFAULT '0.0000',
  `roll` float(7,4) DEFAULT '0.0000',
  `project_id` int(10) unsigned DEFAULT NULL,
  `year` int(11) NOT NULL DEFAULT '0',
  `split_count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mosaic_split_images`
--

DROP TABLE IF EXISTS `mosaic_split_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mosaic_split_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mosaic_image_id` int(10) unsigned NOT NULL,
  `image_id` int(11) NOT NULL,
  `number` int(11) NOT NULL DEFAULT '0',
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mosaic_image_id` (`mosaic_image_id`),
  KEY `image_id` (`image_id`),
  CONSTRAINT `mosaic_split_images_ibfk_1` FOREIGN KEY (`mosaic_image_id`) REFERENCES `mosaic_images` (`id`),
  CONSTRAINT `mosaic_split_images_ibfk_2` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7845 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `observation_types`
--

DROP TABLE IF EXISTS `observation_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `observation_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `category` varchar(64) NOT NULL,
  `instructions` varchar(2048) DEFAULT NULL,
  `sharptailed_grouse` tinyint(1) NOT NULL,
  `least_tern` tinyint(1) NOT NULL,
  `piping_plover` tinyint(1) NOT NULL,
  `expert_only` tinyint(1) DEFAULT '0',
  `possible_tags` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_category` (`name`,`category`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `observations`
--

DROP TABLE IF EXISTS `observations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `observations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bird_leave` tinyint(4) DEFAULT NULL,
  `bird_return` tinyint(4) DEFAULT NULL,
  `bird_presence` tinyint(4) DEFAULT NULL,
  `bird_absence` tinyint(4) DEFAULT NULL,
  `predator_presence` tinyint(4) DEFAULT NULL,
  `nest_defense` tinyint(4) DEFAULT NULL,
  `nest_success` tinyint(4) DEFAULT NULL,
  `interesting` tinyint(4) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `video_segment_id` int(11) NOT NULL,
  `insert_time` datetime DEFAULT NULL,
  `comments` varchar(512) DEFAULT NULL,
  `status` enum('UNVALIDATED','INVALID','VALID','CANONICAL','EXPERT','INCONCLUSIVE') DEFAULT 'UNVALIDATED',
  `corrupt` tinyint(1) DEFAULT NULL,
  `too_dark` tinyint(1) DEFAULT NULL,
  `chick_presence` tinyint(4) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `species_id` int(11) DEFAULT NULL,
  `video_issue` int(2) DEFAULT NULL,
  `awarded_credit` float NOT NULL DEFAULT '0',
  `accuracy_rating` float NOT NULL DEFAULT '0',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`video_segment_id`),
  KEY `video_segment_id` (`video_segment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=266085 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `phase_lookup`
--

DROP TABLE IF EXISTS `phase_lookup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phase_lookup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `species_id` int(11) NOT NULL,
  `root_species_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `species_id` (`species_id`),
  KEY `root_species_id` (`root_species_id`),
  CONSTRAINT `phase_lookup_ibfk_1` FOREIGN KEY (`species_id`) REFERENCES `species_lookup` (`species_id`),
  CONSTRAINT `phase_lookup_ibfk_2` FOREIGN KEY (`root_species_id`) REFERENCES `species_lookup` (`species_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `photos`
--

DROP TABLE IF EXISTS `photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adddate` datetime DEFAULT NULL,
  `site` varchar(255) DEFAULT NULL,
  `period` int(10) DEFAULT NULL,
  `pointID` varchar(255) DEFAULT NULL,
  `predator` varchar(255) DEFAULT NULL,
  `location` varchar(999) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=822 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `progress`
--

DROP TABLE IF EXISTS `progress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `progress` (
  `location_id` int(11) NOT NULL,
  `species_id` int(11) NOT NULL,
  `validated_video_s` int(11) DEFAULT NULL,
  `available_video_s` int(11) DEFAULT NULL,
  `total_video_s` int(11) DEFAULT NULL,
  PRIMARY KEY (`location_id`,`species_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_lookup`
--

DROP TABLE IF EXISTS `project_lookup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_lookup` (
  `project` varchar(40) NOT NULL,
  `project_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `require_watermark` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `registration`
--

DROP TABLE IF EXISTS `registration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `u_id` int(11) DEFAULT NULL,
  `age` varchar(16) DEFAULT NULL,
  `sex` varchar(16) DEFAULT NULL,
  `education` varchar(255) DEFAULT NULL,
  `profession` varchar(255) DEFAULT NULL,
  `english` varchar(16) DEFAULT NULL,
  `eng_fluent` varchar(16) DEFAULT NULL,
  `population` varchar(255) DEFAULT NULL,
  `heard` varchar(255) DEFAULT NULL,
  `joined` varchar(255) DEFAULT NULL,
  `participation` varchar(255) DEFAULT NULL,
  `activities` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reported_video`
--

DROP TABLE IF EXISTS `reported_video`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reported_video` (
  `video_segment_id` int(11) NOT NULL,
  `report_comments` varchar(2048) DEFAULT NULL,
  `review_comments` varchar(2048) DEFAULT NULL,
  `reporter_id` int(11) NOT NULL,
  `reporter_name` varchar(254) NOT NULL,
  `reviewer_id` int(11) DEFAULT NULL,
  `reviewer_name` varchar(254) DEFAULT NULL,
  `instructional` tinyint(1) NOT NULL DEFAULT '0',
  `valid_report` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`video_segment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reported_video_2`
--

DROP TABLE IF EXISTS `reported_video_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reported_video_2` (
  `video_segment_id` int(11) NOT NULL,
  `report_comments` varchar(2048) DEFAULT NULL,
  `review_comments` varchar(2048) DEFAULT NULL,
  `reporter_id` int(11) NOT NULL,
  `reporter_name` varchar(254) NOT NULL,
  `reviewer_id` int(11) DEFAULT NULL,
  `reviewer_name` varchar(254) DEFAULT NULL,
  `instructional` tinyint(1) NOT NULL DEFAULT '0',
  `valid_report` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`video_segment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `species`
--

DROP TABLE IF EXISTS `species`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `species` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `latin_name` varchar(128) NOT NULL,
  `waiting_review` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `species_lookup`
--

DROP TABLE IF EXISTS `species_lookup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `species_lookup` (
  `species_id` int(11) NOT NULL AUTO_INCREMENT,
  `species` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`species_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1000001 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `species_project_lookup`
--

DROP TABLE IF EXISTS `species_project_lookup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `species_project_lookup` (
  `species_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `streaming_video`
--

DROP TABLE IF EXISTS `streaming_video`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `streaming_video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `archive_id` int(11) NOT NULL,
  `location` varchar(999) DEFAULT NULL,
  `add_date` datetime DEFAULT NULL,
  `duration` varchar(10) DEFAULT NULL,
  `votes` int(11) DEFAULT '0',
  `jobs_created` tinyint(1) DEFAULT '0',
  `user_observations` int(11) NOT NULL DEFAULT '0',
  `expert_observations` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=86305 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_image_observations`
--

DROP TABLE IF EXISTS `test_image_observations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_image_observations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `image_id` int(11) NOT NULL,
  `height` smallint(5) unsigned DEFAULT NULL,
  `width` smallint(5) unsigned DEFAULT NULL,
  `top` smallint(6) DEFAULT NULL,
  `left_side` smallint(6) DEFAULT NULL,
  `species_id` int(11) unsigned DEFAULT NULL,
  `comments` varchar(512) DEFAULT NULL,
  `nest` tinyint(4) DEFAULT NULL,
  `nothing_here` tinyint(1) DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=218 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `timed_observations`
--

DROP TABLE IF EXISTS `timed_observations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `timed_observations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `comments` varchar(512) DEFAULT NULL,
  `video_id` int(11) NOT NULL,
  `species_id` int(11) NOT NULL,
  `tags` varchar(512) DEFAULT NULL,
  `location_id` int(11) NOT NULL,
  `expert` tinyint(1) NOT NULL,
  `start_time_s` double NOT NULL DEFAULT '-1',
  `end_time_s` double NOT NULL DEFAULT '-1',
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('UNVALIDATED','VALID','INVALID') NOT NULL DEFAULT 'UNVALIDATED',
  `auto_generated` tinyint(1) NOT NULL DEFAULT '0',
  `report_status` enum('UNREPORTED','REPORTED','RESPONDED') NOT NULL DEFAULT 'UNREPORTED',
  `report_comments` varchar(1024) DEFAULT NULL,
  `response_comments` varchar(1024) DEFAULT NULL,
  `reporter_id` int(11) DEFAULT NULL,
  `responder_id` int(11) DEFAULT NULL,
  `reporter_name` varchar(128) DEFAULT NULL,
  `responder_name` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `video_id` (`video_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=85419 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uas_flight_images`
--

DROP TABLE IF EXISTS `uas_flight_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uas_flight_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `flight_id` int(10) unsigned NOT NULL,
  `timestamp` datetime NOT NULL,
  `name` varchar(64) NOT NULL,
  `latitude` float(7,4) NOT NULL,
  `longitude` float(7,4) NOT NULL,
  `height` float(7,4) NOT NULL,
  `yaw` float(7,4) NOT NULL,
  `pitch` float(7,4) NOT NULL,
  `roll` float(7,4) NOT NULL,
  `img_width` int(11) NOT NULL,
  `img_height` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `flight_id` (`flight_id`),
  CONSTRAINT `uas_flight_images_ibfk_1` FOREIGN KEY (`flight_id`) REFERENCES `uas_flights` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45662 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uas_flight_split_images`
--

DROP TABLE IF EXISTS `uas_flight_split_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uas_flight_split_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uas_flight_image_id` int(10) unsigned NOT NULL,
  `image_id` int(11) NOT NULL,
  `x` int(4) NOT NULL,
  `y` int(4) NOT NULL,
  `width` int(4) NOT NULL,
  `height` int(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uas_flight_image_id` (`uas_flight_image_id`),
  KEY `image_id` (`image_id`),
  CONSTRAINT `uas_flight_split_images_ibfk_1` FOREIGN KEY (`uas_flight_image_id`) REFERENCES `uas_flight_images` (`id`),
  CONSTRAINT `uas_flight_split_images_ibfk_2` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1141525 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uas_flights`
--

DROP TABLE IF EXISTS `uas_flights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uas_flights` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` datetime NOT NULL,
  `name` varchar(64) NOT NULL,
  `directory` varchar(255) NOT NULL,
  `latitude_n` float(7,4) NOT NULL,
  `latitude_s` float(7,4) NOT NULL,
  `longitude_e` float(7,4) NOT NULL,
  `longitude_w` float(7,4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uas_mosaic_users`
--

DROP TABLE IF EXISTS `uas_mosaic_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uas_mosaic_users` (
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `video_2`
--

DROP TABLE IF EXISTS `video_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `video_2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `archive_filename` varchar(256) NOT NULL,
  `watermarked_filename` varchar(256) NOT NULL,
  `project_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `species_id` int(11) NOT NULL,
  `animal_id` varchar(50) DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `crowd_obs_count` int(11) NOT NULL,
  `expert_obs_count` int(11) NOT NULL,
  `machine_obs_count` int(11) NOT NULL,
  `streaming_segments` int(11) NOT NULL,
  `processing_status` enum('UNWATERMARKED','WATERMARKING','WATERMARKED','BGSUB_PROCESSING','BGSUB_COMPLETE','COMPLETE') DEFAULT NULL,
  `duration_s` int(11) NOT NULL,
  `md5_hash` varchar(64) DEFAULT NULL,
  `size` int(32) DEFAULT NULL,
  `expert_finished` enum('UNWATCHED','WATCHED','FINISHED') DEFAULT NULL,
  `ogv_generated` tinyint(1) NOT NULL DEFAULT '0',
  `release_to_public` tinyint(1) NOT NULL DEFAULT '0',
  `rivermile` varchar(10) DEFAULT NULL,
  `watch_count` int(11) NOT NULL DEFAULT '0',
  `required_views` int(11) NOT NULL DEFAULT '2',
  `needs_reconversion` tinyint(1) DEFAULT '0',
  `crowd_status` enum('UNWATCHED','WATCHED','VALIDATED','NO_CONSENSUS') DEFAULT 'UNWATCHED',
  `timed_obs_count` int(11) NOT NULL DEFAULT '0',
  `needs_revalidation` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `archive_filename` (`archive_filename`),
  UNIQUE KEY `watermarked_filename` (`watermarked_filename`)
) ENGINE=MyISAM AUTO_INCREMENT=128269 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `video_segment_2`
--

DROP TABLE IF EXISTS `video_segment_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `video_segment_2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `video_id` int(11) NOT NULL,
  `filename` varchar(256) NOT NULL,
  `crowd_obs_count` int(11) NOT NULL,
  `expert_obs_count` int(11) NOT NULL,
  `machine_obs_count` int(11) NOT NULL,
  `interesting_count` int(11) NOT NULL,
  `processing_status` enum('UNWATERMARKED','WATERMARKED','DONE') DEFAULT NULL,
  `number` int(11) NOT NULL,
  `location_id` int(11) DEFAULT NULL,
  `species_id` int(11) DEFAULT NULL,
  `crowd_status` enum('UNWATCHED','WATCHED','VALIDATED','NO_CONSENSUS') DEFAULT NULL,
  `duration_s` int(11) DEFAULT NULL,
  `broken` tinyint(1) DEFAULT NULL,
  `too_dark` tinyint(1) DEFAULT NULL,
  `required_views` int(11) NOT NULL DEFAULT '3',
  `report_status` enum('UNREPORTED','REPORTED','REVIEWED') NOT NULL DEFAULT 'UNREPORTED',
  `validate_for_review` tinyint(1) NOT NULL DEFAULT '0',
  `instructional` tinyint(1) DEFAULT NULL,
  `release_to_public` tinyint(1) NOT NULL DEFAULT '1',
  `start_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `video_id` (`video_id`)
) ENGINE=MyISAM AUTO_INCREMENT=997106 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `votes`
--

DROP TABLE IF EXISTS `votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `video_id` int(11) DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  `vote` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `watched_videos`
--

DROP TABLE IF EXISTS `watched_videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `watched_videos` (
  `video_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `difficulty` enum('easy','medium','hard') DEFAULT NULL,
  PRIMARY KEY (`video_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-11-04 10:28:18
