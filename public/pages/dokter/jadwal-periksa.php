<?php
include("../../../config/conn.php");
session_start();

$akses = $_SESSION['akses'];
$username = $_SESSION['username'];
$id_dokter = $_SESSION['id'];   

if($akses != 'dokter') {
    header('Location: ../..');
    exit();
}

//cek form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and sanitize
    $id_dokter = htmlspecialchars($_POST['id_dokter']);
    $hari = htmlspecialchars($_POST['hari']);
    $jadwal_mulai = htmlspecialchars($_POST['jadwal_mulai']);
    $jadwal_selesai = htmlspecialchars($_POST['jadwal_selesai']);
   

    // Call the function to insert dokter
    $updateMessage = insertJadwal ($id_dokter, $hari, $jadwal_mulai, $jadwal_selesai, $conn);

    if ($updateMessage === "Record inserted successfully") {
        $_SESSION['update_success'] = true;
        header("Location: " . $_SERVER["PHP_SELF"] . "?id=" . $id);
        exit();
    }
}


// Check if the delete button is clicked
if (isset($_GET['delete'])) {
    // Get the ID of the dokter to delete
    $id = $_GET['id'];

    // Call the deleteDokter function to delete the dokter
    deleteJadwal($id, $conn); // Assuming $conn is your database connection
    echo "<script>alert('Dokter berhasil dihapus.');</script>";
}

$hariOptions = getEnumValues($conn, 'jadwal_periksa', 'hari');
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
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="w-full flex flex-col shadow-lg p-8">
                <h1 class="text-2xl font-bold">Kelola Jadwal Periksa</h1>
                <br>
                <input type="hidden" name="id_dokter" value="<?php echo $id_dokter; ?>">
                <div class="mb-4 flex flex-col">
                    <label for="hari" class="block text-gray-700 font-bold mb-2 text-xl">Hari</label>
                    <select id="hari" name="hari" class="form-control rounded-md border border-blue-900 p-2" required>
                        <?php foreach ($hariOptions as $option): ?>
                            <option value="<?php echo $option; ?>"><?php echo $option; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4 flex flex-col">
                    <label for="jadwal_mulai" class="block text-gray-700 font-bold mb-2 text-xl">Jadwal Mulai</label>
                    <input type="time" id="jadwal_mulai" name="jadwal_mulai" class="form-input rounded-md border border-blue-900 p-2" >
                </div>
                <div class="mb-4 flex flex-col">
                    <label for="jadwal_selesai" class="block text-gray-700 font-bold mb-2 text-xl">Jam Selesai</label>
                    <input type="time" id="jadwal_selesai" name="jadwal_selesai" class="form-input rounded-md border border-blue-900 p-2">
                </div>
                <div class="button-container">
                    <button type="submit" class="submit-button text-white font-bold py-2 px-4 rounded">
                        Submit
                    </button>
                </div>
                <?php if (isset($_SESSION['update_success'])): ?>
                    <script>
                        alert("Data jadwal berhasil ditambahkan");
                        <?php unset($_SESSION['update_success']); // Clear the session variable ?>
                    </script>
                <?php endif; ?>
            </form>

            <h2 class="text-2xl font-bold my-10 text-center">Jadwal Periksa</h2>
            <div class="mt-8 p-8 shadow-lg flex flex-col text-center overflow-auto">
                <table class="table-auto ">
                    <thead class="border-b-2 border-black">
                        <tr>
                            <th class="px-4 py-2">No</th>
                            <th class="px-4 py-2">Hari</th>
                            <th class="px-4 py-2">Jam Mulai</th>
                            <th class="px-4 py-2">Jam Selesai</th>
                            <th class="px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query to fetch data
                        $jadwalList = $conn->prepare('SELECT a.nama as nama_dokter, 
                                    b.hari as hari, 
                                    b.id as id_jp,
                                    b.jadwal_mulai as jam_mulai,
                                    b.jadwal_selesai as jam_selesai
                              FROM dokter as a
                              INNER JOIN jadwal_periksa as b
                              ON a.id = b.id_dokter
                              WHERE b.id_dokter = :dokter_id');
                              
                        $jadwalList->bindParam(':dokter_id', $id_dokter);
                        $jadwalList->execute();
                        // Fetch all rows as an associative array
                        $index = 1;
                        if ($jadwalList->rowCount() == 0) {
                            echo '<option value="">Tidak ada jadwal</option>';
                        } else {
                            while ($jd = $jadwalList->fetch()) {
                            ?>
                                <tr class='text-center'>
                                <td class='px-4 py-2'> <?= ($index++); ?></td>
                                <td class='px-4 py-2'> <?= $jd['hari']; ?></td>
                                <td class='px-4 py-2'> <?= $jd['jam_mulai']; ?></td>
                                <td class='px-4 py-2'> <?= $jd['jam_selesai'];  ?></td>
                                <td class='px-4 py-2 flex justify-center w-full space-x-4'>
                                        <a class='ubah-button text-white font-bold rounded p-2' href='<?php echo str_replace("/jadwal-periksa.php", "", $_SERVER["PHP_SELF"]) . "../jadwal/update.php?id=" . $jd['id_jp']; ?>'>Ubah</a>
                                        <form action='' method='GET' class=''>
                                            <input type='hidden' name='id' value='<?= $jd['id_jp'];  ?>'>
                                            <button type='submit' name='delete' class='reset-button text-white font-bold rounded p-2'>Hapus</button>
                                        </form>
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