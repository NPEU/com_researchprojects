DROP TABLE IF EXISTS `#__researchprojects`;

CREATE TABLE `#__researchprojects` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` MEDIUMTEXT COLLATE utf8mb4_unicode_ci NOT NULL,
  `alias` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `catid` int(11) NOT NULL DEFAULT '0',
  `owner_user_id` INT(11) NOT NULL DEFAULT '0',
  `pi_1` VARCHAR(1024) NOT NULL DEFAULT '',
  `pi_2` VARCHAR(1024) NOT NULL DEFAULT '',
  `collaborators` VARCHAR(1024) NOT NULL DEFAULT '',
  `topics` VARCHAR(1024) NOT NULL DEFAULT '',
  `content` TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
  `funders` VARCHAR(1024) NOT NULL DEFAULT '',
  `start_year` INT(4) NOT NULL DEFAULT '1978',
  `end_year` INT(4) NOT NULL DEFAULT '1978',
  `publications` TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_id` INT(11) NOT NULL DEFAULT '0',
  `params` VARCHAR(1024) NOT NULL DEFAULT '',
  `state` TINYINT(3) NOT NULL DEFAULT '0',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(10) NOT NULL DEFAULT '0',
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT(10) NOT NULL DEFAULT '0',
  `checked_out` INT(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` INT(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
    ENGINE          = MyISAM
    AUTO_INCREMENT  = 0
    DEFAULT CHARSET = utf8;


DROP TABLE IF EXISTS `#__researchprojects_topics`;

CREATE TABLE `#__researchprojects_topics` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `topic` TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
)
    ENGINE          = MyISAM
    AUTO_INCREMENT  = 0
    DEFAULT CHARSET = utf8;

INSERT INTO `#__researchprojects_topics` (`topic`) VALUES
("Alcohol in pregnancy"),
("Antenatal care"),
("Breastfeeding"),
("Care of the compromised term infant"),
("Care of the preterm or low birthweight infant"),
("Child health and development"),
("Congenital anomalies"),
("Disability"),
("Health Economics"),
("Infertility"),
("Labour and delivery"),
("Mental health and wellbeing"),
("Methodology"),
("Multiple births"),
("Obesity"),
("Organisation and delivery of maternity and neonatal care"),
("Paediatric Surgery"),
("Parents"),
("Preterm birth"),
("Resilience"),
("Severe maternal morbidity and mortality"),
("Smoking or vaping in pregnancy"),
("Socioeconomic and ethnic inequalities"),
("Stillbirth and infant death"),
("Women's experience of maternity care");


DROP TABLE IF EXISTS `#__researchprojects_collaborators`;

CREATE TABLE `#__researchprojects_collaborators` (
  `hash` CHAR(32) NOT NULL,
  `collaborator` TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`hash`)
)
    ENGINE          = MyISAM
    AUTO_INCREMENT  = 0
    DEFAULT CHARSET = utf8;



DROP TABLE IF EXISTS `#__researchprojects_funders`;

CREATE TABLE `#__researchprojects_funders` (
  `hash` CHAR(32) NOT NULL,
  `funder` TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`hash`)
)
    ENGINE          = MyISAM
    AUTO_INCREMENT  = 0
    DEFAULT CHARSET = utf8;
