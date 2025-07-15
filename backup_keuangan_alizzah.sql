-- MySQL dump 10.13  Distrib 9.3.0, for macos15.2 (arm64)
--
-- Host: localhost    Database: keuangan_alizzah
-- ------------------------------------------------------
-- Server version	9.3.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bulan`
--

DROP TABLE IF EXISTS `bulan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bulan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_urut` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `angka_bulan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_bulan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bulan`
--

LOCK TABLES `bulan` WRITE;
/*!40000 ALTER TABLE `bulan` DISABLE KEYS */;
INSERT INTO `bulan` VALUES (1,'1','7','Juli','2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(2,'2','8','Agustus','2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(3,'3','9','September','2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(4,'4','10','Oktober','2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(5,'5','11','November','2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(6,'6','12','Desember','2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(7,'7','1','Januari','2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(8,'8','2','Februari','2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(9,'9','3','Maret','2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(10,'10','4','April','2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(11,'11','5','Mei','2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(12,'12','6','Juni','2025-07-10 07:48:29','2025-07-10 07:48:29',NULL);
/*!40000 ALTER TABLE `bulan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `desa`
--

DROP TABLE IF EXISTS `desa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `desa` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kecamatan_id` bigint unsigned NOT NULL,
  `kode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `desa_kode_unique` (`kode`),
  KEY `desa_kecamatan_id_kode_index` (`kecamatan_id`,`kode`),
  CONSTRAINT `desa_kecamatan_id_foreign` FOREIGN KEY (`kecamatan_id`) REFERENCES `kecamatan` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `desa`
--

LOCK TABLES `desa` WRITE;
/*!40000 ALTER TABLE `desa` DISABLE KEYS */;
/*!40000 ALTER TABLE `desa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jenjang`
--

DROP TABLE IF EXISTS `jenjang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jenjang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_jenjang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jenjang`
--

LOCK TABLES `jenjang` WRITE;
/*!40000 ALTER TABLE `jenjang` DISABLE KEYS */;
INSERT INTO `jenjang` VALUES (1,'KB','2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(2,'TK','2025-07-10 07:48:29','2025-07-10 07:48:29',NULL);
/*!40000 ALTER TABLE `jenjang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jenjang_pos_pemasukan`
--

DROP TABLE IF EXISTS `jenjang_pos_pemasukan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jenjang_pos_pemasukan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pos_pemasukan_id` bigint unsigned NOT NULL,
  `jenjang_id` bigint unsigned NOT NULL,
  `tahun_ajaran_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jenjang_pos_pemasukan_pos_pemasukan_id_foreign` (`pos_pemasukan_id`),
  KEY `jenjang_pos_pemasukan_jenjang_id_foreign` (`jenjang_id`),
  KEY `jenjang_pos_pemasukan_tahun_ajaran_id_foreign` (`tahun_ajaran_id`),
  CONSTRAINT `jenjang_pos_pemasukan_jenjang_id_foreign` FOREIGN KEY (`jenjang_id`) REFERENCES `jenjang` (`id`) ON DELETE CASCADE,
  CONSTRAINT `jenjang_pos_pemasukan_pos_pemasukan_id_foreign` FOREIGN KEY (`pos_pemasukan_id`) REFERENCES `pos_pemasukan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `jenjang_pos_pemasukan_tahun_ajaran_id_foreign` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jenjang_pos_pemasukan`
--

LOCK TABLES `jenjang_pos_pemasukan` WRITE;
/*!40000 ALTER TABLE `jenjang_pos_pemasukan` DISABLE KEYS */;
INSERT INTO `jenjang_pos_pemasukan` VALUES (1,1,1,2,'2025-07-10 07:49:20','2025-07-10 07:49:20',NULL),(2,1,2,2,'2025-07-10 07:49:20','2025-07-10 07:49:20',NULL),(3,2,1,2,'2025-07-10 07:50:00','2025-07-10 07:50:00',NULL),(4,2,2,2,'2025-07-10 07:50:00','2025-07-10 07:50:00',NULL),(5,3,1,2,'2025-07-10 07:50:57','2025-07-10 07:50:57',NULL),(6,3,2,2,'2025-07-10 07:50:57','2025-07-10 07:50:57',NULL),(7,4,1,2,'2025-07-10 07:53:03','2025-07-10 07:53:03',NULL),(8,4,2,2,'2025-07-10 07:53:03','2025-07-10 07:53:03',NULL),(9,5,1,2,'2025-07-10 07:53:33','2025-07-10 07:53:33',NULL),(10,5,2,2,'2025-07-10 07:53:33','2025-07-10 07:53:33',NULL),(11,6,1,2,'2025-07-10 07:55:48','2025-07-10 07:55:48',NULL),(12,6,2,2,'2025-07-10 07:55:48','2025-07-10 07:55:48',NULL);
/*!40000 ALTER TABLE `jenjang_pos_pemasukan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kabupaten_kota`
--

DROP TABLE IF EXISTS `kabupaten_kota`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kabupaten_kota` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `provinsi_id` bigint unsigned NOT NULL,
  `kode` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kabupaten_kota_kode_unique` (`kode`),
  KEY `kabupaten_kota_provinsi_id_kode_index` (`provinsi_id`,`kode`),
  CONSTRAINT `kabupaten_kota_provinsi_id_foreign` FOREIGN KEY (`provinsi_id`) REFERENCES `provinsi` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kabupaten_kota`
--

LOCK TABLES `kabupaten_kota` WRITE;
/*!40000 ALTER TABLE `kabupaten_kota` DISABLE KEYS */;
/*!40000 ALTER TABLE `kabupaten_kota` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kategori_dispensasi`
--

DROP TABLE IF EXISTS `kategori_dispensasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kategori_dispensasi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `persentase_default` decimal(15,2) NOT NULL DEFAULT '0.00',
  `nominal_default` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kategori_dispensasi`
--

LOCK TABLES `kategori_dispensasi` WRITE;
/*!40000 ALTER TABLE `kategori_dispensasi` DISABLE KEYS */;
/*!40000 ALTER TABLE `kategori_dispensasi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kecamatan`
--

DROP TABLE IF EXISTS `kecamatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kecamatan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kabupaten_kota_id` bigint unsigned NOT NULL,
  `kode` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kecamatan_kode_unique` (`kode`),
  KEY `kecamatan_kabupaten_kota_id_kode_index` (`kabupaten_kota_id`,`kode`),
  CONSTRAINT `kecamatan_kabupaten_kota_id_foreign` FOREIGN KEY (`kabupaten_kota_id`) REFERENCES `kabupaten_kota` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kecamatan`
--

LOCK TABLES `kecamatan` WRITE;
/*!40000 ALTER TABLE `kecamatan` DISABLE KEYS */;
/*!40000 ALTER TABLE `kecamatan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kelas`
--

DROP TABLE IF EXISTS `kelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kelas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_kelas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenjang_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kelas_jenjang_id_foreign` (`jenjang_id`),
  CONSTRAINT `kelas_jenjang_id_foreign` FOREIGN KEY (`jenjang_id`) REFERENCES `jenjang` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kelas`
--

LOCK TABLES `kelas` WRITE;
/*!40000 ALTER TABLE `kelas` DISABLE KEYS */;
INSERT INTO `kelas` VALUES (1,'MUTIARA 1',1,'2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(2,'MUTIARA 2',1,'2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(3,'MUTIARA 3',1,'2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(4,'MUTIARA 4',1,'2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(5,'MUTIARA 5',1,'2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(6,'MUTIARA 6',1,'2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(7,'INTAN 1',2,'2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(8,'INTAN 2',2,'2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(9,'INTAN 3',2,'2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(10,'INTAN 4',2,'2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(11,'INTAN 5',2,'2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(12,'INTAN 6',2,'2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(13,'BERLIAN 1',2,'2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(14,'BERLIAN 2',2,'2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(15,'BERLIAN 3',2,'2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(16,'BERLIAN 4',2,'2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(17,'BERLIAN 5',2,'2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(18,'BERLIAN 6',2,'2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(19,'BERLIAN 7',2,'2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(20,'BERLIAN 8',2,'2025-07-10 07:48:29','2025-07-10 07:48:29',NULL);
/*!40000 ALTER TABLE `kelas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ledgers`
--

DROP TABLE IF EXISTS `ledgers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ledgers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sumber_tabel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `referensi_id` bigint NOT NULL,
  `tipe` enum('in','out') COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_akun` enum('aset','liabilitas','ekuitas','pendapatan','beban','hutang','piutang') COLLATE utf8mb4_unicode_ci NOT NULL,
  `trx_date` datetime NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `debit` decimal(15,2) NOT NULL,
  `kredit` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ledgers`
--

LOCK TABLES `ledgers` WRITE;
/*!40000 ALTER TABLE `ledgers` DISABLE KEYS */;
/*!40000 ALTER TABLE `ledgers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_reset_tokens_table',1),(3,'2014_10_12_100000_create_password_resets_table',1),(4,'2019_08_19_000000_create_failed_jobs_table',1),(5,'2019_12_14_000001_create_personal_access_tokens_table',1),(6,'2025_04_29_080710_create_siswa_table',1),(7,'2025_04_29_080725_create_jenjang_table',1),(8,'2025_04_29_080747_create_kelas_table',1),(9,'2025_04_29_080756_create_tahun_ajaran_table',1),(10,'2025_04_29_080828_create_kategori_dispensasi_table',1),(11,'2025_04_29_080859_create_pos_table',1),(12,'2025_04_29_080906_create_bulan_table',1),(13,'2025_04_29_080932_create_siswa_kelas_table',1),(14,'2025_04_29_081004_create_pos_pemasukan_table',1),(15,'2025_04_29_081023_create_jenjang_pos_pemasukan_table',1),(16,'2025_04_29_081040_create_siswa_dispensasi_table',1),(17,'2025_04_29_081041_create_tagihan_siswa_table',1),(18,'2025_04_29_081049_create_pemasukan_table',1),(19,'2025_04_29_081113_create_pemasukan_detail_table',1),(20,'2025_04_29_081122_create_pemasukan_pembayaran_table',1),(21,'2025_04_29_081207_create_pos_pengeluaran_table',1),(22,'2025_04_29_081214_create_pengeluaran_table',1),(23,'2025_04_29_081219_create_pengeluaran_detail_table',1),(24,'2025_04_29_081226_create_pengeluaran_pembayaran_table',1),(25,'2025_04_29_081245_create_ledger_table',1),(26,'2025_06_29_230605_create_wilayah_tables',1),(27,'2025_06_30_002101_add_alamat_siswa_table',1),(28,'2025_06_30_005659_add_alamat_string_siswa_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pemasukan`
--

DROP TABLE IF EXISTS `pemasukan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pemasukan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_transaksi` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `siswa_kelas_id` bigint unsigned NOT NULL,
  `tahun_ajaran_id` bigint unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `total` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pemasukan_siswa_kelas_id_foreign` (`siswa_kelas_id`),
  KEY `pemasukan_tahun_ajaran_id_foreign` (`tahun_ajaran_id`),
  CONSTRAINT `pemasukan_siswa_kelas_id_foreign` FOREIGN KEY (`siswa_kelas_id`) REFERENCES `siswa_kelas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pemasukan_tahun_ajaran_id_foreign` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pemasukan`
--

LOCK TABLES `pemasukan` WRITE;
/*!40000 ALTER TABLE `pemasukan` DISABLE KEYS */;
/*!40000 ALTER TABLE `pemasukan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pemasukan_detail`
--

DROP TABLE IF EXISTS `pemasukan_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pemasukan_detail` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pemasukan_id` bigint unsigned NOT NULL,
  `tagihan_siswa_id` bigint unsigned NOT NULL,
  `nominal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pemasukan_detail_pemasukan_id_foreign` (`pemasukan_id`),
  KEY `pemasukan_detail_tagihan_siswa_id_foreign` (`tagihan_siswa_id`),
  CONSTRAINT `pemasukan_detail_pemasukan_id_foreign` FOREIGN KEY (`pemasukan_id`) REFERENCES `pemasukan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pemasukan_detail_tagihan_siswa_id_foreign` FOREIGN KEY (`tagihan_siswa_id`) REFERENCES `tagihan_siswa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pemasukan_detail`
--

LOCK TABLES `pemasukan_detail` WRITE;
/*!40000 ALTER TABLE `pemasukan_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `pemasukan_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pemasukan_pembayaran`
--

DROP TABLE IF EXISTS `pemasukan_pembayaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pemasukan_pembayaran` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pemasukan_id` bigint unsigned NOT NULL,
  `pemasukan_detail_id` bigint unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `metode` enum('tunai','transfer','lainnya') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bukti` text COLLATE utf8mb4_unicode_ci,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pemasukan_pembayaran_pemasukan_id_foreign` (`pemasukan_id`),
  KEY `pemasukan_pembayaran_pemasukan_detail_id_foreign` (`pemasukan_detail_id`),
  CONSTRAINT `pemasukan_pembayaran_pemasukan_detail_id_foreign` FOREIGN KEY (`pemasukan_detail_id`) REFERENCES `pemasukan_detail` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pemasukan_pembayaran_pemasukan_id_foreign` FOREIGN KEY (`pemasukan_id`) REFERENCES `pemasukan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pemasukan_pembayaran`
--

LOCK TABLES `pemasukan_pembayaran` WRITE;
/*!40000 ALTER TABLE `pemasukan_pembayaran` DISABLE KEYS */;
/*!40000 ALTER TABLE `pemasukan_pembayaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pengeluaran`
--

DROP TABLE IF EXISTS `pengeluaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengeluaran` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_transaksi` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bulan_id` bigint unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `total` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pengeluaran_bulan_id_foreign` (`bulan_id`),
  CONSTRAINT `pengeluaran_bulan_id_foreign` FOREIGN KEY (`bulan_id`) REFERENCES `bulan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengeluaran`
--

LOCK TABLES `pengeluaran` WRITE;
/*!40000 ALTER TABLE `pengeluaran` DISABLE KEYS */;
/*!40000 ALTER TABLE `pengeluaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pengeluaran_detail`
--

DROP TABLE IF EXISTS `pengeluaran_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengeluaran_detail` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pengeluaran_id` bigint unsigned NOT NULL,
  `pos_pengeluaran_id` bigint unsigned NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `nominal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pengeluaran_detail_pengeluaran_id_foreign` (`pengeluaran_id`),
  KEY `pengeluaran_detail_pos_pengeluaran_id_foreign` (`pos_pengeluaran_id`),
  CONSTRAINT `pengeluaran_detail_pengeluaran_id_foreign` FOREIGN KEY (`pengeluaran_id`) REFERENCES `pengeluaran` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pengeluaran_detail_pos_pengeluaran_id_foreign` FOREIGN KEY (`pos_pengeluaran_id`) REFERENCES `pos_pengeluaran` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengeluaran_detail`
--

LOCK TABLES `pengeluaran_detail` WRITE;
/*!40000 ALTER TABLE `pengeluaran_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `pengeluaran_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pengeluaran_pembayaran`
--

DROP TABLE IF EXISTS `pengeluaran_pembayaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengeluaran_pembayaran` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pengeluaran_id` bigint unsigned NOT NULL,
  `tanggal` datetime NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `metode` enum('tunai','transfer','lainnya') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bukti` text COLLATE utf8mb4_unicode_ci,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pengeluaran_pembayaran_pengeluaran_id_foreign` (`pengeluaran_id`),
  CONSTRAINT `pengeluaran_pembayaran_pengeluaran_id_foreign` FOREIGN KEY (`pengeluaran_id`) REFERENCES `pengeluaran` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengeluaran_pembayaran`
--

LOCK TABLES `pengeluaran_pembayaran` WRITE;
/*!40000 ALTER TABLE `pengeluaran_pembayaran` DISABLE KEYS */;
/*!40000 ALTER TABLE `pengeluaran_pembayaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pos`
--

DROP TABLE IF EXISTS `pos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_pos` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe` enum('in','out') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos`
--

LOCK TABLES `pos` WRITE;
/*!40000 ALTER TABLE `pos` DISABLE KEYS */;
INSERT INTO `pos` VALUES (1,'PEMASUKAN','in','2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(2,'PENGELUARAN','out','2025-07-10 07:48:29','2025-07-10 07:48:29',NULL);
/*!40000 ALTER TABLE `pos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pos_pemasukan`
--

DROP TABLE IF EXISTS `pos_pemasukan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pos_pemasukan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_pos_pemasukan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pos_id` bigint unsigned NOT NULL,
  `wajib` tinyint(1) NOT NULL DEFAULT '1',
  `pembayaran` enum('sekali','harian','bulanan','tahunan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nominal_valid` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pos_pemasukan_pos_id_foreign` (`pos_id`),
  CONSTRAINT `pos_pemasukan_pos_id_foreign` FOREIGN KEY (`pos_id`) REFERENCES `pos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_pemasukan`
--

LOCK TABLES `pos_pemasukan` WRITE;
/*!40000 ALTER TABLE `pos_pemasukan` DISABLE KEYS */;
INSERT INTO `pos_pemasukan` VALUES (1,'Biaya Awal','',1,1,'sekali',2000000.00,'2025-07-10 07:49:20','2025-07-10 07:49:20',NULL),(2,'Biaya Registrasi','',1,1,'sekali',150000.00,'2025-07-10 07:50:00','2025-07-10 07:50:00',NULL),(3,'SPP','',1,1,'bulanan',150000.00,'2025-07-10 07:50:57','2025-07-10 07:50:57',NULL),(4,'Infaq Harian','',1,1,'harian',7000.00,'2025-07-10 07:53:03','2025-07-10 07:53:03',NULL),(5,'Extrakulikuler','',1,1,'bulanan',50000.00,'2025-07-10 07:53:33','2025-07-10 07:53:33',NULL),(6,'Biaya Lain-lain','',1,0,'sekali',0.00,'2025-07-10 07:55:48','2025-07-10 07:55:48',NULL);
/*!40000 ALTER TABLE `pos_pemasukan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pos_pengeluaran`
--

DROP TABLE IF EXISTS `pos_pengeluaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pos_pengeluaran` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kategori` enum('bebean_operasional','beban_administrasi') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_pos_pengeluaran` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pos_id` bigint unsigned NOT NULL,
  `nominal_valid` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pos_pengeluaran_pos_id_foreign` (`pos_id`),
  CONSTRAINT `pos_pengeluaran_pos_id_foreign` FOREIGN KEY (`pos_id`) REFERENCES `pos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_pengeluaran`
--

LOCK TABLES `pos_pengeluaran` WRITE;
/*!40000 ALTER TABLE `pos_pengeluaran` DISABLE KEYS */;
/*!40000 ALTER TABLE `pos_pengeluaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `provinsi`
--

DROP TABLE IF EXISTS `provinsi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `provinsi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `provinsi_kode_unique` (`kode`),
  KEY `provinsi_kode_index` (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `provinsi`
--

LOCK TABLES `provinsi` WRITE;
/*!40000 ALTER TABLE `provinsi` DISABLE KEYS */;
/*!40000 ALTER TABLE `provinsi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `siswa`
--

DROP TABLE IF EXISTS `siswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `siswa` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_lengkap` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_panggilan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nik` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tempat_lahir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `alamat_lengkap` text COLLATE utf8mb4_unicode_ci,
  `desa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kecamatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kabupaten_kota` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provinsi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `desa_id` bigint unsigned DEFAULT NULL,
  `kecamatan_id` bigint unsigned DEFAULT NULL,
  `kabupaten_kota_id` bigint unsigned DEFAULT NULL,
  `provinsi_id` bigint unsigned DEFAULT NULL,
  `agama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kewarganegaraan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anak_keberapa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah_saudara_kandung` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah_saudara_tiri` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah_saudara_angkat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_orangtua` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bahasa_seharihari` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `golongan_darah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `riwayat_penyakit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `riwayat_imunisasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciri_khusus` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cita_cita` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `siswa_kecamatan_id_foreign` (`kecamatan_id`),
  KEY `siswa_kabupaten_kota_id_foreign` (`kabupaten_kota_id`),
  KEY `siswa_provinsi_id_foreign` (`provinsi_id`),
  KEY `siswa_desa_id_kecamatan_id_kabupaten_kota_id_provinsi_id_index` (`desa_id`,`kecamatan_id`,`kabupaten_kota_id`,`provinsi_id`),
  CONSTRAINT `siswa_desa_id_foreign` FOREIGN KEY (`desa_id`) REFERENCES `desa` (`id`),
  CONSTRAINT `siswa_kabupaten_kota_id_foreign` FOREIGN KEY (`kabupaten_kota_id`) REFERENCES `kabupaten_kota` (`id`),
  CONSTRAINT `siswa_kecamatan_id_foreign` FOREIGN KEY (`kecamatan_id`) REFERENCES `kecamatan` (`id`),
  CONSTRAINT `siswa_provinsi_id_foreign` FOREIGN KEY (`provinsi_id`) REFERENCES `provinsi` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `siswa`
--

LOCK TABLES `siswa` WRITE;
/*!40000 ALTER TABLE `siswa` DISABLE KEYS */;
/*!40000 ALTER TABLE `siswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `siswa_dispensasi`
--

DROP TABLE IF EXISTS `siswa_dispensasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `siswa_dispensasi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint unsigned NOT NULL,
  `kategori_dispensasi_id` bigint unsigned NOT NULL,
  `pos_pemasukan_id` bigint unsigned NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `persentase_overide` decimal(15,2) DEFAULT NULL,
  `nominal_overide` decimal(15,2) DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `status` enum('aktif','nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `siswa_dispensasi_siswa_id_foreign` (`siswa_id`),
  KEY `siswa_dispensasi_kategori_dispensasi_id_foreign` (`kategori_dispensasi_id`),
  KEY `siswa_dispensasi_pos_pemasukan_id_foreign` (`pos_pemasukan_id`),
  CONSTRAINT `siswa_dispensasi_kategori_dispensasi_id_foreign` FOREIGN KEY (`kategori_dispensasi_id`) REFERENCES `kategori_dispensasi` (`id`) ON DELETE CASCADE,
  CONSTRAINT `siswa_dispensasi_pos_pemasukan_id_foreign` FOREIGN KEY (`pos_pemasukan_id`) REFERENCES `pos_pemasukan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `siswa_dispensasi_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `siswa_dispensasi`
--

LOCK TABLES `siswa_dispensasi` WRITE;
/*!40000 ALTER TABLE `siswa_dispensasi` DISABLE KEYS */;
/*!40000 ALTER TABLE `siswa_dispensasi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `siswa_kelas`
--

DROP TABLE IF EXISTS `siswa_kelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `siswa_kelas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tahun_ajaran_id` bigint unsigned NOT NULL,
  `siswa_id` bigint unsigned NOT NULL,
  `kelas_id` bigint unsigned NOT NULL,
  `asal_sekolah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('aktif','nonaktif','alumni') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `siswa_kelas_tahun_ajaran_id_foreign` (`tahun_ajaran_id`),
  KEY `siswa_kelas_siswa_id_foreign` (`siswa_id`),
  KEY `siswa_kelas_kelas_id_foreign` (`kelas_id`),
  CONSTRAINT `siswa_kelas_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `siswa_kelas_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `siswa_kelas_tahun_ajaran_id_foreign` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `siswa_kelas`
--

LOCK TABLES `siswa_kelas` WRITE;
/*!40000 ALTER TABLE `siswa_kelas` DISABLE KEYS */;
/*!40000 ALTER TABLE `siswa_kelas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tagihan_siswa`
--

DROP TABLE IF EXISTS `tagihan_siswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tagihan_siswa` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tahun_ajaran_id` bigint unsigned NOT NULL,
  `bulan_id` bigint unsigned DEFAULT NULL,
  `siswa_kelas_id` bigint unsigned NOT NULL,
  `pos_pemasukan_id` bigint unsigned NOT NULL,
  `tanggal_tagihan` date DEFAULT NULL,
  `tanggal_jatuh_tempo` date DEFAULT NULL,
  `siswa_dispensasi_id` bigint unsigned DEFAULT NULL,
  `nominal_awal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `diskon_persen` decimal(15,2) NOT NULL DEFAULT '0.00',
  `diskon_nominal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `nominal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `jumlah_dibayar` int NOT NULL DEFAULT '0',
  `status` enum('belum_lunas','lunas') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum_lunas',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tagihan_siswa_bulan_id_foreign` (`bulan_id`),
  KEY `tagihan_siswa_siswa_kelas_id_foreign` (`siswa_kelas_id`),
  KEY `tagihan_siswa_pos_pemasukan_id_foreign` (`pos_pemasukan_id`),
  KEY `tagihan_siswa_siswa_dispensasi_id_foreign` (`siswa_dispensasi_id`),
  KEY `idx_tagihan_siswa` (`tahun_ajaran_id`,`bulan_id`,`siswa_kelas_id`,`pos_pemasukan_id`),
  CONSTRAINT `tagihan_siswa_bulan_id_foreign` FOREIGN KEY (`bulan_id`) REFERENCES `bulan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tagihan_siswa_pos_pemasukan_id_foreign` FOREIGN KEY (`pos_pemasukan_id`) REFERENCES `pos_pemasukan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tagihan_siswa_siswa_dispensasi_id_foreign` FOREIGN KEY (`siswa_dispensasi_id`) REFERENCES `siswa_dispensasi` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tagihan_siswa_siswa_kelas_id_foreign` FOREIGN KEY (`siswa_kelas_id`) REFERENCES `siswa_kelas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tagihan_siswa_tahun_ajaran_id_foreign` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tagihan_siswa`
--

LOCK TABLES `tagihan_siswa` WRITE;
/*!40000 ALTER TABLE `tagihan_siswa` DISABLE KEYS */;
/*!40000 ALTER TABLE `tagihan_siswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tahun_ajaran`
--

DROP TABLE IF EXISTS `tahun_ajaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tahun_ajaran` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_tahun_ajaran` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `is_aktif` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tahun_ajaran`
--

LOCK TABLES `tahun_ajaran` WRITE;
/*!40000 ALTER TABLE `tahun_ajaran` DISABLE KEYS */;
INSERT INTO `tahun_ajaran` VALUES (1,'2024/2025','2024-07-01','2025-06-01',0,'2025-07-10 07:48:29','2025-07-10 07:48:29',NULL),(2,'2025/2026','2025-07-01','2026-06-01',1,'2025-07-10 07:48:29','2025-07-10 07:48:29',NULL);
/*!40000 ALTER TABLE `tahun_ajaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin','jayeng30@example.org','2025-07-10 07:48:29','$2y$12$aHsXNuHdciZ28CU5y9Av6.0WGqR4LD7uLxlWba5SqQ8vY3Mbt9aDq','VQaTj8LH9W','2025-07-10 07:48:29','2025-07-10 07:48:29');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-07-10 15:00:19
