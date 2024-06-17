<?php
include("../../../../config/conn.php");
session_start();

$akses = $_SESSION['akses'];
$username = $_SESSION['username'];

if($akses != 'admin') {
    header('Location: ../..');
    exit();
}

$id = $nama_obat = $kemasan = $harga = "";
$updateMessage = "";

// Fetch existing data if ID is present in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT * FROM obat WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $nama_obat = $row['nama_obat'];
        $kemasan = $row['kemasan'];
        $harga = $row['harga'];
    } else {
        $updateMessage = "No record found for ID: " . $id;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_GET['id'];
    $nama_obat = htmlspecialchars($_POST['nama_obat']);
    $kemasan = htmlspecialchars($_POST['kemasan']);
    $harga = htmlspecialchars($_POST['harga']);
    
    // Call the function to update dokter
    $updateMessage = updateObat($id, $nama_obat, $kemasan, $harga, $conn);

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
            <h1 class="text-2xl font-bold">Edit Obat</h1>
            <a href="../obat.php">Kembali</a>
        </div>
        <br>
            <div class="mb-4 flex flex-col">
                <label for="nama_obat" class="block text-gray-700 font-bold mb-2 text-xl">Obat</label>
                <input type="text" id="nama_obat" name="nama_obat" class="border rounded-md border-input p-2" value="<?php echo $nama_obat ?>">
            </div>
            <div class="mb-4 flex flex-col">
                <label for="kemasan" class="block text-gray-700 font-bold mb-2 text-xl">kemasan</label>
                <input type="text" id="kemasan" name="kemasan" class="form-input rounded-md border border-blue-900 p-2" value="<?php echo $kemasan ?>">
            </div>
            <div class="mb-4 flex flex-col">
                <label for="harga" class="block text-gray-700 font-bold mb-2 text-xl">Harga</label>
                <input type="text" id="harga" name="harga" class="form-input rounded-md border border-blue-900 p-2" value="<?php echo $harga ?>">
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
            alert("Data obat berhasil diubah");
            <?php unset($_SESSION['update_success']); // Clear the session variable ?>
        </script>
    <?php endif; ?>
</body>
</html>