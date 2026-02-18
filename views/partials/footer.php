<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-info">
                <p>&copy; <?php echo date('Y'); ?> Ebenezer Albidar Narh. All rights reserved.</p>
            </div>
            <div class="footer-social">
                <a href="https://github.com/DeAlbidar" target="_blank" rel="noopener noreferrer" aria-label="GitHub">
                    <i class="fab fa-github"></i>
                </a>
                <a href="https://www.linkedin.com/in/dealbidar" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                    <i class="fab fa-linkedin"></i>
                </a>
                <a href="https://web.facebook.com/DeAlbidar" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                    <i class="fab fa-facebook"></i>
                </a>
                <a href="https://x.com/DeAlbidar" target="_blank" rel="noopener noreferrer" aria-label="Twitter">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="https://instagram.com/dealbidar" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="mailto:ebenezer.albidar.narh@innink.co.uk" aria-label="Email">
                    <i class="fas fa-envelope"></i>
                </a>
            </div>
        </div>
    </div>
</footer>

<?php
if (isset($this->js)) {
    foreach ($this->js as $js) {
        echo '<script src="' . URL . $js . '"></script>';
    }
}
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-menu a');
    
    navLinks.forEach(link => {
        if (link.getAttribute('href').includes(currentPath.split('/').pop())) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
});
</script>
</body>
</html>