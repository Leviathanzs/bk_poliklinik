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

// Fetch doctor data
$dataDokter = getDokter($conn, $id_dokter);
if ($dataDokter) {
    $nama = htmlspecialchars($dataDokter['nama']);
    $alamat = htmlspecialchars($dataDokter['alamat']);
    $no_hp = htmlspecialchars($dataDokter['no_hp']);
    $poli = htmlspecialchars($dataDokter['id_poli']);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];

    // Call the function to update dokter
    $updateMessage = updateDokter2($id_dokter, $nama, $alamat, $no_hp, $poli, $conn);

    if ($updateMessage === "Record updated successfully") {
        $_SESSION['update_success'] = true;
        header("Location: " . $_SERVER["PHP_SELF"] . "?id=" . $id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poliklinik</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="../../../src/styles.css" rel="stylesheet">
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
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="w-full flex flex-col shadow-lg p-8">
            <h1 class="text-2xl font-bold">Data Dokter</h1>
            <br>
            <div class="mb-4 flex flex-col">
                <label for="nama" class="block text-gray-700 font-bold mb-2 text-xl">Nama</label>
                <input type="text" id="nama" name="nama" class="form-input rounded-md border border-blue-900 p-2" value="<?php echo $nama; ?>">
            </div>
            <div class="mb-4 flex flex-col">
                <label for="alamat" class="block text-gray-700 font-bold mb-2 text-xl">Alamat</label>
                <input type="text" id="alamat" name="alamat" class="form-input rounded-md border border-blue-900 p-2" value="<?php echo $alamat; ?>">
            </div>
            <div class="mb-4 flex flex-col">
                <label for="no_hp" class="block text-gray-700 font-bold mb-2 text-xl">No. HP</label>
                <input type="text" id="no_hp" name="no_hp" class="form-input rounded-md border border-blue-900 p-2" value="<?php echo $no_hp; ?>">
            </div>
            <div class="button-container">
            <button type="submit" class="submit-button text-white font-bold py-2 px-4 rounded">
                Ubah Data
            </button>
            </div>
            <?php if (isset($_SESSION['update_success'])): ?>
                <script>
                    alert("Data dokter berhasil diubah");
                    <?php unset($_SESSION['update_success']); // Clear the session variable ?>
                </script>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>