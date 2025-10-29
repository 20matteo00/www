-- Database: spese
-- Generated: 2025-10-29 12:35:36

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE `categorie` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descrizione` varchar(255) DEFAULT NULL,
  `dati` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `categorie` VALUES
('1','Alimentari','Spese per cibo e bevande','{\"color\": \"#FF6384\"}','2025-10-29 13:32:18'),
('2','Trasporti','Spese relative ai trasporti','{\"color\": \"#36A2EB\"}','2025-10-29 13:32:18'),
('3','Svago','Divertimento, viaggi e tempo libero','{\"color\": \"#FFCE56\"}','2025-10-29 13:32:18'),
('4','Casa','Spese relative all\'abitazione','{\"color\": \"#4BC0C0\"}','2025-10-29 13:32:18'),
('5','Lavoro','Spese legate al lavoro e alla carriera','{\"color\": \"#9966FF\"}','2025-10-29 13:32:18'),
('6','Personali','Spese personali, estetica, abbigliamento','{\"color\": \"#FF9F40\"}','2025-10-29 13:32:18'),
('7','Salute','Cure, farmaci e assicurazioni sanitarie','{\"color\": \"#E74C3C\"}','2025-10-29 13:32:18'),
('8','Regali','Regali per amici, parenti e occasioni speciali','{\"color\": \"#9B59B6\"}','2025-10-29 13:32:18'),
('9','Animali','Spese per animali domestici','{\"color\": \"#27AE60\"}','2025-10-29 13:32:18'),
('10','Tasse e Utenze','Bollette, luce, gas, internet, ecc.','{\"color\": \"#2E86C1\"}','2025-10-29 13:32:18'),
('11','Istruzione','Corsi, libri e materiali formativi','{\"color\": \"#F1C40F\"}','2025-10-29 13:32:18'),
('12','Finanza','Mutui, prestiti, carte e commissioni','{\"color\": \"#1ABC9C\"}','2025-10-29 13:32:18'),
('13','Famiglia','Spese per figli, scuola e attività','{\"color\": \"#D35400\"}','2025-10-29 13:32:18'),
('14','Tempo Libero','Hobby, sport, giardinaggio e uscite','{\"color\": \"#7DCEA0\"}','2025-10-29 13:32:18'),
('15','Tecnologia','Hardware, software e abbonamenti digitali','{\"color\": \"#5DADE2\"}','2025-10-29 13:32:18');

DROP TABLE IF EXISTS `membri`;
CREATE TABLE `membri` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descrizione` varchar(255) DEFAULT NULL,
  `dati` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `membri` VALUES
('1','Matteo','Il vero uomo','{\"color\": \"#000080\"}','2025-10-28 17:05:03'),
('2','Valentina','Detta \"Boiler\"','{\"color\": \"#ff0000\"}','2025-10-28 17:05:16'),
('3','Germano','','{\"color\": \"#008000\"}','2025-10-29 12:18:16'),
('4','Adolfina','','{\"color\": \"#800080\"}','2025-10-29 12:18:27');

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
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `sottocategorie` VALUES
('4','1','Supermercato','Spese alimentari quotidiane','{\"color\": \"#FFD1D1\"}','2025-10-29 13:32:18'),
('5','1','Ristorante','Pranzi e cene fuori casa','{\"color\": \"#FFB3B3\"}','2025-10-29 13:32:18'),
('6','1','Fast Food','McDonald\'s, KFC, Burger King ecc.','{\"color\": \"#FF9999\"}','2025-10-29 13:32:18'),
('7','1','Bar','Colazioni, aperitivi, pause caffè','{\"color\": \"#FF8080\"}','2025-10-29 13:32:18'),
('8','1','Pasticceria','Dolci, torte, prodotti da forno','{\"color\": \"#FF6666\"}','2025-10-29 13:32:18'),
('9','2','Autostrada','Pedaggi, telepass, caselli','{\"color\": \"#A7E3E3\"}','2025-10-29 13:32:18'),
('10','2','Benzina','Carburante per auto e moto','{\"color\": \"#7FD6D6\"}','2025-10-29 13:32:18'),
('11','2','Mezzi Pubblici','Autobus, metro, treni','{\"color\": \"#5AC8FA\"}','2025-10-29 13:32:18'),
('12','2','Taxi','Servizi taxi e NCC','{\"color\": \"#36A2EB\"}','2025-10-29 13:32:18'),
('13','2','Parcheggio','Sosta a pagamento, garage','{\"color\": \"#1E90FF\"}','2025-10-29 13:32:18'),
('14','3','Viaggio','Vacanze e spostamenti','{\"color\": \"#FFF2B2\"}','2025-10-29 13:32:18'),
('15','3','Albergo','Pernottamenti e hotel','{\"color\": \"#FFE699\"}','2025-10-29 13:32:18'),
('16','3','Cinema','Film, popcorn e serate','{\"color\": \"#FFD966\"}','2025-10-29 13:32:18'),
('17','3','Concerti','Eventi musicali','{\"color\": \"#FFCC66\"}','2025-10-29 13:32:18'),
('18','3','Eventi Sportivi','Partite, gare, tornei','{\"color\": \"#FFB266\"}','2025-10-29 13:32:18'),
('19','4','Affitto','Canone mensile abitazione','{\"color\": \"#B2FFFF\"}','2025-10-29 13:32:18'),
('20','4','Bollette','Luce, gas, acqua','{\"color\": \"#80E6E6\"}','2025-10-29 13:32:18'),
('21','4','Manutenzione','Riparazioni domestiche','{\"color\": \"#4BC0C0\"}','2025-10-29 13:32:18'),
('22','4','Arredamento','Mobili, decorazioni','{\"color\": \"#009999\"}','2025-10-29 13:32:18'),
('23','4','Pulizie','Prodotti e servizi di pulizia','{\"color\": \"#66CCCC\"}','2025-10-29 13:32:18'),
('24','5','Pranzi di Lavoro','Ristoranti, pause pranzo','{\"color\": \"#CDA6FF\"}','2025-10-29 13:32:18'),
('25','5','Trasferte','Spese di viaggio per lavoro','{\"color\": \"#B266FF\"}','2025-10-29 13:32:18'),
('26','5','Formazione','Corsi e aggiornamenti','{\"color\": \"#A366FF\"}','2025-10-29 13:32:18'),
('27','5','Attrezzature','PC, strumenti e materiali','{\"color\": \"#9966FF\"}','2025-10-29 13:32:18'),
('28','6','Abbigliamento','Vestiti e accessori','{\"color\": \"#FFCC99\"}','2025-10-29 13:32:18'),
('29','6','Parrucchiere','Taglio, colore e acconciatura','{\"color\": \"#FFB380\"}','2025-10-29 13:32:18'),
('30','6','Benessere','Spa, massaggi, estetica','{\"color\": \"#FF9F40\"}','2025-10-29 13:32:18'),
('31','6','Cosmetici','Prodotti di bellezza','{\"color\": \"#FF9240\"}','2025-10-29 13:32:18'),
('32','7','Farmacia','Medicinali e parafarmaci','{\"color\": \"#E74C3C\"}','2025-10-29 13:32:18'),
('33','7','Visite Mediche','Controlli e specialisti','{\"color\": \"#FF6B6B\"}','2025-10-29 13:32:18'),
('34','7','Assicurazione Sanitaria','Polizze mediche','{\"color\": \"#C0392B\"}','2025-10-29 13:32:18'),
('35','8','Compleanni','Regali per compleanni','{\"color\": \"#E8DAEF\"}','2025-10-29 13:32:18'),
('36','8','Natale','Regali e festività natalizie','{\"color\": \"#D2B4DE\"}','2025-10-29 13:32:18'),
('37','8','Anniversari','Regali per occasioni speciali','{\"color\": \"#BB8FCE\"}','2025-10-29 13:32:18'),
('38','9','Cibo Animali','Crocchette e alimenti','{\"color\": \"#27AE60\"}','2025-10-29 13:32:18'),
('39','9','Veterinario','Cure e visite','{\"color\": \"#52BE80\"}','2025-10-29 13:32:18'),
('40','9','Accessori','Giochi, guinzagli, letti','{\"color\": \"#82E0AA\"}','2025-10-29 13:32:18'),
('41','10','Luce','Energia elettrica','{\"color\": \"#A9CCE3\"}','2025-10-29 13:32:18'),
('42','10','Gas','Fornitura gas metano','{\"color\": \"#85C1E9\"}','2025-10-29 13:32:18'),
('43','10','Acqua','Servizi idrici','{\"color\": \"#5DADE2\"}','2025-10-29 13:32:18'),
('44','10','Internet','Fibra, modem, provider','{\"color\": \"#2E86C1\"}','2025-10-29 13:32:18'),
('45','10','Telefono','Linea fissa e mobile','{\"color\": \"#2874A6\"}','2025-10-29 13:32:18'),
('46','11','Libri','Libri scolastici e romanzi','{\"color\": \"#F9E79F\"}','2025-10-29 13:32:18'),
('47','11','Corsi','Corsi online e in presenza','{\"color\": \"#F4D03F\"}','2025-10-29 13:32:18'),
('48','11','Iscrizioni','Tasse universitarie, iscrizioni','{\"color\": \"#D4AC0D\"}','2025-10-29 13:32:18'),
('49','11','Cancelleria','Materiale da ufficio','{\"color\": \"#B7950B\"}','2025-10-29 13:32:18'),
('50','12','Mutuo','Rate mutuo casa','{\"color\": \"#1ABC9C\"}','2025-10-29 13:32:18'),
('51','12','Prestiti','Finanziamenti e debiti','{\"color\": \"#16A085\"}','2025-10-29 13:32:18'),
('52','12','Carte di Credito','Spese bancarie e carte','{\"color\": \"#117A65\"}','2025-10-29 13:32:18'),
('53','12','Commissioni','Tasse bancarie','{\"color\": \"#0E6251\"}','2025-10-29 13:32:18'),
('54','13','Figli','Spese per figli e infanzia','{\"color\": \"#EB984E\"}','2025-10-29 13:32:18'),
('55','13','Asilo','Rette e materiale scolastico','{\"color\": \"#DC7633\"}','2025-10-29 13:32:18'),
('56','13','Scuola','Libri, trasporto, attività','{\"color\": \"#D35400\"}','2025-10-29 13:32:18'),
('57','13','Attività Extrascolastiche','Sport, corsi e hobby','{\"color\": \"#BA4A00\"}','2025-10-29 13:32:18'),
('58','14','Sport','Palestra, attrezzature sportive','{\"color\": \"#ABEBC6\"}','2025-10-29 13:32:18'),
('59','14','Hobby','Collezionismo, musica, arte','{\"color\": \"#82E0AA\"}','2025-10-29 13:32:18'),
('60','14','Giardinaggio','Piante, attrezzi e semi','{\"color\": \"#58D68D\"}','2025-10-29 13:32:18'),
('61','14','Escursioni','Passeggiate e gite','{\"color\": \"#2ECC71\"}','2025-10-29 13:32:18'),
('62','15','Hardware','Computer, smartphone, accessori','{\"color\": \"#AED6F1\"}','2025-10-29 13:32:18'),
('63','15','Software','Programmi, licenze, aggiornamenti','{\"color\": \"#5DADE2\"}','2025-10-29 13:32:18'),
('64','15','Abbonamenti','Netflix, Spotify, servizi cloud','{\"color\": \"#2E86C1\"}','2025-10-29 13:32:18'),
('65','15','Riparazioni','Riparazioni e manutenzione hardware','{\"color\": \"#21618C\"}','2025-10-29 13:32:18');

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `spese` VALUES
('5','35.60','2025-10-13','Iscrizione a calcio','1','58',NULL,'2025-10-29 12:35:16');

SET FOREIGN_KEY_CHECKS=1;
