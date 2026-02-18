<main>
    <section class="page-header">
        <div class="container">
            <h1>Contact Me</h1>
        </div>
    </section>

    <section class="contact">
        <div class="container">
            <div class="contact-grid">
                <!-- Contact Information -->
                <div class="contact-info">
                    <h2>Let's Connect</h2>
                    <p>I'm always interested in hearing about new opportunities, collaborations, or just having a chat about technology and innovation.</p>
                    
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h3>Email</h3>
                            <a href="mailto:albidarebenezernarh@gmail.com">albidarebenezernarh@gmail.com</a>
                            <a href="mailto:ebenezer.albidar.narh@innink.co.uk">ebenezer.albidar.narh@innink.co.uk</a>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h3>Phone</h3>
                            <a href="tel:+233244285651">+233 24 428 5651</a>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h3>Location</h3>
                            <p>Accra, Ghana (Open to Remote & Relocation)</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <i class="fab fa-github"></i>
                        <div>
                            <h3>GitHub</h3>
                            <a href="https://github.com/DeAlbidar" target="_blank">github.com/DeAlbidar</a>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <i class="fas fa-globe"></i>
                        <div>
                            <h3>Portfolio</h3>
                            <a href="https://www.dealbidar.com" target="_blank">www.dealbidar.com</a>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Form -->
                <div class="contact-form">
                    <h2>Send a Message</h2>
                    
                    <form method="POST" action="" action="<?php echo URL.'index/contact'; ?>">
                        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
                        <input type="hidden" name="action" value="validate_captcha">
                        
                        <div class="form-group">
                            <label for="name">Name *</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject *</label>
                            <input type="text" id="subject" name="subject" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea id="message" name="message" rows="5" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-full">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>