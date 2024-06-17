<?php 
session_start();
include('../../../../config/conn.php');

$akses = $_SESSION['akses'];
$username = $_SESSION['username'];

if($akses != 'dokter') {
    header('Location: ../..');
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare the SQL statement with INNER JOIN and ID filter
    $sql = "SELECT a.nama AS nama_pasien, p.tgl_periksa, p.catatan, p.biaya_periksa, o.nama_obat, o.harga, o.kemasan
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
  
    // Process the result as needed
    $nama_obat = []; // Initialize an array to store medication names
    foreach ($result as $row) {
        $nama_pasien = $row['nama_pasien'];
        $tgl_periksa = $row['tgl_periksa'];
        $catatan = $row['catatan'];
        $biaya_periksa = $row['biaya_periksa'];
        $nama_obat[] = $row; // Store each row (medication) in the array
    }
}

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
    <script>
        function addSelect(event) {
            // Get the selected value
            const selectedValue = event.target.value;

            // Get the container for the select elements
            const container = document.getElementById('select-container');

            // If "Choose an option" is selected, remove empty select elements
            if (!selectedValue) {
                // Find all select elements
                const selects = container.getElementsByTagName('select');

                // Remove the last select if it is empty and there are more than one select elements
                if (selects.length > 1) {
                    container.removeChild(container.lastChild);
                }
                return;
            }

            // Clone the first select element if a valid option is selected
            const firstSelect = document.querySelector('select');
            const newSelect = firstSelect.cloneNode(true);

            // Add an event listener to the new select element
            newSelect.onchange = function(event) {
                addSelect(event);
                updateTotalCost();
            };

            // Reset the value of the new select element
            newSelect.value = '';

            // Append the new select element to the container
            container.appendChild(newSelect);
        }

        function updateTotalCost() {
            var obatSelects = document.getElementsByName('options[]');
            var totalCost = 0;
            var biaya_periksa = 150000;
            for (var i = 0; i < obatSelects.length; i++) {
                if (obatSelects[i].value) {
                    var parts = obatSelects[i].value.split('|');
                    var obatPrice = parseFloat(parts[1]); // Get the price part
                    if (!isNaN(obatPrice)) {
                        totalCost += obatPrice; 
                    }
                }
            }
            // After the loop, add the additional biaya_periksa
            totalCost += biaya_periksa;
            document.getElementById('biaya').value = 'Rp' + totalCost.toFixed(2);
        }
    </script>
</head>
<body>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $id; ?>" method="post" class="flex flex-col shadow-lg" style="padding: 50px;">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Detail Periksa</h1>
            <a href="../periksa.php">Kembali</a>
        </div>
        <br>
            <div class="mb-4 flex flex-col">
                <label for="pasien" class="block text-gray-700 font-bold mb-2 text-xl">Nama Pasien</label>
                <input type="text" id="pasien" name="pasien" class="form-input rounded-md border border-blue-900 p-2" readonly value="<?php echo $nama_pasien ?>">
            </div>
            <div class="mb-4 flex flex-col">
                <label for="tgl_periksa" class="block text-gray-700 font-bold mb-2 text-xl">Tanggal Periksa</label>
                <input type="datetime-local" id="tgl_periksa" name="tgl_periksa" class="form-input rounded-md border border-blue-900 p-2" required value="<?php echo $tgl_periksa ?>">
            </div>
            <div class="mb-4 flex flex-col">
                <label for="catatan" class="block text-gray-700 font-bold mb-2 text-xl">Catatan</label>
                <input type="text" id="catatan" name="catatan" class="form-input rounded-md border border-blue-900 p-2" value="<?php echo $catatan ?>">
            </div>
            <label for="catatan" class="block text-gray-700 font-bold mb-2 text-xl">Obat</label>
            <div class="mb-4 border">
                <ul>
                    <?php foreach ($nama_obat as $obat_info): ?>
                        <li class="p-2">
                            <?php echo $obat_info['nama_obat']; ?> - <?php echo $obat_info['kemasan']; ?> - Rp<?php echo $obat_info['harga']; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="mb-4 flex flex-col">
                <label for="biaya" class="block text-gray-700 font-bold mb-2 text-xl">Biaya</label>
                <input type="text" id="biaya" name="biaya" class="form-input rounded-md border border-blue-900 p-2" readonly value="<?php echo 'Rp.' . $biaya_periksa ?>">
            </div>
    </form>
</body>
</html>