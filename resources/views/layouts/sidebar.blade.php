<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Celengan Monitoring</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap">
    <link rel="stylesheet" href="{{ asset('css/side.css') }}">
</head>
<body>

    <!-- Toggle button -->
    <button class="menu-toggle" onclick="toggleSidebar()">☰</button>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="logo-container">
            <img src="/images/Screenshot 2025-05-20 002803.png" alt="Yayasan Tri Asih" class="logo">
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('students.index') }}" class="menu-item {{ request()->is('students*') ? 'active' : '' }}">Management</a>
        </nav>
    </aside>

    <!-- Overlay -->
    <div class="sidebar-overlay" onclick="closeSidebar()"></div>



    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : 'auto';
            document.body.classList.toggle('sidebar-open', sidebar.classList.contains('active'));
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = 'auto';
            document.body.classList.remove('sidebar-open');
        }

        // Close sidebar when menu item clicked
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.menu-item').forEach(item => {
                item.addEventListener('click', () => {
                    if (window.innerWidth <= 768) setTimeout(closeSidebar, 200);
                });
            });
        });

        // Close sidebar on window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                document.getElementById('sidebar').classList.remove('active');
                document.querySelector('.sidebar-overlay').classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });
    </script>

</body>
</html>
