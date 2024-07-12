<?php 
session_start();
include('../../../../config/conn.php');

$akses = $_SESSION['akses'];
$username = $_SESSION['username'];

if($akses != 'dokter') {
    header('Location: ../..');
    exit();
}

// if (isset($_GET['id']) && is_numeric($_GET['id'])) {
//     $id = $_GET['id'];

//     // Prepare the SQL statement with INNER JOIN and ID filter
//     $sql = "SELECT a.nama AS nama_pasien, p.tgl_periksa, p.catatan, p.biaya_periksa, o.nama_obat, o.harga, o.kemasan
//     FROM daftar_poli AS d
//     INNER JOIN pasien AS a ON d.id_pasien = a.id
//     INNER JOIN periksa AS p ON d.id = p.id_daftar_poli
//     INNER JOIN detail_periksa AS dp ON p.id = dp.id_periksa
//     INNER JOIN obat AS o ON dp.id_obat = o.id
//     WHERE d.id = :id";

//     // Prepare and execute the statement
//     $stmt = $conn->prepare($sql);
//     $stmt->bindParam(':id', $id, PDO::PARAM_INT);
//     $stmt->execute();

//     // Fetch the result
//     $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
//     // Process the result as needed
//     $nama_obat = []; // Initialize an array to store medication names
//     foreach ($result as $row) {
//         $nama_pasien = $row['nama_pasien'];
//         $tgl_periksa = $row['tgl_periksa'];
//         $catatan = $row['catatan'];
//         $biaya_periksa = $row['biaya_periksa'];
//         $nama_obat[] = $row; // Store each row (medication) in the array
//     }
// }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $biaya_periksa = 150000;
    // Extract form data
    $tgl_periksa = $_POST['tgl_periksa'];
    $catatan = $_POST['catatan'];
    $obat = $_POST['options'];
    $id_daftar_poli = $_POST['id'];
    $id_obat = [];
    $total_biaya_obat = 0;

    for ($i = 0; $i < count($obat); $i++) {
        if (!empty($obat[$i])) {  // Check if the value is not empty
            $data_obat = explode("|", $obat[$i]);
            if (count($data_obat) == 2) { // Check if both ID and harga are present
                $id_obat[] = $data_obat[0];
                $total_biaya_obat += $data_obat[1];
            } else {
                echo "Invalid obat data: " . htmlspecialchars($obat[$i]);
                exit();
            }
        }
    }

    $total_biaya = $biaya_periksa + $total_biaya_obat;

    try {
        // Start transaction
        $conn->beginTransaction();

        // Insert data into the periksa table
        $stmt_periksa = $conn->prepare("UPDATE periksa SET tgl_periksa = :tgl_periksa, catatan = :catatan, biaya_periksa = :biaya_periksa WHERE id_daftar_poli = :id_daftar_poli");
        $stmt_periksa->bindParam(':tgl_periksa', $tgl_periksa);
        $stmt_periksa->bindParam(':catatan', $catatan);
        $stmt_periksa->bindParam(':biaya_periksa', $total_biaya, PDO::PARAM_INT);
        $stmt_periksa->bindParam(':id_daftar_poli', $id_daftar_poli, PDO::PARAM_INT);
        $stmt_periksa->execute();

        // Get the last inserted ID
        $periksa_id = $conn->lastInsertId();

       // Construct the SQL query to update the detail_periksa table
        $query_update = "UPDATE detail_periksa SET id_obat = :id_obat WHERE id_periksa = :id_periksa";
        $stmt_update = $conn->prepare($query_update);

        // Bind parameters and execute the statement for each id_obat
        for ($i = 0; $i < count($id_obat); $i++) {
            // Bind parameters
            $stmt_update->bindParam(':id_obat', $id_obat[$i], PDO::PARAM_INT);
            $stmt_update->bindParam(':id_periksa', $periksa_id, PDO::PARAM_INT);

            // Execute the statement
            $stmt_update->execute();
        }

        // Update status_periksa in daftar_poli table
        $stmt_update_status = $conn->prepare("UPDATE daftar_poli SET status_periksa = '1' WHERE id = :id");
        $stmt_update_status->bindParam(':id', $id_daftar_poli, PDO::PARAM_INT);
        $stmt_update_status->execute();

        // Commit transaction
        $conn->commit();

        // Set session variable for success message
        $_SESSION['success_message'] = true;

        // Redirect to a success page or do other actions as needed
        header('Location: ../periksa.php');
        exit();
    } catch (Exception $e) {
        // Rollback transaction if something goes wrong
        $conn->rollBack();
        echo "Failed: " . $e->getMessage();
    }
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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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
<body class="bg-gray-100 p-10">
    <div class="w-max mx-auto bg-white p-5 rounded-lg shadow-md">
        <?php
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id = $_GET['id'];

            // Prepare the SQL statement with INNER JOIN and ID filter
            $sql = "SELECT a.nama AS nama_pasien, a.alamat, p.tgl_periksa, p.catatan, p.biaya_periksa, o.nama_obat, o.harga, o.kemasan
                    FROM daftar_poli AS d
                    INNER JOIN pasien AS a ON d.id_pasien = a.id
                    INNER JOIN periksa AS p ON d.id = p.id_daftar_poli
                    INNER JOIN detail_periksa AS dp ON p.id = dp.id_periksa
                    INNER JOIN obat AS o ON dp.id_obat = o.id
                    WHERE d.id = :id";

            // Prepare and execute the statement
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Fetch the result
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($result)) {
                $nama_obat = [];
                foreach ($result as $row) {
                    $nama_pasien = $row['nama_pasien'];
                    $tgl_periksa = $row['tgl_periksa'];
                    $catatan = $row['catatan'];
                    $alamat = $row['alamat'];
                    $biaya_periksa = $row['biaya_periksa'];
                    $nama_obat[] = $row; // Store each row (medication) in the array
                }
        ?>
        <div class="text-center mb-5">
            <h2 class="text-xl font-bold">Nota Pembayaran</h2>
            <p class="text-gray-600">Nomor: <?php echo htmlspecialchars($id); ?></p>
        </div>
        <div class="mb-5">
            <p><span class="font-bold">Tanggal:</span> <?php echo htmlspecialchars($tgl_periksa); ?></p>
            <p><span class="font-bold">Nama Pasien:</span> <?php echo htmlspecialchars($nama_pasien); ?></p>
            <p><span class="font-bold">Alamat:</span> <?php echo htmlspecialchars($alamat); ?></p>
        </div>

        <table class="min-w-full divide-y divide-gray-200 mb-5">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Nama Obat</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Kemasan</th>
                    <th class="px-4 py-2 text-right text-sm font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php
                foreach ($nama_obat as $obat) {
                    echo '<tr>';
                    echo '<td class="px-4 py-2">' . htmlspecialchars($obat['nama_obat']) . '</td>';
                    echo '<td class="px-4 py-2">' . htmlspecialchars($obat['kemasan']) . '</td>';
                    echo '<td class="px-4 py-2 text-right">Rp ' . number_format($obat['harga'], 0, ',', '.') . '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
        <div class="text-right mb-5">
            <p class="text-xl font-bold"><span class="font-bold">Jasa Dokter:</span> Rp 150.000,00</p>
            <p class="text-xl font-bold"><span class="font-bold">Total Periksa:</span> Rp <?php echo number_format($biaya_periksa, 0, ',', '.'); ?></p>
        </div>
        <div class="text-center">
            <p class="text-gray-600 text-sm">Terima kasih atas kunjungan Anda!</p>
        </div>
        <?php
            } else {
                echo '<p class="text-center text-red-500">Data tidak ditemukan.</p>';
            }
        } else {
            echo '<p class="text-center text-red-500">ID tidak valid.</p>';
        }
        ?>
    </div>
</body>
</html>