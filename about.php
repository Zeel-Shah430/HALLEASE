<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;500&display=swap');

.about-container {
    max-width: 1000px;
    margin: 40px auto;
    padding: 20px;
    font-family: 'Poppins', sans-serif;
}

.about-title {
    text-align: center;
    font-size: 42px;
    font-family: 'Playfair Display', serif;   /* Changed HallEase font */
    color: #1f2d3d;
    margin-bottom: 10px;
    letter-spacing: 1px;
}

.about-desc {
    text-align: center;
    color: #555;
    font-size: 18px;
    margin-bottom: 40px;
}

.mv-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
}

.mv-card {
    background: linear-gradient(135deg, #e3f2fd, #fce4ec); /* New aesthetic background */
    border-radius: 14px;
    padding: 28px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.mv-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.mv-card h3 {
    color: #0d47a1;
    margin-bottom: 10px;
    font-weight: 600;
}

.mv-card p {
    color: #333;
    line-height: 1.7;
}

.contact-box {
    margin-top: 50px;
    padding: 25px;
    background: #eef3ff;
    border-radius: 10px;
}

.contact-box h2 {
    color: #2c3e50;
}
</style>

<div class="about-container">

    <h2 class="about-title">About HallEase</h2>
    <p class="about-desc">
        HallEase is a smart web-based hall reservation system that simplifies event
        booking by connecting clients, hall owners, and administrators on a single platform.
        It minimizes manual work, prevents double bookings, and ensures smooth coordination
        using PHP and MySQL.
    </p>

    <div class="mv-grid">
        <div class="mv-card">
            <h3>Our Mission</h3>
            <p>
                To provide a reliable, user-friendly, and efficient digital platform
                that transforms traditional hall booking into a seamless online experience.
            </p>
        </div>

        <div class="mv-card">
            <h3>Our Vision</h3>
            <p>
                To become the leading hall management solution that empowers event planning
                through automation, transparency, and accessibility.
            </p>
        </div>

        <div class="mv-card">
            <h3>Our Core Values</h3>
            <p>
                • Simplicity in design<br>
                • Accuracy in bookings<br>
                • Trust between users<br>
                • Innovation through technology
            </p>
        </div>
    </div>

    <div class="contact-box">
        <h2>Contact Us</h2>
        <p>
            <b>Email:</b> support@hallease.com <br>
            <b>Phone:</b> +91 98765 43210 <br>
            <b>Address:</b> HallEase Office, City Center
        </p>
    </div>

</div>

<?php include 'includes/footer.php'; ?>

