-- Database: intrattenimento
-- Generated: 2025-11-03 09:30:49

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

SET FOREIGN_KEY_CHECKS=1;
