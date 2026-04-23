-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 02 Apr 2026 pada 16.45
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sisabsi`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `nis` varchar(20) DEFAULT NULL,
  `idm` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `status` enum('H','I','A') DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `absensi`
--

INSERT INTO `absensi` (`id`, `nis`, `idm`, `tanggal`, `status`) VALUES
(1, '9985109543', 2, '2026-03-31', 'H'),
(2, '9974603400', 2, '2026-03-31', 'I'),
(3, '9974603377', 2, '2026-03-31', 'I'),
(4, '9974601993', 2, '2026-03-31', 'I'),
(5, '9965320857', 2, '2026-03-31', 'H'),
(6, '9974601924', 2, '2026-03-31', 'I'),
(7, '9965320876', 2, '2026-03-31', 'I'),
(8, '9965320890', 2, '2026-03-31', 'H'),
(9, '9985109574', 2, '2026-03-31', 'I'),
(10, '9974602096', 2, '2026-03-31', 'H'),
(11, '9974601836', 2, '2026-03-31', 'I'),
(12, '9974602083', 2, '2026-03-31', 'H'),
(13, '9965320870', 2, '2026-03-31', 'I'),
(14, '9965320905', 2, '2026-03-31', 'H'),
(15, '9974602051', 2, '2026-03-31', 'I'),
(16, '9974602034', 2, '2026-03-31', 'A'),
(17, '9965340897', 2, '2026-03-31', 'I'),
(18, '9985109543', 7, '2026-04-01', 'H'),
(19, '01010101022', 7, '2026-04-01', 'I'),
(20, '9974603400', 7, '2026-04-01', 'A'),
(21, '9974603377', 7, '2026-04-01', 'A'),
(22, '9974601993', 7, '2026-04-01', 'A'),
(23, '9965320857', 7, '2026-04-01', 'H'),
(24, '9974601924', 7, '2026-04-01', 'H'),
(25, '9965320876', 7, '2026-04-01', 'H'),
(26, '9965320890', 7, '2026-04-01', 'I'),
(27, '9985109574', 7, '2026-04-01', 'I'),
(28, '9974602096', 7, '2026-04-01', 'H'),
(29, '911111', 7, '2026-04-01', 'I'),
(30, '9974601836', 7, '2026-04-01', 'I'),
(31, '9974602083', 7, '2026-04-01', 'H'),
(32, '9965320870', 7, '2026-04-01', 'I'),
(33, '9965320905', 7, '2026-04-01', 'H'),
(34, '9974602051', 7, '2026-04-01', 'I'),
(35, '9974602034', 7, '2026-04-01', 'H'),
(36, '9965340897', 7, '2026-04-01', 'H');

-- --------------------------------------------------------

--
-- Struktur dari tabel `guru`
--

CREATE TABLE `guru` (
  `idg` int(10) NOT NULL,
  `nip` varchar(50) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jk` varchar(3) NOT NULL,
  `alamat` text NOT NULL,
  `pass` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `guru`
--

INSERT INTO `guru` (`idg`, `nip`, `nama`, `jk`, `alamat`, `pass`) VALUES
(9, '19610506199', 'Utami, M. Pd.', 'P', '-', '77e69c137812518e359196bb2f5e9bb9'),
(10, '19540914972', 'Dra. Hj. Latifah', 'P', '-', '77e69c137812518e359196bb2f5e9bb9'),
(13, '19661025191		', 'Yasin, S.Pd', 'L', '-', '77e69c137812518e359196bb2f5e9bb9'),
(17, '34547566583', 'Ibnu, S.Pd', 'L', '-', '77e69c137812518e359196bb2f5e9bb9'),
(18, '34627426463', 'Drs. Masrur', 'L', '-', '77e69c137812518e359196bb2f5e9bb9'),
(19, '72427476493', 'Syarifuddin, S.Ag', 'L', '-', '77e69c137812518e359196bb2f5e9bb9'),
(21, '44357356372', 'Rina, S.Pd', 'P', '-', '77e69c137812518e359196bb2f5e9bb9'),
(23, '17367626692', 'Rizki, ST.', 'L', '-', '77e69c137812518e359196bb2f5e9bb9'),
(24, '70547476433', 'Drs. Nur', 'L', '-', '77e69c137812518e359196bb2f5e9bb9'),
(25, '74377586613', 'Rizal Hermawan, M.Kom', 'L', '-', '77e69c137812518e359196bb2f5e9bb9');

-- --------------------------------------------------------

--
-- Struktur dari tabel `hari`
--

CREATE TABLE `hari` (
  `idh` int(11) NOT NULL,
  `hari` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `hari`
--

INSERT INTO `hari` (`idh`, `hari`) VALUES
(1, 'Senin'),
(2, 'Selasa'),
(3, 'Rabu'),
(4, 'Kamis'),
(5, 'Jum\'at');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal`
--

CREATE TABLE `jadwal` (
  `idj` int(11) NOT NULL,
  `idh` int(11) NOT NULL,
  `idg` int(11) NOT NULL,
  `idk` int(11) NOT NULL,
  `idm` int(11) NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `aktif` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `jadwal`
--

INSERT INTO `jadwal` (`idj`, `idh`, `idg`, `idk`, `idm`, `jam_mulai`, `jam_selesai`, `aktif`) VALUES
(1, 1, 9, 7, 2, '07:00:00', '09:00:00', 0),
(2, 2, 25, 8, 1, '07:00:00', '09:00:00', 0),
(4, 3, 25, 7, 1, '14:00:00', '15:00:00', 1),
(5, 3, 25, 9, 1, '13:00:00', '15:00:00', 1),
(6, 2, 23, 7, 3, '10:00:00', '12:00:00', 0),
(7, 4, 10, 9, 3, '10:00:00', '12:00:00', 0),
(8, 2, 17, 8, 3, '13:00:00', '15:00:00', 0),
(9, 5, 21, 7, 2, '00:00:00', '14:11:00', 0),
(10, 2, 19, 9, 3, '00:33:00', '13:32:00', 0),
(11, 1, 9, 10, 7, '00:00:00', '00:00:00', 0),
(12, 4, 24, 7, 3, '23:00:00', '09:38:00', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelas`
--

CREATE TABLE `kelas` (
  `idk` int(10) NOT NULL,
  `id` int(10) NOT NULL,
  `nama` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `kelas`
--

INSERT INTO `kelas` (`idk`, `id`, `nama`) VALUES
(7, 2, 'VII'),
(8, 2, 'VIII'),
(9, 2, 'IX'),
(10, 0, 'XII Teknik Mesin');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mata_pelajaran`
--

CREATE TABLE `mata_pelajaran` (
  `idm` int(11) NOT NULL,
  `nama_mp` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `mata_pelajaran`
--

INSERT INTO `mata_pelajaran` (`idm`, `nama_mp`) VALUES
(1, 'Matematika'),
(2, 'Bahasa Indonesia'),
(3, 'Ilmu Pengetahuan Alam'),
(7, 'Geologi');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sekolah`
--

CREATE TABLE `sekolah` (
  `id` int(10) NOT NULL,
  `kode` varchar(50) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `sekolah`
--

INSERT INTO `sekolah` (`id`, `kode`, `nama`, `alamat`) VALUES
(2, '2010902872872', 'SMKN 3 Kota Tangerang Selatan', 'Jl. Puri Serpong 1 Jl. Puspitek, Setu, Kec. Setu, Kota Tangerang Selatan, Banten 15314');

-- --------------------------------------------------------

--
-- Struktur dari tabel `siswa`
--

CREATE TABLE `siswa` (
  `ids` int(10) NOT NULL,
  `nis` varchar(50) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jk` varchar(2) NOT NULL,
  `alamat` text NOT NULL,
  `idk` int(5) NOT NULL,
  `tlp` varchar(20) NOT NULL,
  `bapak` varchar(50) NOT NULL,
  `k_bapak` varchar(50) NOT NULL,
  `ibu` varchar(50) NOT NULL,
  `k_ibu` varchar(50) NOT NULL,
  `pass` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `siswa`
--

INSERT INTO `siswa` (`ids`, `nis`, `nama`, `jk`, `alamat`, `idk`, `tlp`, `bapak`, `k_bapak`, `ibu`, `k_ibu`, `pass`) VALUES
(1, '9965340897', 'Zildjian', 'L', '-', 7, '85733743907', '-', '-', '-', '-', 'bcd724d15cde8c47650fda962968f102'),
(5, '9974601836', 'Mitra', 'P', '-', 7, '85733743907', '-', '-', '-', '-', 'bcd724d15cde8c47650fda962968f102'),
(8, '9974601924', 'Dhea', 'P', '-', 7, '85954590935', '-', '-', '-', '-', 'bcd724d15cde8c47650fda962968f102'),
(9, '9974601993', 'Armando', 'L', '-', 7, '85733743907', '-', '-', '-', '-', 'bcd724d15cde8c47650fda962968f102'),
(10, '9974602034', 'Yunaz', 'L', '-', 7, '85733743907', '-', '-', '-', '-', 'bcd724d15cde8c47650fda962968f102'),
(11, '9974602051', 'Susanto', 'L', '-', 8, '85733743907', '-', '-', '-', '-', 'bcd724d15cde8c47650fda962968f102'),
(12, '9974602083', 'Rani', 'P', '-', 8, '85733743907', '-', '-', '-', '-', 'bcd724d15cde8c47650fda962968f102'),
(13, '9974602096', 'Hardianti', 'P', '-', 8, '85733743907', '-', '-', '-', '-', 'bcd724d15cde8c47650fda962968f102'),
(23, '9974603377', 'Ari Nur', 'L', '-', 8, '85954590935', '-', '-', '-', '-', 'bcd724d15cde8c47650fda962968f102'),
(25, '9985109543', 'Adit', 'L', '-', 8, '85733743907', '-', '-', '-', '-', 'bcd724d15cde8c47650fda962968f102'),
(27, '9965320857', 'Bani', 'L', '-', 8, '85733743907', '-', '-', '-', '-', 'd41d8cd98f00b204e9800998ecf8427e'),
(28, '9974603400', 'Angel', 'P', '-', 9, '85733743907', '-', '-', '-', '-', 'bcd724d15cde8c47650fda962968f102'),
(29, '9965320870', 'Rezah', 'L', '-', 9, '85733743907', '-', '-', '-', '-', 'bcd724d15cde8c47650fda962968f102'),
(30, '9965320876', 'Dwi', 'L', '-', 9, '85733743907', '-', '-', '-', '-', 'bcd724d15cde8c47650fda962968f102'),
(34, '9965320890', 'Evi', 'P', '-', 9, '85733743907', '-', '-', '-', '-', 'bcd724d15cde8c47650fda962968f102'),
(35, '9985109574', 'Fio', 'P', '-', 9, '85733743907', '-', '-', '-', '-', 'bcd724d15cde8c47650fda962968f102'),
(36, '9965320905', 'Rindy', 'P', '-', 9, '85733743907', '-', '-', '-', '-', 'bcd724d15cde8c47650fda962968f102'),
(39, '911111', 'HRD', 'L', 'ffggr5rrd', 10, '8888888888888888', '', '', '', '', '$2y$10$S3NiHYg6WLl/v68HRTBu/OkZ5j5WqQ0HA3j7iy38LcS3eowP2Xc/i');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `idu` int(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `pass` text NOT NULL,
  `level` varchar(50) NOT NULL,
  `id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`idu`, `nama`, `pass`, `level`, `id`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin', 2),
(2, 'admin2', 'admin111', 'admin', 2);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`idg`);

--
-- Indeks untuk tabel `hari`
--
ALTER TABLE `hari`
  ADD PRIMARY KEY (`idh`);

--
-- Indeks untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`idj`);

--
-- Indeks untuk tabel `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`idk`);

--
-- Indeks untuk tabel `mata_pelajaran`
--
ALTER TABLE `mata_pelajaran`
  ADD PRIMARY KEY (`idm`);

--
-- Indeks untuk tabel `sekolah`
--
ALTER TABLE `sekolah`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`ids`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`idu`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT untuk tabel `guru`
--
ALTER TABLE `guru`
  MODIFY `idg` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT untuk tabel `hari`
--
ALTER TABLE `hari`
  MODIFY `idh` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `idj` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `kelas`
--
ALTER TABLE `kelas`
  MODIFY `idk` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `mata_pelajaran`
--
ALTER TABLE `mata_pelajaran`
  MODIFY `idm` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `sekolah`
--
ALTER TABLE `sekolah`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `siswa`
--
ALTER TABLE `siswa`
  MODIFY `ids` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `idu` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
