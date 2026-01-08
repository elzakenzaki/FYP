<?php 
// Fail ini mengandungi semua CSS untuk memastikan UI/UX konsisten dan cantik
header("Content-type: text/css");
?>
/* === TEMA CERAH & HIJAU KORPORAT === */
:root {
    --color-primary: #28a745; /* Hijau Zakat */
    --color-secondary: #007bff; /* Biru Akses */
    --color-danger: #dc3545; /* Merah Ralat/Tolak */
    --color-warning: #ffc107; /* Kuning Peringatan/Tetapan */
    --color-light: #f8f9fa; /* Latar Belakang */
    --color-dark: #343a40; /* Teks Gelap */
    --color-white: #ffffff;
}

* { margin: 0; padding: 0; box-sizing: border-box; }
body { 
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
    background: var(--color-light); 
    color: var(--color-dark); 
    line-height: 1.6;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

/* Header & Navigasi */
.header { 
    background: var(--color-white); 
    padding: 15px 30px; 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    box-shadow: 0 2px 8px rgba(0,0,0,0.1); /* Bayangan lebih jelas */
}
.header .logo-section { display: flex; align-items: center; }
.header .logo { height: 40px; margin-right: 15px; }
.header .title { font-size: 22px; font-weight: 700; color: var(--color-primary); }
.header a { text-decoration: none; color: var(--color-dark); }
/* .header a:hover { text-decoration: underline; } Dihapus sebab guna button style */

/* Struktur Umum */
.content { padding: 30px; flex-grow: 1; }
.card { background: var(--color-white); padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
h1, h2, h3 { color: var(--color-primary); margin-bottom: 20px; font-weight: 600; }

/* Button Generik */
.btn {
    padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;
    font-weight: 600; transition: all 0.2s, transform 0.1s;
    text-align: center; text-decoration: none !important; display: inline-block;
}
.btn-primary { background-color: var(--color-primary); color: white; }
.btn-primary:hover { background-color: #1e7e34; transform: translateY(-1px); }
.btn-secondary { background-color: var(--color-secondary); color: white; }
.btn-secondary:hover { background-color: #0056b3; transform: translateY(-1px); }
.btn-danger { background-color: var(--color-danger); color: white; }
.btn-danger:hover { background-color: #bd2130; transform: translateY(-1px); }
.btn-warning { background-color: var(--color-warning); color: var(--color-dark); }
.btn-warning:hover { background-color: #e0a800; transform: translateY(-1px); }
.btn-view { background-color: var(--color-secondary); color: white; padding: 6px 10px; font-size: 12px; font-weight: bold; border-radius: 4px; text-decoration: none; margin: 2px; display: inline-block; }


/* Mesej Status */
.message-box { padding: 15px; margin-bottom: 20px; border-radius: 5px; font-weight: 500; }
.message-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.message-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
.message-info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }

/* Jadual Data */
.data-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
.data-table th, .data-table td { padding: 12px 15px; border: 1px solid #dee2e6; text-align: left; }
.data-table th { background-color: var(--color-dark); color: white; font-weight: 700; }
.data-table tr:nth-child(even) { background-color: #f7f7f7; }
.data-table tr:hover { background: #fafafa; }
.action-cell a { margin: 2px; } /* Guna .btn-view di atas */

/* Form Elements */
.form-group label { display: block; font-weight: bold; margin-bottom: 5px; font-size: 15px; color: var(--color-dark); }
.form-group input, .form-group textarea, .form-group select {
    width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 5px;
    background-color: #f1f1f1; color: var(--color-dark); box-sizing: border-box;
}
.radio-group, .checkbox-group { margin-top: 5px; display: flex; flex-wrap: wrap; gap: 15px; }

/* Footer */
.footer {
    background-color: var(--color-dark); padding: 20px 30px; text-align: center;
    border-top: 3px solid var(--color-warning); font-size: 12px; color: #adb5bd;
    flex-shrink: 0; margin-top: 30px; 
}
.footer .title { color: var(--color-warning); font-weight: 700; margin-bottom: 10px; }
.footer .footer-logos img { height: 40px; margin: 0 10px 15px 10px; opacity: 0.8; }
.footer .links a { color: #adb5bd; margin: 0 10px; text-decoration: none; }
.footer .links a:hover { color: var(--color-white); }
.footer .info { margin-top: 10px; }
.footer .browser { margin-top: 5px; font-size: 11px; }

/* ================================================================= */
/* === GAYA SPESIFIK DASHBOARD (Butang Navigasi Header Cantik) === */
/* ================================================================= */

/* Memastikan Menu Navigasi di Header tersusun rapi */
.header .nav-menu {
    display: flex;
    align-items: center;
    gap: 10px; /* Kurangkan gap sedikit */
    flex-grow: 1; 
    justify-content: center;
    padding: 0 20px; 
}
.header .header-buttons {
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Gaya Butang Navigasi (DASHBOARD, URUS, TETAPAN) */
.nav-menu .dashboard-link, 
.nav-menu .urus-link {
    /* Menyeragamkan penampilan butang */
    padding: 8px 16px; 
    height: 38px; /* Ketinggian sedikit kecil */
    border-radius: 20px; /* Bentuk pil/rounded penuh */
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    transition: all 0.3s ease;
    text-decoration: none !important;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); /* Bayangan lembut */
    border: none; /* Buang border yang mungkin ada */
}

/* 1. Gaya Butang DASHBOARD UTAMA (Hijau - Paling Utama) */
.nav-menu .dashboard-link {
    background-color: var(--color-primary); 
    color: var(--color-white) !important;
    box-shadow: 0 4px 6px rgba(40, 167, 69, 0.3); 
}
.nav-menu .dashboard-link:hover {
    background-color: #1e7e34; 
    transform: translateY(-1px);
}

/* 2. Gaya Butang URUS (Biru - Akses Pengurusan) */
.nav-menu .urus-link {
    background-color: var(--color-secondary);
    color: var(--color-white) !important;
    box-shadow: 0 4px 6px rgba(0, 123, 255, 0.3);
}
.nav-menu .urus-link:hover {
    background-color: #0056b3; 
    transform: translateY(-1px);
}

/* 3. Gaya Butang TETAPAN (Kuning/Oren - Peringatan/Kritikal) */
/* Override default urus-link style using attribute selector based on inline style */
.nav-menu .urus-link[style*="warning"] { 
    background-color: var(--color-warning) !important;
    color: var(--color-dark) !important;
    box-shadow: 0 4px 6px rgba(255, 193, 7, 0.3);
}
.nav-menu .urus-link[style*="warning"]:hover {
    background-color: #e0a800 !important;
    transform: translateY(-1px);
}

/* Gaya Butang KELUAR (Header Kanan) */
.header-buttons .btn-keluar {
    padding: 8px 16px;
    height: 38px; /* Seragamkan ketinggian */
    border-radius: 20px; /* Bentuk pil/rounded penuh */
    font-size: 14px;
    background-color: var(--color-danger); 
    color: var(--color-white);
    box-shadow: 0 4px 6px rgba(220, 53, 69, 0.3);
    text-transform: uppercase;
    font-weight: 600;
}
.header-buttons .btn-keluar:hover {
    background-color: #bd2130; 
    transform: translateY(-1px);
}

/* Gaya untuk Info Nama Pengguna di Header */
.header-buttons .user-info {
    color: var(--color-dark);
    font-weight: 600;
    font-size: 14px;
    padding: 0 10px;
}