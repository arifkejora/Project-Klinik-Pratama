-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 05, 2024 at 03:27 PM
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
(10, 22, 2, 1, 'Selesai Diperiksa', '19:31:15'),
(11, 22, 4, 2, 'Selesai Diperiksa', '19:40:38');

-- --------------------------------------------------------

--
-- Table structure for table `detail_pasien`
--

CREATE TABLE `detail_pasien` (
  `id_detail_pasien` int(11) NOT NULL,
  `id_pasien` int(11) DEFAULT NULL,
  `jenis_kelamin` varchar(10) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `alamat_pasien` varchar(100) DEFAULT NULL,
  `provinsi` varchar(255) NOT NULL,
  `kabupaten` varchar(255) NOT NULL,
  `kecamatan` varchar(255) NOT NULL,
  `desa` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_pasien`
--

INSERT INTO `detail_pasien` (`id_detail_pasien`, `id_pasien`, `jenis_kelamin`, `tanggal_lahir`, `alamat_pasien`, `provinsi`, `kabupaten`, `kecamatan`, `desa`) VALUES
(4, 1, 'Laki-laki', '2024-06-18', 'Jalan Kencana', '11', '1102', '1102010', '1102010005'),
(5, 3, 'Perempuan', '1999-08-05', 'Jalan Kencana', '32', '3203', '3203200', '3203200010'),
(6, 2, 'Laki-laki', '2022-06-08', 'Jalan Kencana', '33', '3320', '3320050', '3320050002'),
(7, 4, 'Laki-laki', '2002-03-13', 'Jalan Kencana', '51', '5108', '5108050', '5108050010');

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
(4, 'DC0005', 'Lestiani', 'lestiani@gmail.com', 'Dermatologi', '2024-06-07', '25d55ad283aa400af464c76d713c07ad', 'Jalan Merpati', 50000.00);

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
(22, 4, '2024-07-05', '19:29:00', NULL, 0, 'Aktif');

-- --------------------------------------------------------

--
-- Table structure for table `obat`
--

CREATE TABLE `obat` (
  `id_obat` int(11) NOT NULL,
  `nama_obat` varchar(50) NOT NULL,
  `jenis_obat` varchar(50) DEFAULT NULL,
  `stok` int(11) NOT NULL,
  `harga_obat` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `obat`
--

INSERT INTO `obat` (`id_obat`, `nama_obat`, `jenis_obat`, `stok`, `harga_obat`) VALUES
(1, 'Paracetamol', 'Tablet', 14, 10500.00),
(2, 'Bodrex', 'Tablet', 15, 5000.00),
(3, 'Panadol', 'Tablet', 14, 5000.00),
(6, 'CTM', 'Kapsul', 20, 5000.00);

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
(1, 'Hilmy Muflih', 'hilmymuflih@gmail.com', '082312579428', '25d55ad283aa400af464c76d713c07ad'),
(2, 'Naruto', 'naruto@gmail.com', '082233543946', '25d55ad283aa400af464c76d713c07ad'),
(3, 'Lestiani', 'lestiani@gmail.com', '081573691766', '25d55ad283aa400af464c76d713c07ad'),
(4, 'Rizky Billar', 'billar@gmail.com', '087678234567', '25d55ad283aa400af464c76d713c07ad');

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
(21, 10, 'Sakit Panas', 'Demam', '140', '55', '37', 'DBD', '65500', 'Lunas'),
(22, 11, 'Sakit DB', 'Demam', '130', '67', '34', 'Mencret', '', 'Belum Lunas');

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
(12, 1, 21, '', '', 'Antrian'),
(13, 3, 21, '', '', 'Antrian'),
(14, 1, 22, '', '', 'Antrian'),
(15, 3, 22, '', '', 'Antrian');

-- --------------------------------------------------------

--
-- Table structure for table `rujukan`
--

CREATE TABLE `rujukan` (
  `id_rujukan` int(11) NOT NULL,
  `id_rekammedis` int(11) NOT NULL,
  `nama_rumahsakit` varchar(100) NOT NULL,
  `nama_dokter` varchar(255) NOT NULL,
  `poli` varchar(255) NOT NULL,
  `tanggal_rujukan` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `id_antrian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `detail_pasien`
--
ALTER TABLE `detail_pasien`
  MODIFY `id_detail_pasien` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `dokter`
--
ALTER TABLE `dokter`
  MODIFY `id_dokter` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `farmasi`
--
ALTER TABLE `farmasi`
  MODIFY `id_farmasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jadwal_dokter`
--
ALTER TABLE `jadwal_dokter`
  MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `obat`
--
ALTER TABLE `obat`
  MODIFY `id_obat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pasien`
--
ALTER TABLE `pasien`
  MODIFY `id_pasien` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pimpinan_klinik`
--
ALTER TABLE `pimpinan_klinik`
  MODIFY `id_pimpinan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rating`
--
ALTER TABLE `rating`
  MODIFY `id_rating` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rekam_medis`
--
ALTER TABLE `rekam_medis`
  MODIFY `id_rekam_medis` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `resep_obat`
--
ALTER TABLE `resep_obat`
  MODIFY `id_resep` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `rujukan`
--
ALTER TABLE `rujukan`
  MODIFY `id_rujukan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
