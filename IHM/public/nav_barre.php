<nav dir="rtl" class="bg-green-800 text-white shadow-lg">
    <div class="container mx-auto px-4">
        <?php 
        include '../components/nav/MobileMenu.php';
        include '../components/nav/DesktopMenu.php';
        ?>
    </div>
</nav>

<?php 
include '../components/nav/MobileSidebar.php'; 
?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const menuButton = document.getElementById('mobile-menu-button');
        const closeButton = document.getElementById('closeSidebar');

        function openSidebar() {
            sidebar.classList.remove('translate-x-full');
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-50');
            document.body.classList.add('overflow-hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('translate-x-full');
            overlay.classList.remove('opacity-50');
            overlay.classList.add('opacity-0', 'pointer-events-none');
            document.body.classList.remove('overflow-hidden');
        }

        menuButton.addEventListener('click', openSidebar);
        closeButton.addEventListener('click', closeSidebar);
        overlay.addEventListener('click', closeSidebar);

        // Close sidebar on window resize if it hits desktop breakpoint
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) { // lg breakpoint
                closeSidebar();
            }
        });
    });
</script>