<?php
include("../../../../config/conn.php");
session_start();

$akses = $_SESSION['akses'];
$username = $_SESSION['username'];
$no_rm = $_SESSION['no_rm'];
$id_pasien = $_SESSION['id'];

if($akses != 'pasien') {
    header('Location: ../..');
    exit();
}

$id_poli = $_GET['id']
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poliklinik</title>
    <link href="../../../../src/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .border {
            border: 1px solid black;
            border-radius: 5px;
        }
        .submit-button {
            background-color: #1E40AF; /* Default background color */
            border: none;
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease-in-out, border-color 0.3s ease-in-out;
        }

        .submit-button:hover {
            background-color: #0E7490; /* Background color on hover */
            border-color: #0E7490; /* Border color on hover */
        }
        .reset-button {
            background-color: #D33715; /* Default background color */
            border: none;
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease-in-out, border-color 0.3s ease-in-out;
        }

        .reset-button:hover {
            background-color: #FF4848; /* Background color on hover */
            border-color: #0E7490; /* Border color on hover */
        }
        .ubah-button {
            background-color: #ffc107; /* Default background color */
            border: none;
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease-in-out, border-color 0.3s ease-in-out;
        }

        .ubah-button:hover {
            background-color: #FFD148; /* Background color on hover */
            border-color: #FFD148; /* Border color on hover */
        }

        .customHover:hover {
            background-color: #1E40AF;
        }

        .box {
            display: inline-block;
            padding: 10px 15px;
            background-color: #1E40AF;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<h2 class="text-2xl font-bold text-center bg-blue-900 text-white p-2">Detail Poli</h2>
            <div class="mt-8 p-8 shadow-lg flex flex-col text-center overflow-auto">
                        <?php
                        // Query to fetch data
                        $poli = $conn->prepare("SELECT d.nama_poli as poli_nama,
                                                    c.nama as dokter_nama,
                                                    b.hari as jadwal_hari,
                                                    b.jadwal_mulai as jadwal_mulai,
                                                    b.jadwal_selesai as jadwal_selesai,
                                                    a.no_antrian as antrian,
                                                    a.id as poli_id
                                                    
                                                    FROM daftar_poli as a
                                                    
                                                    INNER JOIN jadwal_periksa as b
                                                        ON a.id_jadwal = b.id
                                                    INNER JOIN dokter as c
                                                        ON b.id_dokter = c.id
                                                    INNER JOIN poli as d
                                                        ON c.id_poli = d.id
                                                    WHERE a.id = $id_poli");
                        
                        $poli->execute();
                        if ($poli->rowCount() == 0) {
                            echo "Tidak ada data";
                        } else {
                            while($p = $poli->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                <div class="flex flex-col border-b border-gray-200">
                                    <div class="flex-1 p-4"><h1 class="font-bold">Nama Poli</h1> 
                                        <br><?= $p['poli_nama']; ?></div>
                                    <div class="flex-1 p-4"><h1 class="font-bold">Nama Dokter</h1> 
                                        <br><?= $p['dokter_nama']; ?></div>
                                    <div class="flex-1 p-4"><h1 class="font-bold">Hari</h1> 
                                        <br><?= $p['jadwal_hari']; ?></div>
                                    <div class="flex-1 p-4"><h1 class="font-bold">Jam Mulai</h1> 
                                        <br><?= $p['jadwal_mulai']; ?></div>
                                    <div class="flex-1 p-4"><h1 class="font-bold">Jam Selesai</h1> 
                                        <br><?= $p['jadwal_selesai']; ?></div>
                                    <div class="flex-1 p-4"><h1 class="font-bold">Antrian</h1> 
                                        <br>
                                        <div class="box text-white">
                                            <?= $p['antrian']; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            }
                        }
                        ?>
            </div>
            <a href="../daftar-poli.php" class="customHover flex justify-center items-center bg-blue-900 text-white p-2">Kembali</a>
        </div>

</body>
</html>