-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 24, 2026 at 06:36 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db sip_lab`
--

-- --------------------------------------------------------

--
-- Table structure for table `alat`
--

CREATE TABLE `alat` (
  `id_alat` int(11) NOT NULL,
  `nama_alat` varchar(100) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `kondisi` enum('baik','rusak_ringan','rusak_berat') DEFAULT 'baik',
  `status` enum('tersedia','sedang_dipinjam','tidak_tersedia') DEFAULT 'tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alat`
--

INSERT INTO `alat` (`id_alat`, `nama_alat`, `jumlah`, `kondisi`, `status`) VALUES
(1, 'Smart TV', 3, 'baik', 'tersedia'),
(2, 'Proyektor Portabel', 5, 'baik', 'tersedia'),
(3, 'Laptop', 10, 'baik', 'tersedia'),
(4, 'Kabel Rol', 8, 'baik', 'tersedia');

-- --------------------------------------------------------

--
-- Table structure for table `detail_peminjaman`
--

CREATE TABLE `detail_peminjaman` (
  `id_detail` int(11) NOT NULL,
  `id_peminjaman` int(11) NOT NULL,
  `id_alat` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_peminjaman`
--

INSERT INTO `detail_peminjaman` (`id_detail`, `id_peminjaman`, `id_alat`, `jumlah`) VALUES
(1, 1, 2, 1),
(2, 2, 1, 1),
(3, 2, 3, 2),
(4, 3, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `guru`
--

CREATE TABLE `guru` (
  `id_guru` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_guru` varchar(100) NOT NULL,
  `mata_pelajaran` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guru`
--

INSERT INTO `guru` (`id_guru`, `id_user`, `nama_guru`, `mata_pelajaran`) VALUES
(1, 2, 'Budi Santoso', 'Informatika');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal`
--

CREATE TABLE `jadwal` (
  `id_jadwal` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu_mulai` time NOT NULL,
  `waktu_selesai` time NOT NULL,
  `status` enum('tersedia','digunakan') DEFAULT 'tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id_notifikasi` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `pesan` text NOT NULL,
  `waktu` datetime DEFAULT current_timestamp(),
  `status_baca` enum('belum_dibaca','sudah_dibaca') DEFAULT 'belum_dibaca'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifikasi`
--

INSERT INTO `notifikasi` (`id_notifikasi`, `id_user`, `pesan`, `waktu`, `status_baca`) VALUES
(1, 1, 'Pengajuan peminjaman alat berhasil dibuat.', '2026-11-09 10:58:19', 'sudah_dibaca'),
(2, 1, 'Peminjaman alat tanggal 10 November telah disetujui.', '2026-11-09 10:58:19', 'belum_dibaca'),
(3, 2, 'Pengajuan peminjaman alat sedang menunggu persetujuan.', '2026-11-09 10:58:19', 'belum_dibaca'),
(4, 3, 'Terdapat pengajuan peminjaman alat baru.', '2026-11-09 10:58:19', 'belum_dibaca'),
(5, 1, 'Pengajuan peminjaman alat berhasil dibuat.', '2026-11-12 15:23:16', 'belum_dibaca'),
(6, 1, 'Pengajuan peminjaman alat berhasil dibuat.', '2026-11-12 16:03:30', 'belum_dibaca'),
(7, 1, 'Pengajuan peminjaman alat berhasil dibuat.', '2026-11-12 16:05:06', 'belum_dibaca'),
(8, 1, 'Pengajuan peminjaman alat berhasil dibuat.', '2026-11-12 17:05:25', 'belum_dibaca'),
(9, 1, 'Pengajuan peminjaman alat berhasil dibuat.', '2026-11-14 15:52:40', 'belum_dibaca'),
(10, 1, 'Pengajuan peminjaman alat berhasil dibuat.', '2026-11-23 21:57:34', 'belum_dibaca'),
(11, 1, 'Pengajuan peminjaman alat berhasil dibuat.', '2026-11-23 22:02:16', 'belum_dibaca'),
(12, 1, 'Pengajuan peminjaman alat berhasil dibuat.', '2026-11-24 11:55:55', 'belum_dibaca'),
(13, 1, 'Pengajuan peminjaman alat telah dibatalkan.', '2026-11-24 12:04:41', 'belum_dibaca'),
(14, 1, 'Pengajuan peminjaman alat berhasil dibuat.', '2026-11-24 12:04:50', 'belum_dibaca'),
(15, 1, 'Pengajuan peminjaman alat telah dibatalkan.', '2026-11-24 12:04:53', 'belum_dibaca'),
(16, 1, 'Pengajuan peminjaman alat berhasil dibuat.', '2026-11-24 12:06:05', 'belum_dibaca'),
(17, 1, 'Pengajuan peminjaman alat telah dibatalkan.', '2026-11-24 12:08:23', 'belum_dibaca');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id_peminjaman` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `tanggal_pengajuan` datetime DEFAULT current_timestamp(),
  `tanggal_peminjaman` date NOT NULL,
  `status_peminjaman` enum('menunggu','disetujui','ditolak','sedang_dipinjam','selesai') DEFAULT 'menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id_peminjaman`, `id_user`, `id_jadwal`, `tanggal_pengajuan`, `tanggal_peminjaman`, `status_peminjaman`) VALUES
(1, 1, 1, '2026-11-09 10:58:19', '2026-11-07', 'selesai'),
(2, 1, 2, '2026-11-09 10:58:19', '2026-11-10', 'disetujui'),
(3, 2, 3, '2026-11-09 10:58:19', '2026-11-11', 'menunggu');

-- --------------------------------------------------------

--
-- Table structure for table `pengelola`
--

CREATE TABLE `pengelola` (
  `id_pengelola` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_pengelola` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengelola`
--

INSERT INTO `pengelola` (`id_pengelola`, `id_user`, `nama_pengelola`) VALUES
(1, 3, 'Andi Saputra');

-- --------------------------------------------------------

--
-- Table structure for table `pengembalian`
--

CREATE TABLE `pengembalian` (
  `id_pengembalian` int(11) NOT NULL,
  `id_peminjaman` int(11) NOT NULL,
  `tanggal_pengembalian` datetime NOT NULL,
  `kondisi_kembali` enum('baik','rusak_ringan','rusak_berat') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengembalian`
--

INSERT INTO `pengembalian` (`id_pengembalian`, `id_peminjaman`, `tanggal_pengembalian`, `kondisi_kembali`) VALUES
(1, 1, '2026-11-07 10:15:00', 'baik');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id_siswa` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_siswa` varchar(100) NOT NULL,
  `kelas` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id_siswa`, `id_user`, `nama_siswa`, `kelas`) VALUES
(1, 1, 'Raka Pratama', 'IX A');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `role` enum('siswa','guru','pengelola') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `nama`, `role`) VALUES
(1, 'siswa01', 'password123', 'Raka Pratama', 'siswa'),
(2, 'guru01', 'password123', 'Budi Santoso', 'guru'),
(3, 'admin01', 'password123', 'Andi Saputra', 'pengelola');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alat`
--
ALTER TABLE `alat`
  ADD PRIMARY KEY (`id_alat`);

--
-- Indexes for table `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_peminjaman` (`id_peminjaman`),
  ADD KEY `id_alat` (`id_alat`);

--
-- Indexes for table `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`id_guru`),
  ADD UNIQUE KEY `id_user` (`id_user`);

--
-- Indexes for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id_jadwal`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id_notifikasi`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id_peminjaman`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_jadwal` (`id_jadwal`);

--
-- Indexes for table `pengelola`
--
ALTER TABLE `pengelola`
  ADD PRIMARY KEY (`id_pengelola`),
  ADD UNIQUE KEY `id_user` (`id_user`);

--
-- Indexes for table `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD PRIMARY KEY (`id_pengembalian`),
  ADD UNIQUE KEY `id_peminjaman` (`id_peminjaman`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id_siswa`),
  ADD UNIQUE KEY `id_user` (`id_user`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alat`
--
ALTER TABLE `alat`
  MODIFY `id_alat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `guru`
--
ALTER TABLE `guru`
  MODIFY `id_guru` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id_notifikasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id_peminjaman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `pengelola`
--
ALTER TABLE `pengelola`
  MODIFY `id_pengelola` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pengembalian`
--
ALTER TABLE `pengembalian`
  MODIFY `id_pengembalian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id_siswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  ADD CONSTRAINT `detail_peminjaman_ibfk_1` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id_peminjaman`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_peminjaman_ibfk_2` FOREIGN KEY (`id_alat`) REFERENCES `alat` (`id_alat`) ON UPDATE CASCADE;

--
-- Constraints for table `guru`
--
ALTER TABLE `guru`
  ADD CONSTRAINT `guru_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON UPDATE CASCADE,
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal` (`id_jadwal`) ON UPDATE CASCADE;

--
-- Constraints for table `pengelola`
--
ALTER TABLE `pengelola`
  ADD CONSTRAINT `pengelola_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD CONSTRAINT `pengembalian_ibfk_1` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id_peminjaman`) ON UPDATE CASCADE;

--
-- Constraints for table `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `siswa_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
