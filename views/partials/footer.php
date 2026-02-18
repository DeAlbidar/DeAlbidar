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
                <a href="#" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                    <i class="fab fa-linkedin"></i>
                </a>
                <a href="mailto:albidarebenezernarh@gmail.com" aria-label="Email">
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


</body>
</html>