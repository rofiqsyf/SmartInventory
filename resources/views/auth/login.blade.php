<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Inventori & Kasir</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #31104b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            width: 100%;
            max-width: 480px;
        }

        .login-header {
            padding: 2.25rem 2rem 1.5rem;
            text-align: center;
        }

        .brand-badge {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            color: #ffffff;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.85rem;
            margin-bottom: 1rem;
            box-shadow: 0 10px 25px rgba(67, 97, 238, 0.4);
        }

        .btn-primary {
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
            transform: translateY(-1px);
        }

        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 1.5px solid #e2e8f0;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: #4361ee;
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.15);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <div class="brand-badge">
                <i class="bi bi-boxes"></i>
            </div>
            <h3 class="fw-bold text-dark mb-1">SmartInventory</h3>
            <p class="text-muted small">Sistem Manajemen Stok Gudang & Kasir Toko</p>
        </div>

        <div class="px-4 pb-4">
            @if(session('success'))
                <div class="alert alert-success py-2 small mb-3">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger py-2 small mb-3">
                    <i class="bi bi-exclamation-triangle me-1"></i> {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label small fw-semibold text-secondary">Alamat Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                        <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="password" class="form-label small fw-semibold text-secondary">Kata Sandi</label>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-key text-muted"></i></span>
                        <input type="password" class="form-control border-start-0 ps-0" id="password" name="password" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label small text-muted" for="remember">Ingat Saya</label>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Sistem
                </button>
            </form>
        </div>
    </div>
</body>
</html>

