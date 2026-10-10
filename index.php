<?php
require_once 'config/database.php';
$db = Database::getInstance();
$newsItems = $db->fetchAll("SELECT * FROM news WHERE is_published = 1 ORDER BY published_at DESC LIMIT 3");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bure Town Online Tax Collection System</title>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1a6b3c;
            --primary-dark: #124d2c;
            --secondary: #f4a800;
            --light: #f0f4f8;
            --dark: #1a1a2e;
            --font: 'Segoe UI', sans-serif;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: var(--font); color: var(--dark); line-height: 1.6; background: #fff; }
        a { text-decoration: none; color: var(--primary); }
        
        /* Navbar */
        header { 
            position: absolute; top: 0; left: 0; right: 0; 
            padding: 24px 60px; display: flex; justify-content: space-between; align-items: center;
            z-index: 100;
        }
        .logo { display: flex; align-items: center; gap: 12px; color: #fff; font-size: 1.2rem; font-weight: 800; }
        .logo span { background: var(--secondary); padding: 8px; border-radius: 8px; display: flex; }
        .nav-buttons { display: flex; gap: 16px; }
        .btn { padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.3s; }
        .btn-outline { border: 2px solid rgba(255,255,255,0.3); color: #fff; }
        .btn-outline:hover { border-color: #fff; background: rgba(255,255,255,0.1); }
        .btn-primary { background: var(--secondary); color: var(--dark); border: none; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(244,168,0,0.4); }

        /* Hero */
        .hero {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #0e3d24 40%, var(--primary) 100%);
            min-height: 100vh;
            display: flex; flex-direction: column; justify-content: center;
            padding: 0 60px; color: #fff;
            position: relative; overflow: hidden;
        }
        .hero::before {
            content: ''; position: absolute; inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .hero-content { max-width: 800px; position: relative; z-index: 10; margin-top: 60px; }
        .hero-content h1 { font-size: 4rem; font-weight: 800; line-height: 1.1; margin-bottom: 24px; }
        .hero-content p { font-size: 1.25rem; opacity: 0.9; margin-bottom: 40px; max-width: 600px; }
        
        /* Stats Bar */
        .stats-bar {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 20px; max-width: 1200px; margin: -60px auto 60px;
            padding: 0 40px; position: relative; z-index: 20;
        }
        .stat-item {
            background: #fff; padding: 32px 24px; border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08); text-align: center;
        }
        .stat-item h3 { font-size: 2.5rem; color: var(--primary); font-weight: 800; line-height: 1; margin-bottom: 8px; }
        .stat-item p { color: #666; font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }

        /* Features */
        .section { padding: 80px 40px; max-width: 1200px; margin: 0 auto; }
        .section-title { text-align: center; font-size: 2.5rem; color: var(--primary-dark); margin-bottom: 60px; }
        
        .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 40px; }
        .feature-card { text-align: center; padding: 40px; border-radius: 16px; background: var(--light); transition: 0.3s; }
        .feature-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.05); background: #fff; }
        .feature-icon { font-size: 3rem; margin-bottom: 20px; display: inline-block; }
        .feature-card h3 { font-size: 1.4rem; margin-bottom: 12px; color: var(--dark); }
        .feature-card p { color: #666; }

        /* How it Works */
        .steps { display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; margin-top: 40px; }
        .step { text-align: center; position: relative; }
        .step-num { 
            width: 60px; height: 60px; background: var(--primary); color: #fff; 
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; font-weight: 800; margin: 0 auto 20px;
        }
        .step h3 { margin-bottom: 10px; }
        .step p { color: #666; font-size: 0.9rem; }

        /* News Section */
        .news-section { background: var(--light); padding: 80px 40px; }
        .news-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; max-width: 1200px; margin: 0 auto; }
        .news-card { background: #fff; padding: 30px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .news-date { color: var(--primary); font-size: 0.8rem; font-weight: 700; margin-bottom: 10px; display: block; }
        .news-card h3 { margin-bottom: 15px; font-size: 1.2rem; }
        .news-card p { color: #666; font-size: 0.95rem; }

        /* Footer */
        footer { background: var(--dark); color: #fff; padding: 60px 40px 30px; text-align: center; }
        .footer-content { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; text-align: left; margin-bottom: 40px; }
        footer p { opacity: 0.7; }
        .copyright { border-top: 1px solid rgba(255,255,255,0.1); padding-top: 30px; font-size: 0.9rem; opacity: 0.6; }

        @media (max-width: 900px) {
            .hero-content h1 { font-size: 2.5rem; }
            .stats-bar, .features-grid, .steps, .news-grid { grid-template-columns: 1fr; }
            .stats-bar { margin-top: -30px; padding: 0 20px; }
        }
    </style>
</head>
<body>

    <header>
        <div class="logo">
            <span>🏛️</span> Bure Town TCMS
        </div>
        <div class="nav-buttons">
            <a href="login.php" class="btn btn-outline">Log In</a>
            <a href="register.php" class="btn btn-primary">Register Now</a>
        </div>
    </header>

    <div class="hero">
        <div class="hero-content">
            <h1>Modernizing Tax Collection in Bure Town</h1>
            <p>A secure, efficient, and transparent online system designed to make tax registration and payment easier for everyone.</p>
            <div class="nav-buttons" style="margin-top: 20px;">
                <a href="register.php" class="btn btn-primary" style="padding: 16px 32px; font-size: 1.1rem;">Get Started</a>
                <a href="#how-it-works" class="btn btn-outline" style="padding: 16px 32px; font-size: 1.1rem;">Learn More</a>
            </div>
        </div>
    </div>

    <div class="stats-bar">
        <div class="stat-item">
            <h3>5+</h3>
            <p>Years Service</p>
        </div>
        <div class="stat-item">
            <h3>10k+</h3>
            <p>Taxpayers</p>
        </div>
        <div class="stat-item">
            <h3>100%</h3>
            <p>Secure Payments</p>
        </div>
        <div class="stat-item">
            <h3>24/7</h3>
            <p>Online Access</p>
        </div>
    </div>

    <div class="section" id="how-it-works">
        <h2 class="section-title">How It Works</h2>
        <div class="steps">
            <div class="step">
                <div class="step-num">1</div>
                <h3>Register Account</h3>
                <p>Create your online account and submit your details for verification.</p>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <h3>Submit Documents</h3>
                <p>Provide necessary business and income documents to the tax office.</p>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <h3>Tax Assessment</h3>
                <p>Officers calculate your tax based on standard brackets.</p>
            </div>
            <div class="step">
                <div class="step-num">4</div>
                <h3>Pay Online</h3>
                <p>Submit payments via Bank or Telebirr and download receipts.</p>
            </div>
        </div>
    </div>

    <div class="section">
        <h2 class="section-title">Key Features</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Secure & Reliable</h3>
                <p>Your financial data is protected with industry-standard security and role-based access.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Fast Processing</h3>
                <p>No more long queues. Register, track, and pay your taxes from anywhere, anytime.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Real-time Tracking</h3>
                <p>Monitor your payment status and tax history in real-time through your dashboard.</p>
            </div>
        </div>
    </div>

    <?php if (!empty($newsItems)): ?>
    <div class="news-section">
        <h2 class="section-title" style="margin-bottom: 40px;">Latest Announcements</h2>
        <div class="news-grid">
            <?php foreach ($newsItems as $news): ?>
            <div class="news-card">
                <span class="news-date"><?= date('F j, Y', strtotime($news['published_at'])) ?> • <?= ucfirst($news['category']) ?></span>
                <h3><?= htmlspecialchars($news['title']) ?></h3>
                <p><?= htmlspecialchars(substr($news['content'], 0, 100)) ?>...</p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <footer>
        <div class="footer-content">
            <div>
                <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">🏛️ Bure Town TCMS</h3>
                <p>Empowering the local economy through efficient and transparent tax management.</p>
            </div>
            <div>
                <h3 style="margin-bottom: 20px;">Contact Information</h3>
                <p>📍 Bure Town, Amhara Region, Ethiopia</p>
                <p>📞 +251 910 000 000</p>
                <p>✉️ info@buretcms.gov.et</p>
            </div>
        </div>
        <div class="copyright">
            &copy; <?= date('Y') ?> Bure Town Tax Collection Department. All Rights Reserved.
        </div>
    </footer>

</body>
</html>
