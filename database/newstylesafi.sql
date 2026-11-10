-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 05, 2026 at 11:26 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `newstylesafi`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `icon` varchar(50) DEFAULT NULL COMMENT 'nom d''icône du sprite SVG',
  `sort_order` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'T-shirts', 'tshirts', 'tshirt', 1, 1, '2026-11-10 09:00:00'),
(2, 'Chemises', 'chemises', 'shirt', 2, 1, '2026-11-10 09:00:00'),
(3, 'Pantalons', 'pantalons', 'pants', 3, 1, '2026-11-10 09:00:00'),
(4, 'Vestes & Manteaux', 'vestes', 'jacket', 4, 1, '2026-11-10 09:00:00'),
(5, 'Ensembles', 'ensembles', 'shirt', 5, 1, '2026-11-10 09:00:00'),
(6, 'Accessoires', 'accessoires', 'tag', 6, 1, '2026-11-10 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(280) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `rating` decimal(2,1) NOT NULL DEFAULT 5.0,
  `stock` int(11) NOT NULL DEFAULT 0,
  `sizes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '["S","M","L"]' CHECK (json_valid(`sizes`)),
  `colors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '[{"name":"Noir","hex":"#080808"}]' CHECK (json_valid(`colors`)),
  `is_flash` tinyint(1) NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `title`, `slug`, `description`, `price`, `sale_price`, `rating`, `stock`, `sizes`, `colors`, `is_flash`, `is_featured`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'T-shirt coton « Essentiel »', 't-shirt-coton-essentiel', 'T-shirt en coton, col rond et coupe droite. Une base simple à porter tous les jours.', 99.00, NULL, 5.0, 30, '["S","M","L","XL","XXL"]', '[{"name":"Noir","hex":"#080808"},{"name":"Blanc","hex":"#FFFFFF"},{"name":"Gris","hex":"#6E6E6E"}]', 0, 1, 1, '2026-11-10 09:00:00', '2026-11-10 09:00:00'),
(2, 1, 'T-shirt oversize « Atlas »', 't-shirt-oversize-atlas', 'T-shirt à coupe ample, épaules tombantes et finitions renforcées. Se porte seul ou sous une veste.', 129.00, 99.00, 5.0, 18, '["S","M","L","XL"]', '[{"name":"Noir","hex":"#080808"},{"name":"Jaune","hex":"#FFD400"}]', 1, 0, 1, '2026-11-10 09:00:00', '2026-11-10 09:00:00'),
(3, 2, 'Chemise oxford « Classic »', 'chemise-oxford-classic', 'Chemise en oxford, col boutonné et coupe ajustée. À porter au bureau comme en sortie.', 229.00, 189.00, 5.0, 12, '["S","M","L","XL"]', '[{"name":"Blanc","hex":"#FFFFFF"},{"name":"Bleu","hex":"#1E2A44"}]', 0, 1, 1, '2026-11-10 09:00:00', '2026-11-10 09:00:00'),
(4, 2, 'Chemise en lin « Safi »', 'chemise-lin-safi', 'Chemise en lin léger, col ouvert et manches longues retroussables. Confortable par temps chaud.', 249.00, NULL, 5.0, 10, '["S","M","L","XL"]', '[{"name":"Beige","hex":"#D8C7A8"},{"name":"Blanc","hex":"#FFFFFF"}]', 0, 1, 1, '2026-11-10 09:00:00', '2026-11-10 09:00:00'),
(5, 3, 'Pantalon chino « Medina »', 'pantalon-chino-medina', 'Chino à jambe droite, taille normale et poches latérales. Une coupe propre au quotidien.', 249.00, 199.00, 5.0, 15, '["28","30","32","34","36"]', '[{"name":"Noir","hex":"#080808"},{"name":"Beige","hex":"#D8C7A8"},{"name":"Kaki","hex":"#4B5138"}]', 0, 1, 1, '2026-11-10 09:00:00', '2026-11-10 09:00:00'),
(6, 3, 'Pantalon cargo « Nomade »', 'pantalon-cargo-nomade', 'Pantalon cargo avec poches utilitaires et chevilles resserrées. Solide et pratique.', 299.00, NULL, 5.0, 8, '["28","30","32","34","36"]', '[{"name":"Noir","hex":"#080808"},{"name":"Kaki","hex":"#4B5138"}]', 0, 0, 1, '2026-11-10 09:00:00', '2026-11-10 09:00:00'),
(7, 4, 'Veste en jean « Indigo »', 'veste-jean-indigo', 'Veste en jean, fermeture boutonnée et poches poitrine. La pièce de mi-saison.', 399.00, NULL, 5.0, 6, '["S","M","L","XL"]', '[{"name":"Bleu","hex":"#1E2A44"},{"name":"Noir","hex":"#080808"}]', 0, 1, 1, '2026-11-10 09:00:00', '2026-11-10 09:00:00'),
(8, 4, 'Blouson « Nuit »', 'blouson-nuit', 'Blouson léger à col montant, doublure simple et zip central. Facile à associer.', 449.00, 379.00, 5.0, 4, '["S","M","L","XL"]', '[{"name":"Noir","hex":"#080808"}]', 1, 0, 1, '2026-11-10 09:00:00', '2026-11-10 09:00:00'),
(9, 5, 'Ensemble chemise + pantalon « Signature »', 'ensemble-chemise-pantalon-signature', 'Ensemble deux pièces : chemise et pantalon assortis, même matière et même teinte.', 499.00, NULL, 5.0, 5, '["S","M","L","XL"]', '[{"name":"Noir","hex":"#080808"},{"name":"Beige","hex":"#D8C7A8"}]', 0, 1, 1, '2026-11-10 09:00:00', '2026-11-10 09:00:00'),
(10, 5, 'Ensemble polo + short « Été »', 'ensemble-polo-short-ete', 'Ensemble polo et short en coton léger. Pensé pour les journées chaudes.', 299.00, 249.00, 5.0, 9, '["S","M","L","XL"]', '[{"name":"Blanc","hex":"#FFFFFF"},{"name":"Jaune","hex":"#FFD400"}]', 0, 0, 1, '2026-11-10 09:00:00', '2026-11-10 09:00:00'),
(11, 6, 'Casquette « Newstylesafi »', 'casquette-newstylesafi', 'Casquette à visière courbée, taille réglable à l’arrière.', 89.00, NULL, 5.0, 25, '["Taille unique"]', '[{"name":"Noir","hex":"#080808"},{"name":"Jaune","hex":"#FFD400"}]', 0, 0, 1, '2026-11-10 09:00:00', '2026-11-10 09:00:00'),
(12, 6, 'Ceinture cuir « Safi »', 'ceinture-cuir-safi', 'Ceinture en cuir à boucle métal, ajustable.', 129.00, NULL, 5.0, 14, '["Taille unique"]', '[{"name":"Noir","hex":"#080808"},{"name":"Marron","hex":"#6B4A2F"}]', 0, 0, 1, '2026-11-10 09:00:00', '2026-11-10 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `sort_order`, `is_primary`) VALUES
(1, 1, 'assets/images/placeholders/t-shirt-coton-essentiel.svg', 1, 1),
(2, 1, 'assets/images/placeholders/t-shirt-coton-essentiel-2.svg', 2, 0),
(3, 2, 'assets/images/placeholders/t-shirt-oversize-atlas.svg', 1, 1),
(4, 2, 'assets/images/placeholders/t-shirt-oversize-atlas-2.svg', 2, 0),
(5, 3, 'assets/images/placeholders/chemise-oxford-classic.svg', 1, 1),
(6, 3, 'assets/images/placeholders/chemise-oxford-classic-2.svg', 2, 0),
(7, 4, 'assets/images/placeholders/chemise-lin-safi.svg', 1, 1),
(8, 4, 'assets/images/placeholders/chemise-lin-safi-2.svg', 2, 0),
(9, 5, 'assets/images/placeholders/pantalon-chino-medina.svg', 1, 1),
(10, 5, 'assets/images/placeholders/pantalon-chino-medina-2.svg', 2, 0),
(11, 6, 'assets/images/placeholders/pantalon-cargo-nomade.svg', 1, 1),
(12, 6, 'assets/images/placeholders/pantalon-cargo-nomade-2.svg', 2, 0),
(13, 7, 'assets/images/placeholders/veste-jean-indigo.svg', 1, 1),
(14, 7, 'assets/images/placeholders/veste-jean-indigo-2.svg', 2, 0),
(15, 8, 'assets/images/placeholders/blouson-nuit.svg', 1, 1),
(16, 8, 'assets/images/placeholders/blouson-nuit-2.svg', 2, 0),
(17, 9, 'assets/images/placeholders/ensemble-chemise-pantalon-signature.svg', 1, 1),
(18, 9, 'assets/images/placeholders/ensemble-chemise-pantalon-signature-2.svg', 2, 0),
(19, 10, 'assets/images/placeholders/ensemble-polo-short-ete.svg', 1, 1),
(20, 10, 'assets/images/placeholders/ensemble-polo-short-ete-2.svg', 2, 0),
(21, 11, 'assets/images/placeholders/casquette-newstylesafi.svg', 1, 1),
(22, 11, 'assets/images/placeholders/casquette-newstylesafi-2.svg', 2, 0),
(23, 12, 'assets/images/placeholders/ceinture-cuir-safi.svg', 1, 1),
(24, 12, 'assets/images/placeholders/ceinture-cuir-safi-2.svg', 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `promo_codes`
--

CREATE TABLE `promo_codes` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_type` enum('fixed','percent') NOT NULL DEFAULT 'fixed',
  `discount_value` decimal(10,2) NOT NULL,
  `min_order` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_uses` int(11) DEFAULT NULL,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `promo_codes`
--

INSERT INTO `promo_codes` (`id`, `code`, `discount_type`, `discount_value`, `min_order`, `max_uses`, `used_count`, `is_active`, `expires_at`) VALUES
(1, 'BIENVENUE', 'percent', 10.00, 200.00, 100, 0, 1, '2027-12-31 23:59:59');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'store_name', 'Newstylesafi'),
(2, 'currency', 'DH'),
(3, 'whatsapp_number', '212600000000'),
(4, 'delivery_fee_economy', '25.00'),
(5, 'delivery_fee_express', '45.00'),
(6, 'shipping_eta_economy', '3 à 5 jours ouvrables'),
(7, 'shipping_eta_express', '24 à 48 heures'),
(16, 'delivery_fee', '25.00'),
(17, 'announcement_text', 'Nouvelle collection homme disponible');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_categories_slug` (`slug`),
  ADD KEY `idx_categories_active_sort` (`is_active`,`sort_order`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_products_slug` (`slug`),
  ADD KEY `idx_products_active_flash` (`is_active`,`is_flash`),
  ADD KEY `idx_products_category` (`category_id`),
  ADD KEY `idx_products_created` (`created_at`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_images_product` (`product_id`,`is_primary`);

--
-- Indexes for table `promo_codes`
--
ALTER TABLE `promo_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_promo_code` (`code`),
  ADD KEY `idx_promo_active` (`is_active`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_settings_key` (`setting_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `promo_codes`
--
ALTER TABLE `promo_codes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `fk_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
