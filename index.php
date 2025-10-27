<?php
$pageTitle = "Complaint Management System - Home";
include "includes/header.php";
?>

<!-- BANNER -->
<section class="home-banner full-width-section">
    <div class="container">
        <h1>Welcome to the Complaint Management System</h1>
        <p>Submit, track, and resolve complaints easily online.</p>
        <a href="user/register.php">Register Now</a>
        <a href="login.php">Login</a>
    </div>
</section>

<main>
    <!-- features -->
    <section class="features full-width-section">
        <div class="container">
            <h2>Features</h2>
            <div class="facilities-base">
                <div class="feature-box facility-box">
                    <div class="facility-icon"><i class="fas fa-plus-square"></i></div>
                    <h3>Submit Complaints</h3>
                    <p>Quickly submit your complaint online without hassle.</p>
                </div>
                <div class="feature-box facility-box">
                    <div class="facility-icon"><i class="fas fa-eye"></i></div>
                    <h3>Track Status</h3>
                    <p>Track your complaint progress in real-time.</p>
                </div>
                <div class="feature-box facility-box">
                    <div class="facility-icon"><i class="fas fa-check-circle"></i></div>
                    <h3>Resolve Quickly</h3>
                    <p>Efficient resolution system for faster complaint handling.</p>
                </div>
            </div>
        </div>
    </section>


    <!-- FACILITIES -->
    <section id="facilities" class="section-facilities full-width-section">
        <div class="container">
            <h2>Our Facilities</h2>
            <p>"We provide the best support and tools for efficient complaint handling."</p>
            <div class="facilities-base">
                <div class="facility-box">
                    <div class="facility-icon"><i class="fas fa-headset"></i></div>
                    <h3>24/7 Support</h3>
                    <p>Our team is always available to help you anytime.</p>
                </div>
                <div class="facility-box">
                    <div class="facility-icon"><i class="fas fa-shield-alt"></i></div>
                    <h3>Secure System</h3>
                    <p>Your data is safe with our highly secured system.</p>
                </div>
                <div class="facility-box">
                    <div class="facility-icon"><i class="fas fa-tachometer-alt"></i></div>
                    <h3>Fast Processing</h3>
                    <p>Quick complaint resolution with our streamlined process.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section id="how-it-works" class="how-it-works full-width-section">
        <div class="container">
            <h2>How It Works</h2>
            <div class="timeline">
                <div class="timeline-step">
                    <div class="circle"><i class="fas fa-user-plus"></i></div>
                    <h3>Register</h3>
                    <p>Create your account to submit complaints.</p>
                </div>
                <div class="timeline-step">
                    <div class="circle"><i class="fas fa-file-signature"></i></div>
                    <h3>Submit Complaint</h3>
                    <p>Fill out complaint details and submit online.</p>
                </div>
                <div class="timeline-step">
                    <div class="circle"><i class="fas fa-check"></i></div>
                    <h3>Track & Resolve</h3>
                    <p>Check status and get updates until resolution.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include "includes/footer.php"; ?>