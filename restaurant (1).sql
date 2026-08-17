-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 17, 2026 at 02:21 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `restaurant`
--

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `id_client` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `telephone` varchar(30) DEFAULT NULL,
  `adresse` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`id_client`, `nom`, `telephone`, `adresse`) VALUES
(1, 'Patricia Tiba', '670693464', 'pont'),
(2, 'Patricia Tiba', '670693464', 'pont'),
(3, 'Patricia Tiba', '679013380', 'chez mboma'),
(4, 'jean dupont', '677611038', 'chez mbomq');

-- --------------------------------------------------------

--
-- Table structure for table `commande`
--

CREATE TABLE `commande` (
  `id_commande` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `statut` varchar(50) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `nom` varchar(255) NOT NULL,
  `quantite` int(11) DEFAULT NULL,
  `montant` decimal(10,2) NOT NULL DEFAULT 0.00,
  `id_client` int(11) DEFAULT NULL,
  `id_employe` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `commande`
--

INSERT INTO `commande` (`id_commande`, `type`, `statut`, `date`, `nom`, `quantite`, `montant`, `id_client`, `id_employe`) VALUES
(2, 'A emporter', 'Terminée', '2026-08-05', '', 3, 0.00, NULL, NULL),
(3, 'Sur place', 'Annulée', '2026-05-04', '', 1, 0.00, NULL, NULL),
(4, 'A emporter', 'Terminée', '2025-01-23', '', 3, 0.00, NULL, NULL),
(5, 'Sur place', 'En attente', '2025-12-13', '', 3, 0.00, NULL, NULL),
(6, 'A emporter', 'Terminée', '2026-08-08', '', 6, 0.00, NULL, NULL),
(7, 'À emporter', 'En attente', '2026-08-09', '', 2, 0.00, 1, NULL),
(8, 'À emporter', 'En attente', '2026-08-09', '', 2, 0.00, 2, NULL),
(9, 'À emporter', 'En attente', '2026-08-09', 'new product chocolat', 6, 1500.00, 3, NULL),
(10, 'A emporter', 'Terminée', '2025-03-12', '', 3, 0.00, NULL, NULL),
(11, 'Livraison', 'En attente', '2026-08-11', '', 2, 0.00, 4, NULL),
(12, 'Sur place', 'En attente', '2026-08-11', '', 3, 0.00, NULL, NULL),
(13, 'Sur place', 'En attente', '2026-08-11', '', 4, 0.00, NULL, NULL),
(15, 'Sur place', 'En attente', '2026-08-11', 'coca cola x2 + macaroni x2', 4, 2700.00, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `commande_produit`
--

CREATE TABLE `commande_produit` (
  `id_commande_produit` int(11) NOT NULL,
  `id_commande` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `nom` varchar(159) NOT NULL,
  `quantite` int(11) NOT NULL,
  `prix` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `commande_produit`
--

INSERT INTO `commande_produit` (`id_commande_produit`, `id_commande`, `id_produit`, `nom`, `quantite`, `prix`) VALUES
(1, 9, 9, '', 2, 700.00),
(2, 9, 10, '', 4, 500.00);

-- --------------------------------------------------------

--
-- Table structure for table `depense`
--

CREATE TABLE `depense` (
  `id_depense` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `quantite` int(11) NOT NULL DEFAULT 1,
  `unite` varchar(50) NOT NULL DEFAULT 'unité',
  `montant` decimal(10,2) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `id_utilisateur` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `depense`
--

INSERT INTO `depense` (`id_depense`, `description`, `quantite`, `unite`, `montant`, `date`, `id_utilisateur`) VALUES
(1, 'achat de 2 sac de riz', 1, 'unité', 50000.00, '2026-08-10', 1),
(2, 'achat de bidon d\'huile de 20 litre', 20, 'litre', 17000.00, '2026-08-10', 1);

-- --------------------------------------------------------

--
-- Table structure for table `employe`
--

CREATE TABLE `employe` (
  `id_employe` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `poste` varchar(100) DEFAULT NULL,
  `salaire` decimal(10,2) DEFAULT NULL,
  `horaire` varchar(100) DEFAULT NULL,
  `id_restaurant` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employe`
--

INSERT INTO `employe` (`id_employe`, `nom`, `poste`, `salaire`, `horaire`, `id_restaurant`) VALUES
(3, 'Tiba Patricia ', 'serveur', 50000.00, '6H-18H', NULL),
(4, 'SOFIA ', 'serveuse et livreuse', 20000.00, '6H-13H', NULL),
(6, 'fede', 'serveur', 40000.00, '6H-18H', NULL),
(7, 'Audrey', 'serveuse', 40000.00, '6H-18H', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `facture`
--

CREATE TABLE `facture` (
  `id_facture` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `montant` decimal(10,2) DEFAULT NULL,
  `id_utilisateur` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facture`
--

INSERT INTO `facture` (`id_facture`, `nom`, `montant`, `id_utilisateur`) VALUES
(2, 'macaroni', 7000.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id_notification` int(11) NOT NULL,
  `titre` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) DEFAULT 'info',
  `lu` tinyint(1) DEFAULT 0,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plats`
--

CREATE TABLE `plats` (
  `id_plat` int(11) NOT NULL,
  `id_produit` int(11) DEFAULT NULL,
  `quantite` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produit`
--

CREATE TABLE `produit` (
  `id_produit` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prix` decimal(10,2) DEFAULT NULL,
  `quantite_stock` int(11) DEFAULT NULL,
  `seuil_alerte` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produit`
--

INSERT INTO `produit` (`id_produit`, `nom`, `prix`, `quantite_stock`, `seuil_alerte`) VALUES
(9, 'coca cola', 700.00, 20, 8),
(10, 'macaroni', 300.00, 20, 5),
(11, 'haricot', 300.00, 50, 10),
(12, 'spaghetti', 300.00, 20, 6),
(13, 'Boullion de qeue de boeuf', 600.00, 12, 20);

-- --------------------------------------------------------

--
-- Table structure for table `restaurant`
--

CREATE TABLE `restaurant` (
  `id_restaurant` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `adresse` varchar(150) DEFAULT NULL,
  `telephone` varchar(30) DEFAULT NULL,
  `horaires` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_matiere_premiere`
--

CREATE TABLE `stock_matiere_premiere` (
  `id_stock` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `quantite` decimal(10,2) NOT NULL,
  `unite` varchar(30) NOT NULL,
  `prix_achat` decimal(10,2) NOT NULL,
  `date_achat` date NOT NULL,
  `id_depense` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_matiere_premiere`
--

INSERT INTO `stock_matiere_premiere` (`id_stock`, `nom`, `quantite`, `unite`, `prix_achat`, `date_achat`, `id_depense`) VALUES
(1, 'riz', 2.00, 'sac', 50000.00, '2026-08-10', 1),
(2, 'huile', 2.00, 'litre', 17000.00, '2026-08-10', 2);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_paiement`
--

CREATE TABLE `transaction_paiement` (
  `id_transaction` int(11) NOT NULL,
  `id_commande` int(11) DEFAULT NULL,
  `montant` decimal(10,2) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `statut` varchar(50) DEFAULT NULL,
  `reference_mobile_money` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_paiement`
--

INSERT INTO `transaction_paiement` (`id_transaction`, `id_commande`, `montant`, `type`, `statut`, `reference_mobile_money`) VALUES
(1, 6, 2500.00, 'Espèces', 'Payé', '670693464'),
(2, 7, 3700.00, 'Mobile Money', 'Payé', ''),
(4, 3, 700.00, 'Espèces', 'En attente', '');

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id_utilisateur` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `mot_de_passe` varchar(255) DEFAULT NULL,
  `contact` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`id_utilisateur`, `nom`, `role`, `mot_de_passe`, `contact`) VALUES
(1, 'Patricia ', 'Administrateur', 'admin123', '670693464');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id_client`);

--
-- Indexes for table `commande`
--
ALTER TABLE `commande`
  ADD PRIMARY KEY (`id_commande`),
  ADD KEY `id_client` (`id_client`),
  ADD KEY `id_employe` (`id_employe`);

--
-- Indexes for table `commande_produit`
--
ALTER TABLE `commande_produit`
  ADD PRIMARY KEY (`id_commande_produit`),
  ADD KEY `id_commande` (`id_commande`),
  ADD KEY `id_produit` (`id_produit`);

--
-- Indexes for table `depense`
--
ALTER TABLE `depense`
  ADD PRIMARY KEY (`id_depense`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Indexes for table `employe`
--
ALTER TABLE `employe`
  ADD PRIMARY KEY (`id_employe`),
  ADD KEY `id_restaurant` (`id_restaurant`);

--
-- Indexes for table `facture`
--
ALTER TABLE `facture`
  ADD PRIMARY KEY (`id_facture`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id_notification`);

--
-- Indexes for table `plats`
--
ALTER TABLE `plats`
  ADD PRIMARY KEY (`id_plat`),
  ADD KEY `id_produit` (`id_produit`);

--
-- Indexes for table `produit`
--
ALTER TABLE `produit`
  ADD PRIMARY KEY (`id_produit`);

--
-- Indexes for table `restaurant`
--
ALTER TABLE `restaurant`
  ADD PRIMARY KEY (`id_restaurant`);

--
-- Indexes for table `stock_matiere_premiere`
--
ALTER TABLE `stock_matiere_premiere`
  ADD PRIMARY KEY (`id_stock`),
  ADD KEY `id_depense` (`id_depense`);

--
-- Indexes for table `transaction_paiement`
--
ALTER TABLE `transaction_paiement`
  ADD PRIMARY KEY (`id_transaction`),
  ADD KEY `id_commande` (`id_commande`);

--
-- Indexes for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id_utilisateur`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `id_client` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `commande`
--
ALTER TABLE `commande`
  MODIFY `id_commande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `commande_produit`
--
ALTER TABLE `commande_produit`
  MODIFY `id_commande_produit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `depense`
--
ALTER TABLE `depense`
  MODIFY `id_depense` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `employe`
--
ALTER TABLE `employe`
  MODIFY `id_employe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `facture`
--
ALTER TABLE `facture`
  MODIFY `id_facture` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id_notification` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plats`
--
ALTER TABLE `plats`
  MODIFY `id_plat` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produit`
--
ALTER TABLE `produit`
  MODIFY `id_produit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `restaurant`
--
ALTER TABLE `restaurant`
  MODIFY `id_restaurant` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_matiere_premiere`
--
ALTER TABLE `stock_matiere_premiere`
  MODIFY `id_stock` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transaction_paiement`
--
ALTER TABLE `transaction_paiement`
  MODIFY `id_transaction` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `commande`
--
ALTER TABLE `commande`
  ADD CONSTRAINT `commande_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `client` (`id_client`),
  ADD CONSTRAINT `commande_ibfk_2` FOREIGN KEY (`id_employe`) REFERENCES `employe` (`id_employe`);

--
-- Constraints for table `commande_produit`
--
ALTER TABLE `commande_produit`
  ADD CONSTRAINT `commande_produit_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commande` (`id_commande`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `commande_produit_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON UPDATE CASCADE;

--
-- Constraints for table `depense`
--
ALTER TABLE `depense`
  ADD CONSTRAINT `depense_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`);

--
-- Constraints for table `employe`
--
ALTER TABLE `employe`
  ADD CONSTRAINT `employe_ibfk_1` FOREIGN KEY (`id_restaurant`) REFERENCES `restaurant` (`id_restaurant`);

--
-- Constraints for table `facture`
--
ALTER TABLE `facture`
  ADD CONSTRAINT `facture_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`);

--
-- Constraints for table `plats`
--
ALTER TABLE `plats`
  ADD CONSTRAINT `plats_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`);

--
-- Constraints for table `stock_matiere_premiere`
--
ALTER TABLE `stock_matiere_premiere`
  ADD CONSTRAINT `stock_matiere_premiere_ibfk_1` FOREIGN KEY (`id_depense`) REFERENCES `depense` (`id_depense`);

--
-- Constraints for table `transaction_paiement`
--
ALTER TABLE `transaction_paiement`
  ADD CONSTRAINT `transaction_paiement_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commande` (`id_commande`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
