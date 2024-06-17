<?php
include("../../../config/conn.php");
session_start();

$akses = $_SESSION['akses'];
$username = $_SESSION['username'];

if($akses != 'admin') {
    header('Location: ../..');
    exit();
}

$id_dokter = 0;

// Check if the form is submitted and a success query parameter is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['success'])) {
    // Display an alert
    echo "<script>alert('Data dokter berhasil ditambahkan');</script>";
}

//cek form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and sanitize
    $nama = htmlspecialchars($_POST['nama']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $no_hp = htmlspecialchars($_POST['no_hp']);
    $poli = htmlspecialchars($_POST['poli']);

    // Call the function to insert dokter
    $updateMessage = insertDokter($nama, $alamat, $no_hp, $poli, $conn);

    if ($updateMessage === "Record updated successfully") {
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
    deleteDokter($id, $conn); // Assuming $conn is your database connection
    echo "<script>alert('Dokter berhasil dihapus.');</script>";
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
                <h1 class="text-2xl font-bold">Tambah Dokter</h1>
                <br>
                <div class="mb-4 flex flex-col">
                    <label for="nama" class="block text-gray-700 font-bold mb-2 text-xl">Nama</label>
                    <input type="text" id="nama" name="nama" class="form-input rounded-md border border-blue-900 p-2">
                </div>
                <div class="mb-4 flex flex-col">
                    <label for="alamat" class="block text-gray-700 font-bold mb-2 text-xl">Alamat</label>
                    <input type="text" id="alamat" name="alamat" class="form-input rounded-md border border-blue-900 p-2" >
                </div>
                <div class="mb-4 flex flex-col">
                    <label for="no_hp" class="block text-gray-700 font-bold mb-2 text-xl">No HP</label>
                    <input type="text" id="no_hp" name="no_hp" class="form-input rounded-md border border-blue-900 p-2">
                </div>
                <div class="mb-4 flex flex-col">
                    <label for="poli" class="block text-gray-700 font-bold mb-2 text-xl">Poli</label>
                    <select id="poli" name="poli" class="form-select rounded-md border border-blue-900 p-2">
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
                <div class="button-container">
                    <button type="submit" class="submit-button text-white font-bold py-2 px-4 rounded">
                        Submit
                    </button>
                    <button type="reset" class="reset-button text-white font-bold py-2 px-4 rounded">
                        Reset
                    </button>
                </div>
                <?php if (isset($_SESSION['update_success'])): ?>
                    <script>
                        alert("Data dokter berhasil ditambahkan");
                        <?php unset($_SESSION['update_success']); // Clear the session variable ?>
                    </script>
                <?php endif; ?>
            </form>

            <h2 class="text-2xl font-bold my-10 text-center">Data Dokter</h2>
            <div class="mt-8 p-8 shadow-lg flex flex-col text-center overflow-auto">
                <table class="table-auto ">
                    <thead class="border-b-2 border-black">
                        <tr>
                            <th class="px-4 py-2">No</th>
                            <th class="px-4 py-2">Nama</th>
                            <th class="px-4 py-2">Alamat</th>
                            <th class="px-4 py-2">No HP</th>
                            <th class="px-4 py-2">Poli</th>
                            <th class="px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query to fetch data
                        $sql = "SELECT dokter.*, poli.nama_poli FROM dokter JOIN poli ON dokter.id_poli = poli.id";

                        // Prepare statement
                        $stmt = $conn->prepare($sql);

                        // Execute statement
                        $stmt->execute();

                        // Fetch all rows as an associative array
                        $dokterList = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $index = 1;
                        foreach ($dokterList as $dokter) { ?>
                            <tr class='text-center'>
                                <td class='px-4 py-2'> <?= ($index++); ?></td>
                                <td class='px-4 py-2'> <?= $dokter['nama']; ?></td>
                                <td class='px-4 py-2'> <?= $dokter['alamat']; ?></td>
                                <td class='px-4 py-2'> <?= $dokter['no_hp'];  ?></td>
                                <td class='px-4 py-2'> <?= $dokter['nama_poli'];  ?></td>
                                <td class='px-4 py-2 flex justify-center w-full space-x-4'>
                                        <a class='ubah-button text-white font-bold rounded p-2' href='<?php echo str_replace("/dokter.php", "", $_SERVER["PHP_SELF"]) . "../dokter/update.php?id=" . $dokter['id']; ?>'>Ubah</a>
                                        <form action='' method='GET' class=''>
                                            <input type='hidden' name='id' value='<?= $dokter['id'];  ?>'>
                                            <button type='submit' name='delete' class='reset-button text-white font-bold rounded p-2'>Hapus</button>
                                        </form>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
</body>
</html>