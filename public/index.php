<?php
include_once("../config/conn.php");
session_start();

$is_logged_in = isset($_SESSION['login']) && $_SESSION['login'] === true;
$akses = isset($_SESSION['akses']) ? $_SESSION['akses'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poliklinik</title>
    <link rel="stylesheet" href="../src/styles.css">
    <link rel="stylesheet" href="../src/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="navbar flex p-5 bg-blue-900 text-white px-20 text-2xl justify-between">
        <a href="">Poliklinik</a>
        <?php if ($is_logged_in): ?>
            <?php if ($akses === 'admin'): ?>
                <a href="./pages/admin" class="hover:text-blue-500">Dashboard</a>
            <?php elseif ($akses === 'dokter'): ?>
                <a href="./pages/dokter" class="hover:text-blue-500">Dashboard</a>
            <?php elseif ($akses === 'pasien'): ?>
                <a href="./pages/pasien" class="hover:text-blue-500">Dashboard</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="welcome flex flex-col items-center justify-center bg-blue-900 text-white py-24">
        <h1 class="text-5xl mb-4 text-center font-bold">Sistem Temu Janji <br> Pasien - Dokter</h1>
        <p class="text-md text-gray-400">Buat jadwal temu dengan dokter anda</p>
    </div>
    
    <?php if (!$is_logged_in) : ?>
    <div class="login grid grid-cols-1 md:grid-cols-2 gap-4 p-24 mx-auto justify-items-center items-center text-justify">
        <div class="p-4 shadow-lg">
            <!-- Content for the first column -->
            <i class="fa-solid fa-user text-white bg-blue-900 p-2 rounded text-2xl w-12 h-12 text-center"></i>
            <h1 class="font-bold text-2xl">Login Sebagai Pasien</h1>
            <p>Apabila anda adalah seorang Pasien, silahkan Login terlebih dahulu untuk melakukan pendaftaran sebagai Pasien!</p>
            <div class="mt-3">
                <a href="./pages/auth/login-pasien.php" class="text-sm text-blue-800 hover:text-blue-500">Klik Link Berikut -></a>
            </div>
        </div>
        <div class="p-4 shadow-lg">
            <!-- Content for the second column -->
            <i class="fa-solid fa-user text-white bg-blue-900 p-2 rounded text-2xl w-12 h-12 text-center"></i>
            <h1 class="font-bold text-2xl">Login Sebagai Dokter</h1>
            <p>Apabila anda adalah seorang Dokter, silahkan Login terlebih dahulu untuk memulai melayani Pasien!</p>
            <div class="mt-3">
                <a href="./pages/auth/login.php" class="text-sm text-blue-800 hover:text-blue-500">Klik Link Berikut -></a>
            </div>
        </div>
    </div>
    <?php endif; ?> 

    <div class="testimoni flex items-center justify-center flex-col my-10">
        <h1 class="font-bold text-4xl text-center">Testimoni Pasien</h1>
        <p class="text-sm text-gray-500">Para Pasien yang Setia</p>

        <div class="flex flex-col gap-4 mt-6 px-6 justify-center max-w-2xl mx-auto">
            <div class="p-4 shadow-lg flex flex-col items-start max-w-full max-h-48 overflow-auto">
                <!-- Content for the first column -->
                <div class="flex items-center mb-4 text-justify">
                    <i class="fa-solid fa-message fa-flip-horizontal text-4xl text-blue-800 mr-4"></i>
                    <p class="text-md">Pelayanan di web ini sangat cepat dan mudah. Detail histori tercatat lengkap, termasuk
                        catatan obat. Harga pelayanan terjangkau, dokter ramah, pokoke mantab pol!
                    </p>
                </div>
                <div class="pl-12 text-gray-700 text-sm">
                    <p>- Adi, Semarang</p>
                </div>
            </div>
            <div class="p-4 shadow-lg flex flex-col items-start max-w-full max-h-48 overflow-auto">
                <!-- Content for the first column -->
                <div class="flex items-center mb-4 text-justify">
                    <i class="fa-solid fa-message fa-flip-horizontal text-4xl text-blue-800 mr-4"></i>
                    <p class="text-md"> Aku tidak pernah merasakan mudahnya berobat sebelum aku mengenal 
                        web ini. Web yang mudah digunakan dan dokter yang terampil membuat berobat menjadi
                        lebih menyenangkan!
                    </p>
                </div>
                <div class="pl-12 text-gray-700 text-sm">
                    <p>- Ida, Semarang</p>
                </div>
            </div>
        </div>
    </div>

    <?php include("../src/components/footer.php") ?>
</body>
</html>