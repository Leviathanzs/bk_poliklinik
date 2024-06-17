<?php
session_start();

$akses = $_SESSION['akses'];
$username = $_SESSION['username'];

if ($akses != 'pasien') {
    header('Location: ../..');
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tailwind CSS</title>
  <link href="../../../src/styles.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="wrapper">
        <?php include ("../../../src/components/sidebar.php") ?>
        <?php include ("../../../src/components/navbar-dashboard.php") ?>
    </div>
</body>
</html>