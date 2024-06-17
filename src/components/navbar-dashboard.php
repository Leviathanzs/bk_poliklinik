<div class="dashboard flex-grow md:ml-64 transition-all">
    <nav class="flex justify-between items-center p-4 text-black shadow-lg z-30">
    <!-- Left navbar links -->
        <ul class="flex space-x-4 items-center text-2xl">
            <div class="md:hidden">
                <i class="fa-solid fa-bars cursor-pointer" onclick="Open()"></i>
            </div>
            <h1>Dashboard <?php echo $akses ?></h1>
        </ul>

        <!-- Right navbar links -->
        <ul class="flex space-x-4">
            <li>
                <i class="fa-solid fa-lock"></i>
                <a href="../auth/destroy.php" class="nav-link hover:text-blue-500">Logout</a>
            </li>
        </ul>
    </nav>
</div>