-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : lun. 30 mars 2026 à 15:37
-- Version du serveur : 10.6.23-MariaDB-0ubuntu0.22.04.1
-- Version de PHP : 8.1.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `chaussures_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `nom`, `description`) VALUES
(1, 'Running', 'Chaussures conçues pour la course à pied, légères et amorties.'),
(2, 'Sneakers', 'Baskets lifestyle pour un usage quotidien casual.'),
(3, 'Skate', 'Chaussures renforcées pour la pratique du skateboard.'),
(4, 'Basketball', 'Chaussures montantes avec soutien de la cheville.'),
(5, 'Trail', 'Chaussures robustes pour les sentiers et terrains accidentés.'),
(6, 'Tennis', 'Chaussures stables et latéralement renforcées pour le tennis.');

-- --------------------------------------------------------

--
-- Structure de la table `chaussures`
--

CREATE TABLE `chaussures` (
  `id` int(11) NOT NULL,
  `categorie_id` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `marque` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `chaussures`
--

INSERT INTO `chaussures` (`id`, `categorie_id`, `nom`, `prix`, `marque`, `description`, `created_at`) VALUES
(1, 2, 'Air Max 90', '120.00', 'Nike', 'Sneaker iconique avec amorti Air visible.', '2026-03-16 14:35:15'),
(2, 2, 'Stan Smith', '89.99', 'Adidas', 'Classique en cuir blanc avec détails verts.', '2026-03-16 14:35:15'),
(3, 3, 'Old Skool', '75.00', 'Vans', 'Chaussure skate avec bande latérale signature.', '2026-03-16 14:35:15'),
(4, 4, 'Chuck Taylor All Star', '65.00', 'Converse', 'Toile montante, semelle caoutchouc.', '2026-03-16 14:35:15'),
(5, 1, 'Runner R100', '109.90', 'Puma', 'Running légère avec semelle EVA.', '2026-03-16 14:35:15'),
(6, 1, 'Ultraboost 22', '180.00', 'Adidas', 'Running haut de gamme avec technologie Boost.', '2026-03-16 14:35:15'),
(7, 2, 'Air Force 1', '100.00', 'Nike', 'Basket low en cuir, style urbain classique.', '2026-03-16 14:35:15'),
(8, 1, 'Gel-Nimbus 24', '160.00', 'Asics', 'Running longue distance, amorti maximal.', '2026-03-16 14:35:15'),
(9, 5, 'Speedcross 5', '130.00', 'Salomon', 'Trail running avec crampons agressifs.', '2026-03-16 14:35:15'),
(10, 6, 'Barricade 13', '140.00', 'Adidas', 'Tennis compétition, stabilité maximale.', '2026-03-16 14:35:15'),
(11, 2, 'Forum Low', '95.00', 'Adidas', 'Sneaker rétro basketball avec sangle velcro.', '2026-03-24 08:46:59');

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `montant` decimal(10,2) NOT NULL DEFAULT 0.00,
  `date_commande` datetime NOT NULL DEFAULT current_timestamp(),
  `statut` enum('en_attente','confirmee','expediee','livree','annulee') NOT NULL DEFAULT 'en_attente',
  `shipping_nom` varchar(100) NOT NULL DEFAULT '',
  `shipping_prenom` varchar(100) NOT NULL DEFAULT '',
  `shipping_adresse` text DEFAULT NULL,
  `shipping_npa` int(4) NOT NULL,
  `shipping_ville` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id`, `user_id`, `montant`, `date_commande`, `statut`, `shipping_nom`, `shipping_prenom`, `shipping_adresse`, `shipping_npa`, `shipping_ville`) VALUES
(1, 1, '209.99', '2024-11-10 10:32:00', 'livree', '', '', NULL, 0, ''),
(2, 2, '180.00', '2024-12-01 14:15:00', 'expediee', '', '', NULL, 0, ''),
(4, 1, '100.00', '2025-02-14 18:45:00', 'livree', '', '', NULL, 0, ''),
(6, 5, '89.99', '2025-03-05 16:30:00', 'en_attente', '', '', NULL, 0, ''),
(20, 6, '280.00', '2026-03-23 15:30:24', 'expediee', 'Tido', 'Sam', 'Chemin du Daru 7', 1128, 'Paris'),
(21, 11, '120.00', '2026-03-25 15:11:17', 'en_attente', 'a', 'a', 'a', 1128, 'f'),
(22, 32, '120.00', '2026-03-30 11:09:27', 'en_attente', 'Tido Kaze', 'Samuel', 'dedfrfr', 1222, 'Paris'),
(23, 33, '339.90', '2026-03-30 11:11:28', 'confirmee', 'Bassim', 'ALLAWAKBAR', 'wefrfre', 1212, 'Paris');

-- --------------------------------------------------------

--
-- Structure de la table `commande_items`
--

CREATE TABLE `commande_items` (
  `id` int(11) NOT NULL,
  `commande_id` int(11) NOT NULL,
  `chaussure_id` int(11) NOT NULL,
  `taille` decimal(4,1) NOT NULL,
  `quantite` int(11) NOT NULL DEFAULT 1,
  `prix` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `commande_items`
--

INSERT INTO `commande_items` (`id`, `commande_id`, `chaussure_id`, `taille`, `quantite`, `prix`) VALUES
(1, 1, 1, '40.0', 1, 120),
(2, 1, 2, '39.0', 1, 89.99),
(3, 2, 6, '42.0', 1, 180),
(5, 4, 7, '40.0', 1, 100),
(9, 6, 2, '40.0', 1, 89.99),
(13, 20, 10, '42.0', 2, 140),
(14, 21, 1, '42.0', 1, 120),
(15, 22, 1, '43.0', 1, 120),
(16, 23, 4, '38.0', 2, 65),
(17, 23, 5, '44.0', 1, 109.9),
(18, 23, 7, '39.0', 1, 100);

-- --------------------------------------------------------

--
-- Structure de la table `taille_chaussure`
--

CREATE TABLE `taille_chaussure` (
  `id` int(11) NOT NULL,
  `chaussure_id` int(11) NOT NULL,
  `taille` decimal(4,1) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `taille_chaussure`
--

INSERT INTO `taille_chaussure` (`id`, `chaussure_id`, `taille`, `stock`) VALUES
(1, 1, '39.0', 5),
(2, 1, '40.0', 8),
(3, 1, '41.0', 10),
(4, 1, '42.0', 7),
(5, 1, '43.0', 4),
(6, 1, '44.0', 3),
(7, 2, '38.0', 6),
(8, 2, '39.0', 9),
(9, 2, '40.0', 11),
(10, 2, '41.0', 8),
(11, 2, '42.0', 5),
(12, 3, '37.0', 4),
(13, 3, '38.0', 7),
(14, 3, '39.0', 9),
(15, 3, '40.0', 6),
(16, 3, '41.0', 3),
(17, 4, '36.0', 5),
(18, 4, '37.0', 8),
(19, 4, '38.0', 10),
(20, 4, '39.0', 7),
(21, 4, '40.0', 4),
(22, 5, '40.0', 6),
(23, 5, '41.0', 9),
(24, 5, '42.0', 8),
(25, 5, '43.0', 5),
(26, 5, '44.0', 2),
(27, 6, '40.0', 3),
(28, 6, '41.0', 5),
(29, 6, '42.0', 7),
(30, 6, '43.0', 4),
(31, 7, '39.0', 8),
(32, 7, '40.0', 10),
(33, 7, '41.0', 9),
(34, 7, '42.0', 6),
(35, 7, '43.0', 3),
(36, 8, '40.0', 4),
(37, 8, '41.0', 6),
(38, 8, '42.0', 5),
(39, 8, '43.0', 3),
(40, 8, '44.0', 2),
(41, 9, '39.0', 4),
(42, 9, '40.0', 6),
(43, 9, '41.0', 8),
(44, 9, '42.0', 5),
(45, 9, '43.0', 3),
(46, 10, '39.0', 3),
(47, 10, '40.0', 5),
(48, 10, '41.0', 7),
(49, 10, '42.0', 4),
(50, 10, '43.0', 2),
(51, 11, '39.0', 0),
(52, 11, '40.0', 0),
(53, 11, '41.0', 0),
(54, 11, '42.0', 0),
(55, 11, '43.0', 0);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `role` enum('utilisateur','admin') NOT NULL DEFAULT 'utilisateur',
  `adresse` text DEFAULT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `prenom`, `email`, `role`, `adresse`, `mot_de_passe`, `created_at`) VALUES
(1, 'Dupont', 'Alice', 'alice.dupont@email.com', 'utilisateur', '12 rue des Lilas, Paris', '$2y$10$abc123hashedpassword1', '2026-03-16 14:35:15'),
(2, 'Martin', 'Bruno', 'bruno.martin@email.com', 'utilisateur', '5 avenue Foch, Lyon', '$2y$10$abc123hashedpassword2', '2026-03-16 14:35:15'),
(5, 'Petit', 'Emma', 'emma.petit@email.com', 'utilisateur', '21 rue du Moulin, Lille', '$2y$10$abc123hashedpassword5', '2026-03-16 14:35:15'),
(6, 'Tido', 'Sam', 'sam@gmail.com', 'utilisateur', 'Chemin du Daru 7', '$2y$12$jyq1CKUXTFFVWacSjxLQROMGnf19YmwtljjPVbZ00IcoVjtU3eh8q', '2026-03-17 16:01:17'),
(7, 'Poritin', 'Theo', 'theo@gmail.com', 'utilisateur', 'Chemin d Daesh 97', '$2y$12$lBbEPJ5TZlm45SWTLn7gdeF.d0v/YSCpGeKbEHOIveDf/fhksQSba', '2026-03-17 16:02:12'),
(8, 'Mgrg', 'grgrg', 'gregre@gmail.com', 'utilisateur', 'gregregrg', '$2y$12$eIE62ZWICSkIvI72gES/d.a7QoiRYlwzGrHuYviCI3u78iD8CCQwu', '2026-03-17 16:04:19'),
(9, 'Admin', 'Super', 'admin@admin.com', 'admin', NULL, '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', '2026-03-25 10:26:40'),
(10, 'Admin', 'Super', 'admin@gmail.com', 'admin', '- ', '$2y$12$pxg7n.OqT5LcW.Ovk7cQHuT40Y4yIgVSl.XBfZapZPXTNdVPzaJ6i', '2026-03-25 10:53:06'),
(11, 'a', 'a', 'a@a.a', 'utilisateur', 'a', '$2y$12$GeAKsGDLTpfnDulR1sIEQ.fjPAvrV2xI7OaVYfct8f4hHEtKj5.16', '2026-03-25 15:08:41'),
(12, 'a', 'a', 'api.alice.dupont@email.com', 'utilisateur', 'aaaaaa', '$2y$12$bcpBjvs9SixilE.upNGQAOQYDz0K0eBIvNAhkioWm3wLCbNyo5/JO', '2026-03-25 15:49:46'),
(13, 'Marie', 'Josef', 'marie@gmail.com', 'utilisateur', 'Chemin de Téhéran 93', '$2y$12$hGlx37vVGcsrSOXZNRXtruV9.nV/qc8osHXC9j5yIH2jfG7RYAnMO', '2026-03-30 08:59:09'),
(14, 'Antoine', 'rgreg', 'gffefr@gmail.com', 'utilisateur', 'trhrthth', '$2y$12$cRr0.lCQcLCMo7RCpQnPuOa3raLWsWVGrcbPN5X88C98uv.G2Knoe', '2026-03-30 09:02:53'),
(15, 'FGREG', 'GREGERG', 'gergrg@gmail.com', 'utilisateur', 'efegrwgegdrgre', '$2y$12$SOysGZxC4ryCQ/3n4.L0.O4STWyvaW1xTcelEi0uZ8ppdlCzAz2hK', '2026-03-30 09:06:57'),
(16, 'FGREG', 'GREGERG', 'gerrrrgrg@gmail.com', 'utilisateur', 'efegrwgegdrgre', '$2y$12$V1Qwab0r1mvw/2Y3qymNZ.OfJnnu2Z6RZaGIS9NymtnW9F06b38BK', '2026-03-30 09:13:45'),
(17, 'grgrgr', 'greghzj', 'olukuju@gmail.com', 'utilisateur', 'trhrth667', '$2y$12$9Oc/gumA6IjFho3H/BijO.cCpLGeBgR5RMw.TOvZ6aj9Y75JtOQ1C', '2026-03-30 09:16:54'),
(18, 'grgrgr', 'greghzj', 'olukujmmuju@gmail.com', 'utilisateur', 'trhrth667', '$2y$12$GTo9IoFXAojYvUt4cveo1eu9frXoJ1e3EqkyA1daPB2fUQvJFdi1G', '2026-03-30 09:20:43'),
(19, 'rfegtgthhth', 'greghzj', 'olukujmmujththu@gmail.com', 'utilisateur', 'trhrth667', '$2y$12$koG5VUbg5Mp0w/a1zlUguuYaUD2JWoBwZWqV2DBIZmXHfZScvQyuq', '2026-03-30 09:23:32'),
(20, 'rgrgregreg', 'juju', 'rgrgrth@gmail.com', 'utilisateur', 'dtrjzjzj7989', '$2y$12$.YW622pSaFzSJ2n90gdlae0.Nvaewv3BcfAVyeoZZ6Tlm8lRpbrbK', '2026-03-30 09:24:57'),
(21, 'gthtzjuk', 'hjhtr', 'ewfw@gmail.com', 'utilisateur', 'grgreg56', '$2y$12$jvd44DPUzaKQ9JYlGQSGg.lAeX5mhz/RdLBp2lKhxYjRhTJ/y2AUu', '2026-03-30 09:25:50'),
(28, 'fwfefwef', 'fregegr', 'ewfewf@gmail.com', 'utilisateur', 'frf', '$2y$12$tLDym6DTEMzf5c3vr3X0POnN9KzMDFdUY7ZMurNQfcVMK07tZnxKe', '2026-03-30 10:37:42'),
(31, 'Tido', 'Sam', 'samtido6@gmail.com', 'utilisateur', 'EDEWFRG', '$2y$12$aTlk8Uzmu.SuE6tBYHZeFOmiykNJd8JRME3oO4qejxN.WnE3eHkGe', '2026-03-30 10:48:11'),
(32, 'Tido Kaze', 'Samuel', 'sammonkeyd0@gmail.com', 'utilisateur', 'dedfrfr', '$2y$12$eFNyGnTwGZcAlxz0gxEeWOcmSKwD8.n8zpz1jFTMLF5Rpqhg.DjOm', '2026-03-30 10:51:30'),
(33, 'Bassim', 'ALLAWAKBAR', 'wassim.ddch@eduge.ch', 'utilisateur', 'wefrfre', '$2y$12$HrVQhc9dfMEzgHe0q8Uj8OKdWUEW0nXxx.y19/VFksafU9SYgATje', '2026-03-30 10:57:31');

-- --------------------------------------------------------

--
-- Structure de la table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `chaussure_id` int(11) NOT NULL,
  `date_ajout` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `chaussure_id`, `date_ajout`) VALUES
(1, 1, 6, '2025-01-15 08:00:00'),
(2, 1, 8, '2025-02-01 12:30:00'),
(3, 2, 1, '2025-01-20 17:00:00'),
(6, 5, 3, '2025-03-01 14:45:00'),
(7, 5, 5, '2025-03-03 11:20:00'),
(43, 6, 11, '2026-03-25 15:46:08');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `chaussures`
--
ALTER TABLE `chaussures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_chaussure_categorie` (`categorie_id`);

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_commande_user` (`user_id`);

--
-- Index pour la table `commande_items`
--
ALTER TABLE `commande_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_item_commande` (`commande_id`),
  ADD KEY `fk_item_chaussure` (`chaussure_id`);

--
-- Index pour la table `taille_chaussure`
--
ALTER TABLE `taille_chaussure`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_taille_chaussure` (`chaussure_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_wishlist` (`user_id`,`chaussure_id`),
  ADD KEY `fk_wishlist_chaussure` (`chaussure_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `chaussures`
--
ALTER TABLE `chaussures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT pour la table `commande_items`
--
ALTER TABLE `commande_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `taille_chaussure`
--
ALTER TABLE `taille_chaussure`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT pour la table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `chaussures`
--
ALTER TABLE `chaussures`
  ADD CONSTRAINT `fk_chaussure_categorie` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `fk_commande_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `commande_items`
--
ALTER TABLE `commande_items`
  ADD CONSTRAINT `fk_item_chaussure` FOREIGN KEY (`chaussure_id`) REFERENCES `chaussures` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_item_commande` FOREIGN KEY (`commande_id`) REFERENCES `commandes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `taille_chaussure`
--
ALTER TABLE `taille_chaussure`
  ADD CONSTRAINT `fk_taille_chaussure` FOREIGN KEY (`chaussure_id`) REFERENCES `chaussures` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `fk_wishlist_chaussure` FOREIGN KEY (`chaussure_id`) REFERENCES `chaussures` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
