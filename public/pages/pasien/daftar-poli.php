<?php
include("../../../config/conn.php");
session_start();

$akses = $_SESSION['akses'];
$username = $_SESSION['username'];
$no_rm = $_SESSION['no_rm'];
$id_pasien = $_SESSION['id'];

if($akses != 'pasien') {
    header('Location: ../..');
    exit();
}

//cek form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and sanitize
    $id_jadwal = htmlspecialchars($_POST['id_jadwal']);
    $keluhan = htmlspecialchars($_POST['keluhan']);

    // Call the function to insert dokter
    $updateMessage = insertDaftarPoli($id_pasien, $id_jadwal, $keluhan, $no_antrian, $conn);

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
                <h1 class="text-2xl font-bold">Daftar Poli</h1>
                <br>
                <div class="mb-4 flex flex-col">
                    <label for="no_rm" class="block text-gray-700 font-bold mb-2 text-xl">Nomor Rekam Medis</label>
                    <input type="text" id="no_rm" name="no_rm" class="form-input rounded-md border border-blue-900 p-2" disabled value="<?php echo $no_rm; ?>">
                </div>
                <div class="mb-4 flex flex-col">
                    <label for="inputPoli" class="block text-gray-700 font-bold mb-2 text-xl">Pilih Poli</label>
                    <select id="inputPoli" name="poli" class="form-select rounded-md border border-blue-900 p-2">
                        <option>Open this select menu</option>
                        <?php
                        // Query to fetch 'poli' options from database
                        $sql = "SELECT * FROM poli";
                        $stmt = $conn->query($sql);

                        // Loop through each 'poli' option and create <option> element
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $selected = ($row['id'] == $id_poli) ? "selected" : ""; // Check if this is the selected option
                            echo "<option value='" . $row['id'] . "' $selected>" . $row['nama_poli'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-4 flex flex-col">
                    <label for="inputJadwal" class="block text-gray-700 font-bold mb-2 text-xl">Pilih Jadwal</label>
                    <select name="id_jadwal" id="inputJadwal" class="form-select rounded-md border border-blue-900 p-2">
                        <option value="900">Open this select menu</option>
                    </select>
                </div>
                <div class="mb-4 flex flex-col">
                <label for="keluhan" class="block text-gray-700 font-bold mb-2 text-xl">keluhan</label>
                <textarea name="keluhan" id="keluhan" class="form-select rounded-md border border-blue-900 p-2"></textarea>
                </div>
                <div class="button-container">
                    <button type="submit" class="submit-button text-white font-bold py-2 px-4 rounded">
                        Daftar
                    </button>
                </div>
                <?php if (isset($_SESSION['update_success'])): ?>
                    <script>
                        alert("Berhasil mendaftar poli");
                        <?php unset($_SESSION['update_success']); // Clear the session variable ?>
                    </script>
                <?php endif; ?>
            </form>

            <h2 class="text-2xl font-bold my-10 text-center">Riwayat Daftar Poli</h2>
            <div class="mt-8 p-8 shadow-lg flex flex-col text-center overflow-auto">
                <table class="table-auto ">
                    <thead class="border-b-2 border-black">
                        <tr>
                            <th class="px-4 py-2">No</th>
                            <th class="px-4 py-2">Poli</th>
                            <th class="px-4 py-2">Dokter</th>
                            <th class="px-4 py-2">Hari</th>
                            <th class="px-4 py-2">Mulai</th>
                            <th class="px-4 py-2">Selesai</th>
                            <th class="px-4 py-2">Antrian</th>
                            <th class="px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        // Assuming $conn is your PDO connection and $id_pasien is set and valid
                        try {
                            // Query to fetch data
                            $poli = $conn->prepare("SELECT d.nama_poli as poli_nama,
                                                        c.nama as dokter_nama,
                                                        b.hari as jadwal_hari,
                                                        b.jadwal_mulai as jadwal_mulai,
                                                        b.jadwal_selesai as jadwal_selesai,
                                                        a.no_antrian as antrian,
                                                        a.id as poli_id
                                                    FROM daftar_poli as a
                                                    INNER JOIN jadwal_periksa as b ON a.id_jadwal = b.id
                                                    INNER JOIN dokter as c ON b.id_dokter = c.id
                                                    INNER JOIN poli as d ON c.id_poli = d.id
                                                    WHERE a.id_pasien = :id_pasien
                                                    ORDER BY a.id DESC");
                            
                            // Bind the parameter
                            $poli->bindParam(':id_pasien', $id_pasien, PDO::PARAM_INT);

                            // Execute the query
                            $poli->execute();

                            $index = 1;
                            if ($poli->rowCount() == 0) {
                                echo "Tidak ada data";
                            } else {
                                while ($p = $poli->fetch(PDO::FETCH_ASSOC)) {
                                    ?>
                                    <tr class='text-center'>
                                        <td class='px-4 py-2'><?= ($index++); ?></td>
                                        <td class='px-4 py-2'><?= $p['poli_nama']; ?></td>
                                        <td class='px-4 py-2'><?= $p['dokter_nama']; ?></td>
                                        <td class='px-4 py-2'><?= $p['jadwal_hari']; ?></td>
                                        <td class='px-4 py-2'><?= $p['jadwal_mulai']; ?></td>
                                        <td class='px-4 py-2'><?= $p['jadwal_selesai']; ?></td>
                                        <td class='px-4 py-2'><?= $p['antrian']; ?></td>
                                        <td class='px-4 py-2 flex justify-center w-full space-x-4'>
                                            <a class='ubah-button text-white font-bold rounded p-2' href='<?php echo str_replace("/daftar-poli.php", "", $_SERVER["PHP_SELF"]) . "../poli/detail_poli.php?id=" . $p['poli_id']; ?>'>Detail</a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                        } catch (PDOException $e) {
                            echo "Error: " . $e->getMessage();
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    <script>
        document.getElementById('inputPoli').addEventListener('change', function() {
            var poliId = this.value;
            loadJadwal(poliId);
        });

        function loadJadwal(poliId) {
            var xhr = new XMLHttpRequest();

            xhr.open('GET', 'http://localhost/test/public/pages/pasien/poli/get_jadwal.php?poli_id=' + poliId, true);

            xhr.onload = function() {
                if (xhr.status == 200) {
                    document.getElementById('inputJadwal').innerHTML = xhr.responseText;
                }
            };
            
            xhr.send();
        }
    </script>

</body>
</html>