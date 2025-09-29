    </main>
    
  
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold font-serif mb-3">Grand Hotel</h5>
                    <p class="text-light">Experience luxury beyond compare with world-class amenities and exceptional service. Your perfect stay awaits.</p>
                    <div class="social-links">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 mb-4">
                    <h6 class="fw-bold mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-light text-decoration-none">Home</a></li>
                        <li><a href="rooms.php" class="text-light text-decoration-none">Rooms</a></li>
                        <li><a href="about.php" class="text-light text-decoration-none">About</a></li>
                        <li><a href="contact.php" class="text-light text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 mb-4">
                    <h6 class="fw-bold mb-3">Services</h6>
                    <ul class="list-unstyled">
                        <li class="text-light"><i class="fas fa-wifi me-2"></i>Free Wi-Fi</li>
                        <li class="text-light"><i class="fas fa-car me-2"></i>Valet Parking</li>
                        <li class="text-light"><i class="fas fa-utensils me-2"></i>Fine Dining</li>
                        <li class="text-light"><i class="fas fa-dumbbell me-2"></i>Fitness Center</li>
                    </ul>
                </div>
                
                <div class="col-lg-3 mb-4">
                    <h6 class="fw-bold mb-3">Contact Info</h6>
                    <ul class="list-unstyled text-light">
                        <li><i class="fas fa-map-marker-alt me-2"></i>123 Luxury Avenue, City</li>
                        <li><i class="fas fa-phone me-2"></i>+1 (555) 123-4567</li>
                        <li><i class="fas fa-envelope me-2"></i>info@grandhotel.com</li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-light">&copy; <?php echo date('Y'); ?> Grand Hotel. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-light text-decoration-none me-3">Privacy Policy</a>
                    <a href="#" class="text-light text-decoration-none">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
    </script>
</body>
</html>