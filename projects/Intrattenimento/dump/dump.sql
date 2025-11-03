-- Database: intrattenimento
-- Generated: 2025-11-03 11:03:21

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `intrattenimento` VALUES
('4','a','film','wq','{\"anno\": \"2020\", \"durata\": \"123\", \"generi\": [\"azione\", \"commedia\"], \"piattaforme\": [\"apple_tv\"]}','2025-11-03 10:21:07'),
('5','asda','serie_tv','sasdasda','{\"anno\": \"2012-2023\", \"durata\": \"12-123\", \"finita\": \"0\", \"generi\": [\"giallo\", \"romantico\", \"sportivo\"], \"episodi\": \"2\", \"stagioni\": \"1\", \"piattaforme\": [\"prime_video\", \"pluto_tv\", \"rai_play\"]}','2025-11-03 10:31:14');

SET FOREIGN_KEY_CHECKS=1;
