# ************************************************************
# Sequel Pro SQL dump
# Version 3408
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.1.40-log)
# Database: orm
# Generation Time: 2012-05-13 09:20:18 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table article
# ------------------------------------------------------------

CREATE TABLE `article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `webalized` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `article_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `article` WRITE;
/*!40000 ALTER TABLE `article` DISABLE KEYS */;

INSERT INTO `article` (`id`, `category_id`, `title`, `webalized`, `content`, `status`, `created`, `updated`)
VALUES
	(1,1,'Titulllllkkkkaaaa','moja-prva-titulka','Konreeeeeent','draft','2012-05-11 09:06:19',NULL),
	(119,8,'Moja titulka h7zk',NULL,'Obrash stránky','draft','2012-05-12 22:41:39',NULL),
	(120,NULL,'Moja titulka axzq',NULL,'Obrash stránky','draft','2012-05-12 22:41:39',NULL),
	(121,8,'Moja titulka v6ch',NULL,'Obrash stránky','draft','2012-05-12 22:41:39',NULL),
	(122,NULL,'Moja titulka r5uv',NULL,'Obrash stránky','draft','2012-05-12 22:44:37',NULL),
	(262,69,'Titulllllkkkkaaaa',NULL,'Konreeeeeent','draft','2012-05-13 00:19:04',NULL),
	(389,NULL,'Moja titulka 1p66',NULL,'Obrash stránky','draft','2012-05-13 10:23:32',NULL),
	(391,NULL,'Moja titulka 8ym6',NULL,'Obrash stránky','draft','2012-05-13 10:23:33',NULL),
	(399,NULL,'Moja titulka uivz',NULL,'Obrash stránky','draft','2012-05-13 10:24:32',NULL),
	(401,NULL,'Moja titulka z0t8',NULL,'Obrash stránky','draft','2012-05-13 10:24:33',NULL),
	(404,NULL,'Moja titulka 1byl',NULL,'Obrash stránky','draft','2012-05-13 10:26:28',NULL),
	(406,NULL,'Moja titulka 4jmj',NULL,'Obrash stránky','draft','2012-05-13 10:26:28',NULL),
	(409,NULL,'Moja titulka u7jh',NULL,'Obrash stránky','draft','2012-05-13 10:29:28',NULL),
	(411,NULL,'Moja titulka i6ns',NULL,'Obrash stránky','draft','2012-05-13 10:29:29',NULL),
	(414,NULL,'Moja titulka 8tlp',NULL,'Obrash stránky','draft','2012-05-13 10:29:52',NULL),
	(416,NULL,'Moja titulka e3me',NULL,'Obrash stránky','draft','2012-05-13 10:29:52',NULL);

/*!40000 ALTER TABLE `article` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table article_tag
# ------------------------------------------------------------

CREATE TABLE `article_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int(10) unsigned DEFAULT NULL,
  `tag_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `article_id` (`article_id`,`tag_id`),
  KEY `tag_id` (`tag_id`),
  CONSTRAINT `article_tag_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `article_tag_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table category
# ------------------------------------------------------------

CREATE TABLE `category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `category_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;

INSERT INTO `category` (`id`, `category_id`, `name`, `created`, `updated`)
VALUES
	(1,NULL,'cat1','2012-05-11 11:56:21',NULL),
	(2,NULL,'cat1','2012-05-11 11:57:14',NULL),
	(3,NULL,'cat1','2012-05-11 11:57:26',NULL),
	(4,NULL,'cat1','2012-05-11 11:58:52',NULL),
	(5,NULL,'cat1','2012-05-11 11:59:02',NULL),
	(6,NULL,'cat1','2012-05-11 11:59:23',NULL),
	(7,NULL,'cat1','2012-05-11 12:01:10',NULL),
	(8,NULL,'cat1','2012-05-11 12:01:25',NULL),
	(9,8,'cat2','2012-05-11 12:01:29',NULL),
	(10,9,'cat3','2012-05-11 12:01:59',NULL),
	(11,8,'cat22','2012-05-11 12:02:51',NULL),
	(12,8,'cat222','2012-05-11 12:03:10',NULL),
	(13,NULL,'cat1','2012-05-11 12:09:56',NULL),
	(69,NULL,'cat1','2012-05-13 00:19:04',NULL),
	(505,NULL,'Kategória','2012-05-13 10:22:25',NULL),
	(506,505,'Kategória fgjc','2012-05-13 10:22:25',NULL),
	(507,NULL,'Kategória','2012-05-13 10:22:45',NULL),
	(508,507,'Kategória mcnl','2012-05-13 10:22:45',NULL),
	(509,NULL,'Kategória','2012-05-13 10:22:53',NULL),
	(510,NULL,'Kategória nyrp','2012-05-13 10:22:53',NULL),
	(511,NULL,'Kategória','2012-05-13 10:23:26',NULL),
	(512,NULL,'Kategória e7sb','2012-05-13 10:23:26',NULL),
	(513,NULL,'Kategória','2012-05-13 10:23:33',NULL),
	(514,513,'Kategória d6d7','2012-05-13 10:23:33',NULL),
	(515,513,'Kategória e19f','2012-05-13 10:23:33',NULL),
	(516,513,'Kategória njmz','2012-05-13 10:23:33',NULL),
	(517,NULL,'Kategória qfg9','2012-05-13 10:23:33',NULL),
	(520,NULL,'Kategória','2012-05-13 10:24:10',NULL),
	(521,NULL,'Kategória','2012-05-13 10:24:33',NULL),
	(522,521,'Kategória djda','2012-05-13 10:24:33',NULL),
	(523,521,'Kategória 0hl6','2012-05-13 10:24:33',NULL),
	(524,521,'Kategória kl64','2012-05-13 10:24:33',NULL),
	(525,NULL,'Kategória 8rkm','2012-05-13 10:24:33',NULL),
	(526,NULL,'Kategória','2012-05-13 10:25:48',NULL),
	(527,NULL,'Kategória pudd','2012-05-13 10:25:48',NULL),
	(528,NULL,'Kategória','2012-05-13 10:25:50',NULL),
	(529,NULL,'Kategória he5y','2012-05-13 10:25:50',NULL),
	(530,NULL,'Kategória','2012-05-13 10:26:01',NULL),
	(531,NULL,'Kategória r5mo','2012-05-13 10:26:01',NULL),
	(532,NULL,'Kategória','2012-05-13 10:26:12',NULL),
	(533,NULL,'Kategória 8uid','2012-05-13 10:26:12',NULL),
	(534,NULL,'Kategória','2012-05-13 10:26:28',NULL),
	(535,534,'Kategória e901','2012-05-13 10:26:28',NULL),
	(536,534,'Kategória jbao','2012-05-13 10:26:28',NULL),
	(537,534,'Kategória 01z0','2012-05-13 10:26:28',NULL),
	(538,NULL,'Kategória pf4s','2012-05-13 10:26:28',NULL),
	(539,NULL,'Kategória','2012-05-13 10:26:34',NULL),
	(540,NULL,'Kategória rsri','2012-05-13 10:26:34',NULL),
	(541,NULL,'Kategória','2012-05-13 10:29:29',NULL),
	(542,541,'Kategória j7em','2012-05-13 10:29:29',NULL),
	(543,541,'Kategória ue9a','2012-05-13 10:29:29',NULL),
	(544,541,'Kategória ovxj','2012-05-13 10:29:29',NULL),
	(545,NULL,'Kategória ge7h','2012-05-13 10:29:29',NULL),
	(546,NULL,'Kategória','2012-05-13 10:29:53',NULL),
	(547,546,'Kategória 6930','2012-05-13 10:29:53',NULL),
	(548,546,'Kategória rp2f','2012-05-13 10:29:53',NULL),
	(549,546,'Kategória e7r3','2012-05-13 10:29:53',NULL),
	(550,NULL,'Kategória f68y','2012-05-13 10:29:53',NULL),
	(551,NULL,'Kategória','2012-05-13 10:31:36',NULL),
	(552,NULL,'Kategória 38zj','2012-05-13 10:31:36',NULL),
	(553,NULL,'Kategória','2012-05-13 10:32:04',NULL),
	(554,NULL,'Kategória n9do','2012-05-13 10:32:04',NULL),
	(555,NULL,'Kategória','2012-05-13 10:32:19',NULL),
	(556,NULL,'Kategória xp9d','2012-05-13 10:32:19',NULL),
	(559,NULL,'Kategória','2012-05-13 10:37:38',NULL),
	(560,559,'Kategória 7drq','2012-05-13 10:37:38',NULL),
	(561,559,'Kategória fwt0','2012-05-13 10:37:38',NULL),
	(562,559,'Kategória zmxf','2012-05-13 10:37:38',NULL),
	(563,NULL,'Kategória 00rl','2012-05-13 10:37:38',NULL),
	(564,NULL,'Kategória','2012-05-13 10:38:42',NULL),
	(565,NULL,'Kategória qm53','2012-05-13 10:38:42',NULL),
	(566,NULL,'Kategória','2012-05-13 10:38:43',NULL),
	(567,NULL,'Kategória w4ck','2012-05-13 10:38:43',NULL),
	(570,NULL,'Kategória','2012-05-13 10:38:47',NULL),
	(571,570,'Kategória 3tgn','2012-05-13 10:38:47',NULL),
	(572,570,'Kategória e85g','2012-05-13 10:38:47',NULL),
	(573,570,'Kategória 47nn','2012-05-13 10:38:47',NULL),
	(574,NULL,'Kategória 9uga','2012-05-13 10:38:47',NULL),
	(577,NULL,'Kategória','2012-05-13 10:39:02',NULL),
	(578,577,'Kategória 77qd','2012-05-13 10:39:02',NULL),
	(579,577,'Kategória yp8f','2012-05-13 10:39:02',NULL),
	(580,577,'Kategória p3q4','2012-05-13 10:39:02',NULL),
	(581,NULL,'Kategória sysl','2012-05-13 10:39:02',NULL),
	(584,NULL,'Kategória','2012-05-13 10:40:13',NULL),
	(585,584,'Kategória 9phv','2012-05-13 10:40:13',NULL),
	(586,584,'Kategória be77','2012-05-13 10:40:13',NULL),
	(587,584,'Kategória wvuv','2012-05-13 10:40:13',NULL),
	(588,NULL,'Kategória f0fv','2012-05-13 10:40:13',NULL),
	(589,NULL,'Kategória','2012-05-13 10:40:59',NULL),
	(590,NULL,'Kategória jvvd','2012-05-13 10:40:59',NULL),
	(593,NULL,'Kategória','2012-05-13 10:42:36',NULL),
	(594,593,'Kategória ahpw','2012-05-13 10:42:36',NULL),
	(595,593,'Kategória ibws','2012-05-13 10:42:36',NULL),
	(596,593,'Kategória 0f48','2012-05-13 10:42:36',NULL),
	(597,593,'Kategória a4xe','2012-05-13 10:42:36',NULL),
	(600,NULL,'Kategória','2012-05-13 10:43:41',NULL),
	(601,600,'Kategória 9tju','2012-05-13 10:43:41',NULL),
	(602,600,'Kategória p455','2012-05-13 10:43:41',NULL),
	(603,600,'Kategória zfpr','2012-05-13 10:43:41',NULL),
	(604,600,'Kategória 7llm','2012-05-13 10:43:41',NULL),
	(605,NULL,'Kategória','2012-05-13 10:52:39',NULL),
	(606,NULL,'Kategória hefk','2012-05-13 10:52:39',NULL),
	(609,NULL,'Kategória','2012-05-13 10:52:42',NULL),
	(610,609,'Kategória cn16','2012-05-13 10:52:42',NULL),
	(611,609,'Kategória qaua','2012-05-13 10:52:42',NULL),
	(612,609,'Kategória kdbc','2012-05-13 10:52:42',NULL),
	(613,NULL,'Kategória ng7p','2012-05-13 10:52:42',NULL),
	(616,NULL,'Kategória','2012-05-13 10:56:09',NULL),
	(617,616,'Kategória 25yf','2012-05-13 10:56:09',NULL),
	(618,616,'Kategória itos','2012-05-13 10:56:09',NULL),
	(619,616,'Kategória lekz','2012-05-13 10:56:09',NULL),
	(620,NULL,'Kategória h4tr','2012-05-13 10:56:09',NULL),
	(621,NULL,'Kategória','2012-05-13 10:56:11',NULL),
	(622,NULL,'Kategória el0o','2012-05-13 10:56:11',NULL),
	(623,NULL,'Kategória','2012-05-13 10:56:17',NULL),
	(624,NULL,'Kategória 6vwl','2012-05-13 10:56:17',NULL),
	(627,NULL,'Kategória','2012-05-13 10:56:27',NULL),
	(628,627,'Kategória xnab','2012-05-13 10:56:27',NULL),
	(629,627,'Kategória 1a7o','2012-05-13 10:56:27',NULL),
	(630,627,'Kategória okfa','2012-05-13 10:56:27',NULL),
	(631,NULL,'Kategória qxe3','2012-05-13 10:56:27',NULL),
	(634,NULL,'Kategória','2012-05-13 11:03:10',NULL),
	(635,634,'Kategória ymie','2012-05-13 11:03:10',NULL),
	(636,634,'Kategória wyjv','2012-05-13 11:03:10',NULL),
	(637,634,'Kategória 636e','2012-05-13 11:03:10',NULL),
	(638,NULL,'Kategória otbw','2012-05-13 11:03:10',NULL),
	(639,NULL,'Kategória','2012-05-13 11:18:45',NULL),
	(640,NULL,'Kategória w57e','2012-05-13 11:18:45',NULL),
	(641,NULL,'Kategória','2012-05-13 11:18:45',NULL),
	(642,NULL,'Kategória rwk4','2012-05-13 11:18:45',NULL),
	(643,NULL,'Kategória','2012-05-13 11:18:49',NULL),
	(644,NULL,'Kategória 02lv','2012-05-13 11:18:49',NULL),
	(645,NULL,'Kategória','2012-05-13 11:18:50',NULL),
	(646,NULL,'Kategória oowo','2012-05-13 11:18:50',NULL),
	(649,NULL,'Kategória','2012-05-13 11:19:04',NULL),
	(650,649,'Kategória k059','2012-05-13 11:19:04',NULL),
	(651,649,'Kategória 2i40','2012-05-13 11:19:04',NULL),
	(652,649,'Kategória n8ex','2012-05-13 11:19:04',NULL),
	(653,NULL,'Kategória 5li2','2012-05-13 11:19:04',NULL);

/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tag
# ------------------------------------------------------------

CREATE TABLE `tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `tag` WRITE;
/*!40000 ALTER TABLE `tag` DISABLE KEYS */;

INSERT INTO `tag` (`id`, `name`, `created`, `updated`)
VALUES
	(1,'tag1','2012-05-11 01:11:21',NULL),
	(2,'tag2','2012-05-11 01:11:21',NULL),
	(202,'tag1','2012-05-13 00:10:14',NULL),
	(203,'tag2','2012-05-13 00:10:14',NULL),
	(204,'tag1','2012-05-13 00:17:29',NULL),
	(205,'tag2','2012-05-13 00:17:29',NULL),
	(206,'tag1','2012-05-13 00:17:46',NULL),
	(207,'tag2','2012-05-13 00:17:46',NULL),
	(208,'tag1','2012-05-13 00:18:27',NULL),
	(209,'tag2','2012-05-13 00:18:27',NULL),
	(210,'tag1','2012-05-13 00:22:19',NULL),
	(211,'tag2','2012-05-13 00:22:19',NULL),
	(212,'tag1','2012-05-13 01:03:58',NULL),
	(213,'tag2','2012-05-13 01:03:58',NULL),
	(214,'tag1','2012-05-13 01:04:21',NULL),
	(215,'tag2','2012-05-13 01:04:21',NULL),
	(216,'tag1','2012-05-13 01:07:47',NULL),
	(217,'tag2','2012-05-13 01:07:47',NULL),
	(218,'tag1','2012-05-13 01:08:16',NULL),
	(219,'tag2','2012-05-13 01:08:16',NULL),
	(220,'tag1','2012-05-13 01:09:03',NULL),
	(221,'tag2','2012-05-13 01:09:03',NULL),
	(222,'tag1','2012-05-13 01:09:17',NULL),
	(223,'tag2','2012-05-13 01:09:17',NULL),
	(224,'tag1','2012-05-13 01:09:32',NULL),
	(225,'tag2','2012-05-13 01:09:32',NULL),
	(226,'tag1','2012-05-13 01:10:02',NULL),
	(227,'tag2','2012-05-13 01:10:02',NULL),
	(228,'tag1','2012-05-13 01:10:24',NULL),
	(229,'tag2','2012-05-13 01:10:24',NULL),
	(230,'tag1','2012-05-13 09:54:44',NULL),
	(231,'tag2','2012-05-13 09:54:44',NULL),
	(232,'tag1','2012-05-13 09:57:25',NULL),
	(233,'tag2','2012-05-13 09:57:25',NULL),
	(234,'tag1','2012-05-13 10:05:39',NULL),
	(235,'tag2','2012-05-13 10:05:39',NULL),
	(236,'tag1','2012-05-13 10:06:07',NULL),
	(237,'tag2','2012-05-13 10:06:07',NULL),
	(238,'tag1','2012-05-13 10:06:27',NULL),
	(239,'tag2','2012-05-13 10:06:27',NULL),
	(240,'tag1','2012-05-13 10:06:44',NULL),
	(241,'tag2','2012-05-13 10:06:44',NULL),
	(242,'tag1','2012-05-13 10:07:30',NULL),
	(243,'tag2','2012-05-13 10:07:30',NULL),
	(244,'tag1','2012-05-13 10:07:41',NULL),
	(245,'tag2','2012-05-13 10:07:41',NULL),
	(246,'tag1','2012-05-13 10:07:53',NULL),
	(247,'tag2','2012-05-13 10:07:53',NULL),
	(248,'tag1','2012-05-13 10:08:23',NULL),
	(249,'tag2','2012-05-13 10:08:23',NULL),
	(250,'tag1','2012-05-13 10:09:07',NULL),
	(251,'tag2','2012-05-13 10:09:07',NULL),
	(252,'tag1','2012-05-13 10:10:22',NULL),
	(253,'tag2','2012-05-13 10:10:22',NULL),
	(254,'tag1','2012-05-13 10:11:08',NULL),
	(255,'tag2','2012-05-13 10:11:08',NULL),
	(256,'tag1','2012-05-13 10:11:25',NULL),
	(257,'tag2','2012-05-13 10:11:25',NULL),
	(258,'tag1','2012-05-13 10:12:11',NULL),
	(259,'tag2','2012-05-13 10:12:11',NULL),
	(260,'tag1','2012-05-13 10:23:33',NULL),
	(261,'tag2','2012-05-13 10:23:33',NULL),
	(262,'tag1','2012-05-13 10:24:10',NULL),
	(263,'tag2','2012-05-13 10:24:10',NULL),
	(264,'tag1','2012-05-13 10:24:33',NULL),
	(265,'tag2','2012-05-13 10:24:33',NULL),
	(266,'tag1','2012-05-13 10:26:28',NULL),
	(267,'tag2','2012-05-13 10:26:28',NULL),
	(268,'tag1','2012-05-13 10:29:28',NULL),
	(269,'tag2','2012-05-13 10:29:28',NULL),
	(270,'tag1','2012-05-13 10:29:52',NULL),
	(271,'tag2','2012-05-13 10:29:52',NULL),
	(272,'tag1','2012-05-13 10:37:38',NULL),
	(273,'tag2','2012-05-13 10:37:38',NULL),
	(274,'tag1','2012-05-13 10:38:46',NULL),
	(275,'tag2','2012-05-13 10:38:46',NULL),
	(276,'tag1','2012-05-13 10:39:01',NULL),
	(277,'tag2','2012-05-13 10:39:01',NULL),
	(278,'tag1','2012-05-13 10:40:13',NULL),
	(279,'tag2','2012-05-13 10:40:13',NULL),
	(280,'tag1','2012-05-13 10:42:36',NULL),
	(281,'tag2','2012-05-13 10:42:36',NULL),
	(282,'tag1','2012-05-13 10:43:40',NULL),
	(283,'tag2','2012-05-13 10:43:40',NULL),
	(284,'tag1','2012-05-13 10:52:42',NULL),
	(285,'tag2','2012-05-13 10:52:42',NULL),
	(286,'tag1','2012-05-13 10:56:08',NULL),
	(287,'tag2','2012-05-13 10:56:08',NULL),
	(288,'tag1','2012-05-13 10:56:26',NULL),
	(289,'tag2','2012-05-13 10:56:26',NULL),
	(290,'tag1','2012-05-13 11:03:09',NULL),
	(291,'tag2','2012-05-13 11:03:09',NULL),
	(292,'tag1','2012-05-13 11:19:03',NULL),
	(293,'tag2','2012-05-13 11:19:03',NULL);

/*!40000 ALTER TABLE `tag` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
