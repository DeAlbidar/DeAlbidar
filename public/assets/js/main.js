// Mobile Menu Toggle
const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const navMenu = document.getElementById('navMenu');

if (mobileMenuBtn) {
    mobileMenuBtn.addEventListener('click', () => {
        navMenu.classList.toggle('active');
        
        // Animate hamburger icon
        const spans = mobileMenuBtn.querySelectorAll('span');
        spans.forEach(span => span.classList.toggle('active'));
    });
}

// Close mobile menu when clicking on a link
document.querySelectorAll('.nav-menu a').forEach(link => {
    link.addEventListener('click', () => {
        navMenu.classList.remove('active');
    });
});

// Dark Mode Toggle
const darkModeToggle = document.getElementById('darkModeToggle');
const body = document.body;
const icon = darkModeToggle.querySelector('i');

// Check for saved theme preference
const savedTheme = localStorage.getItem('theme');
if (savedTheme === 'dark') {
    body.classList.add('dark-mode');
    icon.classList.remove('fa-moon');
    icon.classList.add('fa-sun');
}

darkModeToggle.addEventListener('click', () => {
    body.classList.toggle('dark-mode');
    
    if (body.classList.contains('dark-mode')) {
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
        localStorage.setItem('theme', 'dark');
    } else {
        icon.classList.remove('fa-sun');
        icon.classList.add('fa-moon');
        localStorage.setItem('theme', 'light');
    }
});

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Form validation for contact page
const contactForm = document.getElementById('contactForm');
if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        // Basic validation
        let isValid = true;
        const formData = new FormData(contactForm);
        
        for (let [key, value] of formData.entries()) {
            const input = contactForm.querySelector(`[name="${key}"]`);
            if (!value.trim()) {
                isValid = false;
                input.style.borderColor = 'var(--error)';
            } else {
                input.style.borderColor = 'var(--border-color)';
            }
            
            // Email validation
            if (key === 'email' && value.trim()) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    isValid = false;
                    input.style.borderColor = 'var(--error)';
                }
            }
        }
        
        if (isValid) {
            // In a real implementation, this would submit to a PHP backend
            // For now, we'll show a success message
            const formData = new FormData(contactForm);
            const data = Object.fromEntries(formData);
            
            // Simulate form submission
            console.log('Form submitted:', data);
            
            // Show success message (you'd replace this with actual PHP handling)
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success';
            alertDiv.textContent = 'Thank you for your message! This is a demo - in production, this would send an email.';
            
            contactForm.parentNode.insertBefore(alertDiv, contactForm);
            
            // Remove alert after 5 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
            
            contactForm.reset();
        }
    });
}

// Add active class to current page in navigation
const currentLocation = window.location.pathname;
const navLinks = document.querySelectorAll('.nav-menu a');

navLinks.forEach(link => {
    const linkPath = link.getAttribute('href');
    if (currentLocation.includes(linkPath) && linkPath !== '#') {
        link.classList.add('active');
    }
});

// Intersection Observer for fade-in animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe elements for animation
document.querySelectorAll('.skill-category, .project-card, .timeline-item, .education-card').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(el);
});

// Sticky navigation background on scroll
window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 100) {
        navbar.style.backgroundColor = 'var(--bg-light)';
        navbar.style.boxShadow = 'var(--shadow-md)';
    } else {
        navbar.style.backgroundColor = 'var(--bg-light)';
        navbar.style.boxShadow = 'var(--shadow-sm)';
    }
});

// Download CV tracking
const downloadBtn = document.querySelector('.btn-primary[href*="download"]');
if (downloadBtn) {
    downloadBtn.addEventListener('click', () => {
        console.log('CV download initiated');
        // You could add analytics tracking here
    });
}

// Lazy loading for images
if ('loading' in HTMLImageElement.prototype) {
    const images = document.querySelectorAll('img[loading="lazy"]');
    images.forEach(img => {
        img.loading = 'lazy';
    });
} else {
    // Fallback for browsers that don't support lazy loading
    const script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js';
    document.body.appendChild(script);
}