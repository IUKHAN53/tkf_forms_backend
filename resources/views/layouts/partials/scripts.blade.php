<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', () => {
                sidebar.classList.toggle('open');
                sidebarOverlay.classList.toggle('active');
            });
        }
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', () => {
                sidebar.classList.remove('open');
                sidebarOverlay.classList.remove('active');
            });
        }
        // User dropdown menu
        const userMenu = document.getElementById('userMenu');
        const userDropdown = document.getElementById('userDropdown');
        if (userMenu && userDropdown) {
            const trigger = userMenu.querySelector('.user-menu-trigger');
            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('open');
            });
            document.addEventListener('click', (e) => {
                if (!userMenu.contains(e.target)) {
                    userDropdown.classList.remove('open');
                }
            });
        }
        // Sidebar submenu toggle
        const toggleButtons = document.querySelectorAll('.nav-toggle');
        toggleButtons.forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const targetId = btn.getAttribute('data-toggle');
                const submenu = document.getElementById(targetId);
                if (submenu) {
                    submenu.classList.toggle('open');
                    btn.classList.toggle('expanded');
                }
            });
        });
        // Initialize Flatpickr on all date inputs
        if (typeof flatpickr !== 'undefined') {
            flatpickr('input[type="date"]', {
                dateFormat: 'Y-m-d',
                allowInput: true,
                altInput: true,
                altFormat: 'M d, Y',
                animate: true
            });
        }
        // Auto-dismiss alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });
    });
</script>
