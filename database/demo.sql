-- =============================================================
-- Newstylesafi — données de démonstration (optionnel)
-- À importer APRÈS schema.sql.
-- Supprimer ce fichier du serveur après la mise en production.
--
-- Les produits ci-dessous sont des EXEMPLES : noms, prix, tailles et
-- couleurs sont là pour montrer la boutique. Remplacez-les par les
-- vraies pièces et photos fournies par le propriétaire avant la mise
-- en ligne.
-- =============================================================

SET NAMES utf8mb4;

DELETE FROM `product_images`;
DELETE FROM `products`;
DELETE FROM `categories`;
DELETE FROM `promo_codes`;

ALTER TABLE `categories` AUTO_INCREMENT = 1;
ALTER TABLE `products` AUTO_INCREMENT = 1;
ALTER TABLE `product_images` AUTO_INCREMENT = 1;
ALTER TABLE `promo_codes` AUTO_INCREMENT = 1;

-- ----------------------------
-- Catégories (homme)
-- ----------------------------
INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `sort_order`, `is_active`) VALUES
  (1, 'T-shirts', 'tshirts', 'tshirt', 1, 1),
  (2, 'Chemises', 'chemises', 'shirt', 2, 1),
  (3, 'Pantalons', 'pantalons', 'pants', 3, 1),
  (4, 'Vestes & Manteaux', 'vestes', 'jacket', 4, 1),
  (5, 'Ensembles', 'ensembles', 'shirt', 5, 1),
  (6, 'Accessoires', 'accessoires', 'tag', 6, 1);

-- ----------------------------
-- Produits (exemples)
-- ----------------------------
INSERT INTO `products`
(`id`, `category_id`, `title`, `slug`, `description`, `price`, `sale_price`, `rating`, `stock`,
 `sizes`, `colors`, `is_flash`, `is_featured`, `is_active`) VALUES
(1, 1, 'T-shirt coton « Essentiel »', 't-shirt-coton-essentiel',
 'T-shirt en coton, col rond et coupe droite. Une base simple à porter tous les jours.',
 99.00, NULL, 5.0, 30,
 '["S","M","L","XL","XXL"]',
 '[{"name":"Noir","hex":"#080808"},{"name":"Blanc","hex":"#FFFFFF"},{"name":"Gris","hex":"#6E6E6E"}]',
 0, 1, 1),

(2, 1, 'T-shirt oversize « Atlas »', 't-shirt-oversize-atlas',
 'T-shirt à coupe ample, épaules tombantes et finitions renforcées. Se porte seul ou sous une veste.',
 129.00, 99.00, 5.0, 18,
 '["S","M","L","XL"]',
 '[{"name":"Noir","hex":"#080808"},{"name":"Jaune","hex":"#FFD400"}]',
 1, 0, 1),

(3, 2, 'Chemise oxford « Classic »', 'chemise-oxford-classic',
 'Chemise en oxford, col boutonné et coupe ajustée. À porter au bureau comme en sortie.',
 229.00, 189.00, 5.0, 12,
 '["S","M","L","XL"]',
 '[{"name":"Blanc","hex":"#FFFFFF"},{"name":"Bleu","hex":"#1E2A44"}]',
 0, 1, 1),

(4, 2, 'Chemise en lin « Safi »', 'chemise-lin-safi',
 'Chemise en lin léger, col ouvert et manches longues retroussables. Confortable par temps chaud.',
 249.00, NULL, 5.0, 10,
 '["S","M","L","XL"]',
 '[{"name":"Beige","hex":"#D8C7A8"},{"name":"Blanc","hex":"#FFFFFF"}]',
 0, 1, 1),

(5, 3, 'Pantalon chino « Medina »', 'pantalon-chino-medina',
 'Chino à jambe droite, taille normale et poches latérales. Une coupe propre au quotidien.',
 249.00, 199.00, 5.0, 15,
 '["28","30","32","34","36"]',
 '[{"name":"Noir","hex":"#080808"},{"name":"Beige","hex":"#D8C7A8"},{"name":"Kaki","hex":"#4B5138"}]',
 0, 1, 1),

(6, 3, 'Pantalon cargo « Nomade »', 'pantalon-cargo-nomade',
 'Pantalon cargo avec poches utilitaires et chevilles resserrées. Solide et pratique.',
 299.00, NULL, 5.0, 8,
 '["28","30","32","34","36"]',
 '[{"name":"Noir","hex":"#080808"},{"name":"Kaki","hex":"#4B5138"}]',
 0, 0, 1),

(7, 4, 'Veste en jean « Indigo »', 'veste-jean-indigo',
 'Veste en jean, fermeture boutonnée et poches poitrine. La pièce de mi-saison.',
 399.00, NULL, 5.0, 6,
 '["S","M","L","XL"]',
 '[{"name":"Bleu","hex":"#1E2A44"},{"name":"Noir","hex":"#080808"}]',
 0, 1, 1),

(8, 4, 'Blouson « Nuit »', 'blouson-nuit',
 'Blouson léger à col montant, doublure simple et zip central. Facile à associer.',
 449.00, 379.00, 5.0, 4,
 '["S","M","L","XL"]',
 '[{"name":"Noir","hex":"#080808"}]',
 1, 0, 1),

(9, 5, 'Ensemble chemise + pantalon « Signature »', 'ensemble-chemise-pantalon-signature',
 'Ensemble deux pièces : chemise et pantalon assortis, même matière et même teinte.',
 499.00, NULL, 5.0, 5,
 '["S","M","L","XL"]',
 '[{"name":"Noir","hex":"#080808"},{"name":"Beige","hex":"#D8C7A8"}]',
 0, 1, 1),

(10, 5, 'Ensemble polo + short « Été »', 'ensemble-polo-short-ete',
 'Ensemble polo et short en coton léger. Pensé pour les journées chaudes.',
 299.00, 249.00, 5.0, 9,
 '["S","M","L","XL"]',
 '[{"name":"Blanc","hex":"#FFFFFF"},{"name":"Jaune","hex":"#FFD400"}]',
 0, 0, 1),

(11, 6, 'Casquette « Newstylesafi »', 'casquette-newstylesafi',
 'Casquette à visière courbée, taille réglable à l’arrière.',
 89.00, NULL, 5.0, 25,
 '["Taille unique"]',
 '[{"name":"Noir","hex":"#080808"},{"name":"Jaune","hex":"#FFD400"}]',
 0, 0, 1),

(12, 6, 'Ceinture cuir « Safi »', 'ceinture-cuir-safi',
 'Ceinture en cuir à boucle métal, ajustable.',
 129.00, NULL, 5.0, 14,
 '["Taille unique"]',
 '[{"name":"Noir","hex":"#080808"},{"name":"Marron","hex":"#6B4A2F"}]',
 0, 0, 1);

-- ----------------------------
-- Images (placeholders générés, remplacés via l'admin)
-- ----------------------------
INSERT INTO `product_images` (`product_id`, `image_path`, `sort_order`, `is_primary`) VALUES
  (1, 'assets/images/placeholders/t-shirt-coton-essentiel.svg', 1, 1),
  (1, 'assets/images/placeholders/t-shirt-coton-essentiel-2.svg', 2, 0),
  (2, 'assets/images/placeholders/t-shirt-oversize-atlas.svg', 1, 1),
  (2, 'assets/images/placeholders/t-shirt-oversize-atlas-2.svg', 2, 0),
  (3, 'assets/images/placeholders/chemise-oxford-classic.svg', 1, 1),
  (3, 'assets/images/placeholders/chemise-oxford-classic-2.svg', 2, 0),
  (4, 'assets/images/placeholders/chemise-lin-safi.svg', 1, 1),
  (4, 'assets/images/placeholders/chemise-lin-safi-2.svg', 2, 0),
  (5, 'assets/images/placeholders/pantalon-chino-medina.svg', 1, 1),
  (5, 'assets/images/placeholders/pantalon-chino-medina-2.svg', 2, 0),
  (6, 'assets/images/placeholders/pantalon-cargo-nomade.svg', 1, 1),
  (6, 'assets/images/placeholders/pantalon-cargo-nomade-2.svg', 2, 0),
  (7, 'assets/images/placeholders/veste-jean-indigo.svg', 1, 1),
  (7, 'assets/images/placeholders/veste-jean-indigo-2.svg', 2, 0),
  (8, 'assets/images/placeholders/blouson-nuit.svg', 1, 1),
  (8, 'assets/images/placeholders/blouson-nuit-2.svg', 2, 0),
  (9, 'assets/images/placeholders/ensemble-chemise-pantalon-signature.svg', 1, 1),
  (9, 'assets/images/placeholders/ensemble-chemise-pantalon-signature-2.svg', 2, 0),
  (10, 'assets/images/placeholders/ensemble-polo-short-ete.svg', 1, 1),
  (10, 'assets/images/placeholders/ensemble-polo-short-ete-2.svg', 2, 0),
  (11, 'assets/images/placeholders/casquette-newstylesafi.svg', 1, 1),
  (11, 'assets/images/placeholders/casquette-newstylesafi-2.svg', 2, 0),
  (12, 'assets/images/placeholders/ceinture-cuir-safi.svg', 1, 1),
  (12, 'assets/images/placeholders/ceinture-cuir-safi-2.svg', 2, 0);

-- ----------------------------
-- Code promo de bienvenue
-- ----------------------------
INSERT INTO `promo_codes`
(`code`, `discount_type`, `discount_value`, `min_order`, `max_uses`, `used_count`, `is_active`, `expires_at`) VALUES
  ('BIENVENUE', 'percent', 10.00, 200.00, 100, 0, 1, '2027-12-31 23:59:59');
