CREATE TABLE harga (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_ac DECIMAL(10, 2) NOT NULL
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user', 'teknisi') NOT NULL,
    alamat TEXT NOT NULL,
    nomor_hp VARCHAR(15) NOT NULL
);
 
CREATE TABLE teknisi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(50) NOT NULL,
    alamat TEXT NOT NULL,
    nomor_hp VARCHAR(15) NOT NULL,
    spesialisasi TEXT
);

CREATE TABLE service (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    teknisi_id INT,
    tanggal_servis DATETIME NOT NULL,
    location VARCHAR(255),
    harga DECIMAL(10,2) NOT NULL,
    status ENUM('menunggu', 'dikerjakan', 'selesai') NOT NULL,
    deskripsi TEXT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (teknisi_id) REFERENCES teknisi(id)
);

