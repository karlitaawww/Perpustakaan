CREATE DATABASE perpustakaan;
USE perpustakaan;

-- Tabel buku
CREATE TABLE buku (
    id_buku INT PRIMARY KEY AUTO_INCREMENT,
    judul VARCHAR(255) NOT NULL,
    penulis VARCHAR(255) NOT NULL,
    penerbit VARCHAR(255),
    tahun_terbit YEAR,
    kategori VARCHAR(100),
    isbn VARCHAR(20),
    stok INT NOT NULL DEFAULT 0
);

-- Tabel anggota
use perpustakaan;
CREATE TABLE anggota (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    nama VARCHAR(100) NOT NULL,
    alamat TEXT,
    telepon VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
);
SHOW CREATE TABLE anggota;

CREATE TABLE peminjaman (
    id_pinjam INT PRIMARY KEY AUTO_INCREMENT,
    id_anggota INT NOT NULL,
    id_buku INT NOT NULL,
    tanggal_pinjam DATE NOT NULL,
    tanggal_kembali DATE NOT NULL,
    tanggal_dikembalikan DATE,
    denda INT DEFAULT 0,
    denda_dibayar TINYINT(1) DEFAULT 0,
    FOREIGN KEY (id_anggota) REFERENCES anggota(id_anggota),
    FOREIGN KEY (id_buku) REFERENCES buku(id_buku),
    INDEX (id_anggota),
    INDEX (id_buku),
    INDEX (tanggal_pinjam),
    INDEX (tanggal_kembali)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tambahan kolom untuk tracking
ALTER TABLE peminjaman
ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
ADD COLUMN id_petugas INT,
ADD CONSTRAINT fk_petugas FOREIGN KEY (id_petugas) REFERENCES users(id_user);


-- Tabel users (untuk login)
CREATE DATABASE perpustakaan;
USE perpustakaan;
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'member') DEFAULT 'member',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

