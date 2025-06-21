<?php
include('db.php');

function checkRoomAvailability($con, $room_type, $check_in, $check_out) {
    
    $total_rooms_query = "SELECT COUNT(*) as total FROM room WHERE type = '$room_type'";
    $total_result = mysqli_query($con, $total_rooms_query);
    $total_row = mysqli_fetch_assoc($total_result);
    $total_rooms = $total_row['total'];
    
    if ($total_rooms == 0) return false;
    
    
    $booked_query = "SELECT COUNT(DISTINCT r.id) as booked 
                    FROM payment p
                    JOIN room r ON p.troom = r.type
                    WHERE r.type = '$room_type'
                    AND (
                        ('$check_in' BETWEEN p.cin AND p.cout) OR 
                        ('$check_out' BETWEEN p.cin AND p.cout) OR
                        (p.cin BETWEEN '$check_in' AND '$check_out') OR
                        (p.cout BETWEEN '$check_in' AND '$check_out')
                    )";
    
    $booked_result = mysqli_query($con, $booked_query);
    $booked_row = mysqli_fetch_assoc($booked_result);
    $booked_rooms = $booked_row['booked'];
    
    return ($total_rooms - $booked_rooms) > 0;
}


if (isset($_POST['check_availability'])) {
    $room_type = $_POST['room_type'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    
    
    if (strtotime($check_in) >= strtotime($check_out)) {
        echo json_encode([
            'available' => false,
            'message' => 'Check-out date must be after check-in date'
        ]);
        exit;
    }
    
    $available = checkRoomAvailability($con, $room_type, $check_in, $check_out);
    
    echo json_encode([
        'available' => $available,
        'message' => $available ? 
            'Room is available for your selected dates!' : 
            'Sorry, no available rooms for selected dates. Please try different dates or another room type.'
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>El Sanchel Staycation - Azure Urban Resort Residences</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Resort Inn Responsive , Smartphone Compatible web template , Samsung, LG, Sony Ericsson, Motorola web design" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false);
		function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- //for-mobile-apps -->
<link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/font-awesome.css" rel="stylesheet"> 
<link rel="stylesheet" href="css/chocolat.css" type="text/css" media="screen">
<link href="css/easy-responsive-tabs.css" rel='stylesheet' type='text/css'/>
<link rel="stylesheet" href="css/flexslider.css" type="text/css" media="screen" property="" />
<link rel="stylesheet" href="css/jquery-ui.css" />
<link href="style.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript" src="js/modernizr-2.6.2.min.js"></script>
<!--fonts-->
<link href="//fonts.googleapis.com/css?family=Oswald:300,400,700" rel="stylesheet">
<link href="//fonts.googleapis.com/css?family=Federo" rel="stylesheet">
<link href="//fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg fixed-top navbar-ultra-glass">
        <div class="container">
            <a class="navbar-brand" href="#">
                <div class="brand-logo">
                    <span class="brand-text">El</span>
                    <span class="brand-highlight">Sanchel</span>
                </div>
                <span class="brand-subtext">Staycation</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-navbar">
                <i class="bi bi-list"></i>
            </button>
            <div class="collapse navbar-collapse" id="main-navbar">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#rooms">Rooms</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#amenities">Amenities</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#location">Location</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <?php if(isset($_SESSION['customer_id'])): ?>
        <li class="nav-item">
            <a class="nav-link" href="customer_dashboard.php">My Account</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="customer_invoices.php">My Invoices</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="logout.php">Logout</a>
        </li>
    <?php else: ?>
        <li class="nav-item">
            <a class="nav-link" href="login.php"  data-bs-target="#customerLoginModal">Login</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="register.php">Register</a>
        </li>
    <?php endif; ?>
    <li class="nav-item">
            <a class="nav-link" href="customer_dashboard.php">Profile</a>
        </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section d-flex align-items-center">
        <div class="hero-overlay"></div>
        <div class="container position-relative">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center hero-content" data-aos="fade-up">
                    <h1 class="hero-title mb-3">Azure Resort Residences</h1>
                    <h2 class="hero-subtitle mb-4">Bicutan, Parañaque</h2>
                    <p class="hero-text mb-5">FAMOUS STAYCATION MAN-MADE BEACH & WAVE POOL</p>
                    <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                        <a href="#" class="btn hero-btn hero-btn-primary" id="book-now-btn">Book Now</a>
                        <a href="#rooms" class="btn hero-btn hero-btn-outline" id="view-rooms-btn">View Rooms</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="wave-bottom">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                <path fill="#ffffff" fill-opacity="1" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,224C672,245,768,267,864,261.3C960,256,1056,224,1152,208C1248,192,1344,192,1392,192L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
            </svg>
        </div>
    </section>

    <!-- Promo Banner -->
    <section class="promo-banner py-4 text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <h2 class="fw-bold mb-2" data-aos="fade-up">PROMO AVAILABLE TODAY</h2>
                    <p class="mb-1" data-aos="fade-up" data-aos-delay="100">UNLIMITED PS4 + KARAOKE + BILLIARDS + BOARD & CARD GAMES</p>
                    <p class="fs-4 fw-bold" data-aos="fade-up" data-aos-delay="200">FOR AS LOW AS ₱2,499</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="section-title" data-aos="fade-up">About Us</h2>
                <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Your Perfect Staycation Destination</p>
            </div>
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="about-image-container">
                        <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/477249502_534452723009701_4580897204558306495_n.jpg-twDQKX8qE0Yu4kW7vpiouJjyqwVrrc.jpeg" alt="Azure Urban Resort Residences" class="img-fluid rounded shadow main-image">
                        <div class="about-image-floating">
                            <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/481998384_553783997743240_6736502759646972120_n.jpg-liq7ccruaUkOiCKgEIq98IjeGoPAbh.jpeg" alt="Azure Facilities" class="img-fluid rounded shadow">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <h3 class="fw-bold mb-4">LEGIT STAYCATION OWNER</h3>
                    <p class="mb-3"><i class="bi bi-check-circle-fill me-2 text-accent"></i> Not an Agent</p>
                    <p class="mb-3"><i class="bi bi-check-circle-fill me-2 text-accent"></i> OWNERS: Mae Miranda, Frincess Joy Miranda & Michael James Sanje</p>
                    <p class="mb-3"><i class="bi bi-check-circle-fill me-2 text-accent"></i> With Guest Reviews, willing for LEGIT CHECK</p>
                    <div class="card mt-4 border-0 shadow-sm ultra-glass-card">
                        <div class="card-body">
                            <p class="mb-2"><i class="bi bi-clock me-2"></i> CHECK IN - 2PM onwards</p>
                            <p class="mb-0"><i class="bi bi-clock me-2"></i> CHECK OUT - 12NN strict</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Room Highlights Section -->
    <section id="rooms" class="rooms-section py-5 bg-light">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="section-title" data-aos="fade-up">Room Highlights</h2>
                <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Everything you need for a perfect stay</p>
            </div>
            
            <!-- Room Showcase -->
            <div class="row mb-5">
                <div class="col-12" data-aos="fade-up">
                    <div class="room-showcase">
                        <div class="row g-0">
                            <div class="col-md-6">
                                <div class="room-image h-100">
                                    <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/487724462_575401575581482_272771674647289062_n.jpg-DPiMxLaDPbFTISfLU64r9dRDXxDVqY.jpeg" alt="Gaming Themed Bedroom" class="img-fluid h-100 w-100 object-fit-cover">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="room-content d-flex flex-column justify-content-center h-100 p-4 p-md-5">
                                    <h3 class="fw-bold mb-3">Modern Gaming Comfort</h3>
                                    <p class="mb-4">Experience our beautifully designed gaming-themed rooms featuring comfortable beds, modern amenities, and stunning city views. Each room is equipped with everything you need for a relaxing stay.</p>
                                    <ul class="feature-list">
                                        <li><i class="bi bi-lightning-charge"></i> LED Ambient Lighting</li>
                                        <li><i class="bi bi-controller"></i> Gaming Decor & Accessories</li>
                                        <li><i class="bi bi-tv"></i> Smart TV with Netflix</li>
                                        <li><i class="bi bi-window"></i> Panoramic City Views</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-3" data-aos="fade-up">
                    <div class="card h-100 border-0 shadow-sm hover-card ultra-glass-card">
                        <div class="card-body text-center p-4">
                            <i class="bi bi-controller feature-icon mb-3"></i>
                            <h3 class="h4 fw-bold mb-3">Entertainment</h3>
                            <ul class="list-unstyled text-start">
                                <li class="mb-2"><i class="bi bi-dot me-2"></i>PS4 with Tekken, NBA, Overcooked & more</li>
                                <li class="mb-2"><i class="bi bi-dot me-2"></i>Billiards Table</li>
                                <li class="mb-2"><i class="bi bi-dot me-2"></i>Portable Karaoke</li>
                                <li class="mb-2"><i class="bi bi-dot me-2"></i>Board Games & Card Games</li>
                                <li class="mb-2"><i class="bi bi-dot me-2"></i>Smart TV (50 inch)</li>
                                <li class="mb-2"><i class="bi bi-dot me-2"></i>Netflix & YouTube</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="card h-100 border-0 shadow-sm hover-card ultra-glass-card">
                        <div class="card-body text-center p-4">
                            <i class="bi bi-house-door feature-icon mb-3"></i>
                            <h3 class="h4 fw-bold mb-3">Accommodation</h3>
                            <ul class="list-unstyled text-start">
                                <li class="mb-2"><i class="bi bi-dot me-2"></i>1 Double Sized Bed</li>
                                <li class="mb-2"><i class="bi bi-dot me-2"></i>1 Sofa Bed</li>
                                <li class="mb-2"><i class="bi bi-dot me-2"></i>1 Extra Double Size Mattress</li>
                                <li class="mb-2"><i class="bi bi-dot me-2"></i>Good for 2-4 pax + 1 kid (3ft below)</li>
                                <li class="mb-2"><i class="bi bi-dot me-2"></i>Modern Design</li>
                                <li class="mb-2"><i class="bi bi-dot me-2"></i>Ambient Lighting</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="card h-100 border-0 shadow-sm hover-card ultra-glass-card">
                        <div class="card-body text-center p-4">
                            <i class="bi bi-cup-hot feature-icon mb-3"></i>
                            <h3 class="h4 fw-bold mb-3">Kitchen</h3>
                            <ul class="list-unstyled text-start">
                                <li class="mb-2"><i class="bi bi-dot me-2"></i>Refrigerator</li>
                                <li class="mb-2"><i class="bi bi-dot me-2"></i>Rice Cooker</li>
                                <li class="mb-2"><i class="bi bi-dot me-2"></i>Microwave</li>
                                <li class="mb-2"><i class="bi bi-dot me-2"></i>Electric Kettle</li>
                                <li class="mb-2"><i class="bi bi-dot me-2"></i>Induction Cooker</li>
                                <li class="mb-2"><i class="bi bi-dot me-2"></i>Kitchen Utensils & Cookware</li>
                            </ul>
                        </div>
                        
                    </div>
                    
                </div>
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="card h-100 border-0 shadow-sm hover-card ultra-glass-card">
                        <div class="card-body text-center p-4">
                            <i class="bi bi-wifi feature-icon mb-3"></i>
                            <h3 class="h4 fw-bold mb-3">Extras</h3>
                            <ul class="list-unstyled text-start">
                                <li class="mb-2"><i class="bi bi-dot me-2"></i>Unlimited Wi-Fi</li>
                                <li class="mb-2"><i class="bi bi-dot me-2"></i>Air Conditioning</li>
                                <li class="mb-2"><i class="bi bi-dot me-2"></i>Shower Heater</li>
                                <li class="mb-2"><i class="bi bi-dot me-2"></i>FREE Guest Kit (soap & shampoo)</li>
                                <li class="mb-2"><i class="bi bi-dot me-2"></i>Dining Table and Chairs</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gaming Theme Section -->
    <section class="gaming-section py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="section-title text-white" data-aos="fade-up">Gaming Paradise</h2>
                <p class="section-subtitle text-white-50" data-aos="fade-up" data-aos-delay="100">For the ultimate gaming experience</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6" data-aos="fade-right">
                    <div class="card border-0 shadow gaming-card h-100">
                        <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/487854603_576566118798361_453417289174208579_n.jpg-zldOkQbnaFtv9v09nAg5cw0cXM0iXz.jpeg" class="card-img-top" alt="Gaming Setup">
                        <div class="card-body">
                            <h3 class="fw-bold mb-3">Gamer's Dream Setup</h3>
                            <p>Our gaming-themed rooms feature colorful LED lighting, gaming decor, and all the equipment you need for an immersive gaming experience.</p>
                            <ul class="gaming-features">
                                <li><i class="bi bi-controller"></i> Latest PS4 Games</li>
                                <li><i class="bi bi-lightbulb"></i> RGB Lighting</li>
                                <li><i class="bi bi-tv"></i> Large Screen TV</li>
                                <li><i class="bi bi-headset"></i> Gaming Accessories</li>
                            </ul>
                           
                        </div>
                    </div>

                </div>
                <div class="col-md-6" data-aos="fade-left">
                    <div class="card border-0 shadow gaming-card h-100">
                        <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/487668525_575401512248155_275010641830891244_n.jpg-oz9dh9nqu0qaC9Cu84AWzlONV0hZQv.jpeg" class="card-img-top" alt="Gaming Room">
                        <div class="card-body">
                            <h3 class="fw-bold mb-3">Themed Bedrooms</h3>
                            <p>Sleep in style with our gaming-themed bedrooms featuring geometric designs, ambient lighting, and gaming-inspired decor for the ultimate staycation.</p>
                            <ul class="gaming-features">
                                <li><i class="bi bi-lamp"></i> LED Ceiling Lights</li>
                                <li><i class="bi bi-controller"></i> Gaming Wall Art</li>
                                <li><i class="bi bi-palette"></i> Themed Decor</li>
                                <li><i class="bi bi-moon-stars"></i> Ambient Lighting</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-6" data-aos="fade-right" data-aos-delay="100">
                    <div class="card border-0 shadow gaming-card h-100">
                        <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/484506002_561987830256190_141611231311449310_n.jpg-ov3QD6nJspcCBXa773OJmNyNZUTcRT.jpeg" class="card-img-top" alt="Dining Area">
                        <div class="card-body">
                            <h3 class="fw-bold mb-3">Entertainment Lounge</h3>
                            <p>Our modern dining area doubles as an entertainment space with a mini pool table and gaming-themed decor for a complete staycation experience.</p>
                            <ul class="gaming-features">
                                <li><i class="bi bi-table"></i> Mini Billiards</li>
                                <li><i class="bi bi-cup"></i> Dining Area</li>
                                <li><i class="bi bi-lightbulb"></i> Ambient Lighting</li>
                                <li><i class="bi bi-house"></i> Modern Decor</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" data-aos="fade-left" data-aos-delay="100">
                    <div class="card border-0 shadow gaming-card h-100">
                        <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/481250912_553783811076592_3895275037294074466_n.jpg-fgS7MYLaQZBQheAKq3G1nekSnll0mi.jpeg" class="card-img-top" alt="Projector Room">
                        <div class="card-body">
                            <h3 class="fw-bold mb-3">Immersive Experience</h3>
                            <p>Enjoy our ceiling projector displaying space scenes while watching your favorite shows on Netflix, surrounded by motivational wall art.</p>
                            <ul class="gaming-features">
                                <li><i class="bi bi-projector"></i> Ceiling Projector</li>
                                <li><i class="bi bi-tv"></i> Netflix Access</li>
                                <li><i class="bi bi-stars"></i> Space Themes</li>
                                <li><i class="bi bi-quote"></i> Motivational Decor</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <a href="#gaming-room" class="btn btn-light btn-lg rounded-pill px-5" data-aos="fade-up" id="book-gaming-btn">Book Your Gaming Experience</a>
            </div>
        </div>
    </section class="gaming-room">
    
      <section>
     <div class="plans-section" id="rooms">
    <div class="container">
    <h3 class="title-w3-agileits title-black-wthree" data-aos="fade-up">Rooms And Rates</h3>
        <div class="priceing-table-main">
            <
            <div class="col-md-4 price-grid" data-aos="fade-up" data-aos-delay="100">
                <div class="price-block agile">
                    <div class="price-gd-top">
                        <img src="images/galaxy.jpg" alt="MAUI GALAXY Room" class="img-responsive" />
                        <h4>MAUI GALAXY</h4>
                    </div>
                    <div class="price-gd-bottom">
                        <div class="room-details">
                            <p><i class="fa fa-building"></i> Miami Tower, 15th Floor</p>
                           
                            <p><i class="fa fa-users"></i> Max: 4 pax + 1 toodler 3ft below</p>
                            <p><i class="fa fa-gamepad"></i> Gaming: PS4 Pro + 4 controllers</p>
                            <p><i class="fa fa-tv"></i> 55" Smart TV with Netflix</p>
                        </div>
                        <div class="price-list">
                            <ul>
                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                <li><i class="fa fa-star-o" aria-hidden="true"></i></li>
                            </ul>
                        </div>
                        <div class="price-selet">    
    <h3><span>₱</span>2,499-3,199 <small>/night</small></h3>
    <div class="promo-badge">Dynamic Pricing</div>                        
    <a href="admin/reservation.php">Book Now</a>
</div>
                    </div>
                </div>
            </div>
            
           
            <div class="col-md-4 price-grid" data-aos="fade-up" data-aos-delay="200">
                <div class="price-block agile">
                    <div class="price-gd-top">
                        <img src="images/miami.jpg" alt="AURA SANCTUARY Room" class="img-responsive" />
                        <h4>MIAMI GAMING</h4>
                    </div>
                    <div class="price-gd-bottom">
                        <div class="room-details">
                            <p><i class="fa fa-building"></i> Santorni Tower, 9th Floor</p>
                          
                            <p><i class="fa fa-users"></i> Max: 4 pax + 1 toodler 3ft below</p>
                            <p><i class="fa fa-gamepad"></i> Gaming: PS5 + VR Set</p>
                            <p><i class="fa fa-tv"></i> 65" 4K Smart TV</p>
                            <p><i class="fa fa-microphone"></i> Premium Karaoke System</p>
                        </div>
                        <div class="price-list">
                            <ul>
                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                <li><i class="fa fa-star-half-o" aria-hidden="true"></i></li>
                            </ul>
                        </div>
                        <div class="price-selet">    
    <h3><span>₱</span>2,499-3,199 <small>/night</small></h3>
    <div class="promo-badge">Dynamic Pricing</div>                        
    <a href="admin/reservation.php">Book Now</a>
</div>
                    </div>
                    </div>
                </div>
            </div>
            
            <!-- COZZY V1 Room -->
            <div class="col-md-4 price-grid" data-aos="fade-up" data-aos-delay="300">
                <div class="price-block agile">
                    <div class="price-gd-top">
                        <img src="images/cozzy.jpg" alt="BREEZE HORIZON Room" class="img-responsive" />
                        <h4>COZZY V1</h4>
                    </div>
                    <div class="price-gd-bottom">
                        <div class="room-details">
                            <p><i class="fa fa-building"></i> Miami Tower, 19th Floor</p>
                           
                            <p><i class="fa fa-users"></i> Max: 4pax + 1 toodler 3ft below</p>
                            <p><i class="fa fa-gamepad"></i> Gaming: PS4 Slim + 2 controllers</p>
                            <p><i class="fa fa-tv"></i> 50" Smart TV</p>
                            <p><i class="fa fa-table"></i> Board Game Collection</p>
                        </div>
                        <div class="price-list">
                            <ul>
                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                <li><i class="fa fa-star-o" aria-hidden="true"></i></li>
                                <li><i class="fa fa-star-o" aria-hidden="true"></i></li>
                            </ul>
                        </div>
                        <div class="price-selet">    
    <h3><span>₱</span>2,499-3,199 <small>/night</small></h3>
    <div class="promo-badge">Dynamic Pricing</div>                        
    <a href="admin/reservation.php">Book Now</a>
    
</div>
                    </div>
                    </div>
                </div>
            </div>
            
            <div class="clearfix"></div>
        </div>
    </div>
</div>
</section>
<!--// rooms & rates -->

    <!-- Amenities Section -->
    <section id="amenities" class="amenities-section py-5 bg-light">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="section-title" data-aos="fade-up">Resort Amenities</h2>
                <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Free access to amazing facilities</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6" data-aos="fade-up">
                    <div class="card border-0 shadow-sm h-100 amenity-card ultra-glass-card">
                        <div class="card-img-overlay-wrapper">
                            <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/476125819_530328333422140_5472686171726938721_n.jpg-3DLJa63ZVYhFOw7ulMD21jq67cGsHV.jpeg" class="card-img-top" alt="Wave Pool">
                            <div class="card-img-overlay d-flex align-items-end">
                                <div class="overlay-content">
                                    <h3 class="h4 fw-bold">Wave Pool & Man-made Beach</h3>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <p>Experience our famous wave pool and man-made beach - the perfect urban escape without leaving the city.</p>
                            <p class="small text-muted">1st shift: 7am to 12nn | 2nd shift: 2pm to 7pm</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="card border-0 shadow-sm h-100 amenity-card ultra-glass-card">
                        <div class="card-img-overlay-wrapper">
                            <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/481998384_553783997743240_6736502759646972120_n.jpg-liq7ccruaUkOiCKgEIq98IjeGoPAbh.jpeg" class="card-img-top" alt="Sports Facilities">
                            <div class="card-img-overlay d-flex align-items-end">
                                <div class="overlay-content">
                                    <h3 class="h4 fw-bold">Sports & Recreation</h3>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <p>Stay active with our basketball court and other recreational facilities available for all guests.</p>
                            <p class="small text-muted">Open from 6am to 10pm daily</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-6" data-aos="fade-up">
                    <div class="card border-0 shadow-sm h-100 amenity-card ultra-glass-card">
                        <div class="card-img-overlay-wrapper">
                            <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/477249502_534452723009701_4580897204558306495_n.jpg-twDQKX8qE0Yu4kW7vpiouJjyqwVrrc.jpeg" class="card-img-top" alt="Resort Overview">
                            <div class="card-img-overlay d-flex align-items-end">
                                <div class="overlay-content">
                                    <h3 class="h4 fw-bold">Resort Overview</h3>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <p>Azure Urban Resort Residences offers a complete resort experience with multiple pools, man-made beach areas, and relaxation spaces.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="testimonial-card h-100">
                        <div class="card border-0 shadow-sm h-100 ultra-glass-card">
                            <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/486079688_567814113006895_3598730033405397110_n.jpg-pIhm2Qve6CnLr3JtLghbZoTNwgfSys.jpeg" class="card-img-top" alt="Customer Feedback">
                            <div class="card-body">
                                <h3 class="h4 fw-bold mb-3">Guest Testimonials</h3>
                                <p>Our guests love their experience at El Sanchel Staycation. See what they have to say about their stay with us!</p>
                                <div class="text-center mt-3">
                                    <div class="rating">
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <p class="text-muted">Note: ₱250 per head/shift for Wave Pool & Man-made beach access but FREE FOR KIDS 3FT BELOW</p>
            </div>
        </div>
    </section>

    <!-- Location Section -->
    <section id="location" class="location-section py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="section-title" data-aos="fade-up">Location</h2>
                <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Conveniently located at Azure Urban Resort Residences, Bicutan, Parañaque City</p>
            </div>
            <div class="row g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="location-map-container">
                        <div class="ratio ratio-16x9 shadow rounded overflow-hidden">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3863.5661323169396!2d121.03729731483908!3d14.45739998990071!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397cdf5d0b8bea7%3A0x7b7b5fde6f8f7f0c!2sAzure%20Urban%20Resort%20Residences!5e0!3m2!1sen!2sph!4v1617345678901!5m2!1sen!2sph" allowfullscreen="" loading="lazy"></iframe>
                        </div>
                        <div class="location-logo">
                            <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/456448146_397586286696346_1870375836527322297_n.jpg-Ad1B2jzBVzoHxFMOG4iHHqe2KLABce.jpeg" alt="Sanchel Property Rental Logo" class="img-fluid">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <h3 class="h3 fw-bold mb-4">Nearby Destinations:</h3>
                    <ul class="list-unstyled location-list">
                        <li class="mb-3 d-flex">
                            <i class="bi bi-person-walking me-3 fs-4"></i>
                            <span>Walking distance to SM Bicutan</span>
                        </li>
                        <li class="mb-3 d-flex">
                            <i class="bi bi-car-front me-3 fs-4"></i>
                            <span>MOA, City of Dreams, Solaire, and Okada only 15 minutes away by car VIA SKYWAY</span>
                        </li>
                        <li class="mb-3 d-flex">
                            <i class="bi bi-shop me-3 fs-4"></i>
                            <span>Accessible convenience stores, food shops and laundry shops inside the property</span>
                        </li>
                        <li class="mb-3 d-flex">
                            <i class="bi bi-airplane me-3 fs-4"></i>
                            <span>NAIA Airport only 15-20 minutes away VIA SKYWAY</span>
                        </li>
                    </ul>
                    <div class="card mt-4 border-0 shadow-sm ultra-glass-card">
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-3">Azure Urban Resort Residences</h4>
                            <p>Experience the perfect blend of urban living and resort-style amenities at Azure Urban Resort Residences. Our prime location offers convenience and luxury in one package.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Booking Section -->
    <section id="booking" class="booking-section py-5 bg-light">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="section-title" data-aos="fade-up">Book Your Stay</h2>
                <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Experience the perfect staycation</p>
            </div>
            <div class="row g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="card border-0 shadow-sm ultra-glass-card">
                        <div class="card-body p-4">
                            <form id="check-availability-form">
                                <div class="mb-3">
                                    <label for="check-in" class="form-label">Check In</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                                         <input type="date" class="form-control" id="check-in" required 
                                               min="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="check-out" class="form-label">Check Out</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                                        <input type="date" class="form-control" id="check-out" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="room" class="form-label">Room Type</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-people"></i></span>
                                        <select class="form-control" id="room-type" required>
                                            <option value="">Select Room Type</option>
                                            <option value="MAUI GALAXY">MAUI GALAXY</option>
                                            <option value="MIAMI GAMING">MIAMI GAMING</option>
                                            <option value="COZZY ROOM">COZZY ROOM</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="guests" class="form-label">Guests</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-people"></i></span>
                                        < <select class="form-control" id="guests" required>
                                            <option value="1">1 Guest</option>
                                            <option value="2" selected>2 Guests</option>
                                            <option value="3">3 Guests</option>
                                            <option value="4">4 Guests</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
    <p class="text-muted small">Note: Pricing varies by day - Weekdays: ₱2,499 | Fri/Sun: ₱2,999 | Sat/Holidays: ₱3,199</p>
</div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg rounded-pill">Check Availability</button>
                                </div>
                            </form>
                            <!-- Availability Result -->
                            <div id="availability-result" class="mt-4" style="display: none;">
                                <div class="alert" id="availability-alert">
                                    <h4 id="availability-message"></h4>
                                    <div class="mt-3">
                                        <div id="booking-details" class="mb-3"></div>
                                        <a href="admin/reservation.php" class="btn btn-success btn-lg rounded-pill" id="proceed-booking">
                                            Proceed to Booking
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <h3 class="h3 fw-bold mb-4">Why Book With Us?</h3>
                    <ul class="list-unstyled mb-4 feature-list">
                        <li class="mb-3"><i class="bi bi-check-circle-fill me-2 text-accent"></i> Best Price Guarantee</li>
                        <li class="mb-3"><i class="bi bi-check-circle-fill me-2 text-accent"></i> Direct Booking with Owner (No Agents)</li>
                        <li class="mb-3"><i class="bi bi-check-circle-fill me-2 text-accent"></i> Verified Reviews</li>
                        <li class="mb-3"><i class="bi bi-check-circle-fill me-2 text-accent"></i> Flexible Payment Options</li>
                    </ul>
                    <div class="card border-0 shadow-sm ultra-glass-card">
                        <div class="card-body p-4">
                            <p class="mb-2"><i class="bi bi-telephone me-2"></i> Contact: 0970 784 9353 (Mae)</p>
                            <p class="mb-0"><i class="bi bi-people me-2"></i> Owners: Frincess Joy Miranda & Michael James Sanje</p>
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/486079688_567814113006895_3598730033405397110_n.jpg-pIhm2Qve6CnLr3JtLghbZoTNwgfSys.jpeg" alt="Customer Feedback" class="img-fluid rounded shadow">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow ultra-glass-modal">
                <div class="modal-header border-0">
                    <div class="modal-logo">
                        <div class="brand-logo">
                            <span class="brand-text">El</span>
                            <span class="brand-highlight">Sanchel</span>
                        </div>
                        <span class="brand-subtext">Staycation</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-4" id="authTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login-pane" type="button" role="tab" aria-selected="true">Login</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="signup-tab" data-bs-toggle="tab" data-bs-target="#signup-pane" type="button" role="tab" aria-selected="false">Sign Up</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="authTabContent">
                        <div class="tab-pane fade show active" id="login-pane" role="tabpanel">
                            <form id="login-form">
                                <div class="mb-3">
                                    <label for="login-email" class="form-label">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" class="form-control" id="login-email" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="login-password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input type="password" class="form-control" id="login-password" required>
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary rounded-pill">Login</button>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="#" class="text-decoration-none small" id="forgot-password-link">Forgot password?</a>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="signup-pane" role="tabpanel">
                            <form id="signup-form">
                                <div class="mb-3">
                                    <label for="signup-name" class="form-label">Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" class="form-control" id="signup-name" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="signup-email" class="form-label">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" class="form-control" id="signup-email" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="signup-phone" class="form-label">Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                        <input type="tel" class="form-control" id="signup-phone" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="signup-password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input type="password" class="form-control" id="signup-password" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="signup-confirm-password" class="form-label">Confirm Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input type="password" class="form-control" id="signup-confirm-password" required>
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary rounded-pill">Sign Up</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Section -->
    <section id="contact" class="contact-section py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="section-title" data-aos="fade-up">Contact Us</h2>
                <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">We'd love to hear from you</p>
            </div>
            <div class="row g-5">
                <div class="col-lg-5" data-aos="fade-right">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm text-center p-4 h-100 hover-card ultra-glass-card">
                                <i class="bi bi-telephone display-6 mb-3 text-accent"></i>
                                <h3 class="h5 fw-bold">Phone</h3>
                                <p class="mb-0">0970 784 9353</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm text-center p-4 h-100 hover-card ultra-glass-card">
                                <i class="bi bi-envelope display-6 mb-3 text-accent"></i>
                                <h3 class="h5 fw-bold">Email</h3>
                                <p class="mb-0">info@elsanchelstaycation.com</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm text-center p-4 h-100 hover-card ultra-glass-card">
                                <i class="bi bi-geo-alt display-6 mb-3 text-accent"></i>
                                <h3 class="h5 fw-bold">Address</h3>
                                <p class="mb-0">Azure Urban Resort Residences, Bicutan, Parañaque City</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm text-center p-4 h-100 hover-card ultra-glass-card">
                                <i class="bi bi-share display-6 mb-3 text-accent"></i>
                                <h3 class="h5 fw-bold">Social Media</h3>
                                <div class="d-flex justify-content-center gap-3 mt-2">
                                    <a href="https://www.facebook.com/elsanchelstaycation" class="social-icon"><i class="bi bi-facebook"></i></a>
                                    <a href="https://www.instagram.com/elsanchel?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" class="social-icon"><i class="bi bi-instagram"></i></a>
                                    <a href="https://www.tiktok.com/@elsanchel" class="social-icon"><i class="bi bi-tiktok"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7" data-aos="fade-left">
                    <div class="card border-0 shadow-sm ultra-glass-card">
                        <div class="card-body p-4">
                            <form id="inquiry-form">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" class="form-control" id="name" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                        <input type="tel" class="form-control" id="phone" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="message" class="form-label">Message</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-chat-left-text"></i></span>
                                        <textarea class="form-control" id="message" rows="5" required></textarea>
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary rounded-pill">Send Message</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="site-footer py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="footer-brand mb-4">
                        <div class="brand-logo mb-2">
                            <span class="brand-text">El</span>
                            <span class="brand-highlight">Sanchel</span>
                        </div>
                        <p class="text-white-50 mb-0">Property Rental (Booking System)</p>
                    </div>
                    <p class="text-white-50">Experience the perfect blend of comfort, entertainment, and relaxation at Azure Urban Resort Residences.</p>
                </div>
                <div class="col-lg-4">
                    <h3 class="h5 fw-bold mb-3 text-white">Quick Links</h3>
                    <ul class="list-unstyled footer-links">
                        <li class="mb-2"><a href="#home">Home</a></li>
                        <li class="mb-2"><a href="#about">About</a></li>
                        <li class="mb-2"><a href="#amenities">Amenities</a></li>
                        <li class="mb-2"><a href="#rooms">Rooms</a></li>
                        <li class="mb-2"><a href="#location">Location</a></li>
                        <li class="mb-2"><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h3 class="h5 fw-bold mb-3 text-white">Subscribe to our Newsletter</h3>
                    <form id="newsletter-form">
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" placeholder="Your Email" required>
                            <button class="btn btn-light" type="submit">Subscribe</button>
                        </div>
                    </form>
                    <div class="mt-4">
                        <h4 class="h6 fw-bold mb-2 text-white">Payment Methods</h4>
                        <div class="payment-methods">
                            <i class="bi bi-credit-card me-2 fs-4"></i>
                            <i class="bi bi-paypal me-2 fs-4"></i>
                            <i class="bi bi-cash-coin me-2 fs-4"></i>
                            <i class="bi bi-bank fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="my-4 footer-divider">
            <div class="text-center">
                <p class="mb-0 text-white-50">&copy; 2025 El Sanchel Staycation. All Rights Reserved.</p>
            </div>
        </div>
        
    </footer>
    <script src="scroll-preserve.js"></script>
    <!-- Back to Top Button -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- Custom JS -->
    <script src="script.js"></script>
    <style>
/* Chat Widget Styles */
.chat-widget-container {
  position: fixed;
  bottom: 80px;
  right: 20px;
  z-index: 1000;
}

.chat-button {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background-color: #2563EB;
  color: white;
  border: none;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  cursor: pointer;
  transition: transform 0.3s ease, background-color 0.3s ease;
}

.chat-button:hover {
  transform: scale(1.1);
  background-color: #1D4ED8;
}

.chat-button:active {
  transform: scale(0.95);
}

.chat-window {
  position: absolute;
  bottom: 70px;
  right: 0;
  width: 320px;
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transform-origin: bottom right;
  opacity: 0;
  transform: scale(0.95) translateY(20px);
  pointer-events: none;
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.chat-window.open {
  opacity: 1;
  transform: scale(1) translateY(0);
  pointer-events: all;
}

.chat-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  background-color: #2563EB;
  color: white;
}

.chat-title {
  display: flex;
  align-items: center;
}

.chat-title-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background-color: rgba(255, 255, 255, 0.2);
  margin-right: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.chat-title-info h3 {
  margin: 0;
  font-size: 14px;
  font-weight: 600;
}

.chat-title-info p {
  margin: 0;
  font-size: 12px;
  opacity: 0.8;
}

.chat-close-btn {
  background: transparent;
  border: none;
  color: white;
  cursor: pointer;
  padding: 5px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.3s ease;
}

.chat-close-btn:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

.chat-messages {
  height: 320px;
  overflow-y: auto;
  padding: 15px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.chat-message {
  display: flex;
  align-items: flex-start;
  max-width: 85%;
}

.chat-message.user {
  margin-left: auto;
  justify-content: flex-end;
}

.chat-message-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background-color: #2563EB;
  margin-right: 8px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.chat-message-bubble {
  padding: 10px 12px;
  border-radius: 12px;
  position: relative;
  animation: fadeIn 0.3s forwards;
}

.user .chat-message-bubble {
  background-color: #2563EB;
  color: white;
  border-top-right-radius: 0;
}

.bot .chat-message-bubble {
  background-color: #F3F4F6;
  color: #1F2937;
  border-top-left-radius: 0;
}

.chat-input {
  display: flex;
  padding: 10px 15px;
  background-color: white;
  border-top: 1px solid #E5E7EB;
}

.chat-input-field {
  display: flex;
  align-items: center;
  flex: 1;
  background-color: #F3F4F6;
  border-radius: 20px;
  padding: 8px 15px;
}

.chat-input-field input {
  flex: 1;
  border: none;
  background: transparent;
  outline: none;
  font-size: 14px;
}

.chat-send-btn {
  background-color: #2563EB;
  border: none;
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  margin-left: 8px;
  transition: background-color 0.3s ease;
}

.chat-send-btn:hover {
  background-color: #1D4ED8;
}

.chat-send-btn:disabled {
  background-color: #9CA3AF;
  cursor: not-allowed;
}

.quick-replies {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 10px;
  justify-content: center;
  animation: slideUp 0.3s forwards;
}

.quick-reply-btn {
  background-color: white;
  border: 1px solid #2563EB;
  color: #2563EB;
  border-radius: 16px;
  padding: 6px 12px;
  font-size: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.quick-reply-btn:hover {
  background-color: #2563EB;
  color: white;
  transform: scale(1.05);
}

.quick-reply-btn:active {
  transform: scale(0.95);
}

.typing-indicator {
  display: flex;
  align-items: center;
}

.typing-dot {
  display: inline-block;
  width: 4px;
  height: 4px;
  border-radius: 50%;
  background-color: #6B7280;
  margin: 0 2px;
  animation: typingAnimation 1.4s infinite ease-in-out;
}

.typing-dot:nth-child(1) {
  animation-delay: 0s;
}

.typing-dot:nth-child(2) {
  animation-delay: 0.2s;
}

.typing-dot:nth-child(3) {
  animation-delay: 0.4s;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes typingAnimation {
  0%, 100% {
    opacity: 0.2;
  }
  50% {
    opacity: 1;
  }
}
    </style>

    <!-- Chat Widget JavaScript -->
    <script>
document.addEventListener('DOMContentLoaded', function() {
  // Default chat responses
  const chatResponses = [
    {
      questionKey: "booking",
      question: "How do I make a booking?",
      answer: "Sign up an account and you can make a booking through our website by clicking on 'Book Now' button. Select your check-in and check-out dates, choose a room, and follow the instructions to complete your reservation."
    },
    {
      questionKey: "payment",
      question: "What payment methods do you accept?",
      answer: "We Don't accept online payments. We only accept cash payments, Visit our property to pay in cash. You can check our location on the location section of our website."
    },
    {
      questionKey: "cancellation",
      question: "What is your cancellation policy?",
      answer: "Our standard cancellation policy allows free cancellation up to 48 hours before check-in. Cancellations made within 48 hours of check-in may be subject to a fee equal to one night's stay."
    },
    {
      questionKey: "checkin",
      question: "What are the check-in and check-out times?",
      answer: "Check-in is from 2:00 PM onwards, and check-out is until 12:00 PM (noon) strict. Early check-in or late check-out may be available upon request, subject to availability."
    },
    {
      questionKey: "amenities",
      question: "What amenities are included?",
      answer: "Our staycation includes: PS4 with games, Billiards Table, Portable Karaoke, Board Games & Card Games, Smart TV with Netflix, and access to Azure's wave pool and man-made beach."
    }
  ];

  // Chat configuration
  const chatConfig = {
    initialMessage: "Hi there! 👋 How can I help you with your staycation today?",
    placeholderText: "Type your message...",
    supportTeamName: "El Sanchel Support",
    supportTeamStatus: "Typically replies in a few minutes",
    typingDelay: 1000 // milliseconds
  };

  // Chat widget DOM elements
  const chatWidgetContainer = document.createElement('div');
  chatWidgetContainer.className = 'chat-widget-container';
  
  // Create chat button
  const chatButton = document.createElement('button');
  chatButton.className = 'chat-button';
  chatButton.setAttribute('aria-label', 'Open chat');
  chatButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path><circle cx="9" cy="10" r="1"></circle><circle cx="15" cy="10" r="1"></circle></svg>';
  
  // Create chat window
  const chatWindow = document.createElement('div');
  chatWindow.className = 'chat-window';
  
  // Chat header
  const chatHeader = document.createElement('div');
  chatHeader.className = 'chat-header';
  
  const chatTitle = document.createElement('div');
  chatTitle.className = 'chat-title';
  
  const chatTitleAvatar = document.createElement('div');
  chatTitleAvatar.className = 'chat-title-avatar';
  chatTitleAvatar.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>';
  
  const chatTitleInfo = document.createElement('div');
  chatTitleInfo.className = 'chat-title-info';
  chatTitleInfo.innerHTML = `<h3>${chatConfig.supportTeamName}</h3><p>${chatConfig.supportTeamStatus}</p>`;
  
  const chatCloseBtn = document.createElement('button');
  chatCloseBtn.className = 'chat-close-btn';
  chatCloseBtn.setAttribute('aria-label', 'Close chat');
  chatCloseBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
  
  // Chat messages container
  const chatMessages = document.createElement('div');
  chatMessages.className = 'chat-messages';
  
  // Chat input
  const chatInputContainer = document.createElement('div');
  chatInputContainer.className = 'chat-input';
  
  const chatInputField = document.createElement('div');
  chatInputField.className = 'chat-input-field';
  
  const chatInputElement = document.createElement('input');
  chatInputElement.type = 'text';
  chatInputElement.placeholder = chatConfig.placeholderText;
  
  const chatSendBtn = document.createElement('button');
  chatSendBtn.className = 'chat-send-btn';
  chatSendBtn.disabled = true;
  chatSendBtn.setAttribute('aria-label', 'Send message');
  chatSendBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>';
  
  // Assemble the chat widget
  chatTitle.appendChild(chatTitleAvatar);
  chatTitle.appendChild(chatTitleInfo);
  
  chatHeader.appendChild(chatTitle);
  chatHeader.appendChild(chatCloseBtn);
  
  chatInputField.appendChild(chatInputElement);
  chatInputContainer.appendChild(chatInputField);
  chatInputContainer.appendChild(chatSendBtn);
  
  chatWindow.appendChild(chatHeader);
  chatWindow.appendChild(chatMessages);
  chatWindow.appendChild(chatInputContainer);
  
  chatWidgetContainer.appendChild(chatWindow);
  chatWidgetContainer.appendChild(chatButton);
  
  // Insert the chat widget before the back-to-top button
  const backToTopBtn = document.querySelector('.back-to-top');
  if (backToTopBtn) {
    backToTopBtn.parentNode.insertBefore(chatWidgetContainer, backToTopBtn);
  } else {
    document.body.appendChild(chatWidgetContainer);
  }
  
  // Chat widget state and functionality
  let isOpen = false;
  let isTyping = false;
  let messages = [];
  
  // Open/close chat window
  function toggleChat() {
    isOpen = !isOpen;
    if (isOpen) {
      chatWindow.classList.add('open');
      chatButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
      chatButton.setAttribute('aria-label', 'Close chat');
      
      // If no messages yet, add initial messages
      if (messages.length === 0) {
        addMessage(chatConfig.initialMessage, false);
        setTimeout(() => {
          addMessage("Here are some common questions I can help with:", false);
          showQuickReplies();
        }, 500);
      }
      
      // Focus the input field
      setTimeout(() => {
        chatInputElement.focus();
      }, 300);
    } else {
      chatWindow.classList.remove('open');
      chatButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path><circle cx="9" cy="10" r="1"></circle><circle cx="15" cy="10" r="1"></circle></svg>';
      chatButton.setAttribute('aria-label', 'Open chat');
    }
  }
  
  // Event listeners
  chatButton.addEventListener('click', toggleChat);
  chatCloseBtn.addEventListener('click', () => {
    if (isOpen) toggleChat();
  });
  
  // Handle sending messages
  function handleSendMessage() {
    const message = chatInputElement.value.trim();
    if (message && !isTyping) {
      addMessage(message, true);
      chatInputElement.value = '';
      chatSendBtn.disabled = true;
      
      // Find matching response or use fallback
      processUserMessage(message);
    }
  }
  
  chatSendBtn.addEventListener('click', handleSendMessage);
  chatInputElement.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
      handleSendMessage();
    }
  });
  
  chatInputElement.addEventListener('input', () => {
    chatSendBtn.disabled = chatInputElement.value.trim() === '';
  });
  
  // Add a message to the chat
  function addMessage(text, isUser) {
    const messageEl = document.createElement('div');
    messageEl.className = isUser ? 'chat-message user' : 'chat-message bot';
    
    let messageContent = '';
    
    if (!isUser) {
      messageContent += `
        <div class="chat-message-avatar">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
          </svg>
        </div>
      `;
    }
    
    messageContent += `<div class="chat-message-bubble">${text}</div>`;
    messageEl.innerHTML = messageContent;
    
    chatMessages.appendChild(messageEl);
    
    // Store the message
    messages.push({ text, isUser });
    
    // Scroll to bottom
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }
  
  // Show typing indicator
  function showTypingIndicator() {
    isTyping = true;
    
    const typingEl = document.createElement('div');
    typingEl.className = 'chat-message bot typing-message';
    typingEl.innerHTML = `
      <div class="chat-message-avatar">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
          <circle cx="12" cy="7" r="4"></circle>
        </svg>
      </div>
      <div class="chat-message-bubble">
        <div class="typing-indicator">
          <span class="typing-dot"></span>
          <span class="typing-dot"></span>
          <span class="typing-dot"></span>
        </div>
      </div>
    `;
    
    chatMessages.appendChild(typingEl);
    chatMessages.scrollTop = chatMessages.scrollHeight;
    
    return typingEl;
  }
  
  // Hide typing indicator
  function hideTypingIndicator(typingEl) {
    chatMessages.removeChild(typingEl);
    isTyping = false;
  }
  
  // Process user message and provide a response
  function processUserMessage(message) {
    // Show typing indicator
    const typingEl = showTypingIndicator();
    
    // Try to find a matching response
    const matchResponse = chatResponses.find(r => 
      message.toLowerCase().includes(r.question.toLowerCase()) ||
      r.questionKey.toLowerCase().includes(message.toLowerCase())
    );
    
    // Calculate typing delay based on response length
    const response = matchResponse ? matchResponse.answer : 
      "Thank you for your message. Our team will get back to you shortly. In the meantime, feel free to check out our suggested questions.";
    
    const typingDelay = Math.min(
      chatConfig.typingDelay + response.length * 20, 
      3000  // Cap at 3 seconds
    );
    
    // Simulate bot typing and then respond
    setTimeout(() => {
      hideTypingIndicator(typingEl);
      addMessage(response, false);
      
      // Show quick replies again after providing a response
      setTimeout(() => {
        showQuickReplies();
      }, 800);
    }, typingDelay);
  }
  
  // Show quick reply buttons
  function showQuickReplies() {
    // Remove previous quick replies if they exist
    const previousQuickReplies = document.querySelector('.quick-replies');
    if (previousQuickReplies) {
      chatMessages.removeChild(previousQuickReplies);
    }
    
    const quickRepliesContainer = document.createElement('div');
    quickRepliesContainer.className = 'quick-replies';
    
    chatResponses.forEach(response => {
      const quickReplyBtn = document.createElement('button');
      quickReplyBtn.className = 'quick-reply-btn';
      quickReplyBtn.textContent = response.question;
      
      quickReplyBtn.addEventListener('click', () => {
        if (isTyping) return; // Prevent clicking during typing animation
        
        addMessage(response.question, true);
        
        // Show typing indicator
        const typingEl = showTypingIndicator();
        
        // Calculate typing delay based on response length
        const typingDelay = Math.min(
          chatConfig.typingDelay + response.answer.length * 20, 
          3000  // Cap at 3 seconds
        );
        
        // Hide quick replies when a reply is selected
        chatMessages.removeChild(quickRepliesContainer);
        
        // Simulate bot typing and then respond
        setTimeout(() => {
          hideTypingIndicator(typingEl);
          addMessage(response.answer, false);
          
          // Show quick replies again after bot responds
          setTimeout(() => {
            showQuickReplies();
          }, 800);
        }, typingDelay);
      });
      
      quickRepliesContainer.appendChild(quickReplyBtn);
    });
    
    chatMessages.appendChild(quickRepliesContainer);
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }
  
  // Close chat when Escape key is pressed
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && isOpen) {
      toggleChat();
    }
  });
});
    </script>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- Custom JS -->
    <script src="script.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Room type descriptions
        const roomDescriptions = {
            'MAUI GALAXY': 'Gaming Room V1 (15th Floor) - ₱2,499/night',
            'MIAMI GAMING': 'Gaming Room V2 (9th Floor) - ₱3,299/night',
            'COZZY ROOM': 'Cozy Room V1 (19th Floor) - ₱1,999/night'
        };
        
        // Guest capacity info
        const guestInfo = {
            '1': 'Ideal for solo travelers',
            '2': 'Perfect for couples',
            '3': 'Great for small groups',
            '4': 'Maximum capacity for standard rooms'
        };
        
        // Set minimum checkout date
        document.getElementById('check-in').addEventListener('change', function() {
            const checkInDate = new Date(this.value);
            const checkOutDate = new Date(checkInDate);
            checkOutDate.setDate(checkOutDate.getDate() + 1);
            
            const checkOutField = document.getElementById('check-out');
            checkOutField.min = checkOutDate.toISOString().split('T')[0];
            
            // Reset availability result when dates change
            document.getElementById('availability-result').style.display = 'none';
        });
        
        // Show room description when selected
        document.getElementById('room-type').addEventListener('change', function() {
            const desc = roomDescriptions[this.value] || 'Select a room type to see details';
            document.getElementById('room-description').textContent = desc;
            document.getElementById('availability-result').style.display = 'none';
        });
        
        // Show guest info when selected
        document.getElementById('guests').addEventListener('change', function() {
            document.getElementById('guest-info').textContent = guestInfo[this.value];
        });
        
        // Handle availability check
        document.getElementById('check-availability-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const checkIn = document.getElementById('check-in').value;
            const checkOut = document.getElementById('check-out').value;
            const roomType = document.getElementById('room-type').value;
            const guests = document.getElementById('guests').value;
            
            if (!checkIn || !checkOut || !roomType) {
                alert('Please fill all required fields');
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Checking...';
            
            // Send AJAX request
            fetch('el.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `check_availability=true&room_type=${encodeURIComponent(roomType)}&check_in=${encodeURIComponent(checkIn)}&check_out=${encodeURIComponent(checkOut)}`
            })
            .then(response => response.json())
            .then(data => {
                const resultDiv = document.getElementById('availability-result');
                const messageDiv = document.getElementById('availability-message');
                const alertDiv = document.getElementById('availability-alert');
                const detailsDiv = document.getElementById('booking-details');
                const bookBtn = document.getElementById('proceed-booking');
                
                resultDiv.style.display = 'block';
                messageDiv.textContent = data.message;
                
                if (data.available) {
                    alertDiv.className = 'alert alert-success';
                    
                    // Show booking details
                    const checkInDate = new Date(checkIn).toLocaleDateString();
                    const checkOutDate = new Date(checkOut).toLocaleDateString();
                    const nights = Math.ceil((new Date(checkOut) - new Date(checkIn)) / (1000 * 60 * 60 * 24));
                    
                    detailsDiv.innerHTML = `
                        <p><strong>Room:</strong> ${roomType}</p>
                        <p><strong>Check-in:</strong> ${checkInDate}</p>
                        <p><strong>Check-out:</strong> ${checkOutDate}</p>
                        <p><strong>Guests:</strong> ${guests}</p>
                        <p><strong>Nights:</strong> ${nights}</p>
                    `;
                    
                    // Add parameters to booking URL
                    bookBtn.href = `admin/reservation.php?room=${encodeURIComponent(roomType)}&checkin=${checkIn}&checkout=${checkOut}&guests=${guests}`;
                } else {
                    alertDiv.className = 'alert alert-danger';
                    detailsDiv.innerHTML = '';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while checking availability');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Check Availability';
            });
        });
    });
    </script>
    <!-- Customer Login Modal -->
<div class="modal fade" id="customerLoginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow ultra-glass-modal">
            <div class="modal-header border-0">
                <div class="modal-logo">
                    <div class="brand-logo">
                        <span class="brand-text">El</span>
                        <span class="brand-highlight">Sanchel</span>
                    </div>
                    <span class="brand-subtext">Staycation</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="customer-login-form">
                    <div class="mb-3">
                        <label for="customer-email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="customer-email" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="customer-password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="customer-password" required>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary rounded-pill">Login</button>
                    </div>
                   
                </form>
            </div>
        </div>
    </div>
</div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Room type descriptions
        const roomDescriptions = {
            'MAUI GALAXY': 'Gaming Room V1 (15th Floor) - ₱2,499/night',
            'MIAMI GAMING': 'Gaming Room V2 (9th Floor) - ₱3,299/night',
            'COZZY ROOM': 'Cozy Room V1 (19th Floor) - ₱1,999/night'
        };
        
        // Guest capacity info
        const guestInfo = {
            '1': 'Ideal for solo travelers',
            '2': 'Perfect for couples',
            '3': 'Great for small groups',
            '4': 'Maximum capacity for standard rooms'
        };
        
        // Set minimum checkout date
        document.getElementById('check-in').addEventListener('change', function() {
            const checkInDate = new Date(this.value);
            const checkOutDate = new Date(checkInDate);
            checkOutDate.setDate(checkOutDate.getDate() + 1);
            
            const checkOutField = document.getElementById('check-out');
            checkOutField.min = checkOutDate.toISOString().split('T')[0];
            
            // Reset availability result when dates change
            document.getElementById('availability-result').style.display = 'none';
        });
        
        // Show room description when selected
        document.getElementById('room-type').addEventListener('change', function() {
            const desc = roomDescriptions[this.value] || 'Select a room type to see details';
            document.getElementById('room-description').textContent = desc;
            document.getElementById('availability-result').style.display = 'none';
        });
        
        // Show guest info when selected
        document.getElementById('guests').addEventListener('change', function() {
            document.getElementById('guest-info').textContent = guestInfo[this.value];
        });
        
        // Handle availability check
        document.getElementById('check-availability-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const checkIn = document.getElementById('check-in').value;
            const checkOut = document.getElementById('check-out').value;
            const roomType = document.getElementById('room-type').value;
            const guests = document.getElementById('guests').value;
            
            if (!checkIn || !checkOut || !roomType) {
                alert('Please fill all required fields');
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Checking...';
            
            // Send AJAX request
            fetch('el.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `check_availability=true&room_type=${encodeURIComponent(roomType)}&check_in=${encodeURIComponent(checkIn)}&check_out=${encodeURIComponent(checkOut)}`
            })
            .then(response => response.json())
            .then(data => {
                const resultDiv = document.getElementById('availability-result');
                const messageDiv = document.getElementById('availability-message');
                const alertDiv = document.getElementById('availability-alert');
                const detailsDiv = document.getElementById('booking-details');
                const bookBtn = document.getElementById('proceed-booking');
                
                resultDiv.style.display = 'block';
                messageDiv.textContent = data.message;
                
                if (data.available) {
                    alertDiv.className = 'alert alert-success';
                    
                    // Show booking details
                    const checkInDate = new Date(checkIn).toLocaleDateString();
                    const checkOutDate = new Date(checkOut).toLocaleDateString();
                    const nights = Math.ceil((new Date(checkOut) - new Date(checkIn)) / (1000 * 60 * 60 * 24));
                    
                    detailsDiv.innerHTML = `
                        <p><strong>Room:</strong> ${roomType}</p>
                        <p><strong>Check-in:</strong> ${checkInDate}</p>
                        <p><strong>Check-out:</strong> ${checkOutDate}</p>
                        <p><strong>Guests:</strong> ${guests}</p>
                        <p><strong>Nights:</strong> ${nights}</p>
                    `;
                    
                    // Add parameters to booking URL
                    bookBtn.href = `admin/reservation.php?room=${encodeURIComponent(roomType)}&checkin=${checkIn}&checkout=${checkOut}&guests=${guests}`;
                } else {
                    alertDiv.className = 'alert alert-danger';
                    detailsDiv.innerHTML = '';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while checking availability');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Check Availability';
            });
        });
    });
    </script>
    <!-- Customer Login Modal -->
<div class="modal fade" id="customerLoginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow ultra-glass-modal">
            <div class="modal-header border-0">
                <div class="modal-logo">
                    <div class="brand-logo">
                        <span class="brand-text">El</span>
                        <span class="brand-highlight">Sanchel</span>
                    </div>
                    <span class="brand-subtext">Staycation</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="customer-login-form">
                    <div class="mb-3">
                        <label for="customer-email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="customer-email" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="customer-password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="customer-password" required>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary rounded-pill">Login</button>
                    </div>
                   
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
