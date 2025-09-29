<?php
$page_title = "Contact Us";
include 'includes/session.php';
include 'includes/header.php';
?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="mb-4 font-serif">Contact Us</h1>
                <p class="lead">We'd love to hear from you! Please fill out the form below or use the contact information to get in touch with our team.</p>
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
                <hr class="my-5">
                <h5>Contact Information</h5>
                <ul class="list-unstyled">
                    <li><i class="fas fa-map-marker-alt me-2"></i>123 Luxury Avenue, City</li>
                    <li><i class="fas fa-phone me-2"></i>+1 (555) 123-4567</li>
                    <li><i class="fas fa-envelope me-2"></i>info@grandhotel.com</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
