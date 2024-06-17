<?php
session_start();
include_once("../../../config/conn.php");

$is_logged_in = isset($_SESSION['login']) && $_SESSION['login'] === true;

if ($is_logged_in) {
    header('Location: ../../');
    exit();
}

if (isset($_POST['submit'])) {
    $username = stripslashes($_POST['username']);
    $password = $_POST['password'];

    if ($username == 'admin' && $password == 'admin') {
        $_SESSION['login'] = true;
        $_SESSION['id'] = null;
        $_SESSION['username'] = 'admin';
        $_SESSION['akses'] = 'admin';

        header("Location: ../admin");
        exit();
    } else {
        $cek_username = $conn->prepare("SELECT * FROM dokter WHERE nama = :username");
        try {
            $cek_username->bindParam(':username', $username, PDO::PARAM_STR);
            $cek_username->execute();

            if ($cek_username->rowCount() == 1) {
                $baris = $cek_username->fetch(PDO::FETCH_ASSOC);
                if ($password == $baris['alamat']) {
                    $_SESSION['login'] = true;
                    $_SESSION['id'] = $baris['id'];
                    $_SESSION['username'] = $baris['nama'];
                    $_SESSION['akses'] = 'dokter';

                    header('Location: ../dokter');
                    exit();
                }
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = $e->getMessage();
        }
    }
    $_SESSION['error'] = 'Username dan Password Tidak Cocok';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poliklinik</title>
    <link rel="stylesheet" href="../../../src/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        .form-container {
            height: 100vh; /* Full height of the viewport */
        }
        .border-top-blue {
            border-top: 5px solid blue; /* Set the border top color and width */
        }
    </style>
</head>
<body>
    <div class="d-flex justify-content-center align-items-center form-container">
            <form method="POST" action="" class="bg-white p-4 rounded shadow-lg w-100 border-top-blue" style="max-width: 400px;">
                <div class="text-center fs-2">
                    <a href="../.." style="text-decoration: none; font-size: 32px; color: black;"><b>Poli</b>klinik</a>
                </div>
                <div class="text-center mb-5 font-bold fs-3">
                    <p>Sign In</p>
                </div>
                <div class="mb-3 flex">
                    <input type="text" class="form-control" name="username" id="nama" placeholder="Username | Case Sensitive" aria-describedby="nama">
                    <i class="fa-solid fa-envelope text-2xl text-gray-700 p-2 border"></i>
                </div>
                <div class="mb-3 flex">
                    <input type="password" class="form-control" name="password" id="exampleInputPassword1" placeholder="Password | Case Sensitive">
                    <i class="fa-solid fa-lock text-2xl text-gray-700 p-2 border"></i>
                </div>
                <div class="flex justify-content-between align-items-center p-2">
                    <div>
                        <input type="checkbox" class="form-check-input" id="exampleCheck1">
                        <label class="form-check-label" for="exampleCheck1">Remember Me</label>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary flex">Submit</button>
                </div>
                <?php if (!empty($_SESSION['error'])): ?>
                    <p style="color: red;" class="text-sm mb-4"><?= $_SESSION['error'] ?></p>
                    <?php unset($_SESSION['error']); ?>
                 <?php endif; ?>
            </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>