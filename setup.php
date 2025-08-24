
<?php
require __DIR__ . '/includes/config.php';

// Create tables
$schema = <<<SQL
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  phone VARCHAR(40) DEFAULT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('consumer','provider','admin') NOT NULL DEFAULT 'consumer',
  status ENUM('pending','approved','suspended') NOT NULL DEFAULT 'approved',
  city VARCHAR(120) DEFAULT NULL,
  bio TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  icon VARCHAR(16) DEFAULT '🔧'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS provider_skills (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  category_id INT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  consumer_id INT NOT NULL,
  provider_id INT NOT NULL,
  date DATE NOT NULL,
  time TIME NOT NULL,
  hours INT NOT NULL DEFAULT 1,
  address VARCHAR(255) NOT NULL,
  notes TEXT,
  status ENUM('pending','accepted','completed','cancelled') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (consumer_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (provider_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  consumer_id INT NOT NULL,
  provider_id INT NOT NULL,
  rating INT NOT NULL,
  comment TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;

try{
  $pdo->exec($schema);
} catch(Exception $e){
  die("Error creating tables: " . e($e->getMessage()));
}

// Seed categories if empty
$exists = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
if(!$exists){
  $cats = [
    ['Plumber','🔧'], ['Electrician','💡'], ['Welder','🔩'], ['Painter','🎨'],
    ['Carpenter','🪚'], ['Labour','🧱'], ['AC Technician','❄️'], ['Mechanic','🚗']
  ];
  $stmt = $pdo->prepare("INSERT INTO categories(name,icon) VALUES(?,?)");
  foreach($cats as $c){ $stmt->execute($c); }
}

// Create default admin if missing
$adm = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role='admin'");
$adm->execute();
if(!$adm->fetchColumn()){
  $pass = password_hash('admin123', PASSWORD_DEFAULT);
  $pdo->prepare("INSERT INTO users(name,email,phone,password_hash,role,status,city) VALUES('Admin','admin@example.com','',?, 'admin','approved','Karachi')")->execute([$pass]);
}

echo "<h3>Setup complete ✅</h3><p>Admin login: <b>admin@example.com</b> / <b>admin123</b></p><p>Now go to <a href='/index.php'>Home</a>.</p>";
