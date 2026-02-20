-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 18 fév. 2026 à 05:55
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `vite_gourmand`
--

-- --------------------------------------------------------

--
-- Structure de la table `diets`
--

CREATE TABLE `diets` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `diets`
--

INSERT INTO `diets` (`id`, `name`) VALUES
(1, 'Classique'),
(2, 'Végétarien'),
(3, 'Vegan');

-- --------------------------------------------------------

--
-- Structure de la table `menus`
--

CREATE TABLE `menus` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `starter` varchar(255) DEFAULT NULL,
  `main_course` varchar(255) DEFAULT NULL,
  `dessert` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `min_people` int(11) NOT NULL,
  `remaining_quantity` int(11) NOT NULL,
  `conditions` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `theme_id` int(11) NOT NULL,
  `diet_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `allergens` text DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `menus`
--

INSERT INTO `menus` (`id`, `title`, `description`, `starter`, `main_course`, `dessert`, `price`, `min_people`, `remaining_quantity`, `conditions`, `image`, `theme_id`, `diet_id`, `created_at`, `allergens`, `is_active`) VALUES
(1, 'Menu Classic', 'Menu traditionnel pour tous types d’événements \r\n\r\n\r\n\r\n', 'Salade composée de crudités crevettes et sauce vinaigre', 'Cotes d\'agneau à la sauce mysterieuse secret red', 'Crème brulée', 15.00, 10, 71, 'Commander 48h avant', 'classic_1.jpg,classic_2.jpg,classic_3.jpg', 1, 1, '2026-02-07 01:24:12', 'Présence de gluten', NULL),
(3, 'Menu Vegan', 'Menu pour tous types d’événements', 'salade crudités œufs pomme de terre sautés', 'steak vegan champignons haricots verts épinards sauce verte', 'cheesecake ', 19.00, 8, 50, 'commander minimum 48 heures avant', 'vegan_1.jpg,vegan_2.jpg,vegan_3.jpg', 7, 3, '2026-02-15 13:59:55', 'Peut contenir des traces de soja ', NULL),
(4, 'Menu Mariage ', 'Menu traditionnel pour mariage ou tous événements de votre choix', 'Salade mozzarella aux tomates et sauce olives', 'Purée avec un demi poulet, carottes et tomates grillé', 'Fondant au chocolat', 20.00, 15, 40, 'A consommer de préférence le jour même.\r\nA stocker a l\'abri de la chaleur et du soleil', 'mariage_1.jpg,mariage_2.jpg,mariage_3.jpg', 1, 1, '2026-02-15 14:20:01', 'Contient du Gluten !', NULL),
(5, 'Menu Pâques en famille', 'Menu conseillé pour fêter pâques', 'Œufs mimosa marbrés violets', 'Souris d’agneau confite et sa secret-sauce ', 'Carrot cake – Gâteau de carotte', 30.00, 10, 59, 'Ne pas dépasser 2 jours après livraison pour sa consommation !', 'paques_1.jpg,paques_2.jpg,paques_3.jpg', 6, 1, '2026-02-15 14:29:08', 'Peut contenir soja et fruits a coques ', NULL),
(6, 'Menu Noël Signature', 'Menu conseillé pour fêter Noël en convivialité ', 'Cuillère au Foie Gras mi-cuit et son chutney d\'oignons', 'Filet mignon de veau au romarin', 'buche de noel', 25.00, 20, 49, 'Ne pas exposer a une température ne dépassant pas 25°C', 'noel_1.jpg,noel_2.jpg,noel_3.jpg', 5, 1, '2026-02-15 15:17:46', 'Amidon , soja , fruits a coques', NULL),
(7, 'Menu Père Noël', 'Menus traditionnel de noël', 'Magret de canard fumé au mousseux de foie gras', 'Dinde de Noël farcie aux marrons', 'Kouglof glacé de Noël', 30.00, 25, 99, 'A consommer de préférence juste après livraison', 'perenoel_1.jpg,perenoel_2.jpg,perenoel_3.jpg', 5, 1, '2026-02-15 15:24:11', 'Contient fruits à coques', NULL),
(9, 'Menu Bio', 'Menu 100% produits bio pour divers évènements', 'salade de légumes crus ', 'Pommes de terre gingembre et piment a la sauce tomate', 'Salades de fruits', 15.00, 10, 40, 'Plat qui peut être consommé en 3 jours max', 'bio_1.jpg,bio_2.jpg,bio_3.jpg', 7, 2, '2026-02-15 15:43:30', 'Contient soja', NULL),
(10, 'Menu Grillade', 'Divers viandes au barbecue pour toutes occasions', 'Salades de pâtes au cèleri et tomates a la sauce secret blanc ', 'Viandes : cotes , escalopes de veau ,escalopes de poulet, brochettes de poulet , Merguez', 'Macarons', 35.00, 25, 99, NULL, 'grillade_1.jpg,grillade_2.jpg,grillade_3.jpg', 7, 1, '2026-02-15 16:08:28', NULL, NULL),
(11, 'Menu Fisher', 'Menus adapté a tout type d’évènement  ', 'Salade omelette bacon fromage tomates', 'Un saumon entier avec galette et sauce aux légumes frits', 'cupcakes ', 40.00, 15, 49, 'A consommer le jour même ', 'fisher_1.jpg,fisher_2.jpg,fisher_3.jpg', 7, 1, '2026-02-15 16:21:49', 'Contient lactose', NULL),
(12, 'Menu Ailé', 'Menu pour tout évènement ', 'salade composée de de petits morceaux d\'escalope de poulet, de bacon , de pain maison le tout recouvert de gruyère et une sauce blanche ', 'plateau d\'ailes de poulet accompagné de courgette, et pommes de terre frites ainsi qu\'une sauce mystère ', 'tiramisu ', 26.00, 10, 50, NULL, 'ailé_1.jpg,ailé_2.jpg,ailé_3.jpg', 7, 1, '2026-02-15 16:37:38', 'soja', NULL),
(13, 'Menu Saisonnier', 'Menu convient pour tout les évènements', 'salade composée de divers légumes et fruits des bois recouverte de fromage blanc ', 'un morceau de saumon assaisonné  frit accompagné d\'une sauce , carottes, choux fleurs et pommes de terre', 'Paris brest', 25.00, 8, 49, NULL, 'saisonnier_1.jpg,saisonnier_2.jpg,saisonnier_3.jpg', 7, 1, '2026-02-15 19:21:17', NULL, NULL),
(14, 'Menu buffets', 'Pièces et cocktails pour vos Anniversaire , baptême etc...', ' - PLATEAU DE FROMAGES DÉCOUPES AFFINES\r\n - SALADE DE RAVIOLES, CHORIZO, PETITS POIS (1KG)\r\n - SALADE FARFALLES, POULET, AMANDES,POMME ROUGE (1KG)\r\n\r\n- Et autres sur demande(rubrique contact)', ' - PLATEAU DE 20 MINI BURGERS \r\n - PLATEAU MINI PIZZAS VÉGÉTARIENNES 20 PIÈCES\r\n - PLATEAU MINI QUICHES 20 PIÈCES\r\n - CROUSTILLANTS DE GAMBAS ET SAUCE THAÏ (20 PIÈCES)\r\n\r\n- Et autres sur demande(rubrique contact)', ' - TOUS LES DESSERT VU DANS NOS MENUS\r\n\r\n\r\n\r\n- Et autres sur demande(rubrique contact)', 40.00, 30, 94, 'Pour les plats froids a conserver au frais ', 'buffet_1.jpg,buffet_2.jpg,buffet_3.jpg', 2, 1, '2026-02-15 20:18:50', 'Pour ce menu dites nous vos allergènes et nous obéissons !\r\n\r\nNe pas oublier (rubrique contact)', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `opening_hours`
--

CREATE TABLE `opening_hours` (
  `id` int(11) NOT NULL,
  `day_name` varchar(20) NOT NULL,
  `open_time` time DEFAULT '09:00:00',
  `close_time` time DEFAULT '18:00:00',
  `is_closed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `opening_hours`
--

INSERT INTO `opening_hours` (`id`, `day_name`, `open_time`, `close_time`, `is_closed`) VALUES
(1, 'Lundi', '09:00:00', '18:00:00', 0),
(2, 'Mardi', '09:00:00', '18:00:00', 0),
(3, 'Mercredi', '09:00:00', '18:00:00', 0),
(4, 'Jeudi', '09:00:00', '18:00:00', 0),
(5, 'Vendredi', '09:00:00', '18:00:00', 0),
(6, 'Samedi', '09:00:00', '18:00:00', 0),
(7, 'Dimanche', '09:00:00', '18:00:00', 0);

-- --------------------------------------------------------

--
-- Structure de la table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `order_status` varchar(50) NOT NULL,
  `number_people` int(11) NOT NULL,
  `equipment_ready` tinyint(1) DEFAULT 0,
  `user_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `delivery_address` varchar(255) NOT NULL,
  `delivery_date` date NOT NULL DEFAULT current_timestamp(),
  `delivery_time` time NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `contact_method` enum('GSM','Email') DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `is_modified_by_client` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `order_date`, `order_status`, `number_people`, `equipment_ready`, `user_id`, `menu_id`, `delivery_address`, `delivery_date`, `delivery_time`, `total_price`, `cancellation_reason`, `contact_method`, `rating`, `comment`, `is_modified_by_client`) VALUES
(18, 'ORD-6993D3A1D9024', '2026-02-17 03:34:09', 'finished', 8, 0, 14, 13, '5 rue de la viennoiserie (Bordeaux)', '2026-02-26', '12:00:00', 200.00, NULL, NULL, NULL, NULL, 0),
(19, 'ORD-6993D3D4A4E9A', '2026-02-17 03:35:00', 'finished', 20, 0, 14, 6, '5 rue de la viennoiserie (Bordeaux)', '2026-02-28', '12:00:00', 500.00, NULL, NULL, NULL, NULL, 0),
(20, 'ORD-6993D410DD7A7', '2026-02-17 03:36:00', 'finished', 10, 0, 14, 5, '5 rue de la viennoiserie (Bordeaux)', '2026-04-16', '12:00:00', 300.00, NULL, NULL, NULL, NULL, 0),
(21, 'ORD-6994B637B4903', '2026-02-17 19:40:55', 'finished', 25, 0, 14, 10, '5 rue de la viennoiserie (Bordeaux)', '2026-05-30', '12:00:00', 875.00, NULL, NULL, NULL, NULL, 0),
(22, 'ORD-6995233BE4910', '2026-02-18 03:26:03', 'finished', 10, 1, 14, 9, '5 rue de la viennoiserie (Bordeaux)', '2026-02-23', '12:00:00', 150.00, NULL, NULL, NULL, NULL, 0),
(23, 'ORD-699537DC055B1', '2026-02-18 04:54:04', 'finished', 15, 0, 14, 11, '5 rue de la viennoiserie (Bordeaux)', '2026-02-27', '12:00:00', 600.00, NULL, NULL, NULL, NULL, 0),
(24, 'ORD-69953E6E8054E', '2026-02-18 05:22:06', 'pending', 25, 1, 14, 7, '5 rue de la patisserie (Bordeaux)', '2026-04-12', '14:00:00', 750.00, NULL, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Structure de la table `order_history`
--

CREATE TABLE `order_history` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reviews`
--

INSERT INTO `reviews` (`id`, `order_id`, `user_id`, `rating`, `comment`, `is_published`, `created_at`) VALUES
(6, 18, 14, 5, 'Super prestation merci vite et gourmand pour votre professionnalisme ', 1, '2026-02-17 02:43:22'),
(7, 19, 14, 4, 'Plats delicieux juste un peu de retard pour la livraison sinon imppecable je recommande ', 1, '2026-02-17 02:45:20'),
(8, 20, 14, 5, 'Excellent repas pour paques nous sommes tres satisfaits\r\nbravo a  vous', 1, '2026-02-17 02:47:17');

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `label` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `name`, `label`) VALUES
(1, 'admin', 'Administrateur'),
(2, 'employee', 'Employé'),
(3, 'utilisateur', 'Utilisateur');

-- --------------------------------------------------------

--
-- Structure de la table `themes`
--

CREATE TABLE `themes` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `themes`
--

INSERT INTO `themes` (`id`, `name`) VALUES
(1, 'Mariage'),
(2, 'Anniversaire'),
(3, 'Entreprise'),
(5, 'Noel'),
(6, 'Paques'),
(7, 'Tout évènement');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `zip_code` varchar(10) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `role_id` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `address`, `city`, `zip_code`, `phone`, `email`, `password`, `created_at`, `role_id`, `is_active`, `reset_token`, `reset_expires_at`) VALUES
(9, 'José', 'Admin', '', '', '', '', 'jose@vitegourmand.fr', '$2y$12$NbxpKz7qMuQZoZjaK4QnresVgk18AQ3yjkvHPY44lIYIzDyLf2Ug6', '2026-02-12 21:14:49', 1, 1, NULL, NULL),
(11, 'Julie', 'Livreuse', '', '', '', '', 'julie@vitegourmand.fr', '$2y$12$28A.dFFnOiWdEChyn02HfeFxKGrjyH6PKh1qVsfwJzap3/2pNfJc6', '2026-02-12 21:45:45', 2, 1, NULL, NULL),
(14, 'Juliette', 'Josette', '5 rue de la viennoiserie', 'Bordeaux', '33000', '0512003400', 'jujudu33@email.fr', '$2y$10$KZ/NeW36r62wm2lK3TcceuwvFv4sUq6/Oyz1nN2IigNAkFT7OjsV6', '2026-02-13 00:28:05', 3, 1, NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `diets`
--
ALTER TABLE `diets`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `theme_id` (`theme_id`),
  ADD KEY `diet_id` (`diet_id`);

--
-- Index pour la table `opening_hours`
--
ALTER TABLE `opening_hours`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Index pour la table `order_history`
--
ALTER TABLE `order_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Index pour la table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `themes`
--
ALTER TABLE `themes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_user_role` (`role_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `diets`
--
ALTER TABLE `diets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `opening_hours`
--
ALTER TABLE `opening_hours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pour la table `order_history`
--
ALTER TABLE `order_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `themes`
--
ALTER TABLE `themes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `menus_ibfk_1` FOREIGN KEY (`theme_id`) REFERENCES `themes` (`id`),
  ADD CONSTRAINT `menus_ibfk_2` FOREIGN KEY (`diet_id`) REFERENCES `diets` (`id`);

--
-- Contraintes pour la table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`);

--
-- Contraintes pour la table `order_history`
--
ALTER TABLE `order_history`
  ADD CONSTRAINT `order_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
