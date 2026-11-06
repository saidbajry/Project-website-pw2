-- Mejeng Auto — skema + data demo MySQL (alternatif dari `php artisan migrate --seed`).
-- Pilih SALAH SATU: impor file ini ATAU jalankan migrate. Jangan keduanya pada database yang sama.
-- Semua akun demo berkata sandi: password
CREATE DATABASE IF NOT EXISTS `mejeng_auto_web` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `mejeng_auto_web`;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS password_reset_tokens, reminder_logs, posts, purchase_orders, work_order_parts, parts, work_orders, service_bookings, garage_vehicles, prospects, vehicles, branches, users;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL UNIQUE,
  email_verified_at TIMESTAMP NULL, password VARCHAR(255) NOT NULL, role VARCHAR(20) NOT NULL DEFAULT 'customer',
  phone VARCHAR(30) NULL, remember_token VARCHAR(100) NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE branches (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, address VARCHAR(255) NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vehicles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, brand VARCHAR(255) NOT NULL, model VARCHAR(255) NOT NULL, variant VARCHAR(255) NULL,
  year SMALLINT UNSIGNED NOT NULL, price BIGINT UNSIGNED NOT NULL, transmission VARCHAR(20) NOT NULL DEFAULT 'Automatic', fuel VARCHAR(20) NOT NULL DEFAULT 'Bensin',
  body_type VARCHAR(30) NOT NULL, color VARCHAR(30) NULL, mileage INT UNSIGNED NOT NULL DEFAULT 0, kondisi VARCHAR(10) NOT NULL DEFAULT 'baru',
  status VARCHAR(20) NOT NULL DEFAULT 'tersedia', image_url VARCHAR(500) NULL, image VARCHAR(255) NULL, description TEXT NULL, featured TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE prospects (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, vehicle_id BIGINT UNSIGNED NULL, user_id BIGINT UNSIGNED NULL, name VARCHAR(255) NOT NULL, phone VARCHAR(30) NOT NULL,
  email VARCHAR(255) NULL, down_payment BIGINT UNSIGNED NOT NULL DEFAULT 0, tenor TINYINT UNSIGNED NOT NULL DEFAULT 36, installment BIGINT UNSIGNED NOT NULL DEFAULT 0,
  status VARCHAR(20) NOT NULL DEFAULT 'baru', notes TEXT NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL, FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE garage_vehicles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id BIGINT UNSIGNED NOT NULL, plate VARCHAR(20) NOT NULL UNIQUE, name VARCHAR(255) NOT NULL, year SMALLINT UNSIGNED NULL,
  current_km INT UNSIGNED NOT NULL DEFAULT 0, avg_km_per_month INT UNSIGNED NOT NULL DEFAULT 1000, last_service_date DATE NULL, last_service_km INT UNSIGNED NOT NULL DEFAULT 0,
  interval_km INT UNSIGNED NOT NULL DEFAULT 5000, interval_months TINYINT UNSIGNED NOT NULL DEFAULT 6, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE service_bookings (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id BIGINT UNSIGNED NOT NULL, garage_vehicle_id BIGINT UNSIGNED NOT NULL, branch_id BIGINT UNSIGNED NOT NULL,
  service_type VARCHAR(255) NOT NULL, booking_date DATE NOT NULL, booking_time VARCHAR(5) NOT NULL, complaint TEXT NULL, status VARCHAR(20) NOT NULL DEFAULT 'menunggu',
  created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, FOREIGN KEY (garage_vehicle_id) REFERENCES garage_vehicles(id) ON DELETE CASCADE, FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE work_orders (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, code VARCHAR(255) NOT NULL UNIQUE, service_booking_id BIGINT UNSIGNED NULL, garage_vehicle_id BIGINT UNSIGNED NOT NULL, mechanic_id BIGINT UNSIGNED NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'antri', km_in INT UNSIGNED NOT NULL DEFAULT 0, km_out INT UNSIGNED NULL, labor_cost BIGINT UNSIGNED NOT NULL DEFAULT 0, notes TEXT NULL,
  started_at TIMESTAMP NULL, finished_at TIMESTAMP NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  FOREIGN KEY (service_booking_id) REFERENCES service_bookings(id) ON DELETE SET NULL, FOREIGN KEY (garage_vehicle_id) REFERENCES garage_vehicles(id) ON DELETE CASCADE, FOREIGN KEY (mechanic_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE parts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, sku VARCHAR(255) NOT NULL UNIQUE, name VARCHAR(255) NOT NULL, stock INT NOT NULL DEFAULT 0, min_stock INT NOT NULL DEFAULT 5,
  unit_price BIGINT UNSIGNED NOT NULL DEFAULT 0, supplier VARCHAR(255) NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE work_order_parts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, work_order_id BIGINT UNSIGNED NOT NULL, part_id BIGINT UNSIGNED NOT NULL, qty INT UNSIGNED NOT NULL, price BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  FOREIGN KEY (work_order_id) REFERENCES work_orders(id) ON DELETE CASCADE, FOREIGN KEY (part_id) REFERENCES parts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE purchase_orders (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, part_id BIGINT UNSIGNED NOT NULL, qty INT UNSIGNED NOT NULL, status VARCHAR(20) NOT NULL DEFAULT 'draf',
  created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL, FOREIGN KEY (part_id) REFERENCES parts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE posts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255) NOT NULL, type VARCHAR(20) NOT NULL DEFAULT 'promo', body TEXT NULL, image VARCHAR(255) NULL, is_published TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reminder_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, garage_vehicle_id BIGINT UNSIGNED NOT NULL, phone VARCHAR(30) NOT NULL, message TEXT NOT NULL, status VARCHAR(20) NOT NULL DEFAULT 'simulasi',
  created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL, FOREIGN KEY (garage_vehicle_id) REFERENCES garage_vehicles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE password_reset_tokens (email VARCHAR(255) NOT NULL PRIMARY KEY, token VARCHAR(255) NOT NULL, created_at TIMESTAMP NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================= DATA DEMO =================
SET @pw = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
INSERT INTO users (id,name,email,password,role,phone,created_at,updated_at) VALUES
(1,'Pemilik Dealer','owner@mejengauto.test',@pw,'owner','081200000001',NOW(),NOW()),
(2,'Admin Mejeng','admin@mejengauto.test',@pw,'staff','081200000002',NOW(),NOW()),
(3,'Rudi Mekanik','rudi@mejengauto.test',@pw,'staff','081200000003',NOW(),NOW()),
(4,'Agus Mekanik','agus@mejengauto.test',@pw,'staff','081200000004',NOW(),NOW()),
(5,'Budi Santoso','budi@mejengauto.test',@pw,'customer','081234567890',NOW(),NOW());

INSERT INTO branches (id,name,city,address,created_at,updated_at) VALUES
(1,'Mejeng Auto Kebayoran','Jakarta Selatan','Jl. Raya Kebayoran Lama No. 18',NOW(),NOW()),
(2,'Mejeng Auto Bekasi','Bekasi','Jl. Ahmad Yani No. 77',NOW(),NOW()),
(3,'Mejeng Auto BSD','Tangerang Selatan','Jl. BSD Raya Barat No. 5',NOW(),NOW());

INSERT INTO vehicles (id,brand,model,variant,year,price,transmission,fuel,body_type,color,kondisi,status,description,featured,created_at,updated_at) VALUES
(1,'Toyota','Innova Zenix','Hybrid Q',2025,489000000,'Automatic','Hybrid','MPV','Putih','baru','tersedia','Toyota Innova Zenix Hybrid Q siap kirim. Garansi resmi, free servis berkala pertama, dan opsi kredit fleksibel.',1,NOW(),NOW()),
(2,'Toyota','Fortuner','2.8 VRZ 4x4',2024,679000000,'Automatic','Diesel','SUV','Hitam','baru','tersedia','Toyota Fortuner 2.8 VRZ 4x4 siap kirim. Garansi resmi, free servis berkala pertama, dan opsi kredit fleksibel.',1,NOW(),NOW()),
(3,'Honda','CR-V','1.5 Turbo Prestige',2024,624000000,'Automatic','Bensin','SUV','Silver','baru','tersedia','Honda CR-V 1.5 Turbo Prestige siap kirim. Garansi resmi, free servis berkala pertama, dan opsi kredit fleksibel.',1,NOW(),NOW()),
(4,'Mitsubishi','Xpander','Ultimate',2025,330000000,'Automatic','Bensin','MPV','Abu-abu','baru','tersedia','Mitsubishi Xpander Ultimate siap kirim. Garansi resmi, free servis berkala pertama, dan opsi kredit fleksibel.',0,NOW(),NOW()),
(5,'Suzuki','Ertiga','Hybrid GX',2024,274000000,'Automatic','Hybrid','MPV','Merah','baru','tersedia','Suzuki Ertiga Hybrid GX siap kirim. Garansi resmi, free servis berkala pertama, dan opsi kredit fleksibel.',0,NOW(),NOW()),
(6,'Hyundai','Creta','Prime',2024,394000000,'Automatic','Bensin','SUV','Biru','baru','tersedia','Hyundai Creta Prime siap kirim. Garansi resmi, free servis berkala pertama, dan opsi kredit fleksibel.',0,NOW(),NOW()),
(7,'Daihatsu','Terios','R Deluxe',2023,255000000,'Automatic','Bensin','SUV','Putih','baru','tersedia','Daihatsu Terios R Deluxe siap kirim. Garansi resmi, free servis berkala pertama, dan opsi kredit fleksibel.',0,NOW(),NOW()),
(8,'Honda','Brio','RS CVT',2024,215000000,'Automatic','Bensin','Hatchback','Kuning','baru','tersedia','Honda Brio RS CVT siap kirim. Garansi resmi, free servis berkala pertama, dan opsi kredit fleksibel.',0,NOW(),NOW());

INSERT INTO parts (id,sku,name,stock,min_stock,unit_price,supplier,created_at,updated_at) VALUES
(1,'OLI-5W30','Oli mesin 5W-30 (1 L)',40,12,98000,'PT Lubrindo',NOW(),NOW()),
(2,'FLT-OLI','Filter oli',25,10,65000,'PT Autoparts Jaya',NOW(),NOW()),
(3,'FLT-UDR','Filter udara',14,8,120000,'PT Autoparts Jaya',NOW(),NOW()),
(4,'BRK-PAD','Kampas rem depan',9,6,380000,'CV Rem Prima',NOW(),NOW()),
(5,'BUSI-IR','Busi iridium',20,8,95000,'PT Autoparts Jaya',NOW(),NOW()),
(6,'AC-FREON','Freon AC R134a',6,6,150000,'CV Dingin Sejuk',NOW(),NOW()),
(7,'ACCU-NS60','Aki NS60',4,5,950000,'PT Energi Mobil',NOW(),NOW());

INSERT INTO purchase_orders (part_id,qty,status,created_at,updated_at) VALUES (6,12,'draf',NOW(),NOW()),(7,11,'draf',NOW(),NOW());

INSERT INTO garage_vehicles (id,user_id,plate,name,year,current_km,avg_km_per_month,last_service_date,last_service_km,created_at,updated_at) VALUES
(1,5,'B 1234 MJG','Toyota Innova Zenix',2024,9400,1200,DATE_SUB(CURDATE(),INTERVAL 4 MONTH),5000,NOW(),NOW()),
(2,5,'B 4321 BRO','Honda Brio RS',2022,24500,900,DATE_SUB(CURDATE(),INTERVAL 2 MONTH),22000,NOW(),NOW());

INSERT INTO work_orders (id,code,garage_vehicle_id,mechanic_id,status,km_in,km_out,labor_cost,notes,started_at,finished_at,created_at,updated_at) VALUES
(1,CONCAT('WO-',DATE_FORMAT(DATE_SUB(CURDATE(),INTERVAL 4 MONTH),'%y%m%d'),'-001'),1,3,'diambil',5000,5000,250000,'Servis berkala 5.000 km',DATE_SUB(NOW(),INTERVAL 4 MONTH),DATE_SUB(NOW(),INTERVAL 4 MONTH),NOW(),NOW());
INSERT INTO work_order_parts (work_order_id,part_id,qty,price,created_at,updated_at) VALUES (1,1,4,98000,NOW(),NOW()),(1,2,1,65000,NOW(),NOW());

INSERT INTO service_bookings (user_id,garage_vehicle_id,branch_id,service_type,booking_date,booking_time,complaint,status,created_at,updated_at)
VALUES (5,1,1,'Servis berkala',DATE_ADD(CURDATE(),INTERVAL 3 DAY),'09:00','Servis 10.000 km','menunggu',NOW(),NOW());

INSERT INTO prospects (vehicle_id,name,phone,down_payment,tenor,installment,status,created_at,updated_at) VALUES
(4,'Sari Wulandari','081311112222',82500000,36,8215625,'baru',NOW(),NOW()),
(2,'Hendra Gunawan','081322223333',169750000,36,16904271,'dihubungi',NOW(),NOW()),
(4,'Maya Putri','081333334444',82500000,36,8215625,'survei',NOW(),NOW());

INSERT INTO posts (title,type,body,is_published,created_at,updated_at) VALUES
('Langkah baru. Standar lebih tinggi.','banner','Gratis servis berkala pertama untuk setiap pembelian unit baru bulan ini.',1,NOW(),NOW()),
('Promo DP ringan hingga 15%','promo','Berlaku untuk SUV dan MPV pilihan.',1,NOW(),NOW()),
('Paket ganti oli + cek 21 titik','promo','Mulai Rp 350.000 di semua cabang.',1,NOW(),NOW());
