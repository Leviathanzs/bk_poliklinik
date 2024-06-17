<?php
session_start();
include("../../../config/conn.php");

$akses = $_SESSION['akses'];
$username = $_SESSION['username'];

if($akses != 'admin') {
    header('Location: ../..');
    exit();
}

//cek form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and sanitize
    $nama = htmlspecialchars($_POST['nama']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $no_ktp = htmlspecialchars($_POST['no_ktp']);
    $no_hp = htmlspecialchars($_POST['no_hp']);
    $no_rm = htmlspecialchars($_POST['no_rm']);

    // Call the function to insert dokter
    
    $updateMessage = insertPasien($nama, $alamat, $no_ktp, $no_hp, $no_rm, $conn);

    if ($updateMessage === "Record updated successfully") {
        $_SESSION['update_success'] = true;
        header("Location: " . $_SERVER["PHP_SELF"] . "?id=" . $id);
        exit();
    }
}

if (isset($_GET['delete'])) {
    // Get the ID of the dokter to delete
    $id = $_GET['id'];

    // Call the deleteDokter function to delete the dokter
    deletePasien($id, $conn); // Assuming $conn is your database connection
    echo "<script>alert('Pasien berhasil dihapus.');</script>";
}

$nextNoRm = getNextNoRm($conn);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
                <h1 class="text-2xl font-bold">Tambah Pasien</h1>
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
                    <label for="no_ktp" class="block text-gray-700 font-bold mb-2 text-xl">No KTP</label>
                    <input type="text" id="no_ktp" name="no_ktp" class="form-input rounded-md border border-blue-900 p-2">
                </div>
                <div class="mb-4 flex flex-col">
                    <label for="no_hp" class="block text-gray-700 font-bold mb-2 text-xl">No HP</label>
                    <input type="text" id="no_hp" name="no_hp" class="form-input rounded-md border border-blue-900 p-2">
                </div>
                <div class="mb-4 flex flex-col">
                    <label for="no_rm" class="block text-gray-700 font-bold mb-2 text-xl">No RM</label>
                    <input type="text" id="no_rm" name="no_rm" class="form-input rounded-md border border-blue-900 p-2" value="<?php echo $nextNoRm; ?>" readonly>
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
                        alert("Data pasien berhasil ditambahkan");
                        <?php unset($_SESSION['update_success']); // Clear the session variable ?>
                    </script>
                <?php endif; ?>
            </form>

            <h2 class="text-2xl font-bold my-10 text-center">Data Pasien</h2>
            <div class="mt-8 p-8 shadow-lg flex flex-col text-center overflow-auto">
                <table class="table-auto ">
                    <thead class="border-b-2 border-black">
                        <tr>
                            <th class="px-4 py-2">No</th>
                            <th class="px-4 py-2">Nama</th>
                            <th class="px-4 py-2">Alamat</th>
                            <th class="px-4 py-2">No KTP</th>
                            <th class="px-4 py-2"> No HP</th>
                            <th class="px-4 py-2"> No RM</th>
                            <th class="px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        // Query to fetch data
                        $sql = "SELECT * FROM pasien";

                        // Prepare statement
                        $stmt = $conn->prepare($sql);

                        // Execute statement
                        $stmt->execute();

                        // Fetch all rows as an associative array
                        $pasienList = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $index = 1;
                        foreach ($pasienList as $pasien) { ?>
                            <tr class='text-center'>
                                <td class='px-4 py-2'> <?= ($index++); ?></td>
                                <td class='px-4 py-2'> <?= $pasien['nama']; ?></td>
                                <td class='px-4 py-2'> <?= $pasien['alamat']; ?></td>
                                <td class='px-4 py-2'> <?= $pasien['no_ktp'];  ?></td>
                                <td class='px-4 py-2'> <?= $pasien['no_hp'];  ?></td>
                                <td class='px-4 py-2'> <?= $pasien['no_rm'];  ?></td>
                                <td class='px-4 py-2 flex justify-center w-full space-x-4'>
                                        <a class='ubah-button text-white font-bold rounded p-2' href='<?php echo str_replace("/pasien.php", "", $_SERVER["PHP_SELF"]) . "../pasien/update.php?id=" . $pasien['id']; ?>'>Ubah</a>
                                        <form action='' method='GET' class=''>
                                            <input type='hidden' name='id' value='<?= $pasien['id'];  ?>'>
                                            <button type='submit' name='delete' class='reset-button text-white font-bold rounded p-2'>Hapus</button>
                                        </form>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
            </div>
    </div>
</body>
</html>