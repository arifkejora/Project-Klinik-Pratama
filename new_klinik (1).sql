-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2024 at 05:49 PM
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
-- Database: `new_klinik`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `nama_lengkap` varchar(20) NOT NULL,
  `email` varchar(20) NOT NULL,
  `kata_sandi` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `nama_lengkap`, `email`, `kata_sandi`) VALUES
(1, 'admin', 'admin@gmail.com', '25d55ad283aa400af464c76d713c07ad');

-- --------------------------------------------------------

--
-- Table structure for table `antrian`
--

CREATE TABLE `antrian` (
  `id_antrian` int(11) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `id_pasien` int(11) DEFAULT NULL,
  `antrian` int(11) DEFAULT NULL,
  `status_antrian` varchar(50) DEFAULT NULL,
  `dtmcrt` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `antrian`
--

INSERT INTO `antrian` (`id_antrian`, `id_jadwal`, `id_pasien`, `antrian`, `status_antrian`, `dtmcrt`) VALUES
(3, 18, 1, 1, 'Selesai Diperiksa', '00:00:00'),
(6, 20, 1, 1, 'Selesai Diperiksa', '21:18:34');

-- --------------------------------------------------------

--
-- Table structure for table `detail_pasien`
--

CREATE TABLE `detail_pasien` (
  `id_detail_pasien` int(11) NOT NULL,
  `id_pasien` int(11) DEFAULT NULL,
  `jenis_kelamin` varchar(10) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `alamat_pasien` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_pasien`
--

INSERT INTO `detail_pasien` (`id_detail_pasien`, `id_pasien`, `jenis_kelamin`, `tanggal_lahir`, `alamat_pasien`) VALUES
(1, 1, 'Laki-Laki', '1999-01-01', 'Jalan Kencana no 12');

-- --------------------------------------------------------

--
-- Table structure for table `dokter`
--

CREATE TABLE `dokter` (
  `id_dokter` int(11) NOT NULL,
  `nip` varchar(20) DEFAULT NULL,
  `nama_dokter` varchar(50) NOT NULL,
  `email_dokter` varchar(50) NOT NULL,
  `spesialis` varchar(50) DEFAULT NULL,
  `mulai_bekerja` date DEFAULT NULL,
  `katasandi_dokter` varchar(255) NOT NULL,
  `alamat_dokter` varchar(100) DEFAULT NULL,
  `harga_perkunjungan` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dokter`
--

INSERT INTO `dokter` (`id_dokter`, `nip`, `nama_dokter`, `email_dokter`, `spesialis`, `mulai_bekerja`, `katasandi_dokter`, `alamat_dokter`, `harga_perkunjungan`) VALUES
(1, 'DC0001', 'Marpuah Sido', 'marpuah12@gmail.com', 'Umum', '2024-06-01', '25d55ad283aa400af464c76d713c07ad', 'Jalan Melati', 50000.00);

-- --------------------------------------------------------

--
-- Table structure for table `farmasi`
--

CREATE TABLE `farmasi` (
  `id_farmasi` int(11) NOT NULL,
  `nip` varchar(20) DEFAULT NULL,
  `nama_farmasi` varchar(50) DEFAULT NULL,
  `mulai_bekerja` date DEFAULT NULL,
  `email_farmasi` varchar(50) DEFAULT NULL,
  `katasandi_farmasi` varchar(255) DEFAULT NULL,
  `alamat_farmasi` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farmasi`
--

INSERT INTO `farmasi` (`id_farmasi`, `nip`, `nama_farmasi`, `mulai_bekerja`, `email_farmasi`, `katasandi_farmasi`, `alamat_farmasi`) VALUES
(1, 'FM00001', 'Arif Widiarto', '2022-07-14', 'arifwidiarto@gmail.com', '25d55ad283aa400af464c76d713c07ad', 'Jalan Kamboja 107G');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_dokter`
--

CREATE TABLE `jadwal_dokter` (
  `id_jadwal` int(11) NOT NULL,
  `id_dokter` int(11) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `waktu_mulai` time DEFAULT NULL,
  `waktu_selesai` time DEFAULT NULL,
  `kuota` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jadwal_dokter`
--

INSERT INTO `jadwal_dokter` (`id_jadwal`, `id_dokter`, `tanggal`, `waktu_mulai`, `waktu_selesai`, `kuota`, `status`) VALUES
(18, 1, '2024-06-08', '10:44:00', '21:05:00', 3, 'Tidak aktif'),
(20, 1, '2024-06-09', '21:18:00', '21:33:00', 0, 'Tidak aktif');

-- --------------------------------------------------------

--
-- Table structure for table `obat`
--

CREATE TABLE `obat` (
  `id_obat` int(11) NOT NULL,
  `nama_obat` varchar(50) NOT NULL,
  `jenis_obat` varchar(50) DEFAULT NULL,
  `harga_obat` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `obat`
--

INSERT INTO `obat` (`id_obat`, `nama_obat`, `jenis_obat`, `harga_obat`) VALUES
(1, 'Paracetamol', 'Imunomodulator', 10500.00),
(2, 'Bodrex', 'Sakit Kepala', 5000.00),
(3, 'Panadol', 'Analgesik', 5000.00);

-- --------------------------------------------------------

--
-- Table structure for table `pasien`
--

CREATE TABLE `pasien` (
  `id_pasien` int(11) NOT NULL,
  `nama_pasien` varchar(50) NOT NULL,
  `email_pasien` varchar(50) NOT NULL,
  `nomorhp_pasien` varchar(15) DEFAULT NULL,
  `katasandi_pasien` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pasien`
--

INSERT INTO `pasien` (`id_pasien`, `nama_pasien`, `email_pasien`, `nomorhp_pasien`, `katasandi_pasien`) VALUES
(1, 'Hilmy Muflih', 'hilmymuflih@gmail.com', '087678567234', '25d55ad283aa400af464c76d713c07ad'),
(2, 'Naruto', 'naruto@gmail.com', '08882384343', '25d55ad283aa400af464c76d713c07ad');

-- --------------------------------------------------------

--
-- Table structure for table `pimpinan_klinik`
--

CREATE TABLE `pimpinan_klinik` (
  `id_pimpinan` int(11) NOT NULL,
  `nama_pimpinan` varchar(50) NOT NULL,
  `email_pimpinan` varchar(50) NOT NULL,
  `katasandi_pimpinan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rating`
--

CREATE TABLE `rating` (
  `id_rating` int(11) NOT NULL,
  `id_rekam_medis` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `ulasan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rating`
--

INSERT INTO `rating` (`id_rating`, `id_rekam_medis`, `rating`, `ulasan`) VALUES
(2, 14, 2, 'Dokternya begok'),
(3, 12, 1, 'sdegregeg');

-- --------------------------------------------------------

--
-- Table structure for table `rekam_medis`
--

CREATE TABLE `rekam_medis` (
  `id_rekam_medis` int(11) NOT NULL,
  `id_antrian` int(11) DEFAULT NULL,
  `keluhan` text DEFAULT NULL,
  `diagnosa` text DEFAULT NULL,
  `tekanan_darah` varchar(10) DEFAULT NULL,
  `berat_badan` varchar(10) DEFAULT NULL,
  `suhu_badan` varchar(10) DEFAULT NULL,
  `hasil_pemeriksaan` text NOT NULL,
  `pembayaran` varchar(10) DEFAULT NULL,
  `status_pembayaran` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rekam_medis`
--

INSERT INTO `rekam_medis` (`id_rekam_medis`, `id_antrian`, `keluhan`, `diagnosa`, `tekanan_darah`, `berat_badan`, `suhu_badan`, `hasil_pemeriksaan`, `pembayaran`, `status_pembayaran`) VALUES
(12, 3, 'Sakit Hati gegara putus cinta, si betina selingkuh sama Hallo Dek, Aduhai', 'Broken Heart Stadium 4', '120/150', '55', '40', 'f4gg', '15500', 'Lunas'),
(14, 6, 'Muntaber', 'Muntaber', '120/70', '67', '34', 'Aman', '60500', 'Lunas');

-- --------------------------------------------------------

--
-- Table structure for table `resep_obat`
--

CREATE TABLE `resep_obat` (
  `id_resep` int(11) NOT NULL,
  `id_obat` int(11) NOT NULL,
  `id_rekammedis` int(11) NOT NULL,
  `aturan` text NOT NULL,
  `keterangan` text NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resep_obat`
--

INSERT INTO `resep_obat` (`id_resep`, `id_obat`, `id_rekammedis`, `aturan`, `keterangan`, `status`) VALUES
(3, 1, 12, '3 x 1', 'Habiskan', 'Selesai'),
(4, 2, 12, '2 x 1', 'Setelah makan', 'Selesai'),
(6, 1, 14, '3 x 1', 'Habiskan', 'Selesai');

-- --------------------------------------------------------

--
-- Table structure for table `rujukan`
--

CREATE TABLE `rujukan` (
  `id_rujukan` int(11) NOT NULL,
  `id_rekammedis` int(11) NOT NULL,
  `nama_rumahsakit` varchar(100) NOT NULL,
  `tanggal_rujukan` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rujukan`
--

INSERT INTO `rujukan` (`id_rujukan`, `id_rekammedis`, `nama_rumahsakit`, `tanggal_rujukan`) VALUES
(1, 12, 'RS Kumala Siwi', '2024-06-29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `antrian`
--
ALTER TABLE `antrian`
  ADD PRIMARY KEY (`id_antrian`),
  ADD KEY `id_pasien` (`id_pasien`),
  ADD KEY `antrian_ibfk_2` (`id_jadwal`);

--
-- Indexes for table `detail_pasien`
--
ALTER TABLE `detail_pasien`
  ADD PRIMARY KEY (`id_detail_pasien`),
  ADD KEY `id_pasien` (`id_pasien`);

--
-- Indexes for table `dokter`
--
ALTER TABLE `dokter`
  ADD PRIMARY KEY (`id_dokter`);

--
-- Indexes for table `farmasi`
--
ALTER TABLE `farmasi`
  ADD PRIMARY KEY (`id_farmasi`);

--
-- Indexes for table `jadwal_dokter`
--
ALTER TABLE `jadwal_dokter`
  ADD PRIMARY KEY (`id_jadwal`),
  ADD KEY `id_dokter` (`id_dokter`);

--
-- Indexes for table `obat`
--
ALTER TABLE `obat`
  ADD PRIMARY KEY (`id_obat`);

--
-- Indexes for table `pasien`
--
ALTER TABLE `pasien`
  ADD PRIMARY KEY (`id_pasien`);

--
-- Indexes for table `pimpinan_klinik`
--
ALTER TABLE `pimpinan_klinik`
  ADD PRIMARY KEY (`id_pimpinan`);

--
-- Indexes for table `rating`
--
ALTER TABLE `rating`
  ADD PRIMARY KEY (`id_rating`),
  ADD KEY `id_rekam_medis` (`id_rekam_medis`);

--
-- Indexes for table `rekam_medis`
--
ALTER TABLE `rekam_medis`
  ADD PRIMARY KEY (`id_rekam_medis`),
  ADD KEY `id_antrian` (`id_antrian`);

--
-- Indexes for table `resep_obat`
--
ALTER TABLE `resep_obat`
  ADD PRIMARY KEY (`id_resep`),
  ADD KEY `resep_obat_ibfk_1` (`id_obat`),
  ADD KEY `rekam_media_ibfk_2` (`id_rekammedis`);

--
-- Indexes for table `rujukan`
--
ALTER TABLE `rujukan`
  ADD PRIMARY KEY (`id_rujukan`),
  ADD KEY `id_rekam_medis_fk_1` (`id_rekammedis`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `antrian`
--
ALTER TABLE `antrian`
  MODIFY `id_antrian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `detail_pasien`
--
ALTER TABLE `detail_pasien`
  MODIFY `id_detail_pasien` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dokter`
--
ALTER TABLE `dokter`
  MODIFY `id_dokter` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `farmasi`
--
ALTER TABLE `farmasi`
  MODIFY `id_farmasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jadwal_dokter`
--
ALTER TABLE `jadwal_dokter`
  MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `obat`
--
ALTER TABLE `obat`
  MODIFY `id_obat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pasien`
--
ALTER TABLE `pasien`
  MODIFY `id_pasien` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pimpinan_klinik`
--
ALTER TABLE `pimpinan_klinik`
  MODIFY `id_pimpinan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rating`
--
ALTER TABLE `rating`
  MODIFY `id_rating` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rekam_medis`
--
ALTER TABLE `rekam_medis`
  MODIFY `id_rekam_medis` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `resep_obat`
--
ALTER TABLE `resep_obat`
  MODIFY `id_resep` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `rujukan`
--
ALTER TABLE `rujukan`
  MODIFY `id_rujukan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `antrian`
--
ALTER TABLE `antrian`
  ADD CONSTRAINT `antrian_ibfk_1` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id_pasien`),
  ADD CONSTRAINT `antrian_ibfk_2` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal_dokter` (`id_jadwal`);

--
-- Constraints for table `detail_pasien`
--
ALTER TABLE `detail_pasien`
  ADD CONSTRAINT `detail_pasien_ibfk_1` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id_pasien`);

--
-- Constraints for table `jadwal_dokter`
--
ALTER TABLE `jadwal_dokter`
  ADD CONSTRAINT `jadwal_dokter_ibfk_1` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id_dokter`);

--
-- Constraints for table `rating`
--
ALTER TABLE `rating`
  ADD CONSTRAINT `rating_ibfk_1` FOREIGN KEY (`id_rekam_medis`) REFERENCES `rekam_medis` (`id_rekam_medis`);

--
-- Constraints for table `rekam_medis`
--
ALTER TABLE `rekam_medis`
  ADD CONSTRAINT `rekam_medis_ibfk_3` FOREIGN KEY (`id_antrian`) REFERENCES `antrian` (`id_antrian`);

--
-- Constraints for table `resep_obat`
--
ALTER TABLE `resep_obat`
  ADD CONSTRAINT `rekam_media_ibfk_2` FOREIGN KEY (`id_rekammedis`) REFERENCES `rekam_medis` (`id_rekam_medis`),
  ADD CONSTRAINT `resep_obat_ibfk_1` FOREIGN KEY (`id_obat`) REFERENCES `obat` (`id_obat`);

--
-- Constraints for table `rujukan`
--
ALTER TABLE `rujukan`
  ADD CONSTRAINT `id_rekam_medis_fk_1` FOREIGN KEY (`id_rekammedis`) REFERENCES `rekam_medis` (`id_rekam_medis`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
