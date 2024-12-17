<div class="row">
    <ol class="breadcrumb">
        <li><a href="index.php?page=beranda">
                <em class="fa fa-home"></em>
            </a></li>
        <li class="active">Beranda</li>
    </ol>
</div>
<!--/.row-->

<div class="row">
    <div class="panel panel-default">
        <div class="panel-heading">Dashboard</div>
        <div class="panel-body">

            <!-- Menampilkan Nama Pengguna Sesuai Level -->
            <?php if ($_SESSION['level'] == 'Admin'|| 'Pimpinan'): ?>
                <h3>Selamat Datang.</h3>

                <!-- Info Aplikasi -->
                <?php
                include 'config/database.php';
                $query_site = mysqli_query($kon, "SELECT * FROM tbl_site LIMIT 1");
                $row = mysqli_fetch_array($query_site);
                ?>
                <p>Selamat Datang di Aplikasi Absensi dan Kegiatan Harian Mahasiswa berbasis Web. Sistem ini dirancang untuk memudahkan mahasiswa PKL/magang di <?php echo $row['nama_instansi']; ?> dalam mencatat absensi dan kegiatan harian.</p>
                <!-- Info Aplikasi -->

                <!-- Statistik untuk Admin -->
                <div class="row">
                    <?php
                    // Query data statistik
                    $query_magang = mysqli_query($kon, "SELECT COUNT(*) AS jumlah FROM tbl_mahasiswa WHERE CURDATE() BETWEEN mulai_magang AND akhir_magang");
                    $data_magang = mysqli_fetch_array($query_magang);

                    $query_selesai = mysqli_query($kon, "SELECT COUNT(*) AS jumlah FROM tbl_mahasiswa WHERE CURDATE() > akhir_magang");
                    $data_selesai = mysqli_fetch_array($query_selesai);

                    $query_kegiatan = mysqli_query($kon, "SELECT COUNT(*) AS jumlah FROM tbl_kegiatan");
                    $data_kegiatan = mysqli_fetch_array($query_kegiatan);
                    ?>
                    <div class="col-md-4">
                        <div class="card bg-success text-white text-center p-3">
                            <h4><i class="fa fa-user"></i> Mahasiswa Magang</h4>
                            <h3><?php echo isset($data_magang['jumlah']) ? $data_magang['jumlah'] : 0; ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white text-center p-3">
                            <h4><i class="fa fa-check-circle"></i> Mahasiswa Selesai</h4>
                            <h3><?php echo isset($data_selesai['jumlah']) ? $data_selesai['jumlah'] : 0; ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-warning text-white text-center p-3">
                            <h4><i class="fa fa-tasks"></i> Total Kegiatan</h4>
                            <h3><?php echo isset($data_kegiatan['jumlah']) ? $data_kegiatan['jumlah'] : 0; ?></h3>
                        </div>
                    </div>
                </div>

                <!-- Statistik untuk Admin -->
                <div class="row">
                    <!-- Tambahkan query baru -->
                    <?php
                    $query_absensi_hari_ini = mysqli_query($kon, "SELECT 
                        SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS hadir,
                        SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) AS izin,
                        SUM(CASE WHEN status = 3 THEN 1 ELSE 0 END) AS sakit,
                        SUM(CASE WHEN status = 4 THEN 1 ELSE 0 END) AS alpa
                        FROM tbl_absensi WHERE DATE(tanggal) = CURDATE()");
                    $data_absensi = mysqli_fetch_array($query_absensi_hari_ini);

                    $query_mahasiswa_baru = mysqli_query($kon, "SELECT COUNT(*) AS jumlah FROM tbl_mahasiswa WHERE mulai_magang >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
                    $data_mahasiswa_baru = mysqli_fetch_array($query_mahasiswa_baru);
                    ?>
                    <div class="col-md-3">
                        <div class="card bg-primary text-white text-center p-3">
                            <h4><i class="fa fa-check-circle"></i> Hadir Hari Ini</h4>
                            <h3><?php echo isset($data_absensi['hadir']) ? $data_absensi['hadir'] : 0; ?></h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white text-center p-3">
                            <h4><i class="fa fa-exclamation-circle"></i> Izin Hari Ini</h4>
                            <h3><?php echo isset($data_absensi['izin']) ? $data_absensi['izin'] : 0; ?></h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white text-center p-3">
                            <h4><i class="fa fa-times-circle"></i> Tidak Hadir Hari Ini</h4>
                            <h3><?php echo isset($data_absensi['tidak_hadir']) ? $data_absensi['tidak_hadir'] : 0; ?></h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white text-center p-3">
                            <h4><i class="fa fa-users"></i> Mahasiswa Baru (30 Hari)</h4>
                            <h3><?php echo isset($data_mahasiswa_baru['jumlah']) ? $data_mahasiswa_baru['jumlah'] : 0; ?></h3>
                        </div>
                    </div>
                </div>

                <!-- Tambahkan Daftar Kegiatan Hari Ini -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">Kegiatan Hari Ini</div>
                            <div class="panel-body">
                                <?php
                                $query_kegiatan_hari_ini = mysqli_query($kon, "SELECT 
                                    tbl_kegiatan.kegiatan, 
                                    tbl_mahasiswa.nama, 
                                    tbl_kegiatan.waktu_awal, 
                                    tbl_kegiatan.waktu_akhir 
                                    FROM tbl_kegiatan 
                                    JOIN tbl_mahasiswa ON tbl_kegiatan.id_mahasiswa = tbl_mahasiswa.id_mahasiswa 
                                    WHERE DATE(tbl_kegiatan.tanggal) = CURDATE() 
                                    ORDER BY tbl_kegiatan.waktu_awal");
                                ?>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nama Mahasiswa</th>
                                            <th>Kegiatan</th>
                                            <th>Waktu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = mysqli_fetch_array($query_kegiatan_hari_ini)): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                            <td><?php echo htmlspecialchars($row['kegiatan']); ?></td>
                                            <td><?php echo $row['waktu_awal'] . ' - ' . $row['waktu_akhir']; ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            

            <?php elseif ($_SESSION['level'] == 'Mahasiswa'): ?>
                <h3>Selamat Datang, <?php echo $_SESSION["nama_mahasiswa"]; ?>.</h3>

                <!-- Info Aplikasi -->
                <?php
                $query_site = mysqli_query($kon, "SELECT * FROM tbl_site LIMIT 1");
                $row = mysqli_fetch_array($query_site);
                ?>
                <p>Selamat Datang di Aplikasi Absensi dan Kegiatan Harian Mahasiswa berbasis Web. Sistem ini memudahkan mahasiswa PKL/magang di <?php echo $row['nama_instansi']; ?> untuk mencatat absensi dan kegiatan harian.</p>
                <!-- Info Aplikasi -->

                <!-- Statistik untuk Mahasiswa -->
                <div class="row">
                    <?php
                    $id_mahasiswa = $_SESSION['id_mahasiswa'];

                    $query_kegiatan = mysqli_query($kon, "SELECT COUNT(*) AS jumlah FROM tbl_kegiatan WHERE id_mahasiswa='$id_mahasiswa'");
                    $data_kegiatan = mysqli_fetch_array($query_kegiatan);

                    $query_hadir = mysqli_query($kon, "SELECT COUNT(*) AS jumlah FROM tbl_absensi WHERE id_mahasiswa='$id_mahasiswa' AND status=1");
                    $data_hadir = mysqli_fetch_array($query_hadir);

                    $query_tidak_hadir = mysqli_query($kon, "SELECT COUNT(*) AS jumlah FROM tbl_absensi WHERE id_mahasiswa='$id_mahasiswa' AND status=3");
                    $data_tidak_hadir = mysqli_fetch_array($query_tidak_hadir);
                    ?>
                    <div class="col-md-4">
                        <div class="card bg-success text-white text-center p-3">
                            <h4><i class="fa fa-tasks"></i> Total Kegiatan Anda</h4>
                            <h3><?php echo isset($data_kegiatan['jumlah']) ? $data_kegiatan['jumlah'] : 0; ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white text-center p-3">
                            <h4><i class="fa fa-check-circle"></i> Kehadiran</h4>
                            <h3><?php echo isset($data_hadir['jumlah']) ? $data_hadir['jumlah'] : 0; ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-danger text-white text-center p-3">
                            <h4><i class="fa fa-times-circle"></i> Ketidakhadiran</h4>
                            <h3><?php echo isset($data_tidak_hadir['jumlah']) ? $data_tidak_hadir['jumlah'] : 0; ?></h3>
                        </div>
                    </div>
                </div>
                <!-- Statistik untuk Mahasiswa -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">Kegiatan Terakhir</div>
                            <div class="panel-body">
                                <?php
                                $id_mahasiswa = $_SESSION['id_mahasiswa'];
                                $query_kegiatan_terakhir = mysqli_query($kon, "SELECT 
                                    kegiatan, 
                                    tanggal, 
                                    waktu_awal, 
                                    waktu_akhir 
                                    FROM tbl_kegiatan 
                                    WHERE id_mahasiswa='$id_mahasiswa' 
                                    ORDER BY tanggal DESC 
                                    LIMIT 5");
                                ?>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Kegiatan</th>
                                            <th>Waktu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = mysqli_fetch_array($query_kegiatan_terakhir)): ?>
                                        <tr>
                                            <td><?php echo date('d M Y', strtotime($row['tanggal'])); ?></td>
                                            <td><?php echo htmlspecialchars($row['kegiatan']); ?></td>
                                            <td><?php echo $row['waktu_awal'] . ' - ' . $row['waktu_akhir']; ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tambahkan Grafik Kehadiran -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">Grafik Kehadiran Bulanan</div>
                            <div class="panel-body">
                                <?php
                                $query_kehadiran_bulanan = mysqli_query($kon, "SELECT 
                                    MONTH(tanggal) as bulan, 
                                    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS hadir,
                                    SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) AS izin,
                                    SUM(CASE WHEN status = 3 THEN 1 ELSE 0 END) AS tidak_hadir
                                    FROM tbl_absensi 
                                    WHERE id_mahasiswa='$id_mahasiswa' 
                                    GROUP BY MONTH(tanggal) 
                                    ORDER BY bulan");
                                
                                // Siapkan data untuk chart
                                $bulan = [];
                                $hadir = [];
                                $izin = [];
                                $tidak_hadir = [];
                                
                                while($row = mysqli_fetch_array($query_kehadiran_bulanan)) {
                                    $bulan[] = $row['bulan'];
                                    $hadir[] = $row['hadir'];
                                    $izin[] = $row['izin'];
                                    $tidak_hadir[] = $row['tidak_hadir'];
                                }
                                ?>
                                <canvas id="kehadiranChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tambahkan script untuk chart -->
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var ctx = document.getElementById('kehadiranChart').getContext('2d');
                    var chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: <?php echo json_encode($bulan); ?>,
                            datasets: [
                                {
                                    label: 'Hadir',
                                    data: <?php echo json_encode($hadir); ?>,
                                    backgroundColor: 'rgba(75, 192, 192, 0.6)'
                                },
                                {
                                    label: 'Izin',
                                    data: <?php echo json_encode($izin); ?>,
                                    backgroundColor: 'rgba(255, 206, 86, 0.6)'
                                },
                                {
                                    label: 'Tidak Hadir',
                                    data: <?php echo json_encode($tidak_hadir); ?>,
                                    backgroundColor: 'rgba(255, 99, 132, 0.6)'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                });
                </script>

            <?php endif; ?>
            

            

        </div>
    </div>
</div>
