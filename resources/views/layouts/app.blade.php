<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Inventori') - Inventory System</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom Modern Styling -->
    <style>
        :root {
            --primary: #4361ee;
            --primary-hover: #3a0ca3;
            --secondary: #4cc9f0;
            --sidebar-width: 260px;
            --body-bg: #f4f6fa;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--body-bg);
            color: #2b2d42;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: #ffffff;
            border-right: 1px solid #e9ecef;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s;
        }

        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #f1f3f5;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            box-shadow: 0 4px 10px rgba(67, 97, 238, 0.3);
        }

        .brand-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.2;
        }

        .brand-subtitle {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 500;
        }

        .sidebar-nav {
            padding: 1rem 0;
            overflow-y: auto;
            max-height: calc(100vh - 80px);
        }

        .nav-section-title {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            padding: 0.75rem 1.5rem 0.25rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.75rem 1.5rem;
            color: #475569;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.925rem;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .sidebar-link:hover {
            color: var(--primary);
            background-color: #f8faff;
        }

        .sidebar-link.active {
            color: var(--primary);
            background-color: #eef2ff;
            border-left-color: var(--primary);
            font-weight: 600;
        }

        .sidebar-link i {
            font-size: 1.15rem;
            width: 22px;
            text-align: center;
        }

        /* Main Content Wrapper */
        #content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s;
        }

        /* Top Navbar */
        .top-navbar {
            background: #ffffff;
            border-bottom: 1px solid #e9ecef;
            padding: 0.75rem 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .role-badge-admin {
            background-color: #ede9fe;
            color: #6d28d9;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .role-badge-kasir {
            background-color: #ccfbf1;
            color: #0f766e;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .main-container {
            padding: 1.75rem;
            flex: 1;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 14px;
            box-shadow: var(--card-shadow);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card-header {
            background: #ffffff;
            border-bottom: 1px solid #f1f3f5;
            padding: 1.1rem 1.4rem;
            font-weight: 600;
            border-top-left-radius: 14px !important;
            border-top-right-radius: 14px !important;
        }

        /* Stat Cards */
        .stat-card {
            border-radius: 14px;
            padding: 1.25rem 1.4rem;
            position: relative;
            overflow: hidden;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        /* Tables */
        .table {
            vertical-align: middle;
        }

        .table th {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
            font-weight: 600;
            background-color: #f8fafc;
            border-top: none;
            padding: 0.85rem 1rem;
        }

        .table td {
            padding: 0.85rem 1rem;
            font-size: 0.9rem;
        }

        /* Badges */
        .badge {
            font-weight: 500;
            padding: 0.4em 0.75em;
            border-radius: 6px;
        }

        @media (max-width: 991.98px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            #sidebar.show {
                margin-left: 0;
            }
            #content {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar -->
    <aside id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <i class="bi bi-boxes"></i>
            </div>
            <div>
                <div class="brand-title">SmartInventory</div>
                <div class="brand-subtitle">Warehouse & POS</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-title">Menu Utama</div>
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') || request()->routeIs('home') ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i>
                <span>Dashboard</span>
            </a>

            <!-- Menu Kasir / Point of Sale -->
            <div class="nav-section-title">Kasir & Penjualan</div>
            <a href="{{ route('pos.index') }}" class="sidebar-link {{ request()->routeIs('pos.index') ? 'active' : '' }}">
                <i class="bi bi-cart4 text-success"></i>
                <span>Kasir (POS)</span>
            </a>
            <a href="{{ route('pos.history') }}" class="sidebar-link {{ request()->routeIs('pos.history') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i>
                <span>Riwayat Penjualan</span>
            </a>

            <!-- Menu Barang & Stok -->
            <div class="nav-section-title">Katalog & Stok</div>
            <a href="{{ route('products.index') }}" class="sidebar-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i>
                <span>Daftar Produk</span>
            </a>

            @if(Auth::user()->isAdminGudang())
                <!-- Menu Khusus Admin Gudang -->
                <div class="nav-section-title">Transaksi Gudang</div>
                <a href="{{ route('stock-in.index') }}" class="sidebar-link {{ request()->routeIs('stock-in.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-down-left-square-fill text-primary"></i>
                    <span>Stok Masuk</span>
                </a>
                <a href="{{ route('stock-out.index') }}" class="sidebar-link {{ request()->routeIs('stock-out.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-up-right-square-fill text-danger"></i>
                    <span>Stok Keluar</span>
                </a>

                <div class="nav-section-title">Master Data</div>
                <a href="{{ route('categories.index') }}" class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i>
                    <span>Kategori Produk</span>
                </a>
                <a href="{{ route('suppliers.index') }}" class="sidebar-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                    <i class="bi bi-truck"></i>
                    <span>Supplier</span>
                </a>
            @endif

            <!-- Menu Laporan -->
            <div class="nav-section-title">Laporan</div>
            @if(Auth::user()->isAdminGudang())
                <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line"></i>
                    <span>Pusat Laporan</span>
                </a>
            @endif
            <a href="{{ route('reports.stock') }}" class="sidebar-link {{ request()->routeIs('reports.stock') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-spreadsheet"></i>
                <span>Laporan Stok</span>
            </a>
            @if(Auth::user()->isAdminGudang())
                <a href="{{ route('reports.stock-in') }}" class="sidebar-link {{ request()->routeIs('reports.stock-in') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-arrow-down"></i>
                    <span>Laporan Stok Masuk</span>
                </a>
                <a href="{{ route('reports.stock-out') }}" class="sidebar-link {{ request()->routeIs('reports.stock-out') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-arrow-up"></i>
                    <span>Laporan Stok Keluar</span>
                </a>
            @endif
            <a href="{{ route('reports.sales') }}" class="sidebar-link {{ request()->routeIs('reports.sales') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i>
                <span>Laporan Penjualan</span>
            </a>

            @if(Auth::user()->isAdminGudang())
                <!-- Menu Pengaturan Pengguna -->
                <div class="nav-section-title">Sistem</div>
                <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Kelola Pengguna</span>
                </a>
            @endif
        </nav>
    </aside>

    <!-- Main Content -->
    <div id="content">
        <!-- Top Navbar -->
        <header class="top-navbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-outline-secondary d-lg-none" id="sidebarToggle" type="button">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="d-none d-md-block">
                    <span class="text-muted small">
                        <i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Role Badge -->
                @if(Auth::user()->isAdminGudang())
                    <span class="role-badge-admin">
                        <i class="bi bi-shield-check me-1"></i> Admin Gudang
                    </span>
                @else
                    <span class="role-badge-kasir">
                        <i class="bi bi-person-badge me-1"></i> Kasir
                    </span>
                @endif

                <!-- User Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light btn-sm d-flex align-items-center gap-2 border-0 bg-transparent" type="button" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; font-weight: 600;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="text-start d-none d-sm-block">
                            <div class="fw-semibold text-dark small lh-1">{{ Auth::user()->name }}</div>
                            <small class="text-muted" style="font-size: 0.725rem;">{{ Auth::user()->email }}</small>
                        </div>
                        <i class="bi bi-chevron-down text-muted small ms-1"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                        <li>
                            <div class="px-3 py-2 text-muted small border-bottom">
                                Signed in as <strong class="d-block text-dark">{{ Auth::user()->name }}</strong>
                            </div>
                        </li>
                        <li><a class="dropdown-menu-item dropdown-item py-2" href="{{ route('profile') }}"><i class="bi bi-person me-2"></i> Profil Akun</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Keluar (Logout)
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="main-container">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <div class="fw-semibold mb-1"><i class="bi bi-exclamation-octagon me-1"></i> Terjadi kesalahan input:</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Sidebar Toggle on Mobile
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
            });
        }

        // Global Rupiah Formatter
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        }
    </script>
    @stack('scripts')
</body>
</html>

