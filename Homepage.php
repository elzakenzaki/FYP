<?php 
// Fail: Homepage.php
include_once 'db_connect.php'; 
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laman Utama - Sistem Pengurusan Zakat ATM</title>
    <link rel="stylesheet" href="ui_styles.php"> 
    
    <style> 
        /* Gaya khusus untuk mencantikkan UI Homepage tanpa Navbar */
        .hero-banner { 
            background: linear-gradient(rgba(255,255,255,0.95), rgba(255,255,255,0.95)), url('background_atm.jpg');
            background-size: cover;
            background-position: center;
            padding: 100px 20px; 
            text-align: center; 
            border-bottom: 3px solid var(--color-primary); 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            gap: 50px;
        }

        .hero-text h1 { 
            font-size: 48px; 
            margin-bottom: 10px; 
            color: var(--color-primary);
            letter-spacing: 2px;
            font-weight: 800;
        }

        .hero-text p { 
            font-size: 22px; 
            color: #444; 
            font-weight: 500;
            font-style: italic;
        }

        .features-section { 
            padding: 80px 20px; 
            background-color: var(--color-light);
            text-align: center;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 50px auto;
        }

        .feature-card {
            background: white;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
            border-top: 5px solid transparent;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .feature-icon {
            font-size: 55px;
            margin-bottom: 25px;
            display: block;
        }

        .cta-buttons {
            margin-top: 50px;
            display: flex;
            justify-content: center;
            gap: 25px;
        }

        .btn-hero {
            padding: 18px 45px;
            font-size: 20px;
            font-weight: bold;
            border-radius: 50px;
            text-decoration: none;
            transition: 0.3s;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

    <div class="content" style="padding: 0;">
        <div class="hero-banner">
            <img src="logo_atm.png" alt="Logo ATM" style="height: 150px;">
            <div class="hero-text">
                <h1>E-ZAKAT ATM</h1>
                <p>"Transformasi Digital Pengurusan Zakat Ketenteraan"</p>
            </div>
            <img src="logo_kagat.png" alt="Logo KAGAT" style="height: 150px;">
        </div>

        <div class="features-section">
            <h2 style="color: var(--color-dark); font-size: 34px; font-weight: 700;">Keunggulan Sistem Digital</h2>
            <div style="width: 80px; height: 5px; background: var(--color-primary); margin: 15px auto 40px;"></div>

            <div class="features-grid">
                <div class="feature-card" style="border-top-color: var(--color-primary);">
                    <span class="feature-icon">📝</span>
                    <h3 style="color: var(--color-primary); margin-bottom: 15px;">Permohonan Mudah</h3>
                    <p style="color: #666; line-height: 1.6;">Hantar borang bantuan zakat secara digital lengkap dengan muat naik dokumen sokongan tanpa perlu beratur.</p>
                </div>

                <div class="feature-card" style="border-top-color: var(--color-secondary);">
                    <span class="feature-icon">🛡️</span>
                    <h3 style="color: var(--color-secondary); margin-bottom: 15px;">Data Terjamin</h3>
                    <p style="color: #666; line-height: 1.6;">Setiap maklumat peribadi dan rekod kewangan dilindungi oleh sistem keselamatan pangkalan data ATM yang tinggi.</p>
                </div>

                <div class="feature-card" style="border-top-color: var(--color-warning);">
                    <span class="feature-icon">📊</span>
                    <h3 style="color: var(--color-warning); margin-bottom: 15px;">Penjejakan Telus</h3>
                    <p style="color: #666; line-height: 1.6;">Pantau status permohonan anda secara masa nyata dari peringkat Markas sehingga keputusan akhir KAGAT.</p>
                </div>
            </div>

            <div class="cta-buttons">
                <a href="Login.php" class="btn btn-primary btn-hero">LOG MASUK SISTEM</a>
                <a href="Register.php" class="btn btn-hero" style="background-color: var(--color-dark); color: white;">DAFTAR AKAUN BARU</a>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

</body>
</html>