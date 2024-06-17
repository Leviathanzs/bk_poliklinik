<?php 
session_start();
include('../../../config/conn.php');

$akses = $_SESSION['akses'];
$username = $_SESSION['username'];
$id_dokter = $_SESSION['id'];

if($akses != 'dokter') {
    header('Location: ../..');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poliklinik</title>
    <link href="../../../src/styles.css" rel="stylesheet">
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
    </style>
</head>
<body>
        <?php include ("../../../src/components/sidebar.php") ?>
        <?php include ("../../../src/components/navbar-dashboard.php") ?>
    
        <div class="main-content p-5 flex-grow md:ml-64 transition-all">
            <h2 class="text-2xl font-bold my-10 text-center">Daftar Periksa Pasien</h2>
            <div class="mt-8 p-8 shadow-lg flex flex-col text-center overflow-auto">
                <table class="table-auto ">
                    <thead class="border-b-2 border-black">
                        <tr>
                            <th class="px-4 py-2">No</th>
                            <th class="px-4 py-2">Nama Pasien</th>
                            <th class="px-4 py-2">Keluhan</th>
                            <th class="px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query to fetch data
                        $daftarPeriksa = $conn->prepare("SELECT a.nama as nama_pasien,
                                            b.keluhan as keluhan,
                                            b.id as dp_id,
                                            c.id_dokter as id_dokter,
                                            b.status_periksa as status_periksa
                                        FROM daftar_poli as b
                                        INNER JOIN jadwal_periksa as c ON b.id_jadwal = c.id
                                        INNER JOIN pasien as a ON b.id_pasien = a.id
                                        WHERE c.id_dokter = $id_dokter
                                        ORDER BY b.id DESC");
                              
                        $daftarPeriksa->execute();
                        // Fetch all rows as an associative array
                        $index = 1;
                        if ($daftarPeriksa->rowCount() == 0) {
                            echo '<option value="">Tidak ada jadwal</option>';
                        } else {
                            while ($dp = $daftarPeriksa->fetch()) {
                            ?>
                                <tr class='text-center'>
                                <td class='px-4 py-2'> <?= ($index++); ?></td>
                                <td class='px-4 py-2'> <?= $dp['nama_pasien']; ?></td>
                                <td class='px-4 py-2'> <?= $dp['keluhan']; ?></td>
                                <td class='px-4 py-2 flex justify-center w-full space-x-4'>
                                <?php
                                    // Check the status_periksa value
                                    if ($dp['status_periksa'] == '1') {
                                        // If status_periksa is 1, change the link to 'detail'
                                        ?>
                                        <div class="flex items-center ubah-button text-white font-bold rounded p-2">
                                            <i class="fa-solid fa-notes-medical"></i>
                                            <a class='' href='<?php echo str_replace("/periksa.php", "", $_SERVER["PHP_SELF"]) . "../periksa/detail.php?id=" . $dp['dp_id']; ?>'>
                                                Detail
                                            </a>
                                        </div>
                                    <?php } else { ?>
                                        <div class="flex items-center submit-button text-white font-bold rounded p-2">
                                            <i class="fa-solid fa-stethoscope pe-2"></i>
                                            <a class='' href='<?php echo str_replace("/periksa.php", "", $_SERVER["PHP_SELF"]) . "../periksa/create.php?id=" . $dp['dp_id']; ?>'>
                                                Periksa
                                            </a>
                                        </div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php
                            }
                        }
                        ?> 
                    </tbody>
                </table>
            </div>
        </div>
</body>
</html>