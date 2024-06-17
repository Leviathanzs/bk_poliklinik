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
    </style>
</head>
<body>
    <?php include ("../../../src/components/sidebar.php") ?>
    <?php include ("../../../src/components/navbar-dashboard.php") ?>

    <div class="main-content p-5 flex-grow md:ml-64 transition-all">
        <h2 class="text-2xl font-bold my-10 text-center">Daftar Riwayat Periksa</h2>
        <div class="mt-8 p-8 shadow-lg flex flex-col text-center overflow-auto">
            <table class="table-auto">
                <thead class="border-b-2 border-black">
                    <tr>
                        <th class="px-4 py-2">No</th>
                        <th class="px-4 py-2">Nama Pasien</th>
                        <th class="px-4 py-2">Alamat</th>
                        <th class="px-4 py-2">No. KTP</th>
                        <th class="px-4 py-2">No. Telepon</th>
                        <th class="px-4 py-2">No. RM</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query to fetch data
                    $query = '
                    SELECT 
                        p.id AS id_pasien,
                        p.nama AS nama_pasien,
                        p.alamat,
                        p.no_ktp,
                        p.no_hp AS no_telepon,
                        p.no_rm
                    FROM 
                        pasien AS p
                    LEFT JOIN 
                        daftar_poli AS dp ON dp.id_pasien = p.id
                    LEFT JOIN 
                        periksa ON periksa.id_daftar_poli = dp.id
                    WHERE 
                        periksa.id IS NOT NULL
                    GROUP BY 
                        p.id';

                    $stmt = $conn->prepare($query);
                    $stmt->execute();

                    $index = 1;
                    if ($stmt->rowCount() == 0) {
                        echo '<tr><td colspan="7" class="text-center px-4 py-2">Tidak ada data</td></tr>';
                    } else {
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                            <tr class='text-center'>
                                <td class='px-4 py-2'><?= ($index++); ?></td>
                                <td class='px-4 py-2'><?= htmlspecialchars($row['nama_pasien']); ?></td>
                                <td class='px-4 py-2'><?= htmlspecialchars($row['alamat']); ?></td>
                                <td class='px-4 py-2'><?= htmlspecialchars($row['no_ktp']); ?></td>
                                <td class='px-4 py-2'><?= htmlspecialchars($row['no_telepon']); ?></td>
                                <td class='px-4 py-2'><?= htmlspecialchars($row['no_rm']); ?></td>
                                <td class='px-4 py-2 flex justify-center w-full space-x-4'>
                                    <a class='submit-button text-white font-bold rounded p-2' href='<?php echo str_replace("/riwayat-pasien.php", "", $_SERVER["PHP_SELF"]) . "../periksa/detail_riwayat.php?id_pasien=" . $row['id_pasien']; ?>'>Detail Riwayat Periksa</a>
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
