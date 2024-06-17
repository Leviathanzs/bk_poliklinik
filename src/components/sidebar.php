<div id="sidebar" class="sidebar fixed inset-y-0 left-0 w-64 bg-gray-900 text-white p-4 transform md:translate-x-0 -translate-x-full transition-transform duration-300 ease-in-out overflow-y-auto">
    <div class="flex items-center mb-6 justify-between">
        <h2 class="text-2xl font-bold text-center"><b>Poli</b>klinik</h2>
        <div class="md:hidden">
            <i class="fa-solid fa-xmark cursor-pointer" onclick="Open()"></i>
        </div>
    </div>
    <p class="mb-6"><?php echo $username ?></p>
    <hr class="my-2 text-gray-600">
    
    <?php if ($akses == 'admin') : ?>
    <ul class="space-y-4">
        <li class="relative flex items-center py-2 px-4 rounded hover:bg-gray-700 justify-between">
            <a href="../admin">
                <i class="fa-solid fa-grip mr-2"></i>
                Dashboard
            </a>
            <span class="absolute right-1 bg-green-900 px-2 py-1 rounded text-xs font-bold">Admin</span>
        </li>
        <li class="relative flex items-center py-2 px-4 rounded hover:bg-gray-700 justify-between">
            <a href="../admin/dokter.php">
                <i class="fa-solid fa-user-doctor mr-2"></i>
                Dokter
            </a>
            <span class="absolute right-1 bg-green-900 px-2 py-1 rounded text-xs font-bold">Admin</span>
        </li>
        <li class="relative flex items-center py-2 px-4 rounded hover:bg-gray-700 justify-between">
            <a href="../admin/pasien.php">
                <i class="fa-solid fa-user-injured mr-2"></i>
                Pasien
            </a>
            <span class="absolute right-1 bg-green-900 px-2 py-1 rounded text-xs font-bold">Admin</span>
        </li>
        <li class="relative flex items-center py-2 px-4 rounded hover:bg-gray-700 justify-between">
            <a href="../admin/poli.php">
                <i class="fa-solid fa-building mr-2"></i>
                Poli
            </a>
            <span class="absolute right-1 bg-green-900 px-2 py-1 rounded text-xs font-bold">Admin</span>
        </li>
        <li class="relative flex items-center py-2 px-4 rounded hover:bg-gray-700 justify-between">
            <a href="../admin/obat.php">
                <i class="fa-solid fa-capsules mr-2"></i>
                Obat
            </a>
            <span class="absolute right-1 bg-green-900 px-2 py-1 rounded text-xs font-bold">Admin</span>
        </li>

        <?php elseif ($akses == 'dokter') : ?>
        <li class="relative flex items-center py-2 px-4 rounded hover:bg-gray-700 justify-between">
            <a href="../dokter">
                <i class="fa-solid fa-grip mr-2" style="width:10px;"></i>
                Dashboard
            </a>
            <span class="absolute right-1 bg-red-900 px-2 py-1 rounded text-xs font-bold">Dokter</span>
        </li>
        <li class="relative flex items-center py-2 px-4 rounded hover:bg-gray-700 justify-between">
            <a href="../dokter/jadwal-periksa.php">
                <i class="fa-solid fa-clipboard mr-2 " style="width:10px;"></i>
                Jadwal Periksa
            </a>
            <span class="absolute right-1 bg-red-900 px-2 py-1 rounded text-xs font-bold">Dokter</span>
        </li>
        <li class="relative flex items-center py-2 px-4 rounded hover:bg-gray-700 justify-between">
            <a href="../dokter/periksa.php">
                <i class="fa-solid fa-stethoscope mr-2" style="width:10px;"></i>
                Periksa Pasien
            </a>
            <span class="absolute right-1 bg-red-900 px-2 py-1 rounded text-xs font-bold">Dokter</span>
        </li>
        <li class="relative flex items-center py-2 px-4 rounded hover:bg-gray-700 justify-between">
            <a href="../dokter/riwayat-pasien.php">
                <i class="fa-solid fa-clock-rotate-left mr-2" style="width:10px;"></i>
                Riwayat Pasien
            </a>
            <span class="absolute right-1 bg-red-900 px-2 py-1 rounded text-xs font-bold">Dokter</span>
        </li>
        <li class="relative flex items-center py-2 px-4 rounded hover:bg-gray-700 justify-between">
            <a href="../dokter/profile.php">
                <i class="fa-solid fa-user-doctor mr-2" style="width:10px;"></i>
                Profil
            </a>
            <span class="absolute right-1 bg-red-900 px-2 py-1 rounded text-xs font-bold">Dokter</span>
        </li>

        <?php elseif ($akses == 'pasien') : ?>
        <li class="relative flex items-center py-2 px-4 rounded hover:bg-gray-700 justify-between">
            <a href="">
                <i class="fa-solid fa-grip mr-2"></i>
                Dashboard
            </a>
            <span class="absolute right-1 bg-gray-500 px-2 py-1 rounded text-xs font-bold">Pasien</span>
        </li>
        <li class="relative flex items-center py-2 px-4 rounded hover:bg-gray-700 justify-between">
            <a href="../pasien/daftar-poli.php">
                <i class="fa-solid fa-hospital-user mr-2"></i>
                Daftar Poli
            </a>
            <span class="absolute right-1 bg-gray-500 px-2 py-1 rounded text-xs font-bold">Pasien</span>
        </li>
        <?php endif; ?>
    </ul>
</div>

<script>
    function Open() {
        const sidebar = document.querySelector('.sidebar');
        const dashboard = document.querySelector('.dashboard');
        const mainContent = document.querySelector('.main-content');

        // Toggle sidebar visibility
        sidebar.classList.toggle('-translate-x-full'); // Toggle hiding/showing sidebar

        // Toggle margin of dashboard and main content
        if (window.innerWidth < 768) { // Check if screen width is less than 1024px (mobile screen)
            dashboard.classList.toggle('md:ml-64');
            mainContent.classList.toggle('md:ml-64');
        } else { // For larger screens
            dashboard.classList.toggle('ml-0');
            mainContent.classList.toggle('ml-0');
        }
    }
</script>
