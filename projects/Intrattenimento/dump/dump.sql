-- Database: intrattenimento
-- Generated: 2025-11-03 11:56:30

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `intrattenimento`;
CREATE TABLE `intrattenimento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `tipo` varchar(255) NOT NULL,
  `descrizione` text,
  `dati` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `intrattenimento` VALUES
('6','La Mummia','film','1° film della saga La Mummia','{\"anno\": \"1999\", \"durata\": \"132\", \"generi\": [\"thriller\", \"horror\"], \"piattaforme\": [\"streaming\"]}','2025-11-03 11:55:37');

SET FOREIGN_KEY_CHECKS=1;
