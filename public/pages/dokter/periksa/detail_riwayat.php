<?php 
session_start();
include('../../../../config/conn.php');

$akses = $_SESSION['akses'];
$username = $_SESSION['username'];
$id_pasien = $_GET['id_pasien']; // Get id_pasien from GET parameter

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
    <link href="../../../../src/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            transition: background-color 0.3s ease-in-out, border-color 0.3s ease-in-out;
        }

        .submit-button:hover {
            background-color: #0E7490; /* Background color on hover */
            border-color: #0E7490; /* Border color on hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        // Query to fetch data
        $query = '
        SELECT 
            periksa.id AS id_periksa,
            periksa.tgl_periksa,
            d.nama AS nama_dokter,
            periksa.catatan AS catatan,
            GROUP_CONCAT(o.nama_obat SEPARATOR ", ") AS obat_list,
            periksa.biaya_periksa,
            dp.keluhan
        FROM 
            periksa
        INNER JOIN 
            daftar_poli AS dp ON periksa.id_daftar_poli = dp.id
        INNER JOIN 
            pasien AS p ON dp.id_pasien = p.id
        INNER JOIN 
            jadwal_periksa AS jp ON dp.id_jadwal = jp.id
        INNER JOIN 
            dokter AS d ON jp.id_dokter = d.id
        INNER JOIN 
            detail_periksa AS dp2 ON dp2.id_periksa = periksa.id
        INNER JOIN 
            obat AS o ON dp2.id_obat = o.id
        WHERE 
            p.id = :id_pasien
        GROUP BY 
            periksa.id';

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_pasien', $id_pasien, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            echo '<p class="text-center">Tidak ada data</p>';
        } else {
            ?>

            <h2 class="text-2xl font-bold my-10 text-center">Riwayat Periksa Pasien</h2>
            <div class="mt-8 p-8 shadow-lg flex flex-col text-center overflow-auto">
                <table class="table-auto w-full">
                    <thead class="border-b-2 border-black">
                        <tr>
                            <th class="px-4 py-2">No</th>
                            <th class="px-4 py-2">Tanggal Periksa</th>
                            <th class="px-4 py-2">Nama Dokter</th>
                            <th class="px-4 py-2">Keluhan</th>
                            <th class="px-4 py-2">Catatan</th>
                            <th class="px-4 py-2">Obat</th>
                            <th class="px-4 py-2">Biaya</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $index = 1;
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                            <tr class='text-center'>
                                <td class='px-4 py-2'><?= $index++; ?></td>
                                <td class='px-4 py-2'><?= htmlspecialchars($row['tgl_periksa']); ?></td>
                                <td class='px-4 py-2'><?= htmlspecialchars($row['nama_dokter']); ?></td>
                                <td class='px-4 py-2'><?= htmlspecialchars($row['keluhan']); ?></td>
                                <td class='px-4 py-2'><?= htmlspecialchars($row['catatan']); ?></td>
                                <td class='px-4 py-2'><?= htmlspecialchars($row['obat_list']); ?></td>
                                <td class='px-4 py-2'><?= htmlspecialchars($row['biaya_periksa']); ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php
        }
        ?>
        <div class="mt-4 text-center">
            <a href="javascript:history.back()" class="flex submit-button mt-1 items-center justify-center">Kembali</a>
        </div>
    </div>
</body>
</html>
