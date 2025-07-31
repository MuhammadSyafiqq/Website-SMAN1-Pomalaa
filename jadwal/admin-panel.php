    <?php

    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $timeout_duration = 900; 
    
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
        session_unset();     // hapus semua session
        session_destroy();   // hancurkan session
        header("Location: ../login.php?timeout=true"); // redirect ke login (ganti dengan nama file login jika perlu)
        exit();
    }
    $_SESSION['LAST_ACTIVITY'] = time(); // perbarui waktu aktivitas terakhir
    
    // require_once 'theme.php';
    $role = $_SESSION['role'] ?? null;
    $username = $_SESSION['username'] ?? null;
    $nama = $_SESSION['nama'] ?? null;
    
    // Cek jika belum login
    if (!isset($_SESSION['username'])) {
        header("Location: ../login.php");
        exit();
    }

 
    require_once '../config/database.php';
    require_once 'models/KelasModel.php';
    require_once 'models/JurusanModel.php';
    require_once 'models/MataPelajaranModel.php';
    require_once 'models/JadwalUjianModel.php';
    require_once 'helpers/functions.php';

    $kelasModel = new KelasModel($connection);
    $jurusanModel = new JurusanModel($connection);
    $mataPelajaranModel = new MataPelajaranModel($connection);
    $jadwalModel = new JadwalUjianModel($connection);

    $dataKelas = $kelasModel->getAll();
    $dataJurusan = $jurusanModel->getAll();
    $dataMapel = $mataPelajaranModel->getAll();
    $dataJadwal = $jadwalModel->getAll();

    $jadwalByKelasJurusan = [];
    foreach ($dataJadwal as $jadwal) {
        $key = $jadwal['kelas_id'] . '-' . $jadwal['jurusan_id'];
        $jadwalByKelasJurusan[$key][] = $jadwal;
    }

    $mapelUmum = array_filter($dataMapel, fn($m) => strtolower($m['kategori']) === 'umum');
    $mapelIPA  = array_filter($dataMapel, fn($m) => strtolower($m['kategori']) === 'ipa');
    $mapelIPS  = array_filter($dataMapel, fn($m) => strtolower($m['kategori']) === 'ips');

    // Handle request (kelas, jurusan, mapel, jadwal)
    include 'handlers/handle_request.php';

    ?>


    <!DOCTYPE html>
    <html>
    <head>
        <title>Admin Panel - Jadwal Ujian</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="../assets/image/logo_sekolah.png">
        <link rel="stylesheet" href="../assets/style/style.css?v=<?php echo time(); ?>">

        <style>
         * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
         /* Floating Dashboard Button */
        .floating-dashboard-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 50%;
            cursor: pointer;
            z-index: 10001;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            text-decoration: none;
            animation: float 3s ease-in-out infinite;
        }

        .floating-dashboard-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.6);
            background: linear-gradient(135deg, #764ba2, #667eea);
        }

        .floating-dashboard-btn:active {
            transform: scale(0.95);
        }

        /* Floating animation */
        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-8px);
            }
        }

        /* Tooltip */
        .floating-dashboard-btn::before {
            content: 'Kembali ke Dashboard';
            position: absolute;
            right: 70px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            pointer-events: none;
        }

        .floating-dashboard-btn::after {
            content: '';
            position: absolute;
            right: 60px;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-left: 8px solid rgba(0, 0, 0, 0.8);
            border-top: 8px solid transparent;
            border-bottom: 8px solid transparent;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .floating-dashboard-btn:hover::before,
        .floating-dashboard-btn:hover::after {
            opacity: 1;
            visibility: visible;
        }

        /* Icon styling */
        .floating-dashboard-btn .icon {
            width: 24px;
            height: 24px;
            fill: currentColor;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .floating-dashboard-btn {
                width: 50px;
                height: 50px;
                bottom: 20px;
                right: 20px;
                font-size: 20px;
            }
            
            .floating-dashboard-btn::before {
                right: 60px;
                font-size: 12px;
                padding: 6px 12px;
            }
            
            .floating-dashboard-btn::after {
                right: 50px;
                border-left-width: 6px;
                border-top-width: 6px;
                border-bottom-width: 6px;
            }
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: white;
            margin-top: 110px;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            padding: 30px;
            position: relative;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 40px;
            font-size: 2.8em;
            font-weight: 600;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 2px;
        }

        h2 {
            color: #34495e;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #3498db;
            display: inline-block;
            font-size: 1.8em;
            font-weight: 600;
        }

        /* Flash Message */
        #flash-message {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            padding: 15px 25px;
            border-radius: 10px;
            font-weight: bold;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.5s ease-out, fadeOut 4s ease-in-out;
            transform: translateX(100%);
            backdrop-filter: blur(10px);
        }

        .flash-success {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
        }

        .flash-error {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        @keyframes slideIn {
            to { transform: translateX(0); }
        }

        @keyframes fadeOut {
            0%, 80% { opacity: 1; transform: translateX(0); }
            100% { opacity: 0; transform: translateX(100%); }
        }

        /* Form Section */
        .form-section {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid #e3e6ea;
        }

        .form-section:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        /* Form Styling */
        form {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 25px;
            align-items: end;
        }

        form input, form select {
            flex: 1;
            min-width: 220px;
            padding: 15px;
            border: 2px solid #e0e6ed;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: white;
            font-family: inherit;
        }

        form input:focus, form select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            transform: translateY(-2px);
        }

        form button {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            min-width: 140px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        form button:hover {
            background: linear-gradient(135deg, #2980b9, #3498db);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(52, 152, 219, 0.4);
        }

        form button:active {
            transform: translateY(-1px);
        }

        /* Button Styles */
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
            color: white;
        }

        .btn-reset {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        /* Dropdown Styling */
        .dropdown-container, .filter-container {
            margin: 25px 0;
            padding: 25px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid #e3e6ea;
        }

        .dropdown-container label, .filter-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
        }

        .dropdown-select, .filter-dropdown-select {
            padding: 15px;
            border: 2px solid #e0e6ed;
            border-radius: 10px;
            background: white;
            font-size: 14px;
            min-width: 220px;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .dropdown-select:focus, .filter-dropdown-select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .filter-dropdown-container {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
            align-items: end;
            margin-bottom: 25px;
        }

        .filter-dropdown-container > div {
            flex: 1;
            min-width: 200px;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            background: white;
            border-radius: 15px;
            overflow: visible;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        th {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            padding: 20px 15px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 13px;
        }

        td {
            padding: 18px 15px;
            border-bottom: 1px solid #f1f2f6;
            transition: all 0.3s ease;
            vertical-align: middle;
        }

        tr:hover td {
            background-color: #f8f9fa;
            transform: scale(1.01);
        }

        tr:last-child td {
            border-bottom: none;
        }

        /* Category Styles */
        .mata-pelajaran-category, .jadwal-category {
            margin-bottom: 35px;
            padding: 30px;
            border: 2px solid #e3e6ea;
            border-radius: 20px;
            background: linear-gradient(135deg, #fafbfc, #f1f3f4);
            display: none;
            transition: all 0.4s ease;
            position: relative;
        }

        .mata-pelajaran-category.show, .jadwal-category.show {
            display: block;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .mata-pelajaran-category h3, .jadwal-category h3 {
            margin-top: 0;
            color: white;
            font-size: 1.3em;
            margin-bottom: 20px;
            background: #1e40af;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Badge Styles */
        .kategori-badge {
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }

        .kategori-umum {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        .kategori-ipa {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
        }

        .kategori-ips {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
        }

        /* No Data Styles */
        .no-data, .no-jadwal {
            text-align: center;
            color: #7f8c8d;
            font-style: italic;
            padding: 50px;
            background: linear-gradient(135deg, #ecf0f1, #bdc3c7);
            border-radius: 15px;
            margin: 25px 0;
            font-size: 16px;
        }

        /* Action Menu */
        
        .action-menu {
            position: relative; /* Penting untuk posisi absolute turunannya */
        }
        .action-menu-wrapper {
            position: relative;
            display: inline-block;
            z-index: 9999;
        }

        .action-menu-btn {
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .action-menu-btn:hover {
            background: linear-gradient(135deg, #7f8c8d, #95a5a6);
            transform: scale(1.1);
        }

        .action-menu-content {
            display: none;
            position: absolute;
            right: 0;
            background: white;
            min-width: 140px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            z-index: 9999;
            overflow: visible;
            border: 1px solid #e3e6ea;
        }

        .action-menu-content button {
            display: block;
            width: 100%;
            padding: 15px 20px;
            border: none;
            background: white;
            cursor: pointer;
            text-align: left;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 14px;
        }

        .action-menu-content button:hover {
            background: #f8f9fa;
            transform: translateX(5px);
        }

        .action-menu-content button.edit {
            color: #3498db;
        }

        .action-menu-content button.delete {
            color: #e74c3c;
        }

        .action-menu-wrapper:hover .action-menu-content {
            display: block;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Modal Styles - IMPROVED */
        .modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
        }

        .modal-content {
            background: white;
            margin: 3% auto;
            padding: 40px;
            border-radius: 20px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.4s ease-out;
            position: relative;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-content h2, .modal-content h3 {
            margin-bottom: 30px;
            color: #2c3e50;
            font-size: 1.8em;
            font-weight: 600;
            text-align: center;
            border-bottom: 3px solid #3498db;
            padding-bottom: 15px;
        }

        .close {
            color: #95a5a6;
            float: right;
            font-size: 32px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            position: absolute;
            top: 15px;
            right: 20px;
        }

        .close:hover {
            color: #e74c3c;
            transform: scale(1.2);
        }

        /* Modal Form Styling */
        .modal-content form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .modal-content form label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .modal-content form input,
        .modal-content form select {
            padding: 15px;
            border: 2px solid #e0e6ed;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: white;
            font-family: inherit;
            width: 100%;
        }

        .modal-content form input:focus,
        .modal-content form select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .modal-content form input[readonly] {
            background-color: #f8f9fa;
            cursor: not-allowed;
            color: #6c757d;
        }

        .modal-content form button {
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 10px;
        }

        .modal-content form button[type="submit"] {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        .modal-content form button[type="submit"]:hover {
            background: linear-gradient(135deg, #2980b9, #3498db);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(52, 152, 219, 0.4);
        }

        .modal-content form button[type="button"] {
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
            color: white;
        }

        .modal-content form button[type="button"]:hover {
            background: linear-gradient(135deg, #7f8c8d, #95a5a6);
            transform: translateY(-2px);
        }

        /* Form Group for Better Organization */
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-row .form-group {
            flex: 1;
        }

        /* Button Group */
        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 20px;
        }

        .button-group button {
            flex: 1;
            max-width: 200px;
        }
        
        .btn-back {
            background-color: #888;
            color: white;
            margin-bottom: 15px;
        }
        
        .btn-back:hover {
            background-color: #666;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 10px;
            }

            h1 {
                font-size: 2.2em;
            }

            form {
                flex-direction: column;
            }

            form input, form select {
                min-width: 100%;
            }

            .filter-dropdown-container {
                flex-direction: column;
            }

            .filter-dropdown-container > div {
                min-width: 100%;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 12px 8px;
            }

            .modal-content {
                width: 95%;
                margin: 5% auto;
                padding: 25px;
            }

            .form-row {
                flex-direction: column;
            }

            .button-group {
                flex-direction: column;
            }

            .button-group button {
                max-width: 100%;
            }
        }

        /* Additional Interactive Elements */
        .form-section {
            position: relative;
            overflow: hidden;
        }

        .form-section::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 17px;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .form-section:hover::before {
            opacity: 0.1;
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Smooth Scrolling */
        html {
            scroll-behavior: smooth;
        }


        /* Enhanced Visual Effects */
        .form-section, .modal-content {
            position: relative;
        }

        .form-section::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
            pointer-events: none;
            border-radius: 15px;
        }

        /* Improved Table Responsiveness */
        @media (max-width: 768px) {
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
        
        @media (max-width: 480px) {
    h1 {
        font-size: 1.6em;
    }

    h2 {
        font-size: 1.4em;
    }

    .container {
        padding: 15px;
        margin: 5px;
    }

    .form-section,
    .dropdown-container,
    .filter-container {
        padding: 15px;
    }

    form input, form select {
        padding: 12px;
        font-size: 13px;
    }

    .floating-dashboard-btn {
        width: 45px;
        height: 45px;
        font-size: 18px;
        bottom: 15px;
        right: 15px;
    }

    .floating-dashboard-btn::before {
        font-size: 11px;
        padding: 5px 10px;
        right: 50px;
    }

    .floating-dashboard-btn::after {
        right: 40px;
        border-left-width: 5px;
        border-top-width: 5px;
        border-bottom-width: 5px;
    }

    table {
        font-size: 11px;
    }

    th, td {
        padding: 10px 6px;
    }

    .modal-content {
        padding: 20px;
    }

    .modal-content form input,
    .modal-content form select {
        padding: 12px;
        font-size: 13px;
    }

    .modal-content form button {
        padding: 12px;
        font-size: 13px;
    }

    .action-menu-btn {
        padding: 8px 12px;
        font-size: 14px;
    }

    .action-menu-content button {
        padding: 10px 15px;
        font-size: 13px;
    }

    .no-data,
    .no-jadwal {
        font-size: 14px;
        padding: 30px;
    }
}
    </style>
    </head>
    <body>
        
        <?php include '../partials/navbar.php'; ?>
        
        

        <?php if (isset($_SESSION['success'])): ?>
        <div id="flash-message" class="flash-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); endif; ?>
    
        <?php if (isset($_SESSION['error'])): ?>
        <div id="flash-message" class="flash-error"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); endif; ?>
            <h1>Admin Panel</h1>
            <div class ="container"> 
            
            <a href="../dashboard_admin.php" class="btn btn-back">← Kembali ke Dashboard</a>


            <!-- Kelas Section
            <section class="form-section" aria-label="Form Input Kelas">
                <h2>Manajemen Kelas</h2>
                <form method="POST" style="display : none">
                    <input type="text" name="kelas_nama" placeholder="Nama Kelas">
                    <button type="submit" name="add_kelas">Tambah</button>
                </form>

                <button type="button" class="toggle-table" data-target="kelas-table" style="display":none>Lihat Data</button>

                <table id="kelas-table" style=>
                    <tr><th>ID</th><th>Nama</th><th>Aksi</th></tr>
                    <?php foreach ($dataKelas as $kelas): ?>
                        <tr>
                            <td><?= $kelas['id'] ?></td>
                            <td><?= $kelas['nama'] ?></td>
                            <td style="text-align: right;">
                            <div class="action-menu-wrapper">
                                <button type="button" class="action-menu-btn">⋮</button>
                                <div class="action-menu-content">
                                <button onclick="editKelas('<?= $kelas['id'] ?>', '<?= htmlspecialchars($kelas['nama']) ?>')">Edit</button>
                                <button class="delete" onclick="confirmDelete('kelas', '<?= $kelas['id'] ?>', '<?= htmlspecialchars($kelas['nama']) ?>')">Hapus</button>
                                </div>
                            </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </section> -->

            <!-- Jurusan Section
            <section class="form-section " aria-label="Form Input Jurusan">
                <h2>Manajemen Jurusan</h2>
                <form method="POST">
                    <input type="text" name="jurusan_nama" placeholder="Nama Jurusan">
                    <button type="submit" name="add_jurusan">Tambah</button>
                </form>

                <button type="button" class="toggle-table" data-target="jurusan-table">Lihat Data</button>

                <table id="jurusan-table" style="display: none">
                    <tr><th>ID</th><th>Nama</th><th>Aksi</th></tr>
                    <?php foreach ($dataJurusan as $jurusan): ?>
                        <tr>
                            <td><?= $jurusan['id'] ?></td>
                            <td><?= $jurusan['nama'] ?></td>
                            <td style="text-align: right;">
                            <div class="action-menu-wrapper">
                                <button type="button" class="action-menu-btn">⋮</button>
                                <div class="action-menu-content">
                                <button class="edit" onclick="editJurusan('<?= $jurusan['id'] ?>', '<?= htmlspecialchars($jurusan['nama']) ?>')">Edit</button>
                                <button class="delete" onclick="confirmDelete('jurusan', '<?= $jurusan['id'] ?>', '<?= htmlspecialchars($jurusan['nama']) ?>')">Hapus</button>
                                </div>
                            </div>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </section> -->

            <section class="form-section" aria-label="Form Input Mata Pelajaran">
        <h2>Manajemen Mata Pelajaran</h2>
        
        <!-- Form untuk menambah mata pelajaran -->
        <form method="POST">
            <input type="text" name="nama" placeholder="Nama Mata Pelajaran" required>
            <select name="kategori" required>
                <option value="">-- Pilih Kategori --</option>
                <option value="Umum">Umum</option>
                <option value="IPA">IPA</option>
                <option value="IPS">IPS</option>
            </select>
            <button type="submit" name="add_mata_pelajaran">Tambah</button>
        </form>

        <!-- Dropdown untuk memilih kategori -->
        <div class="dropdown-container">
            <label for="kategori-dropdown">Pilih Kategori untuk Melihat Data:</label>
            <select id="kategori-dropdown" class="dropdown-select" onchange="showKategori(this.value)">
                <option value="">-- Pilih Kategori --</option>
                <option value="umum">Umum</option>
                <option value="ipa">IPA</option>
                <option value="ips">IPS</option>
                <option value="all">Tampilkan Semua</option>
            </select>
        </div>

        <!-- Kategori Umum -->
        <div id="kategori-umum" class="mata-pelajaran-category">
            <h3>Kategori Umum</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($mapelUmum) > 0): ?>
                        <?php foreach ($mapelUmum as $mapel): ?>
                            <tr>
                                <td><?= $mapel['nama'] ?></td>
                                <td><span class="kategori-badge kategori-umum"><?= $mapel['kategori'] ?></span></td>
                                <td>
                                    <div class="action-menu-wrapper">
                                        <button type="button" class="action-menu-btn">⋮</button>
                                        <div class="action-menu-content">
                                            <button class="edit" onclick="editMataPelajaran('<?= $mapel['id'] ?>', '<?= htmlspecialchars($mapel['nama']) ?>', '<?= htmlspecialchars($mapel['kategori']) ?>')">Edit</button>
                                            <button class="delete" onclick="confirmDelete('mata_pelajaran', '<?= $mapel['id'] ?>', '<?= htmlspecialchars($mapel['nama']) ?>')">Hapus</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="no-data">Tidak ada mata pelajaran kategori Umum</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Kategori IPA -->
        <div id="kategori-ipa" class="mata-pelajaran-category">
            <h3>Kategori IPA</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($mapelIPA) > 0): ?>
                        <?php foreach ($mapelIPA as $mapel): ?>
                            <tr>
                                <td><?= $mapel['nama'] ?></td>
                                <td><span class="kategori-badge kategori-ipa"><?= $mapel['kategori'] ?></span></td>
                                <td>
                                    <div class="action-menu-wrapper">
                                        <button type="button" class="action-menu-btn">⋮</button>
                                        <div class="action-menu-content">
                                            <button class="edit" onclick="editMataPelajaran('<?= $mapel['id'] ?>', '<?= htmlspecialchars($mapel['nama']) ?>', '<?= htmlspecialchars($mapel['kategori']) ?>')">Edit</button>
                                            <button class="delete" onclick="confirmDelete('mata_pelajaran', '<?= $mapel['id'] ?>', '<?= htmlspecialchars($mapel['nama']) ?>')">Hapus</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="no-data">Tidak ada mata pelajaran kategori IPA</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Kategori IPS -->
        <div id="kategori-ips" class="mata-pelajaran-category">
            <h3>Kategori IPS</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($mapelIPS) > 0): ?>
                        <?php foreach ($mapelIPS as $mapel): ?>
                            <tr>
                                <td><?= $mapel['nama'] ?></td>
                                <td><span class="kategori-badge kategori-ips"><?= $mapel['kategori'] ?></span></td>
                                <td>
                                    <div class="action-menu-wrapper">
                                        <button type="button" class="action-menu-btn">⋮</button>
                                        <div class="action-menu-content">
                                            <button class="edit" onclick="editMataPelajaran('<?= $mapel['id'] ?>', '<?= htmlspecialchars($mapel['nama']) ?>', '<?= htmlspecialchars($mapel['kategori']) ?>')">Edit</button>
                                            <button class="delete" onclick="confirmDelete('mata_pelajaran', '<?= $mapel['id'] ?>', '<?= htmlspecialchars($mapel['nama']) ?>')">Hapus</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="no-data">Tidak ada mata pelajaran kategori IPS</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

            <!-- Jadwal Ujian Section --> 
            <section class="form-section">
                <h2>Manajemen Jadwal Ujian</h2>
                <form method="POST">
                    <select name="kelas_id" id="kelas_id" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($dataKelas as $k): ?>
                        <option value="<?= $k['id'] ?>"><?= $k['nama'] ?></option>
                    <?php endforeach; ?>
                    </select>

                    <select name="jurusan_id" id="jurusan_id" required>
                    <option value="">-- Pilih Jurusan --</option>
                    <?php foreach ($dataJurusan as $j): ?>
                        <option value="<?= $j['id'] ?>"><?= $j['nama'] ?></option>
                    <?php endforeach; ?>
                    </select>

                    <select name="mata_pelajaran_id" id="mata_pelajaran_id" required>
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    <?php foreach ($dataMapel as $mapel): ?>
                        <option value="<?= $mapel['id'] ?>">
                            <?= $mapel['nama'] ?> (<?= ucfirst(strtolower($mapel['kategori'])) ?>)
                        </option>
                    <?php endforeach; ?>
                    </select>


                    <input type="date" name="tanggal" id="tanggal_input" required min="2000-01-01" max="2030-12-31">  
                    <input type='time' class="time" name="jam_mulai" placeholder="Jam Mulai" required>
                    <input type="time" name="jam_selesai" placeholder="Jam Selesai" required>
                    <input type="hidden" name="hari" id="hari_input" required>
                    <button type="submit" name="add_jadwal">Tambah</button>
                </form>
                
            </section>

                

            <!-- Section untuk Filter Jadwal dengan Dropdown -->
            <section class="form-section" aria-label="Filter Jadwal Ujian">
                <h2>Lihat Jadwal Ujian Berdasarkan Kelas & Jurusan</h2>
                
                <!-- Dropdown untuk filter -->
                <div class="form-section">
                            <label for="kelas-dropdown">Pilih Kelas:</label>
                            <select id="kelas-dropdown" class="filter-dropdown-select" onchange="filterJadwalByKelasJurusan()">
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach ($dataKelas as $kelas): ?>
                                    <option value="<?= $kelas['id'] ?>"><?= $kelas['nama'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            
                            <label for="jurusan-dropdown">Pilih Jurusan:</label>
                            <select id="jurusan-dropdown" class="filter-dropdown-select" onchange="filterJadwalByKelasJurusan()">
                                <option value="">-- Pilih Jurusan --</option>
                                <?php foreach ($dataJurusan as $jurusan): ?>
                                    <option value="<?= $jurusan['id'] ?>"><?= $jurusan['nama'] ?></option>
                                <?php endforeach; ?>
                            </select>
                    
                </div>
                <button type="button" style="align-itens: left;" onclick="showAllJadwal()" class="btn btn-primary">Tampilkan Semua</button>
                <button type="button" onclick="resetJadwalFilter()" class="btn btn-reset">Reset</button>
            
                <!-- Container untuk menampilkan jadwal -->
                <div id="jadwal-container">
                    <?php foreach ($dataKelas as $kelas): ?>
                        <?php foreach ($dataJurusan as $jurusan): ?>
                            <?php 
                            $key = $kelas['id'] . '-' . $jurusan['id'];
                            $jadwalList = isset($jadwalByKelasJurusan[$key]) ? $jadwalByKelasJurusan[$key] : [];
                            ?>
                            
                            <div id="jadwal-<?= $kelas['id'] ?>-<?= $jurusan['id'] ?>" class="jadwal-category" data-kelas="<?= $kelas['id'] ?>" data-jurusan="<?= $jurusan['id'] ?>">
                                <h3><?= $kelas['nama'] ?> - <?= $jurusan['nama'] ?></h3>
                                
                                <?php if (count($jadwalList) > 0): ?>
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Mata Pelajaran</th>
                                                <th>Kategori</th>
                                                <th>Hari</th>
                                                <th>Tanggal</th>
                                                <th>Jam Mulai</th>
                                                <th>Jam Selesai</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($jadwalList as $jadwal): ?>
                                                <tr>
                                                    <td><?= $jadwal['mata_pelajaran_nama'] ?></td>
                                                    <td>
                                                        <?php 
                                                        // Dapatkan kategori mata pelajaran
                                                        $kategoriMapel = '';
                                                        foreach ($dataMapel as $mapel) {
                                                            if ($mapel['id'] == $jadwal['mata_pelajaran_id']) {
                                                                $kategoriMapel = ucfirst(strtolower($mapel['kategori']));
                                                                break;
                                                            }
                                                        }
                                                        ?>
                                                        <span class="kategori-badge kategori-<?= strtolower($kategoriMapel) ?>"><?= $kategoriMapel ?></span>
                                                    </td>
                                                    <td><?= $jadwal['hari'] ?></td>
                                                    <td><?= date('d/m/Y', strtotime($jadwal['tanggal'])) ?></td>
                                                    <td><?= $jadwal['jam_mulai'] ?></td>
                                                    <td><?= $jadwal['jam_selesai'] ?></td>
                                                    <td>
                                                        <div class="action-menu-wrapper">
                                                            <button type="button" class="action-menu-btn">⋮</button>
                                                            <div class="action-menu-content">
                                                                <button class="edit" onclick="editJadwal('<?= $jadwal['id'] ?>')">Edit</button>
                                                                <button class="delete" onclick="confirmDelete('jadwal', '<?= $jadwal['id'] ?>', 'Jadwal <?= htmlspecialchars($jadwal['mata_pelajaran_nama']) ?>')">Hapus</button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <div class="no-jadwal">Tidak ada jadwal ujian untuk kelas <?= $kelas['nama'] ?> - <?= $jurusan['nama'] ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </section>

            </div>
        </div>



        <!-- Modal Edit Kelas -->
    <div id="editKelasModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editKelasModal')">&times;</span>
            <h3>Edit Kelas</h3>
            <form method="POST">
                <input type="hidden" name="edit_id" id="edit_kelas_id">
                <label for="edit_kelas_nama">Nama Kelas:</label>
                <input type="text" name="edit_nama" id="edit_kelas_nama" required maxlength="10">
                <button type="submit" name="edit_kelas">Update Kelas</button>
            </form>
        </div>
    </div>

    <!-- Modal Edit Jurusan -->
    <div id="editJurusanModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editJurusanModal')">&times;</span>
            <h3>Edit Jurusan</h3>
            <form method="POST">
                <input type="hidden" name="edit_id" id="edit_jurusan_id">
                <label for="edit_jurusan_nama">Nama Jurusan:</label>
                <input type="text" name="edit_nama" id="edit_jurusan_nama" required maxlength="10">
                <button type="submit" name="edit_jurusan">Update Jurusan</button>
            </form>
        </div>
    </div>

    <!-- Modal Edit Mata Pelajaran -->
    <div id="editMataPelajaranModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editMataPelajaranModal')">&times;</span>
            <h3>Edit Mata Pelajaran</h3>
            <form method="POST">    
                <input type="hidden" name="edit_id" id="edit_mp_id">
                <label for="edit_mp_nama">Nama Mata Pelajaran:</label>
                <input type="text" name="edit_nama" id="edit_mp_nama" required maxlength="100">
                <label for="edit_mp_kategori">Kategori:</label>
                <select name="edit_kategori" id="edit_mp_kategori" required>
                    <option value="umum">Umum</option>
                    <option value="ipa">Peminatan IPA</option>
                    <option value="ips">Peminatan IPS</option>
                </select>
                <button type="submit" name="edit_mata_pelajaran">Update Mata Pelajaran</button>
            </form>
        </div>
    </div>

    <!-- Modal Edit Jadwal Ujian - PERBAIKAN -->
<div id="editJadwalUjianModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editJadwalUjianModal')">&times;</span>
        <h2>Edit Jadwal Ujian</h2>
        
        <!-- PERBAIKAN: Form action dan method -->
        <form method="POST">
            <input type="hidden" id="edit_jadwal_id" name="edit_id">
            
           
                <input type="hidden" id="edit_kelas_nama" name="edit_kelas_nama" readonly 
                       style="background-color: #f5f5f5; cursor: not-allowed;">
          
                <input type="hidden" id="edit_jurusan_nama" name="edit_jurusan_nama" readonly 
                       style="background-color: #f5f5f5; cursor: not-allowed;">
          
                <input type="hidden" id="edit_mata_pelajaran_id" name="edit_mata_pelajaran_id" readonly 
                       style="background-color: #f5f5f5; cursor: not-allowed;">
         
     
                <label for="edit_tanggal">Tanggal:</label>
                <input type="date" id="edit_tanggal" name="edit_tanggal" required min="2000-01-01" max="2030-12-31">
        
            
       
                <label for="edit_jam_mulai">Jam Mulai:</label>
                <input type="time" id="edit_jam_mulai" name="edit_jam_mulai" required>
       
            
          
                <label for="edit_jam_selesai">Jam Selesai:</label>
                <input type="time" id="edit_jam_selesai" name="edit_jam_selesai" required>
  

        
                <!-- PERBAIKAN: Nama button submit -->
                <button type="submit" name="edit_jadwal" class="btn btn-primary">Update Jadwal</button>
                <button type="button" onclick="closeModal('editJadwalUjianModal')" class="btn btn-secondary">Batal</button>
        </form>
    </div>
</div>
        <script src="../assets/js/scripts.js"></script>
        <script>
        function showKategori(kategori) {
            // Sembunyikan semua kategori
            const allCategories = document.querySelectorAll('.mata-pelajaran-category');
            allCategories.forEach(category => {
                category.classList.remove('show');
            });

            // Tampilkan kategori yang dipilih
            if (kategori === 'all') {
                // Tampilkan semua kategori
                allCategories.forEach(category => {
                    category.classList.add('show');
                });
            } else if (kategori !== '') {
                // Tampilkan kategori spesifik
                const selectedCategory = document.getElementById('kategori-' + kategori);
                if (selectedCategory) {
                    selectedCategory.classList.add('show');
                }
            }
        }

        function filterJadwalByKelasJurusan() {
            const kelasId = document.getElementById('kelas-dropdown').value;
            const jurusanId = document.getElementById('jurusan-dropdown').value;
            
            // Sembunyikan semua kategori jadwal
            const allJadwalCategories = document.querySelectorAll('.jadwal-category');
            allJadwalCategories.forEach(category => {
                category.classList.remove('show');
            });
            
            // Tampilkan berdasarkan filter
            if (kelasId && jurusanId) {
                // Tampilkan jadwal untuk kelas dan jurusan spesifik
                const targetCategory = document.getElementById('jadwal-' + kelasId + '-' + jurusanId);
                if (targetCategory) {
                    targetCategory.classList.add('show');
                }
            } else if (kelasId && !jurusanId) {
                // Tampilkan semua jadwal untuk kelas tertentu
                allJadwalCategories.forEach(category => {
                    if (category.getAttribute('data-kelas') === kelasId) {
                        category.classList.add('show');
                    }
                });
            } else if (!kelasId && jurusanId) {
                // Tampilkan semua jadwal untuk jurusan tertentu
                allJadwalCategories.forEach(category => {
                    if (category.getAttribute('data-jurusan') === jurusanId) {
                        category.classList.add('show');
                    }
                });
            }
        }

        function showAllJadwal() {
            // Tampilkan semua jadwal yang memiliki data
            const allJadwalCategories = document.querySelectorAll('.jadwal-category');
            allJadwalCategories.forEach(category => {
                // Hanya tampilkan yang memiliki data jadwal (bukan yang kosong)
                const hasData = category.querySelector('table') !== null;
                if (hasData) {
                    category.classList.add('show');
                }
            });
        }

        function resetJadwalFilter() {
            // Reset dropdown
            document.getElementById('kelas-dropdown').value = '';
            document.getElementById('jurusan-dropdown').value = '';
            
            // Sembunyikan semua kategori
            const allJadwalCategories = document.querySelectorAll('.jadwal-category');
            allJadwalCategories.forEach(category => {
                category.classList.remove('show');
            });
        }
        
         // Optional: Smooth scroll behavior
        document.addEventListener('DOMContentLoaded', function() {
            const floatingBtn = document.querySelector('.floating-dashboard-btn');
            
            // Add click animation
            floatingBtn.addEventListener('click', function(e) {
                this.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });
            
            // Hide/show on scroll (optional)
            let lastScrollY = window.scrollY;
            
            window.addEventListener('scroll', function() {
                const currentScrollY = window.scrollY;
                
                if (currentScrollY > lastScrollY && currentScrollY > 100) {
                    // Scrolling down
                    floatingBtn.style.transform = 'translateY(100px)';
                } else {
                    // Scrolling up
                    floatingBtn.style.transform = 'translateY(0)';
                }
                
                lastScrollY = currentScrollY;
            });
        });
        
        
        </script>


    </body>
    </html>