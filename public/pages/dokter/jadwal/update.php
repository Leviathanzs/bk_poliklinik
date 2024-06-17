<?php
include("../../../../config/conn.php");
session_start();

$akses = $_SESSION['akses'];
$username = $_SESSION['username'];

if($akses != 'dokter') {
    header('Location: ../..');
    exit();
}

$id = $nama = $alamat = $no_hp = $poli = "";
$updateMessage = "";

// Fetch existing data if ID is present in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM jadwal_periksa WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $id_dokter = $row['id_dokter'];
        $hari = $row['hari'];
        $jadwal_mulai = $row['jadwal_mulai'];
        $jadwal_selesai = $row['jadwal_selesai'];
    } else {
        $updateMessage = "No record found for ID: " . $id;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hari = htmlspecialchars($_POST['hari']);
    $jadwal_mulai = htmlspecialchars($_POST['jadwal_mulai']);
    $jadwal_selesai = htmlspecialchars($_POST['jadwal_selesai']);
    
    // Call the function to update dokter
    $updateMessage = updateJadwal ($id, $hari, $jadwal_mulai, $jadwal_selesai, $conn);

    if ($updateMessage === "Record updated successfully") {
        $_SESSION['update_success'] = true;
        header("Location: " . $_SERVER["PHP_SELF"] . "?id=" . $id);
        exit();
    }
}

// Fetch current 'hari' value for the dokter
$currentHariStmt = $conn->prepare('SELECT hari FROM jadwal_periksa WHERE id_dokter = :dokter_id');
$currentHariStmt->bindParam(':dokter_id', $id_dokter);
$currentHariStmt->execute();
$currentHariRow = $currentHariStmt->fetch(PDO::FETCH_ASSOC);
$currentHari = $currentHariRow['hari'];

$hariOptions = getEnumValues($conn, 'jadwal_periksa', 'hari');
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
            <h1 class="text-2xl font-bold">Edit Dokter</h1>
            <a href="../jadwal-periksa.php">Kembali</a>
        </div>
        <br>
            <div class="mb-4 flex flex-col">
                <label for="hari" class="block text-gray-700 font-bold mb-2 text-xl">Hari</label>
                <select id="hari" name="hari" class="form-control rounded-md border border-blue-900 p-2" required value="<?php echo $hari ?>">
                    <?php
                        // Loop through each enum value and create <option> element
                        foreach ($hariOptions as $hari) {
                            $selected = ($hari == $currentHari) ? "selected" : "";
                            echo "<option value='$hari' $selected>$hari</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="mb-4 flex flex-col">
                <label for="jadwal_mulai" class="block text-gray-700 font-bold mb-2 text-xl">Jam Mulai</label>
                <input type="text" id="jadwal_mulai" name="jadwal_mulai" class="form-input rounded-md border border-blue-900 p-2" value="<?php echo $jadwal_mulai ?>">
            </div>
            <div class="mb-4 flex flex-col">
                <label for="jadwal_selesai" class="block text-gray-700 font-bold mb-2 text-xl">Jam Selesai</label>
                <input type="text" id="jadwal_selesai" name="jadwal_selesai" class="form-input rounded-md border border-blue-900 p-2" value="<?php echo $jadwal_selesai ?>">
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
            alert("Data dokter berhasil diupdate");
            <?php unset($_SESSION['update_success']); // Clear the session variable ?>
        </script>
    <?php endif; ?>
</body>
</html>