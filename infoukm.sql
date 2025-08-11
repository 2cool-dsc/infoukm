-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 11, 2025 at 09:50 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `infoukm`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int NOT NULL,
  `nama_event` varchar(255) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `deskripsi` text,
  `biaya` int DEFAULT NULL,
  `link_pendaftaran` varchar(255) DEFAULT NULL,
  `status` enum('Aktif','Mendatang','Selesai') DEFAULT 'Mendatang',
  `banner` varchar(255) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `jam_mulai` time DEFAULT NULL,
  `jam_selesai` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `nama_event`, `tanggal_mulai`, `tanggal_selesai`, `lokasi`, `deskripsi`, `biaya`, `link_pendaftaran`, `status`, `banner`, `created_by`, `created_at`, `jam_mulai`, `jam_selesai`) VALUES
(4, 'DSCompetition', '2025-07-21', '2025-08-16', 'R. Said Sukanto', '🚨 Calling All UBHARA Students! Ayo tunjukkan kemampuan desain & coding terbaikmu!\r\n\r\n✨ DSCompetition dari DSC UBHARA hadir sebagai wadah untuk kamu yang passionate di dunia:\r\n🎨 UI/UX Design – Desain antarmuka yang impactful\r\n💻 Web Development – Bangun solusi web untuk layanan publik\r\n\r\n🧠 Tema: Public Services \r\n\r\n🗓 Pendaftaran: 24 Juni – 21 Juli 2025\r\n💸 Biaya Pendaftaran: Rp 30.000 / tim\r\n🎁 Total Hadiah: Rp 2.000.000++ dan Sertifikat Juara\r\n📜 Semua peserta yang submit akan dapat e-sertifikat\r\n', 30000, 'https://forms.gle/xg2rKTVXkWenYgGR6', 'Aktif', '6895d3e595580.jpg', 13, '2025-08-08 10:39:33', '00:00:00', '23:59:00'),
(7, 'DSC Recruitment Core Team', '2025-07-29', '2025-08-20', 'Universitas Bhayangkara Jakarta Raya', '🚀 DSC UBHARA Core Team Recruitment 2025–2026\r\n✨ Saatnya kamu jadi bagian dari tim inti DSC UBHARA! ✨\r\n\r\nDSC UBHARA Chapter resmi buka rekrutmen Core Team periode 2025 – 2026!\r\nBuat kamu yang passionate di teknologi, kreatif, dan pengen berkontribusi nyata di komunitas, this is your moment to shine!\r\n\r\n\r\n❓Kenapa harus join?\r\n\r\n💡 Skill Upgrade – Pengalaman nyata untuk upgrade skill teknis & leadership.\r\n🤝 Build Network – Kolaborasi dengan mentor, profesional, & teman dari berbagai jurusan.\r\n🎯 Lead & Inspire – Pimpin proyek dan jadi inspirasi buat komunitas kampus.\r\n📁 Sertifikat & Portfolio – Tambah value di CV kamu dengan sertifikat & portofolio proyek.\r\n🌍 Impact Creator – Berkontribusi lewat solusi teknologi yang berdampak nyata.\r\n\r\n\r\n🎉 Let’s grow and build something amazing together with DSC UBHARA!\r\nJangan sampai kelewatan kesempatan ini!\r\n', 0, 'https://forms.gle/WtyamdhS2DG4i62WA', 'Aktif', '68986c3fb12a3.jpg', 13, '2025-08-10 09:54:07', '00:00:00', '23:59:00'),
(14, 'LDKM/LKMM', '2025-08-19', '2025-08-19', 'Universitas Bhayangkara Jakarta Raya Kota Bekasi (M. Yasin 405)', '📣 ‼Announcement‼📣\r\n\r\nAssalamu\'alaikum, Wr.Wb.\r\nShalom\r\nOm Swasiastu\r\nNamo Buddhaya\r\nSalam Kebajikan\r\nSalam Mahasiswa\r\n\r\nHalo Mahasiswa FASILKOM! 👋\r\nKami mengundang kepada seluruh Mahasiswa/Mahasiswi FAKULTAS Ilmu Komputer untuk mengikuti kegiatan Latihan Dasar Kepemimpinan Mahasiswa (LDKM) dan Latihan Kepemimpinan Manjemen Mahasiswa (LKMM) sebagai wadah karakter kepemimpinan.\r\n\r\nSudah saatnya kamu mengasah jiwa kepemimpinan, berpikir kritis, dan memperkuat kemampuan organisasi!\r\n\r\n✨ BENEFIT PESERTA:\r\n✅ E-Sertifikat\r\n✅ Ilmu Kepemimpinan & Organisasi\r\n✅ Pelatihan Critical Thinking', 0, 'https://docs.google.com/forms/d/e/1FAIpQLSft65JHN8kzUtlSL1eghMPTh9HzlwTqURbXkdLWwzcQFYvIHw/viewform?usp=dialog', 'Mendatang', '6899b907c335e.jpg', 14, '2025-08-11 09:33:59', '07:00:00', '12:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama_ukm` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `pembina` varchar(100) DEFAULT NULL,
  `sosial_media` varchar(255) DEFAULT NULL,
  `deskripsi_ukm` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `logo` varchar(255) DEFAULT NULL,
  `role` enum('admin','ukm') DEFAULT 'ukm',
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `tiktok` varchar(255) DEFAULT NULL,
  `x` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama_ukm`, `email`, `password`, `remember_token`, `pembina`, `sosial_media`, `deskripsi_ukm`, `logo`, `role`, `status`, `created_at`, `tiktok`, `x`, `facebook`) VALUES
(4, 'Admin InfoUKM', 'admin@infoukm.ac.id', '$2y$10$dYnFe2qWfbK.5N92Awcqc.YxcTyVujw8ix8VvtjyEPO5Ly878EVz2', NULL, '', '', '', '', 'admin', 'aktif', '2025-08-04 08:26:27', NULL, NULL, NULL),
(13, 'DSC Ubhara', 'dsc@ukm.ac.id', '$2y$10$mmGXhqvUcSWpNxOjERjIDe0x.i44Fbw.pMOOtmZrCvCZKvJYHd/i6', NULL, 'Pak Ubhara', 'https://www.instagram.com/dsc_ubhara', 'Bem Fasilkom ada lah Bem dari Fakultas Ilmu Komputer Bhayangkara Jakarta Raya Kota Bekasi yang siap melayani dan membantu para Mahasiswa untuk menyampaikan aspirasinya ke pada pihak Kampus', '1754826780_466338522_2041424172965650_2417572721970086567_n.jpg', 'ukm', 'aktif', '2025-08-07 09:56:39', 'https://www.tiktok.com/@douxxxie', 'https://x.com/elonmusk', 'https://web.facebook.com/p/Elon-Musk-official-61568107145925/?_rdc=1&_rdr#'),
(14, 'BEM Fasilkom', 'bemfasilkom@ukm.ac.id', '$2y$10$jlDoKsYyDXUcJ7Ej77jaOeXZ.frGGewfYR0fmz4rEKIB25ir3Pl6G', NULL, 'Pak Zain', 'https://www.instagram.com/bemfasilkom_ubhara/', 'Badan Eksekutif Mahasiswa Fakultas Ilmu Komputer Universitas Bhayangkara Jakarta Raya', '6898953d8a68a.jpeg', 'ukm', 'aktif', '2025-08-10 12:49:01', '', '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
