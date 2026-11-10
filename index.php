<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Tugas: Sistem Inventory Alat Medis & CRUD SIMRS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #F0F4F9 0%, #E0F2FE 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 2rem 1rem;
        }
        .main-card {
            background: #FFFFFF;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(2, 132, 199, 0.1);
            border: 1px solid #E2E8F0;
            max-width: 850px;
            width: 100%;
            padding: 2.5rem;
        }
        .portal-btn-primary {
            background: linear-gradient(135deg, #0284C7 0%, #0EA5E9 100%);
            color: #FFFFFF;
            border: none;
            padding: 1rem 1.5rem;
            border-radius: 14px;
            font-weight: 600;
            transition: all 0.25s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.25);
        }
        .portal-btn-primary:hover {
            background: linear-gradient(135deg, #0369A1 0%, #0284C7 100%);
            color: #FFFFFF;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(2, 132, 199, 0.35);
        }
        .portal-btn-secondary {
            background: #FFFFFF;
            color: #0F172A;
            border: 2px solid #E2E8F0;
            padding: 1rem 1.5rem;
            border-radius: 14px;
            font-weight: 600;
            transition: all 0.25s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .portal-btn-secondary:hover {
            border-color: #0284C7;
            background: #F0F9FF;
            color: #0284C7;
            transform: translateY(-2px);
        }
        .badge-student {
            background: #E0F2FE;
            color: #0369A1;
            padding: 0.35rem 0.85rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.82rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
    </style>
</head>
<body>

<div class="main-card">
    <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 64px; height: 64px; background: #E0F2FE; color: #0284C7;">
            <i class="bi bi-hospital fs-2"></i>
        </div>
        <h2 class="fw-bold text-dark mb-1">Tugas Sistem Inventory Alat Medis & CRUD</h2>
        <p class="text-muted mb-3">Sistem Informasi Manajemen Rumah Sakit (SIMRS) • Pemrograman Web</p>
        
        <div class="badge-student mb-2">
            <i class="bi bi-person-badge-fill"></i>
            <span>Syuja Ridhwan Fairuz Zahran &bull; NIM: 1202405030</span>
        </div>
        <div class="text-muted small">Universitas Muhammadiyah PKU Surakarta</div>
    </div>

    <div class="row g-3 mb-4">
        <!-- Pilihan 1: Inventory Alat Medis (Full UI) -->
        <div class="col-12 col-md-6">
            <a href="inventory_alat/login.php" class="portal-btn-primary h-100">
                <div>
                    <div class="fs-5 mb-1"><i class="bi bi-laptop me-2"></i>Inventory Alat (Full UI)</div>
                    <small class="opacity-75 d-block">Spark Admin, Login, Log Akses, Pencarian, Filter & CRUD</small>
                </div>
                <i class="bi bi-arrow-right-circle fs-3 ms-2"></i>
            </a>
        </div>

        <!-- Pilihan 2: CRUD Standar -->
        <div class="col-12 col-md-6">
            <a href="crud/index.php" class="portal-btn-secondary h-100">
                <div>
                    <div class="fs-5 mb-1"><i class="bi bi-code-slash me-2"></i>CRUD Standar / Dasar</div>
                    <small class="text-muted d-block">Tugas CRUD murni PHP & MySQL (Add, Edit, Delete, Read)</small>
                </div>
                <i class="bi bi-box-arrow-up-right fs-4 text-muted ms-2"></i>
            </a>
        </div>
    </div>

    <!-- Informasi Database & Akun Login -->
    <div class="card border-0 bg-light p-3 rounded-4 mb-3">
        <h6 class="fw-bold text-dark mb-2"><i class="bi bi-key-fill text-primary me-2"></i>Informasi Akun Login Demo:</h6>
        <div class="row g-2 small text-muted">
            <div class="col-12 col-md-4">
                <strong>Super Admin:</strong> <code>admin</code> / <code>admin123</code>
            </div>
            <div class="col-12 col-md-4">
                <strong>Teknisi:</strong> <code>syuja</code> / <code>1202405030</code>
            </div>
            <div class="col-12 col-md-4">
                <strong>Staf:</strong> <code>elektromedik</code> / <code>123456</code>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center pt-3 border-top small text-muted">
        <span><i class="bi bi-database me-1"></i> File Database: <code>database_simrs.sql</code></span>
        <span>Repository: <a href="https://github.com/f4ayruze/inventory-alat" target="_blank" class="text-decoration-none">f4ayruze/inventory-alat</a></span>
    </div>
</div>

</body>
</html>
