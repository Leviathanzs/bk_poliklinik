<?php
include("../../../../config/conn.php");
session_start();

$akses = $_SESSION['akses'];
$username = $_SESSION['username'];

if($akses != 'admin') {
    header('Location: ../..');
    exit();
}

$id = $nama = $alamat = $no_ktp = $no_hp = $no_rm = "";
$updateMessage = "";

// Fetch existing data if ID is present in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT * FROM pasien WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $nama = $row['nama'];
        $alamat = $row['alamat'];
        $no_ktp = $row['no_ktp'];
        $no_hp = $row['no_hp'];
        $no_rm = $row['no_rm'];
    } else {
        $updateMessage = "No record found for ID: " . $id;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = htmlspecialchars($_POST['nama']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $no_ktp = htmlspecialchars($_POST['no_ktp']);
    $no_hp = htmlspecialchars($_POST['no_hp']);
    $no_rm = htmlspecialchars($_POST['no_rm']);
    
    // Call the function to update dokter
    $updateMessage = updatePasien($id, $nama, $alamat, $no_hp, $no_ktp, $no_rm, $conn);

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
    <title>Document</title>
    <link href="../../../../src/styles.css" rel="stylesheet">
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
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $id; ?>" method="post" class="flex flex-col shadow-lg" style="padding: 50px;">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Edit Pasien</h1>
            <a href="../pasien.php">Kembali</a>
        </div>
        <br>
            <div class="mb-4 flex flex-col">
                <label for="nama" class="block text-gray-700 font-bold mb-2 text-xl">Nama</label>
                <input type="text" id="nama" name="nama" class="border rounded-md border-input p-2" value="<?php echo $nama ?>">
            </div>
            <div class="mb-4 flex flex-col">
                <label for="alamat" class="block text-gray-700 font-bold mb-2 text-xl">Alamat</label>
                <input type="text" id="alamat" name="alamat" class="form-input rounded-md border border-blue-900 p-2" value="<?php echo $alamat ?>">
            </div>
            <div class="mb-4 flex flex-col">
                <label for="no_ktp" class="block text-gray-700 font-bold mb-2 text-xl">No KTP</label>
                <input type="text" id="no_ktp" name="no_ktp" class="form-input rounded-md border border-blue-900 p-2" value="<?php echo $no_ktp ?>">
            </div>
            <div class="mb-4 flex flex-col">
                <label for="no_hp" class="block text-gray-700 font-bold mb-2 text-xl">No HP</label>
                <input type="text" id="no_hp" name="no_hp" class="form-input rounded-md border border-blue-900 p-2" value="<?php echo $no_hp ?>">
            </div>
            <div class="mb-4 flex flex-col">
                <label for="no_rm" class="block text-gray-700 font-bold mb-2 text-xl">No RM</label>
                <input type="text" id="no_rm" name="no_rm" class="form-input rounded-md border border-blue-900 p-2" value="<?php echo $no_rm ?>" readonly>
            </div>
           
            <div class="button-container">
                <button type="submit" class="submit-button text-white font-bold py-2 px-4 rounded">
                    Submit
                </button>
                <button type="reset" class="reset-button text-white font-bold py-2 px-4 rounded">
                    Reset
                </button>
            </div>
    </form>

    <?php if (isset($_SESSION['update_success'])): ?>
        <script>
            alert("Data pasien berhasil diubah");
            <?php unset($_SESSION['update_success']); // Clear the session variable ?>
        </script>
    <?php endif; ?>
</body>
</html>