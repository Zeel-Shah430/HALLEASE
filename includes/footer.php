<!-- FOOTER -->
<footer class="footer">
    <div class="footer-container">

        <!-- ABOUT -->
        <div class="footer-col">
            <h3>About HallEase</h3>
            <p>
                HallEase is a smart web-based hall reservation system to discover,
                compare, and book event halls for weddings, conferences, parties,
                and programs with ease and confidence.
            </p>

        
        </div>

        <!-- SERVICES -->
        <div class="footer-col">
            <h3>Our Services</h3>
            <ul>
                <li>🏛 Hall Booking</li>
                <li>📅 Event Scheduling</li>
                <li>💳 Secure Payments</li>
                <li>✔ Verified Venues</li>
                <li>📊 Booking Management</li>
            </ul>
        </div>

        <!-- QUICK LINKS -->
        <div class="footer-col">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="/HALLEASE/user/dashboard.php">Home</a></li>
                <li><a href="/HALLEASE/user/book_hall.php">Book Hall</a></li>
                <li><a href="/HALLEASE/user/my_bookings.php">My Bookings</a></li>
                <li><a href="/HALLEASE/about.php">About Us</a></li>
            </ul>
        </div>

        <!-- CONTACT -->
        <div class="footer-col">
            <h3>Contact Us</h3>
            <p>📞 +91 98765 43210</p>
            <p>✉ support@hallease.com</p>
            <p>📍 Rajkot, Gujarat, India</p>

            <h4>Stay Updated</h4>
            <div class="newsletter">
                <input type="email" placeholder="Your email">
                <button>→</button>
            </div>
        </div>

    </div>

    <div class="footer-bottom">
        <p>© 2025 HallEase. All rights reserved.</p>
        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Cookie Policy</a>
        </div>
    </div>
</footer>

<!-- FOOTER CSS -->
<style>
.footer {
    background: linear-gradient(135deg, #1b5e20, #2ecc71);
    color: #fff;
    padding: 60px 40px 30px;
    font-family: 'Segoe UI', sans-serif;
}

.footer-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 40px;
    margin-bottom: 40px;
}

.footer-col h3 {
    font-size: 20px;
    margin-bottom: 15px;
    position: relative;
}

.footer-col h3::after {
    content: '';
    width: 50px;
    height: 3px;
    background: #fff;
    display: block;
    margin-top: 6px;
}

.footer-col p {
    line-height: 1.7;
    font-size: 14px;
    opacity: 0.95;
}

.footer-col ul {
    list-style: none;
    padding: 0;
}

.footer-col ul li {
    margin-bottom: 10px;
    font-size: 14px;
}

.footer-col ul li a {
    color: #fff;
    text-decoration: none;
    opacity: 0.95;
}

.footer-col ul li a:hover {
    text-decoration: underline;
}

/* SOCIAL ICONS */
.social-icons {
    display: flex;
    gap: 12px;
    margin-top: 15px;
}

.social-icons a {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: bold;
    text-decoration: none;
    transition: transform 0.3s;
}

.social-icons a:hover {
    transform: translateY(-4px);
}

/* NEWSLETTER */
.newsletter {
    display: flex;
    margin-top: 12px;
}

.newsletter input {
    flex: 1;
    padding: 10px;
    border-radius: 6px 0 0 6px;
    border: none;
    outline: none;
}

.newsletter button {
    padding: 10px 14px;
    border: none;
    background: #27ae60;
    color: white;
    font-size: 16px;
    border-radius: 0 6px 6px 0;
    cursor: pointer;
}

/* FOOTER BOTTOM */
.footer-bottom {
    border-top: 1px solid rgba(255,255,255,0.3);
    padding-top: 20px;
    text-align: center;
    font-size: 14px;
}

.footer-links {
    margin-top: 8px;
}

.footer-links a {
    color: #fff;
    margin: 0 10px;
    text-decoration: none;
    opacity: 0.9;
}

.footer-links a:hover {
    text-decoration: underline;
}

/* RESPONSIVE */
@media (max-width: 600px) {
    .footer {
        padding: 40px 20px;
    }
}
</style>

<!-- FOOTER JS -->
<script>
document.querySelector('.newsletter button').addEventListener('click', function () {
    const email = document.querySelector('.newsletter input').value;
    if (email === '') {
        alert('Please enter your email');
    } else {
        alert('Thank you for subscribing!');
        document.querySelector('.newsletter input').value = '';
    }
});
</script>

</body>
</html>
