<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', \App\Models\Setting::get('app_name', 'Koperasi Sejahtera Bersama'))</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd', 400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 800: '#1e40af', 900: '#1e3a8a' },
                        secondary: { 50: '#ecfeff', 100: '#cffafe', 200: '#a5f3fc', 300: '#67e8f9', 400: '#22d3ee', 500: '#06b6d4', 600: '#0891b2' },
                        success: { 50: '#f0fdf4', 100: '#dcfce7', 500: '#22c55e', 600: '#16a34a' },
                        danger: { 50: '#fef2f2', 100: '#fee2e2', 500: '#ef4444', 600: '#dc2626' },
                        warning: { 50: '#fffbeb', 100: '#fef3c7', 500: '#f59e0b', 600: '#d97706' },
                        surface: '#ffffff',
                        background: '#f8fafc',
                        slate: { 50: '#f8fafc', 100: '#f1f5f9', 200: '#e2e8f0', 300: '#cbd5e1', 700: '#334155', 800: '#1e293b', 900: '#0f172a' },
                    },
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                        inter: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.tailwindcss.min.css">

    <style>
        * { font-family: 'Poppins', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Poppins', sans-serif; font-weight: 600; }

        /* Scrollbar Custom */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #64748b; }

        /* Page Transition */
        .page-enter { animation: fadeInUp 0.3s ease-out; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255,255,255,0.8); backdrop-filter: blur(4px);
            z-index: 9999; display: none; align-items: center; justify-content: center;
        }
        .loading-overlay.active { display: flex; }

        .spinner {
            width: 40px; height: 40px; border: 4px solid #e2e8f0;
            border-top-color: #2563eb; border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Card Hover Effect */
        .stat-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.1); }

        /* Sidebar */
        .sidebar-link { transition: all 0.2s ease; }
        .sidebar-link:hover { background: rgba(37, 99, 235, 0.08); }
        .sidebar-link.active { background: #2563eb; color: white; }
        .sidebar-link.active i { color: white; }

        /* DataTables Override */
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e2e8f0 !important; border-radius: 0.5rem !important;
            padding: 0.5rem 1rem !important; font-size: 0.875rem !important;
        }
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #e2e8f0 !important; border-radius: 0.5rem !important;
            padding: 0.25rem 2rem 0.25rem 0.5rem !important;
        }
        table.dataTable tbody tr:hover { background-color: #f8fafc !important; }

        /* Modal */
        .modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 50; }
        .modal-content {
            animation: modalSlideIn 0.3s ease-out;
        }
        @keyframes modalSlideIn {
            from { opacity: 0; transform: scale(0.95) translateY(-10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        /* Notification Badge Pulse */
        .notif-badge { animation: pulse 2s infinite; }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        /* Gradient text */
        .text-gradient {
            background: linear-gradient(135deg, #2563eb, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-background text-slate-800 antialiased">
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="text-center">
            <div class="spinner mx-auto mb-4"></div>
            <p class="text-sm text-slate-500">Memproses...</p>
        </div>
    </div>

    @yield('body')

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.tailwindcss.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
        // ===========================
        // GLOBAL AJAX SETUP
        // ===========================
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // ===========================
        // GLOBAL FUNCTIONS
        // ===========================
        function showLoading() { document.getElementById('loadingOverlay').classList.add('active'); }
        function hideLoading() { document.getElementById('loadingOverlay').classList.remove('active'); }

        // Toast Notification
        const Toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false,
            timer: 3000, timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        function showToast(type, message) {
            Toast.fire({ icon: type, title: message });
        }

        // Confirm Delete
        function confirmAction(title, text, callback) {
            Swal.fire({
                title: title, text: text, icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) callback();
            });
        }

        // Format Rupiah
        function formatRupiah(angka) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
        }

        // Global AJAX Error Handler
        $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
            hideLoading();
            if (jqxhr.status === 422) {
                let errors = jqxhr.responseJSON.errors;
                if (errors) {
                    let errorMessages = Object.values(errors).flat().join('<br>');
                    Swal.fire({ icon: 'error', title: 'Validasi Gagal', html: errorMessages });
                } else if (jqxhr.responseJSON.message) {
                    showToast('error', jqxhr.responseJSON.message);
                }
            } else if (jqxhr.status === 403) {
                showToast('error', 'Anda tidak memiliki akses.');
            } else if (jqxhr.status === 500) {
                showToast('error', 'Terjadi kesalahan server.');
            } else if (jqxhr.status === 419) {
                Swal.fire({
                    icon: 'warning', title: 'Sesi Expired',
                    text: 'Silakan refresh halaman.',
                    confirmButtonText: 'Refresh'
                }).then(() => location.reload());
            }
        });

        // ===========================
        // NOTIFICATION SYSTEM
        // ===========================
        function loadNotifications() {
            $.get('{{ route("notifications.index") }}', function(data) {
                let unreadCount = data.filter(n => !n.is_read).length;
                let badge = $('#notifBadge');
                let list = $('#notifList');

                if (unreadCount > 0) {
                    badge.text(unreadCount).show();
                } else {
                    badge.hide();
                }

                list.html('');
                if (data.length === 0) {
                    list.html('<div class="p-4 text-center text-sm text-slate-400">Tidak ada notifikasi</div>');
                    return;
                }

                data.slice(0, 10).forEach(function(n) {
                    let readClass = n.is_read ? 'bg-white' : 'bg-blue-50';
                    let icon = n.type === 'success' ? 'fa-check-circle text-green-500' :
                               n.type === 'danger' ? 'fa-exclamation-circle text-red-500' :
                               n.type === 'warning' ? 'fa-exclamation-triangle text-yellow-500' :
                               'fa-info-circle text-blue-500';
                    list.append(`
                        <div class="p-3 border-b border-slate-100 hover:bg-slate-50 cursor-pointer ${readClass}" onclick="markNotifRead(${n.id}, '${n.link || ''}')">
                            <div class="flex gap-3">
                                <i class="fas ${icon} mt-1"></i>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-800 truncate">${n.title}</p>
                                    <p class="text-xs text-slate-500 truncate">${n.message}</p>
                                    <p class="text-xs text-slate-400 mt-1">${timeAgo(n.created_at)}</p>
                                </div>
                            </div>
                        </div>
                    `);
                });
            });
        }

        function markNotifRead(id, link) {
            $.post('/notifications/' + id + '/read', function() {
                loadNotifications();
                if (link) window.location.href = link;
            });
        }

        function markAllRead() {
            $.post('{{ route("notifications.read-all") }}', function() {
                loadNotifications();
                showToast('success', 'Semua notifikasi ditandai dibaca.');
            });
        }

        function timeAgo(dateString) {
            const now = new Date();
            const date = new Date(dateString);
            const seconds = Math.floor((now - date) / 1000);
            if (seconds < 60) return 'Baru saja';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' menit lalu';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' jam lalu';
            return Math.floor(seconds / 86400) + ' hari lalu';
        }

        // Load notifications on page load
        $(document).ready(function() {
            @auth
            loadNotifications();
            setInterval(loadNotifications, 30000); // Refresh every 30s
            @endauth
        });
    </script>

    @stack('scripts')
</body>
</html>
