<?php require "auth.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center | Cobites</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --brand: #ff6b35; 
            --brand-dark: #102f15; 
            --slate: #94a3b8; 
            --glass: rgba(255, 255, 255, 0.05);
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--brand-dark); 
            color: white; 
            margin: 0; 
            line-height: 1.6;
        }

        .section-padding { padding: 80px 8%; }

        .section-title { 
            font-family: 'Playfair Display'; 
            font-size: clamp(2.5rem, 5vw, 3.5rem); 
            margin-bottom: 40px; 
        }

        .section-title span { color: var(--brand); }

        .story-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 30px; 
        }

        .story-card { 
            background: var(--glass); 
            padding: 40px; 
            border-radius: 30px; 
            border: 1px solid rgba(255,255,255,0.1); 
            transition: 0.3s;
        }

        .story-card:hover {
            border-color: var(--brand);
            transform: translateY(-5px);
        }

        .story-card h3 { 
            color: var(--brand); 
            margin-bottom: 15px; 
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .section-subtitle { 
            color: var(--brand); 
            font-weight: 700; 
            text-transform: uppercase; 
            letter-spacing: 2px; 
            font-size: 0.8rem;
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
    <section class="section-padding">
        <a href="homepage.php" class="btn-back">← Back to Home</a>
        <br>
        <span class="section-subtitle">Support Hub</span>
        <h1 class="section-title">Common <span>Questions.</span></h1>
        
        <div class="story-grid">
            <div class="story-card">
                <h3><i class="fas fa-carrot"></i> What food can I donate?</h3>
                <p style="color: var(--slate);">We accept surplus cooked meals, sealed packaged goods, and fresh produce. All items must be fit for consumption and handled with hygiene.</p>
            </div>

            <div class="story-card">
                <h3><i class="fas fa-route"></i> How does pickup work?</h3>
                <p style="color: var(--slate);">Once you submit a donation, a local charity claims it. A delivery partner is then alerted to pick it up from your address within 2 hours.</p>
            </div>

            <div class="story-card">
                <h3><i class="fas fa-shield-halved"></i> Is my profile public?</h3>
                <p style="color: var(--slate);">No. Your contact details are only shared with the specific charity and driver handling your donation to ensure a smooth hand-off.</p>
            </div>

            <div class="story-card">
                <h3><i class="fas fa-check-double"></i> What does 'Pending' mean?</h3>
                <p style="color: var(--slate);">It means your donation is live on our network and waiting for a local charity to claim it based on their current needs.</p>
            </div>
        </div>
    </section>
</body>
</html>