<?php require "auth.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy | Cobites</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand: #ff6b35; 
            --brand-dark: #102f15; 
            --white: #ffffff;
            --slate: #94a3b8;
            --glass: rgba(255, 255, 255, 0.05);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--brand-dark);
            color: var(--white);
            line-height: 1.6;
            margin: 0;
        }

           .legal-hero {
    padding: 60px 8% 30px; 
    background: transparent; 
}
        

        .content-section {
            padding: 0 8% 100px;
        }

        .legal-card {
            background: var(--glass);
            padding: 50px;
            border-radius: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            max-width: 900px;
        }

        h1 { font-family: 'Playfair Display'; font-size: 3.5rem; margin-bottom: 10px; }
        h1 span { color: var(--brand); }
        
        h2 { color: var(--brand); margin-top: 30px; font-size: 1.5rem; }
        p { color: var(--slate); margin-bottom: 20px; }

        .last-updated {
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--brand);
            display: block;
            margin-bottom: 20px;
        }
        
        .btn-back {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--brand);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <div class="legal-hero">
                <a href="homepage.php" class="btn-back">← Back to Home</a>

        <span class="last-updated">Last Updated: May 2026</span>
        <h1>Privacy <span>Policy.</span></h1>
        <p>How we protect your data within the Cobites network.</p>
    </div>

    <section class="content-section">
        <div class="legal-card">
            <h2>1. Information We Collect</h2>
            <p>To facilitate food logistics, we collect your name, phone number, and pickup/delivery addresses. For organizations, we also collect verification documents and tax IDs.</p>

            <h2>2. How Data is Shared</h2>
            <p>Your contact details are only shared "just-in-the-time." For example, a delivery partner only sees your phone number once they accept your specific pickup request.</p>

            <h2>3. Location Tracking</h2>
            <p>We use GPS data to optimize routes for our delivery partners. This data is only active during a live delivery and is not stored permanently for tracking purposes.</p>

            <h2>4. Security Standards</h2>
            <p>All data is encrypted via SSL. We do not sell your personal data to third-party marketing agencies. Your information is used strictly for fighting food waste.</p>
        </div>
    </section>

</body>
</html>