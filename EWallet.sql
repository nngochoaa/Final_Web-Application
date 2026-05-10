-- -------------------------------------------------------------
-- TablePlus 6.9.1(670)
--
-- https://tableplus.com/
--
-- Database: ewallet
-- Generation Time: 2026-05-10 13:32:30.1220
-- -------------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


CREATE TABLE `credit_cards` (
  `id` int NOT NULL AUTO_INCREMENT,
  `card_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cvv` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `max_per_tx` decimal(15,2) DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `card_number` (`card_number`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `credit_cards` (`id`, `card_number`, `expiration`, `cvv`, `max_per_tx`, `note`) VALUES
(1, '111111', '10/10/2022', '411', NULL, 'Unlimited deposits'),
(2, '222222', '11/11/2022', '443', 1000000.00, 'Max 1,000,000 VND per transaction'),
(3, '333333', '12/12/2022', '577', NULL, 'Always out of money');


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;