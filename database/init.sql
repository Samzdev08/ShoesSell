-- ============================================================
--  Site de vente de chaussures — Schéma de base de données
-- ============================================================
CREATE DATABASE IF NOT EXISTS chaussures_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE chaussures_db;

-- ─────────────────────────────────────────
--  1. USERS
-- ─────────────────────────────────────────
CREATE TABLE users (
    id           INT           NOT NULL AUTO_INCREMENT,
    nom          VARCHAR(100)  NOT NULL,
    prenom       VARCHAR(100)  NOT NULL,
    email        VARCHAR(150)  NOT NULL UNIQUE,
    adresse      TEXT,
    mot_de_passe VARCHAR(255)  NOT NULL,
    created_at   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

-- ─────────────────────────────────────────
--  2. CATEGORIES
-- ─────────────────────────────────────────
CREATE TABLE categories (
    id          INT          NOT NULL AUTO_INCREMENT,
    nom         VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    PRIMARY KEY (id)
);

-- ─────────────────────────────────────────
--  3. CHAUSSURES
-- ─────────────────────────────────────────
CREATE TABLE chaussures (
    id           INT             NOT NULL AUTO_INCREMENT,
    categorie_id INT             NOT NULL,
    nom          VARCHAR(150)    NOT NULL,
    prix         DECIMAL(10, 2)  NOT NULL,
    marque       VARCHAR(100),
    description  TEXT,
    created_at   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_chaussure_categorie
        FOREIGN KEY (categorie_id) REFERENCES categories(id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- ─────────────────────────────────────────
--  4. TAILLE_CHAUSSURE  (stock par taille)
-- ─────────────────────────────────────────
CREATE TABLE taille_chaussure (
    id            INT           NOT NULL AUTO_INCREMENT,
    chaussure_id  INT           NOT NULL,
    taille        DECIMAL(4, 1) NOT NULL,
    stock         INT           NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    CONSTRAINT fk_taille_chaussure
        FOREIGN KEY (chaussure_id) REFERENCES chaussures(id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- ─────────────────────────────────────────
--  5. COMMANDES
-- ─────────────────────────────────────────
CREATE TABLE commandes (
    id             INT            NOT NULL AUTO_INCREMENT,
    user_id        INT            NOT NULL,
    montant        DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    date_commande  DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut         ENUM('en_attente', 'confirmee', 'expediee', 'livree', 'annulee')
                                  NOT NULL DEFAULT 'en_attente',
    PRIMARY KEY (id),
    CONSTRAINT fk_commande_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- ─────────────────────────────────────────
--  6. COMMANDE_ITEMS
-- ─────────────────────────────────────────
CREATE TABLE commande_items (
    id            INT            NOT NULL AUTO_INCREMENT,
    commande_id   INT            NOT NULL,
    chaussure_id  INT            NOT NULL,
    taille        DECIMAL(4, 1)  NOT NULL,
    quantite      INT            NOT NULL DEFAULT 1,
    prix          DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_item_commande
        FOREIGN KEY (commande_id) REFERENCES commandes(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_item_chaussure
        FOREIGN KEY (chaussure_id) REFERENCES chaussures(id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- ─────────────────────────────────────────
--  7. WISHLIST
-- ─────────────────────────────────────────
CREATE TABLE wishlist (
    id            INT      NOT NULL AUTO_INCREMENT,
    user_id       INT      NOT NULL,
    chaussure_id  INT      NOT NULL,
    date_ajout    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT uq_wishlist UNIQUE (user_id, chaussure_id),
    CONSTRAINT fk_wishlist_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_wishlist_chaussure
        FOREIGN KEY (chaussure_id) REFERENCES chaussures(id)
        ON DELETE CASCADE ON UPDATE CASCADE
);


-- ============================================================
--  DONNÉES DE TEST
-- ============================================================

-- ─────────────────────────────────────────
--  USERS
-- ─────────────────────────────────────────
INSERT INTO users (nom, prenom, email, adresse, mot_de_passe) VALUES
('Dupont',  'Alice', 'alice.dupont@email.com',  '12 rue des Lilas, Paris',       '$2y$10$abc123hashedpassword1'),
('Martin',  'Bruno', 'bruno.martin@email.com',  '5 avenue Foch, Lyon',           '$2y$10$abc123hashedpassword2'),
('Bernard', 'Clara', 'clara.bernard@email.com', '8 boulevard Victor, Marseille', '$2y$10$abc123hashedpassword3'),
('Leroy',   'David', 'david.leroy@email.com',   '3 impasse des Roses, Bordeaux', '$2y$10$abc123hashedpassword4'),
('Petit',   'Emma',  'emma.petit@email.com',    '21 rue du Moulin, Lille',       '$2y$10$abc123hashedpassword5');

-- ─────────────────────────────────────────
--  CATEGORIES
-- ─────────────────────────────────────────
INSERT INTO categories (id, nom, description) VALUES
(1, 'Running',   'Chaussures conçues pour la course à pied, légères et amorties.'),
(2, 'Sneakers',  'Baskets lifestyle pour un usage quotidien casual.'),
(3, 'Skate',     'Chaussures renforcées pour la pratique du skateboard.'),
(4, 'Basketball','Chaussures montantes avec soutien de la cheville.'),
(5, 'Trail',     'Chaussures robustes pour les sentiers et terrains accidentés.'),
(6, 'Tennis',    'Chaussures stables et latéralement renforcées pour le tennis.');

-- ─────────────────────────────────────────
--  CHAUSSURES  (sans image, avec categorie_id)
-- ─────────────────────────────────────────
INSERT INTO chaussures (id, categorie_id, nom, prix, marque, description) VALUES
(1, 2, 'Air Max 90',            120.00, 'Nike',    'Sneaker iconique avec amorti Air visible.'),
(2, 2, 'Stan Smith',             89.99, 'Adidas',  'Classique en cuir blanc avec détails verts.'),
(3, 3, 'Old Skool',              75.00, 'Vans',    'Chaussure skate avec bande latérale signature.'),
(4, 4, 'Chuck Taylor All Star',  65.00, 'Converse','Toile montante, semelle caoutchouc.'),
(5, 1, 'Runner R100',           109.90, 'Puma',    'Running légère avec semelle EVA.'),
(6, 1, 'Ultraboost 22',         180.00, 'Adidas',  'Running haut de gamme avec technologie Boost.'),
(7, 2, 'Air Force 1',           100.00, 'Nike',    'Basket low en cuir, style urbain classique.'),
(8, 1, 'Gel-Nimbus 24',         160.00, 'Asics',   'Running longue distance, amorti maximal.'),
(9, 5, 'Speedcross 5',          130.00, 'Salomon', 'Trail running avec crampons agressifs.'),
(10,6, 'Barricade 13',          140.00, 'Adidas',  'Tennis compétition, stabilité maximale.');

-- ─────────────────────────────────────────
--  TAILLE_CHAUSSURE
-- ─────────────────────────────────────────
INSERT INTO taille_chaussure (chaussure_id, taille, stock) VALUES
-- Air Max 90 (Sneakers)
(1, 39.0, 5), (1, 40.0, 8), (1, 41.0, 10), (1, 42.0, 7), (1, 43.0, 4), (1, 44.0, 3),
-- Stan Smith (Sneakers)
(2, 38.0, 6), (2, 39.0, 9), (2, 40.0, 11), (2, 41.0, 8), (2, 42.0, 5),
-- Old Skool (Skate)
(3, 37.0, 4), (3, 38.0, 7), (3, 39.0, 9), (3, 40.0, 6), (3, 41.0, 3),
-- Chuck Taylor (Basketball)
(4, 36.0, 5), (4, 37.0, 8), (4, 38.0, 10), (4, 39.0, 7), (4, 40.0, 4),
-- Runner R100 (Running)
(5, 40.0, 6), (5, 41.0, 9), (5, 42.0, 8), (5, 43.0, 5), (5, 44.0, 2),
-- Ultraboost 22 (Running)
(6, 40.0, 3), (6, 41.0, 5), (6, 42.0, 7), (6, 43.0, 4),
-- Air Force 1 (Sneakers)
(7, 39.0, 8), (7, 40.0, 10), (7, 41.0, 9), (7, 42.0, 6), (7, 43.0, 3),
-- Gel-Nimbus 24 (Running)
(8, 40.0, 4), (8, 41.0, 6), (8, 42.0, 5), (8, 43.0, 3), (8, 44.0, 2),
-- Speedcross 5 (Trail)
(9, 39.0, 4), (9, 40.0, 6), (9, 41.0, 8), (9, 42.0, 5), (9, 43.0, 3),
-- Barricade 13 (Tennis)
(10, 39.0, 3), (10, 40.0, 5), (10, 41.0, 7), (10, 42.0, 4), (10, 43.0, 2);

-- ─────────────────────────────────────────
--  COMMANDES
-- ─────────────────────────────────────────
INSERT INTO commandes (user_id, montant, date_commande, statut) VALUES
(1, 209.99, '2024-11-10 10:32:00', 'livree'),
(2, 180.00, '2024-12-01 14:15:00', 'expediee'),
(3,  65.00, '2025-01-05 09:20:00', 'confirmee'),
(1, 100.00, '2025-02-14 18:45:00', 'livree'),
(4, 269.90, '2025-03-01 11:00:00', 'en_attente'),
(5,  89.99, '2025-03-05 16:30:00', 'confirmee');

-- ─────────────────────────────────────────
--  COMMANDE_ITEMS
-- ─────────────────────────────────────────
INSERT INTO commande_items (commande_id, chaussure_id, taille, quantite, prix) VALUES
-- Commande 1 : Alice — Air Max 90 + Stan Smith
(1, 1, 40.0, 1, 120.00),
(1, 2, 39.0, 1,  89.99),
-- Commande 2 : Bruno — Ultraboost 22
(2, 6, 42.0, 1, 180.00),
-- Commande 3 : Clara — Chuck Taylor
(3, 4, 38.0, 1,  65.00),
-- Commande 4 : Alice — Air Force 1
(4, 7, 40.0, 1, 100.00),
-- Commande 5 : David — Runner R100 + Old Skool + Gel-Nimbus 24
(5, 5, 43.0, 1, 109.90),
(5, 3, 40.0, 1,  75.00),
(5, 8, 42.0, 1, 160.00),
-- Commande 6 : Emma — Stan Smith
(6, 2, 40.0, 1,  89.99);

-- ─────────────────────────────────────────
--  WISHLIST
-- ─────────────────────────────────────────
INSERT INTO wishlist (user_id, chaussure_id, date_ajout) VALUES
(1, 6, '2025-01-15 08:00:00'),  -- Alice aime Ultraboost
(1, 8, '2025-02-01 12:30:00'),  -- Alice aime Gel-Nimbus
(2, 1, '2025-01-20 17:00:00'),  -- Bruno aime Air Max 90
(3, 7, '2025-02-10 10:00:00'),  -- Clara aime Air Force 1
(4, 2, '2025-02-20 09:15:00'),  -- David aime Stan Smith
(5, 3, '2025-03-01 14:45:00'),  -- Emma aime Old Skool
(5, 5, '2025-03-03 11:20:00');  -- Emma aime Runner R100