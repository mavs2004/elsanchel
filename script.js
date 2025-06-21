/**
* El Sanchel Staycation - Main JavaScript
* This file contains all the interactive functionality for the website
*/


document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize libraries and components
    initializeLibraries();
    
    // Setup navigation and scrolling
    setupNavigation();
    
    // Initialize all forms
    initializeForms();
    
    // Setup scroll effects
    setupScrollEffects();
    
    // Setup interactive elements
    setupInteractiveElements();
});

/**
* Initialize external libraries
*/
function initializeLibraries() {
    // Initialize AOS Animation Library
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 1000,
            easing: 'ease-in-out',
            once: true,
            mirror: false
        });
    }
    
    // Initialize Bootstrap tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    if (tooltipTriggerList.length > 0 && typeof bootstrap !== 'undefined') {
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    }
}

// Customer login form submission
document.getElementById('customer-login-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const email = document.getElementById('customer-email').value;
    const password = document.getElementById('customer-password').value;
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Logging in...';
    
    // Send AJAX request
    fetch('customer_login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // Reload the page to update the navigation
            window.location.reload();
        } else {
            alert(data.message);
            submitBtn.disabled = false;
            submitBtn.textContent = 'Login';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred during login');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Login';
    });
});

/**
* Setup navigation and modal functionality
*/
function setupNavigation() {
    // Login Modal
    const loginBtn = document.getElementById('login-btn');
    let loginModal;
    
    if (document.getElementById('loginModal') && typeof bootstrap !== 'undefined') {
        loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    }
    
    if (loginBtn && loginModal) {
        loginBtn.addEventListener('click', function(e) {
            e.preventDefault();
            loginModal.show();
        });
    }
    
    // Fix for modal close button
    const modalCloseBtn = document.querySelector('#loginModal .btn-close');
    if (modalCloseBtn && loginModal) {
        modalCloseBtn.addEventListener('click', function() {
            loginModal.hide();
        });
    }
    
    // Book Now button functionality - directly open login modal
    const bookNowBtn = document.getElementById('book-now-btn');
    const bookGamingBtn = document.getElementById('book-gaming-btn');
    
    if (bookNowBtn && loginModal) {
        bookNowBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // Open signup tab in the login modal
            const signupTab = document.getElementById('signup-tab');
            if (signupTab && typeof bootstrap !== 'undefined') {
                const tab = new bootstrap.Tab(signupTab);
                tab.show();
            }
            loginModal.show();
        });
    }
    
    if (bookGamingBtn && loginModal) {
        bookGamingBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // Open signup tab in the login modal
            const signupTab = document.getElementById('signup-tab');
            if (signupTab && typeof bootstrap !== 'undefined') {
                const tab = new bootstrap.Tab(signupTab);
                tab.show();
            }
            loginModal.show();
        });
    }
    
    // View Rooms button - scroll to rooms section
    const viewRoomsBtn = document.getElementById('view-rooms-btn');
    if (viewRoomsBtn) {
        viewRoomsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const roomsSection = document.getElementById('rooms');
            if (roomsSection) {
                const navbarHeight = document.querySelector('.navbar').offsetHeight;
                const roomsPosition = roomsSection.getBoundingClientRect().top + window.pageYOffset - navbarHeight;
                
                window.scrollTo({
                    top: roomsPosition,
                    behavior: 'smooth'
                });
            }
        });
    }
    
    // Smooth scrolling for navigation links
    const navLinks = document.querySelectorAll('.navbar-nav a[href^="#"]');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Close mobile menu if open
            const navbarCollapse = document.querySelector('.navbar-collapse');
            if (navbarCollapse && navbarCollapse.classList.contains('show') && typeof bootstrap !== 'undefined') {
                const bsCollapse = new bootstrap.Collapse(navbarCollapse);
                bsCollapse.hide();
            }
            
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                const navbarHeight = document.querySelector('.navbar').offsetHeight;
                const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - navbarHeight;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
}

/**
* Initialize all forms with validation and submission handling
*/
function initializeForms() {
    // Get all form elements
    const loginForm = document.getElementById('login-form');
    const signupForm = document.getElementById('signup-form');
    const checkAvailabilityForm = document.getElementById('check-availability-form');
    const inquiryForm = document.getElementById('inquiry-form');
    const newsletterForm = document.getElementById('newsletter-form');
    
    // Login Form
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            
            // This would be replaced with actual authentication logic
            console.log('Login attempt:', { email, password });
            
            // Simulate successful login
            alert('Login successful!');
            
            // Close modal if it exists
            if (typeof bootstrap !== 'undefined') {
                const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
                if (loginModal) {
                    loginModal.hide();
                }
            }
            
            // Redirect to dashboard (this would be a real URL in production)
            console.log('Redirecting to dashboard...');
        });
    }
    
    // Signup Form
    if (signupForm) {
        signupForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const name = document.getElementById('signup-name').value;
            const email = document.getElementById('signup-email').value;
            const phone = document.getElementById('signup-phone').value;
            const password = document.getElementById('signup-password').value;
            const confirmPassword = document.getElementById('signup-confirm-password').value;
            
            // Basic validation
            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }
            
            // This would be replaced with actual registration logic
            console.log('Signup attempt:', { name, email, phone, password });
            
            // Simulate successful registration
            alert('Registration successful! You can now log in.');
            
            // Switch to login tab
            const loginTab = document.getElementById('login-tab');
            if (loginTab && typeof bootstrap !== 'undefined') {
                const tab = new bootstrap.Tab(loginTab);
                tab.show();
            }
        });
    }
    
    // Check Availability Form
   
    
    // Inquiry Form
    if (inquiryForm) {
        inquiryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const message = document.getElementById('message').value;
            
            // This would be replaced with actual form submission logic
            console.log('Inquiry submitted:', { name, email, phone, message });
            
            // Simulate successful submission
            alert('Thank you for your inquiry! We will get back to you soon.');
            
            // Reset form
            this.reset();
        });
    }
    
    // Newsletter Form
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = this.querySelector('input[type="email"]').value;
            
            // This would be replaced with actual newsletter subscription logic
            console.log('Newsletter subscription:', { email });
            
            // Simulate successful subscription
            alert('Thank you for subscribing to our newsletter!');
            
            // Reset form
            this.reset();
        });
    }
    
    // Set minimum date for check-in and check-out
    const today = new Date().toISOString().split('T')[0];
    const checkInInput = document.getElementById('check-in');
    const checkOutInput = document.getElementById('check-out');
    
    if (checkInInput && checkOutInput) {
        checkInInput.min = today;
        
        checkInInput.addEventListener('change', function() {
            // Set check-out min date to be at least the check-in date
            checkOutInput.min = this.value;
            
            // If check-out date is before new check-in date, update it
            if (checkOutInput.value < this.value) {
                checkOutInput.value = this.value;
            }
        });
    }
    
    // Forgot password link
    const forgotPasswordLink = document.getElementById('forgot-password-link');
    if (forgotPasswordLink) {
        forgotPasswordLink.addEventListener('click', function(e) {
            e.preventDefault();
            alert('Password reset functionality would be implemented here.');
        });
    }
}

/**
* Setup scroll-based effects and animations
*/
function setupScrollEffects() {
    // Active navigation link based on scroll position
    function updateActiveNavLink() {
        const sections = document.querySelectorAll('section[id]');
        const scrollPosition = window.scrollY + 100; // Offset for navbar height
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.offsetHeight;
            const sectionId = section.getAttribute('id');
            const navLink = document.querySelector(`.navbar-nav a[href="#${sectionId}"]`);
            
            if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                document.querySelector('.navbar-nav a.active')?.classList.remove('active');
                navLink?.classList.add('active');
            }
        });
    }
    
    window.addEventListener('scroll', updateActiveNavLink);
    
    // Initialize active nav link on page load
    updateActiveNavLink();
    
    // Navbar background change on scroll
    function updateNavbarBackground() {
        const navbar = document.querySelector('.navbar');
        if (navbar) {
            if (window.scrollY > 50) {
                navbar.classList.add('shadow-sm');
                navbar.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
            } else {
                navbar.classList.remove('shadow-sm');
                navbar.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
            }
        }
    }
    
    window.addEventListener('scroll', updateNavbarBackground);
    updateNavbarBackground();
    
    // Back to top button visibility
    const backToTopButton = document.querySelector('.back-to-top');
    
    if (backToTopButton) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTopButton.classList.add('active');
            } else {
                backToTopButton.classList.remove('active');
            }
        });
        
        backToTopButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

/**
* Setup interactive UI elements and effects
*/
function setupInteractiveElements() {
    // Image hover effect
    const hoverCards = document.querySelectorAll('.hover-card');
    
    hoverCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
            this.style.boxShadow = '0 15px 30px rgba(0, 0, 0, 0.1)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.05)';
        });
    });
    
    // LED light effect for gaming section
    const gamingCards = document.querySelectorAll('.gaming-card');
    
    gamingCards.forEach(card => {
        card.classList.add('led-effect');
    });
    
    // Add pulse animation to brand logo
    const brandHighlight = document.querySelector('.brand-highlight');
    if (brandHighlight) {
        setInterval(() => {
            brandHighlight.classList.add('pulse-animation');
            setTimeout(() => {
                brandHighlight.classList.remove('pulse-animation');
            }, 1000);
        }, 5000);
    }
    
    // Ensure modal can be closed with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && typeof bootstrap !== 'undefined') {
            const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
            if (loginModal) {
                loginModal.hide();
            }
        }
    });
    
    // Ensure modal backdrop click closes the modal
    const modalBackdrop = document.querySelector('.modal-backdrop');
    if (modalBackdrop && typeof bootstrap !== 'undefined') {
        modalBackdrop.addEventListener('click', function() {
            const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
            if (loginModal) {
                loginModal.hide();
            }
        });
    }
}
