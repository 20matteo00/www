-- Database: spese
-- Generated: 2025-10-28 22:12:41

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE `categorie` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descrizione` varchar(255) DEFAULT NULL,
  `dati` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `categorie` VALUES
('1','Alimentari','spese riguardanti il cibo',NULL,'2025-10-28 17:05:32'),
('2','Trasporti','Spese riguaradanti viaggi/mezzi/pedaggi/benzina',NULL,'2025-10-28 17:06:06');

DROP TABLE IF EXISTS `membri`;
CREATE TABLE `membri` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descrizione` varchar(255) DEFAULT NULL,
  `dati` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `membri` VALUES
('1','Matteo','Il vero uomo',NULL,'2025-10-28 17:05:03'),
('2','Valentina','Detta \"Boiler\"',NULL,'2025-10-28 17:05:16');

DROP TABLE IF EXISTS `sottocategorie`;
CREATE TABLE `sottocategorie` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_categoria` int NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descrizione` varchar(255) DEFAULT NULL,
  `dati` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_categoria` (`id_categoria`),
  CONSTRAINT `sottocategorie_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `sottocategorie` VALUES
('1','1','Fast Food','Mc, KFC',NULL,'2025-10-28 17:06:28'),
('2','2','Benzina','',NULL,'2025-10-28 17:06:34');

DROP TABLE IF EXISTS `spese`;
CREATE TABLE `spese` (
  `id` int NOT NULL AUTO_INCREMENT,
  `importo` decimal(10,2) NOT NULL,
  `data` date NOT NULL,
  `descrizione` varchar(255) DEFAULT NULL,
  `id_membro` int NOT NULL,
  `id_sottocategoria` int NOT NULL,
  `dati` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_membro` (`id_membro`),
  KEY `id_sottocategoria` (`id_sottocategoria`),
  CONSTRAINT `spese_ibfk_1` FOREIGN KEY (`id_membro`) REFERENCES `membri` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `spese_ibfk_2` FOREIGN KEY (`id_sottocategoria`) REFERENCES `sottocategorie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `spese` VALUES
('1','48.00','2025-10-28','Dio cane quanto costa','1','2',NULL,'2025-10-28 17:06:49');

SET FOREIGN_KEY_CHECKS=1;
