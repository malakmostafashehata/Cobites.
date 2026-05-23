<?php
require "auth.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cobites | Verified Food Logistics</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --brand: #ff6b35; 
            --brand-dark: #102f15; 
            --brand-sage: #728c5a; 
            --brand-soft: #eaf1b1; 
            --white: #ffffff;
            --slate: #94a3b8;
            --glass: rgba(255, 255, 255, 0.05);
        }
        
.toast{
    position:fixed;

    bottom:20px;   
    right:20px;

    background:#10b981;
    color:white;

    padding:15px 25px;
    border-radius:10px;

    font-size:14px;
    box-shadow:0 5px 20px rgba(0,0,0,.25);

    opacity:0;
    transform:translateX(120%);
    transition:all .4s ease;

    z-index:99999;
}

.toast.show{
    opacity:1;
    transform:translateX(0);
}
        * { box-sizing: border-box; margin: 0; padding: 0; scroll-behavior: smooth; }
        
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-thumb { background: var(--brand); border-radius: 10px; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--brand-dark);
            color: var(--white);
            overflow-x: hidden;
        }

        .reveal { opacity: 0; transform: translateY(30px); transition: 0.8s ease-out; }
        .reveal.active { opacity: 1; transform: translateY(0); }

        nav {
            position: fixed; top: 0; width: 100%;
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 8%; 
            background: rgba(16, 47, 21, 0.98);
            backdrop-filter: blur(15px); z-index: 1000;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav-links { display: flex; gap: 30px; align-items: center; }
.nav-links a{
    color: var(--text) ; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: 0.3s; opacity: 0.8; 
}        .nav-links a:hover { opacity: 1; color: var(--brand); }

        .logo { color: var(--text); font-family: 'Playfair Display', serif; font-size: 1.6rem; font-weight: 800; text-decoration: none; }
        .logo span { color: var(--brand); }

       .btn-auth{
    background: var(--brand);
    color: #fff;
    
    padding: 6px 14px;   
    font-size: 0.8rem;
    
    border-radius: 10px;
    
    font-weight: 600;
    text-decoration: none;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    height: 34px;        
    min-width: 110px;    

    transition: 0.3s;
}
.btn-auth:hover{
    transform: translateY(-2px);
    background: white; 
}
        .hero {
            height: 90vh; display: flex; align-items: center;
            padding: 0 8%; background: radial-gradient(circle at 80% 20%, rgba(255, 107, 53, 0.1), transparent 40%);
        }
        .hero h1 { font-size: clamp(3rem, 7vw, 5rem); font-family: 'Playfair Display'; line-height: 1.1; margin-bottom: 20px; }
        .hero h1 span { color: var(--brand); }
        .hero p { max-width: 600px; font-size: 1.1rem; color: var(--slate); margin-bottom: 30px; }

        .section-padding { padding: 100px 8%; }
        .section-title { font-family: 'Playfair Display'; font-size: 2.5rem; margin-bottom: 10px; }
        .section-subtitle { color: var(--brand); font-weight: 700; text-transform: uppercase; letter-spacing: 2px; font-size: 0.8rem; margin-bottom: 10px; display: block; }

        .story-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 50px; }
        .story-card { background: var(--glass); padding: 40px; border-radius: 30px; border: 1px solid rgba(255,255,255,0.1); }
        .story-card h3 { color: var(--brand); margin-bottom: 15px; font-size: 1.5rem; display: flex; align-items: center; gap: 15px; }

        .stats-container { 
            display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 20px; margin-top: 50px;
        }
        .stat-box { 
            text-align: center; padding: 30px; background: var(--brand-sage); 
            border-radius: 20px; transition: 0.3s;
        }
        .stat-box:hover { transform: scale(1.05); }
        .stat-number { display: block; font-size: 2.5rem; font-weight: 800; }
        .stat-label { font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; }

        .process-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-top: 40px; }
        .step { position: relative; padding: 20px; }
        .step-num { font-size: 4rem; font-weight: 800; color: rgba(255,255,255,0.05); position: absolute; top: 0; left: 0; }
        .step-content { position: relative; z-index: 1; }
        .step-content h4 { margin-bottom: 10px; font-size: 1.2rem; }

        .reviews-section { padding: 100px 0; background: rgba(255,255,255,0.02); overflow: hidden; }
        
        .reviews-track {
            display: flex;
            gap: 30px;
            width: max-content;
            animation: scrollReviews 40s linear infinite;
            padding: 20px 0;
        }

        .reviews-section:hover .reviews-track { animation-play-state: paused; }

        .review-card {
            width: 380px;
            background: var(--glass);
            padding: 40px;
            border-radius: 30px;
            border: 1px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
            transition: 0.3s;
        }
        .review-card:hover { border-color: var(--brand); transform: translateY(-5px); }
        .review-text { font-style: italic; line-height: 1.6; color: var(--white); opacity: 0.9; }
        .reviewer { margin-top: 20px; font-weight: 700; color: var(--brand); display: block; font-size: 0.9rem; }

        @keyframes scrollReviews {
            0% { transform: translateX(0); }
            100% { transform: translateX(calc(-50% - 15px)); }
        }

        footer{
    background:#081a0c;
    padding:80px 8% 40px;
    border-top:1px solid rgba(255,255,255,0.05);

    display:grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap:60px;
}
.footer-col{
    display:flex;
    flex-direction:column;
}
.footer-col h4{
    color:var(--brand);
    margin-bottom:20px;
    font-size:1rem;
}        .footer-col ul{
    list-style:none;
    padding:0;
}

.footer-col ul li{
    margin-bottom:12px;
}

.footer-col ul li a{
    text-decoration:none;
    color:var(--white);
    opacity:0.7;
    transition:0.3s;
}

.footer-col ul li a:hover{
    color:var(--brand);
    opacity:1;
}

.footer-col ul li a:visited{
    color:var(--white);
}.footer-col i{
    transition:0.3s;
}

.footer-col i:hover{
    color:var(--brand);
    transform:translateY(-3px);
}@media(max-width:900px){
    footer{
        grid-template-columns:1fr;
        gap:40px;
        text-align:center;
    }
}
        @media (max-width: 900px) {
            .story-grid, footer { grid-template-columns: 1fr; }
            .nav-links { display: none; }
            .hero h1 { font-size: 3rem; }
        }

.contact-container{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:40px;
    margin-top:50px;
}

.contact-card{
    background:var(--glass);
    padding:40px;
    border-radius:30px;
    border:1px solid rgba(255,255,255,0.1);
}

.contact-card input,
.contact-card textarea{
    width:100%;
    padding:14px;
    margin-bottom:15px;
    border-radius:10px;
    border:none;
    outline:none;
    background:rgba(255,255,255,0.08);
    color:white;
}

.contact-card textarea{
    resize:none;
    height:140px;
}

.contact-card button{
    background:var(--brand);
    border:none;
    padding:14px;
    width:100%;
    border-radius:12px;
    font-weight:700;
    cursor:pointer;
    color:white;
}

.contact-info p{
    color:var(--slate);
    margin-bottom:15px;
}

@media(max-width:900px){
    .contact-container{
        grid-template-columns:1fr;
    }
}#adminPanelBtn{
    position: relative;
    z-index: 9999;
}
body.light {
    background: #f5f7f5;
    color: #102f15;
}
body.light nav {
    background: rgba(245, 247, 245, 0.95);
}
body.light {
    background: #eaf4ec; 
    color: #102f15;
}

body.light nav{
    background: rgba(234, 244, 236, 0.95);
}

body.light .story-card,
body.light .review-card,
body.light .contact-card{
    background: rgba(16,47,21,0.05);
    border:1px solid rgba(16,47,21,0.1);
}

body.light .review-text,
body.light .contact-info p,
body.light .hero p{
    color:#2f4f38;
}
.toggle-btn{
    background: transparent;
    border: 1px solid rgba(255,255,255,0.3);
    color: inherit;
    padding: 8px 12px;
    border-radius: 10px;
    cursor: pointer;
}
html {
    scroll-behavior: auto;
}
    </style>
</head>


<body>

    <nav>
       
        <a href="#" class="logo">Cobites<span>.</span></a>
     <div class="nav-links">

<a href="#stats">Analytics</a>
<a href="#process">How it Works</a>
<a href="#reviews-section">Latest news</a>
<a href="#contact">Contact Us</a>
<?php if(isset($_SESSION['user_id'])): ?>

    <?php if($_SESSION['role'] === 'volunteer'): ?>
        <a href="volunteer.php" class="btn-auth">Donate Now</a>

    <?php elseif($_SESSION['role'] === 'charity'): ?>
        <a href="charity.php" class="btn-auth">View Donations</a>

    <?php elseif($_SESSION['role'] === 'delivery'): ?>
        <a href="delivery.php" class="btn-auth">View Orders</a>

    <?php elseif($_SESSION['role'] === 'admin'): ?>
        <a href="admin.php" class="btn-auth">Admin Panel</a>
    <?php endif; ?>
    <a href="profile.php" class="btn-auth">
        👤 <?= htmlspecialchars($_SESSION['full_name']); ?>
    </a>

<?php else: ?>

    <a href="index.php" class="btn-auth">Login / Register</a>

<?php endif; ?>
</div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <span class="section-subtitle reveal">Verified Food Logistics</span>
            <h1 class="reveal">Automating the path from <span>Surplus</span> to <span>Shelter</span>.</h1>
            <p class="reveal">The central digital nervous system for food donations, connecting volunteers, charities, and delivery partners in real-time.</p>
            <div class="reveal">
                <a href="#story" class="btn-auth" style="padding: 15px 40px;">Our Story</a>
            </div>
        </div>
    </section>

    <section class="section-padding" id="story">
        <span class="section-subtitle">Our Mission</span>
        <h2 class="section-title">The Challenge & Our Answer</h2>
        <div class="story-grid">
            <div class="story-card reveal">
                <h3><i class="fas fa-exclamation-triangle"></i> The Problem</h3>
                <p>Food waste is a global crisis, yet community shelters remain under-resourced. Traditional donation methods are fragmented, rely on manual coordination, and lack the transparency needed to ensure safe delivery.</p>
            </div>
            <div class="story-card reveal" style="border-color: var(--brand-sage);">
                <h3><i class="fas fa-check-circle"></i> The Solution</h3>
                <p>CO-BITES provides a streamlined digital ecosystem. We automate the verification process, optimize route planning for drivers, and provide a centralized dashboard for real-time impact monitoring.</p>
            </div>
        </div>
    </section>

    <section class="section-padding" id="stats" style="background: rgba(255,255,255,0.01);">
        <div class="reveal" style="text-align: center; margin-bottom: 40px;">
            <h2 class="section-title">Live Impact Analytics</h2>
            <p style="color: var(--slate);">Real-time data from our global logistics network.</p>
        </div>
        <div class="stats-container reveal">
            <div class="stat-box">
                <span class="stat-number">12.4k</span>
                <span class="stat-label">KG Diverted</span>
            </div>
            <div class="stat-box">
                <span class="stat-number">99.8%</span>
                <span class="stat-label">Delivery Rate</span>
            </div>
            <div class="stat-box">
                <span class="stat-number">450+</span>
                <span class="stat-label">Verified NGOs</span>
            </div>
            <div class="stat-box">
                <span class="stat-number">22%</span>
                <span class="stat-label">Efficiency Gain</span>
            </div>
        </div>
    </section>

    <section class="section-padding" id="process">
        <span class="section-subtitle">Workflow</span>
        <h2 class="section-title">How the Process Works</h2>
        <div class="process-row">
            <div class="step reveal">
                <span class="step-num">01</span>
                <div class="step-content">
                    <h4>Register</h4>
                    <p style="color: var(--slate); font-size: 0.9rem;">Donors log surplus food details, quantity, and expiration windows through our portal.</p>
                </div>
            </div>
            <div class="step reveal">
                <span class="step-num">02</span>
                <div class="step-content">
                    <h4>Verify & Claim</h4>
                    <p style="color: var(--slate); font-size: 0.9rem;">Verified charities receive instant alerts and claim donations that match their needs.</p>
                </div>
            </div>
            <div class="step reveal">
                <span class="step-num">03</span>
                <div class="step-content">
                    <h4>Logistics</h4>
                    <p style="color: var(--slate); font-size: 0.9rem;">Drivers are routed via GPS to pick up and deliver using AI-optimized paths.</p>
                </div>
            </div>
            <div class="step reveal">
                <span class="step-num">04</span>
                <div class="step-content">
                    <h4>Confirm</h4>
                    <p style="color: var(--slate); font-size: 0.9rem;">Admins monitor the entire hand-off to maintain 100% transparency and safety.</p>
                </div>
            </div>
        </div>
    </section>
<section id="reviews-section" class="reviews-section">
<div style="padding: 0 8%; margin-bottom: 40px;">
    <span class="section-subtitle">Testimonials</span>
    <h2 class="section-title">Latest News</h2>
</div>

<div class="reviews-track" id="reviews-track">
    <div class="review-card">
        
        <p class="review-text">
            "CoBites successfully redistributed over 10,000 meals this quarter, reducing food waste across multiple cities."
        </p>
        <span class="reviewer">— May 2026 | Project Achievement</span>
    </div>

    <div class="review-card">
        <p class="review-text">
            "Launch of the real-time donation tracking system allowing NGOs to monitor food delivery instantly."
        </p>
        <span class="reviewer">— April 2026 | System Update</span>
    </div>

    <div class="review-card">
        <p class="review-text">
            "Partnership signed with regional charities to expand emergency food distribution coverage."
        </p>
        <span class="reviewer">— March 2026 | Partnership News</span>
    </div>

    <div class="review-card">
        <p class="review-text">
            "Volunteer registrations increased by 60%, strengthening last-mile food logistics operations."
        </p>
        <span class="reviewer">— February 2026 | Community Growth</span>
    </div>

    <div class="review-card">
        <p class="review-text">
            "New AI-based demand prediction added to improve food allocation efficiency."
        </p>
        <span class="reviewer">— January 2026 | Technology Release</span>
    </div>

</div>
</section>
<section class="section-padding" id="contact">
    
    <span class="section-subtitle">Get In Touch</span>
    <h2 class="section-title">Contact Us</h2>

    <div class="contact-container">

        <div class="contact-card reveal">
<form id="contactForm">

<input type="text" name="name" placeholder="Your Name" required>

<input type="email" name="email" placeholder="Email Address" required>

<textarea name="message" placeholder="Your Message" required></textarea>

<button type="submit">Send Message</button>

</form>
        </div>

        <div class="contact-card contact-info reveal">
            <h3 style="color:var(--brand); margin-bottom:20px;">Let's Talk</h3>

            <p><i class="fas fa-envelope"></i> support@cobites.com</p>
            <p><i class="fas fa-phone"></i> +2012 0123 4567</p>
            <p><i class="fas fa-location-dot"></i> Tanta, Egypt</p>

            <p>
                Our logistics team is available 24/7 to support food donors,
                volunteers, and partner organizations.
            </p>
        </div>

    </div>
</section>
   <footer>

    <div class="footer-col">
        <a href="#" class="logo">Cobites<span>.</span></a>

        <p style="color: var(--slate); margin-top: 20px; font-size: 0.9rem; line-height: 1.6;">
            Cobites is a smart food-logistics platform connecting restaurants,
            donors, volunteers, and NGOs to reduce food waste and fight hunger
            through real-time verified delivery systems.
        </p>

        <div style="margin-top:20px; font-size:1.2rem;">
            <i class="fab fa-facebook" style="margin-right:15px; cursor:pointer;"></i>
            <i class="fab fa-instagram" style="margin-right:15px; cursor:pointer;"></i>
            <i class="fab fa-linkedin" style="margin-right:15px; cursor:pointer;"></i>
            <i class="fab fa-twitter" style="cursor:pointer;"></i>
        </div>
    </div>


    <div class="footer-col">
        <h4>Platform</h4>
        <ul>
            <li><a href="#stats"> Live Analytics</a></li>
            <li><a href="#process">How it Works</a></li>
<li><a href="#reviews-section">Latest news</a></li>            <li><a href="index.php">Join Cobites</a></li>
        </ul>
    </div>


 <div class="footer-col">
    <h4>Support</h4>
    <ul>
        <li><a href="Help Center.php">Help Center</a></li>
<li><a href="Privacy_Policy.php">Privacy Policy</a></li>
<li><a href="Terms and Conditions.php">Terms & Conditions</a></li>
        <li><a href="homepage.php#contact">Contact Us</a></li>
    </ul>
</div>

    <div style="
        grid-column: 1 / -1;
        border-top: 1px solid rgba(255,255,255,0.05);
        padding-top: 25px;
        text-align:center;
        font-size:0.8rem;
        opacity:0.6;
    ">
        © 2026 Cobites Logistics Network — Fighting Food Waste with Technology 🌍
    </div>

</footer>

    <script>
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) entry.target.classList.add('active');
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    </script>
<script>
function toggleMode(){
    document.body.classList.toggle('light');

    localStorage.setItem(
        'mode',
        document.body.classList.contains('light') ? 'light' : 'dark'
    );
}

window.onload = () => {
    if(localStorage.getItem('mode') === 'light'){
        document.body.classList.add('light');
    }
};
</script>
<script>
document.getElementById("contactForm")
.addEventListener("submit", function(e){

    e.preventDefault();

    let formData = new FormData(this);

    fetch("contact.php",{
        method:"POST",
        body:formData
    })
    .then(res=>res.text())
    .then(data=>{

      let toast = document.getElementById("toast");

if(toast){
    toast.classList.add("show");

    setTimeout(()=>{
        toast.classList.remove("show");
    },3000);
}

        document.getElementById("contactForm").reset();
    });
});
</script>
<div id="toast" class="toast">
    Message Sent Successfully ✅
</div>
</body>
</html>