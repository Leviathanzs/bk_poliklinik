<?php
session_start();

$akses = $_SESSION['akses'];
$username = $_SESSION['username'];

if ($akses != 'dokter') {
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

        <div class="main-content md:ml-64 transition-all p-8">
            <div class="container-fluid py-4">
                <div class="row chart-container">
                    <!-- Chart 1: Bar Chart -->
                    <div class="col-12 col-lg-6 p-4 d-flex align-items-stretch">
                        <div class="bg-white rounded-lg shadow-md p-4 w-100">
                            <h2 class="text-xl font-semibold mb-4">Monthly Sales</h2>
                            <canvas id="barChart"></canvas>
                        </div>
                    </div>
                    <!-- Chart 2: Pie Chart -->
                    <div class="col-12 col-lg-6 p-4 d-flex align-items-stretch">
                        <div class="bg-white rounded-lg shadow-md p-4 w-100">
                            <h2 class="text-xl font-semibold mb-4">Product Distribution</h2>
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <script>
        // Sample data for the charts
        const monthlySalesData = {
            labels: ['January', 'February', 'March', 'April', 'May', 'June'],
            datasets: [{
            label: 'Sales',
            data: [5000, 7000, 6000, 9000, 8000, 10000],
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
            }]
        };

        const productDistributionData = {
            labels: ['Product A', 'Product B', 'Product C'],
            datasets: [{
            label: 'Distribution',
            data: [30, 40, 30],
            backgroundColor: [
                'rgba(255, 99, 132, 0.5)',
                'rgba(54, 162, 235, 0.5)',
                'rgba(255, 206, 86, 0.5)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)'
            ],
            borderWidth: 1
            }]
        };

        // Create bar chart
        var ctx1 = document.getElementById('barChart').getContext('2d');
        var barChart = new Chart(ctx1, {
            type: 'bar',
            data: monthlySalesData,
            options: {
            scales: {
                y: {
                beginAtZero: true
                }
            }
            }
        });

        // Create pie chart
        var ctx2 = document.getElementById('pieChart').getContext('2d');
        var pieChart = new Chart(ctx2, {
            type: 'pie',
            data: productDistributionData
        });
    </script>
</body>
</html>