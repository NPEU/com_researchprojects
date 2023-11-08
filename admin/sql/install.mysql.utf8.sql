DROP TABLE IF EXISTS `#__researchprojects`;

CREATE TABLE `#__researchprojects` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` MEDIUMTEXT COLLATE utf8mb4_unicode_ci NOT NULL,
  `alias` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `catid` int(11) NOT NULL DEFAULT '0',
  `owner_user_id` INT(11) NOT NULL DEFAULT '0',
  `pi_1` VARCHAR(1024) NOT NULL DEFAULT '',
  `pi_2` VARCHAR(1024) NOT NULL DEFAULT '',
  `collaborators` TEXT NOT NULL DEFAULT '',
  `content` TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
  `funders` TEXT NOT NULL DEFAULT '',
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
  `title` TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
  `alias` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
)
    ENGINE          = MyISAM
    AUTO_INCREMENT  = 0
    DEFAULT CHARSET = utf8;

INSERT INTO `#__researchprojects_topics` (`title`, `alias`) VALUES
("Alcohol in pregnancy", "alcohol-in-pregnancy"),
("Antenatal care", "antenatal-care"),
("Breastfeeding", "breastfeeding"),
("Care of the compromised term infant", "care-of-the-compromised-term-infant"),
("Care of the preterm or low birthweight infant", "Care-of-the-preterm-or-low-birthweight-infant"),
("Child health and development", "child-health-and-development"),
("Congenital anomalies", "congenital-anomalies"),
("Disability", "disability"),
("Health economics", "health-economics"),
("Infertility", "infertility"),
("Labour and delivery", "labour-and-delivery"),
("Mental health and wellbeing", "mental-health-and-wellbeing"),
("Methodology", "methodology"),
("Multiple births", "multiple-births"),
("Obesity", "obesity"),
("Organisation and delivery of maternity and neonatal care", "organisation-and-delivery-of-maternity-and-neonatal-care"),
("Paediatric surgery", "paediatric-surgery"),
("Parents", "parents"),
("Preterm birth", "preterm-birth"),
("Resilience", "resilience"),
("Severe maternal morbidity and mortality", "severe-maternal-morbidity-and-mortality"),
("Smoking or vaping in pregnancy", "smoking-or-vaping-in-pregnancy"),
("Socioeconomic and ethnic inequalities", "socioeconomic-and-ethnic-inequalities"),
("Stillbirth and infant death", "stillbirth-and-infant-death"),
("Women's experience of maternity care", "womens-experience-of-maternity-care");


DROP TABLE IF EXISTS `#__researchprojects_topics_map`;

CREATE TABLE `#__researchprojects_topics_map` (
  `project_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__researchprojects.id',
  `topic_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__researchprojects_topics.id',
  PRIMARY KEY (`project_id`,`topic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


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
