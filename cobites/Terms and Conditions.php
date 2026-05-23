<?php require "auth.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Legal | Cobites</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        :root { --brand: #ff6b35; --brand-dark: #102f15; --slate: #94a3b8; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--brand-dark); color: white; line-height: 1.8; }
       .legal-header {
    padding: 60px 8% 40px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}
        .btn-back {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--brand);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
        }
        h1 { font-family: 'Playfair Display'; font-size: 3rem; color: var(--brand); margin-bottom: 10px; }
        h2 { color: white; margin-top: 40px; border-left: 4px solid var(--brand); padding-left: 15px; }
        p { color: var(--slate); font-size: 1.05rem; }
        .last-update { opacity: 0.6; font-size: 0.8rem; text-transform: uppercase; margin-bottom: 50px; }
    </style>
</head>
<body>
    <div class="legal-header">
                        <a href="homepage.php" class="btn-back">← Back to Home</a>

        <h1>Terms and Conditions</h1>
        <div class="last-update">Effective Date: May 06, 2026</div>

        <section>
            <h2>1. Our Role</h2>
            <p>Cobites provides a digital logistics bridge. We facilitate the connection between surplus food sources and community organizations. We do not manufacture or prepare the food ourselves.</p>
            
            <h2>2. User Conduct</h2>
            <p>Donors agree to provide accurate information regarding food freshness and expiration. Charities agree to use donations solely for community support and not for resale.</p>

            <h2>3. Liability</h2>
            <p>While we verify all partners, Cobites Logistics Network is not liable for any illness or injury resulting from the consumption of donated goods. Participation is at the user's own risk.</p>
            
            <h2>4. Termination</h2>
            <p>We reserve the right to ban accounts that provide false information or fail to complete scheduled pickups multiple times.</p>
        </section>
    </div>
</body>
</html>