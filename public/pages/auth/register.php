<?php
session_start();
include("../../../config/conn.php");


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //Get form value
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_ktp = $_POST['no_ktp'];
    $no_hp = $_POST['no_hp'];
    
    //Check pasien if already registered by no_ktp
    $query_check_pasien = "SELECT id, nama, no_rm FROM pasien WHERE no_ktp = :no_ktp";
    $stmt_check_pasien = $conn->prepare($query_check_pasien);
    $stmt_check_pasien->bindParam(':no_ktp', $no_ktp);
    $stmt_check_pasien->execute();
    $result_check_pasien = $stmt_check_pasien->fetch(PDO::FETCH_ASSOC);

    if ($result_check_pasien) {
        // Pasien already registered, handle accordingly
        $pasien_id = $result_check_pasien['id'];
        $pasien_nama = $result_check_pasien['nama'];
        $pasien_no_rm = $result_check_pasien['no_rm'];

        if ($pasien_nama != $nama) {
            echo "<script>
                alert('Nama pasien tidak sesuai dengan nomor KTP yang terdaftar.');
                window.location.href = '" . $_SERVER['PHP_SELF'] . "';
            </script>";
            exit();
        }
        
        $_SESSION['login'] = true;
        $_SESSION['id'] = $row['id'];
        $_SESSION['username'] = $nama;
        $_SESSION['no_rm'] = $row['no_rm'];
        $_SESSION['akses'] = 'pasien';

        header("Location: ../pasien");
        exit();
    }

    $updateMessage =  insertPasien($nama, $alamat, $no_hp, $no_ktp, $no_rm, $conn);
    $_SESSION['login'] = true;
    $_SESSION['id'] = $row['id'];
    $_SESSION['username'] = $nama;
    $_SESSION['no_rm'] = $row['no_rm'];
    $_SESSION['akses'] = 'pasien';

    if ($updateMessage === "Record updated successfully") {
        header("Location: ../pasien");
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../../src/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        .form-container {
            height: 100vh; /* Full height of the viewport */
        }
        .border-top-blue {
            border-top: 5px solid blue; /* Set the border top color and width */
        }
        .form-control {
            flex-grow: 1;
        }
        .input-group {
            display: flex;
            align-items: center;
        }
        .input-group i {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f1f1f1;
            border-radius: 0 0.25rem 0.25rem 0;
        }
    </style>
</head>
<body>
    <div class="d-flex justify-content-center align-items-center form-container">
            <form method="POST" action="" class="bg-white p-4 rounded shadow-sm w-100 border-top-blue" style="max-width: 400px;">
                <div class="text-center">
                    <h1><b>Poli</b>klinik</h1>
                </div>
                <div class="text-center mb-5 font-bold fs-4">
                    <p>Register</p>
                </div>
                <div class="mb-3 input-group">
                    <input type="text" class="form-control" name="nama" id="nama" placeholder="Nama" required>
                    <i class="fa-solid fa-envelope text-2xl text-gray-700 p-2 border"></i>
                </div>
                <div class="mb-3 input-group">
                    <input type="text" class="form-control" name="alamat" id="alamat" placeholder="Alamat" required>
                    <i class="fa-solid fa-location-dot text-2xl text-gray-700 p-2 border"></i>
                </div>
                <div class="mb-3 input-group">
                    <input type="text" class="form-control" name="no_ktp" id="no_ktp" placeholder="No KTP" required>
                    <i class="fa-solid fa-id-card text-2xl text-gray-700 p-2 border"></i>
                </div>
                <div class="mb-3 input-group">
                    <input type="text" class="form-control" name="no_hp" id="no_hp" placeholder="No HP" required>
                    <i class="fa-solid fa-lock text-2xl text-gray-700 p-2 border"></i>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1">
                    <label class="form-check-label" for="exampleCheck1">I Agree To <a href="#" style="text-decoration: none;">Terms and Conditions</a></label>
                </div>
                <button type="submit" name="submit" class="btn btn-primary w-100">Submit</button>
                <div class="mt-2">
                    <p>Already have an account? <a href="login-pasien.php" class="text-decoration-none">Sign in here!</a></p>
                </div>
                <?php if (!empty($_SESSION['error'])): ?>
                    <p class="text-red-500 text-sm mb-4"><?= $_SESSION['error'] ?></p>
                    <?php unset($_SESSION['error']); ?>
                 <?php endif; ?>

                 <?php if (isset($_SESSION['update_success'])): ?>
                    <script>
                        alert("Berhasil mendaftar!");
                        <?php unset($_SESSION['update_success']); // Clear the session variable ?>
                    </script>
                <?php endif; ?>
            </form>
    </div>
</body>
</html>