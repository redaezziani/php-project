-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    points INTEGER DEFAULT 0,
    role VARCHAR(20) DEFAULT 'client',
    reset_token TEXT NULL,
    reset_token_expires DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE IF NOT EXISTS produits (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    reference VARCHAR(50) UNIQUE NOT NULL,
    designation VARCHAR(255) NOT NULL,
    description TEXT,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    quantite_stock INTEGER DEFAULT 0,
    image VARCHAR(255),
    promotion DECIMAL(5,2) DEFAULT 0,
    category_id INTEGER,
    ingredients TEXT,
    nutritional_info TEXT,
    allergens TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Orders Table
CREATE TABLE IF NOT EXISTS commandes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    points_earned INTEGER DEFAULT 0,
    points_used INTEGER DEFAULT 0,
    shipping_address TEXT,
    phone_number VARCHAR(20),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS commande_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    commande_id INTEGER NOT NULL,
    produit_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL,
    price_at_time DECIMAL(10,2) NOT NULL,
    promotion_at_time DECIMAL(5,2) DEFAULT 0,
    FOREIGN KEY (commande_id) REFERENCES commandes(id),
    FOREIGN KEY (produit_id) REFERENCES produits(id)
);

-- Reviews Table
CREATE TABLE IF NOT EXISTS avis (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    produit_id INTEGER NOT NULL,
    rating INTEGER NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (produit_id) REFERENCES produits(id)
);

CREATE INDEX IF NOT EXISTS idx_products_category ON produits(category_id);
CREATE INDEX IF NOT EXISTS idx_orders_user ON commandes(user_id);
CREATE INDEX IF NOT EXISTS idx_order_items_order ON commande_items(commande_id);
CREATE INDEX IF NOT EXISTS idx_order_items_product ON commande_items(produit_id);
CREATE INDEX IF NOT EXISTS idx_reviews_product ON avis(produit_id);
CREATE INDEX IF NOT EXISTS idx_reviews_user ON avis(user_id);