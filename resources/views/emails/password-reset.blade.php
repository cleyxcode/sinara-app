<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - SINARA</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 0;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }
        .token-container {
            background-color: #f8f9fa;
            border: 2px dashed #4CAF50;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }
        .token-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .token-code {
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: bold;
            color: #4CAF50;
            background-color: #e8f5e8;
            padding: 10px;
            border-radius: 4px;
            word-break: break-all;
            margin: 10px 0;
        }
        .copy-instruction {
            font-size: 12px;
            color: #666;
            font-style: italic;
        }
        .warning-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }
        .warning-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .warning-icon {
            color: #ff9800;
            margin-right: 8px;
        }
        .info-box {
            background-color: #e3f2fd;
            border: 1px solid #90caf9;
            color: #1565c0;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }
        .info-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .info-icon {
            color: #2196f3;
            margin-right: 8px;
        }
        .steps {
            counter-reset: step-counter;
        }
        .step {
            counter-increment: step-counter;
            margin: 15px 0;
            padding-left: 30px;
            position: relative;
        }
        .step:before {
            content: counter(step-counter);
            position: absolute;
            left: 0;
            top: 0;
            background-color: #4CAF50;
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #e9ecef;
        }
        .footer-logo {
            color: #4CAF50;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .support-info {
            margin-top: 20px;
            font-size: 12px;
            color: #999;
        }
        ul {
            padding-left: 20px;
        }
        li {
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Reset Password</h1>
            <p>SINARA - Sistem Informasi Kanker Serviks</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                Halo <strong>{{ $user->name }}</strong>,
            </div>
            
            <p>Kami menerima permintaan untuk mereset password akun Anda yang terdaftar dengan email <strong>{{ $email }}</strong>.</p>
            
            <p>Jika Anda yang meminta reset password, silakan ikuti langkah-langkah berikut:</p>
            
            <div class="steps">
                <div class="step">Buka aplikasi SINARA di ponsel Anda</div>
                <div class="step">Di halaman login, tap "Lupa Password?"</div>
                <div class="step">Masukkan email Anda</div>
                <div class="step">Masukkan token di bawah ini ketika diminta</div>
                <div class="step">Buat password baru yang aman</div>
            </div>
            
            <div class="token-container">
                <div class="token-label">TOKEN RESET PASSWORD</div>
                <div class="token-code">{{ $token }}</div>
                <div class="copy-instruction">
                    💡 Tap dan tahan untuk copy token di atas
                </div>
            </div>
            
            <div class="warning-box">
                <div class="warning-title">
                    <span class="warning-icon">⚠️</span>
                    Penting untuk Diketahui
                </div>
                <ul>
                    <li>Token ini <strong>hanya berlaku selama 24 jam</strong></li>
                    <li>Token hanya dapat digunakan <strong>satu kali</strong></li>
                    <li><strong>Jangan bagikan</strong> token ini kepada siapapun</li>
                    <li>Jika Anda tidak meminta reset password, <strong>abaikan email ini</strong></li>
                </ul>
            </div>
            
            <div class="info-box">
                <div class="info-title">
                    <span class="info-icon">🛡️</span>
                    Tips Keamanan Password
                </div>
                <ul>
                    <li>Gunakan password minimal 6 karakter</li>
                    <li>Kombinasikan huruf besar, kecil, angka</li>
                    <li>Hindari menggunakan informasi pribadi</li>
                    <li>Jangan gunakan password yang sama untuk akun lain</li>
                </ul>
            </div>
            
            <p>Jika Anda mengalami kesulitan atau tidak meminta reset password ini, silakan hubungi tim support kami.</p>
        </div>
        
        <div class="footer">
            <div class="footer-logo">SINARA</div>
            <p>Sistem Informasi Deteksi Dini Kanker Serviks</p>
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
            
            <div class="support-info">
                <p>Butuh bantuan? Hubungi support kami:</p>
                <p>📧 support@sinara.id | 📱 +62-XXX-XXXX-XXXX</p>
                <p>&copy; 2025 SINARA. Semua hak dilindungi.</p>
            </div>
        </div>
    </div>
</body>
</html>
