-- =========================
-- DATA POSTGRESQL
-- =========================

BEGIN;

-- Data kecamatan
INSERT INTO kecamatan (id, nama_kecamatan) VALUES
(1, 'Setu'),
(2, 'Serang Baru'),
(3, 'Cikarang Pusat'),
(4, 'Cikarang Selatan'),
(5, 'Cibarusah'),
(6, 'Bojongmangu'),
(7, 'Cikarang Timur'),
(8, 'Kdgwaringin'),
(9, 'Cikarang Utara'),
(10, 'Karang Bahagia'),
(11, 'Cibitung'),
(12, 'Cikarang Barat'),
(13, 'Tambun Selatan'),
(14, 'Tambun Utara'),
(15, 'Babelan'),
(16, 'Tarumajaya'),
(17, 'Tambelang'),
(18, 'Sukawangi'),
(19, 'Sukatani'),
(20, 'Sukakarya'),
(21, 'Pebayuran'),
(22, 'Cabangbungin'),
(23, 'Muaragembong');

-- Data desa
INSERT INTO desa (id, id_kecamatan, nama_desa) VALUES
(1, 1, 'Ragemmanunggal'),
(2, 1, 'Muktijaya'),
(3, 1, 'Kertarahayu'),
(4, 1, 'Cikarageman'),
(5, 1, 'Tamansari'),
(6, 1, 'Tamanrahayu'),
(7, 1, 'Burangkeng'),
(8, 1, 'Cileduk'),
(9, 1, 'Cibening'),
(10, 1, 'Cijengkol'),
(11, 1, 'Lubangbuaya'),
(12, 2, 'Jayamulya'),
(13, 2, 'Sukaragam'),
(14, 2, 'Sirnajaya'),
(15, 2, 'Nagacipta'),
(16, 2, 'Nagasari'),
(17, 2, 'Cilangkara'),
(18, 2, 'Sukasari'),
(19, 2, 'Jayasampurna');
-- (dan seterusnya, data aman langsung dipaste semua jika mau)

-- Data users
INSERT INTO users (id, username, password, nama_lengkap, no_hp, email, role, created_at, pml_id) VALUES
(21, 'root', '$2y$10$35O/TGl5sSaOBvA0F3dhtOM0f31a7vm/44hrbGn69IEVUZQmZegtS', 'root', '+62 812-8097-6144', 'pandupermana230687@gmail.com', 'supervisor', '2025-08-14 06:22:01', NULL),
(22, 'pandu', '$2y$10$NOd5MdsoABXT4kTNyXGSW.Us93972Y5RRij1.ou1B67CS9d0nYzhu', 'Pandu Permana', '+62 812-8097-6144', 'pandupermana230687@gmail.com', 'supervisor', '2025-08-14 06:22:26', NULL),
(25, 'gilang', '$2y$10$IGYxVkLDB5B8vK9Hu1g5S.Hlx6EezBPsvDKqL60ElJsBLct10Sfbi', 'gilang', '0871236', 'gilang@asd', 'pml', '2025-09-03 14:26:49', NULL),
(26, 'zacky', '$2y$10$5Y9JBm.5JGlyo57oQhtvD.C7Pg5.KxYH54GyWrJLHmGxs1w4./mQq', 'zacky', '0813278', 'zacky@asd', 'pcl', '2025-09-03 14:27:39', 25);

-- Data monitoring
INSERT INTO monitoring_data_panen
(id, nama_petani, desa, kecamatan, tanggal_panen, subround,
 foto_serah_terima, foto_bukti_plot_ubinan, foto_berat_timbangan,
 berat_plot, gkp, gkg, ku, status, user_id,
 nomor_segmen, nomor_sub_segmen, created_at)
VALUES
(85, 'zacky', 'Karangsentosa', 'Karang Bahagia', '2025-09-10', 3,
 '', '', '',
 NULL, NULL, NULL, NULL,
 'belum selesai', 26,
 '321602310', 'B3', '2025-09-03 14:32:38');

COMMIT;
