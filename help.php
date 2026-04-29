<?php require_once 'header.php'; ?>
<?php
$status = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $msg = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO complaints (name, email, message) VALUES ('$name', '$email', '$msg')";
    
    if ($conn->query($sql) === TRUE) {
        $status = "success";
    } else {
        $status = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Help & Support - Parking App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('WhatsApp Image 2026-04-23 at 7.03.59 PM.jpeg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', sans-serif;
            color: white;
            min-height: 100vh;
        }
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: -1;
        }
        .help-container {
            margin-top: 80px;
            margin-bottom: 60px;
            z-index: 1;
            position: relative;
        }
        .card {
            border-radius: 15px;
            background-color: rgba(255,255,255,0.1);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.2);
            margin-bottom: 20px;
        }
        .card h4 {
            color: #fff;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }
        .accordion-item {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
        }
        .accordion-button {
            background: rgba(0,0,0,0.3);
            color: white;
        }
        .accordion-button:not(.collapsed) {
            background: rgba(26, 115, 232, 0.5);
            color: white;
        }
        .contact-box {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(10px);
            color: white;
            border-radius: 18px;
            padding: 30px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .btn-custom {
            background: linear-gradient(135deg, #0ea5a4 0%, #0f766e 100%);
            color: white;
            border-radius: 25px;
            padding: 10px 30px;
            border: none;
            transition: 0.3s;
            font-weight: bold;
        }
        .btn-custom:hover {
            transform: scale(1.05);
            color: white;
            box-shadow: 0 5px 15px rgba(14, 165, 164, 0.4);
        }
        .alert {
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container help-container">
    <div class="text-center mb-5">
        <h1 style="font-weight: 800; text-shadow: 0 4px 10px rgba(0,0,0,0.5);">Help & Support</h1>
        <p style="font-size: 18px;">Your guide to using the Smart Parking App</p>
    </div>

    <?php if($status == "success"): ?>
        <div class="alert alert-success text-center">Your message has been sent successfully! We will get back to you soon.</div>
    <?php elseif($status == "error"): ?>
        <div class="alert alert-danger text-center">There was an error sending your message. Please try again.</div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card p-4">
                <h4><i class="fa-solid fa-car"></i> How to Book a Spot</h4>
                <ul class="mt-3">
                    <li>Open the app and allow location access</li>
                    <li>Choose your desired parking area</li>
                    <li>Select an available parking space</li>
                    <li>Pick date and time and confirm booking</li>
                    <li>Complete payment to secure your spot</li>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-4">
                <h4><i class="fa-solid fa-credit-card"></i> Payment Issues</h4>
                <ul class="mt-3">
                    <li>Check your internet connection</li>
                    <li>Verify card details and sufficient balance</li>
                    <li>Wait for the confirmation screen</li>
                    <li>Contact support if funds are deducted without a ticket</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card p-4">
        <h4 class="text-center">Frequently Asked Questions</h4>
        <div class="accordion mt-3" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q1">
                        Can I cancel my booking?
                    </button>
                </h2>
                <div id="q1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Yes, you can cancel your booking before the start time through the History tab.</div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q2">
                        Will I get a refund?
                    </button>
                </h2>
                <div id="q2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Refunds are processed according to the cancellation policy (usually 100% if cancelled 1 hour before).</div>
                </div>
            </div>
        </div>
    </div>

    <div class="contact-box text-center mt-4">
        <h4><i class="fa-solid fa-headset"></i> Need More Help?</h4>
        <p class="mb-1">Email: help@smartparking.com</p>
        <p>Phone: +05036724863</p>
        <button class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#contactModal">Send Message</button>
    </div>
</div>

<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background: rgba(30, 41, 59, 0.95); backdrop-filter: blur(20px); color:white; border-radius:20px; border: 1px solid rgba(255,255,255,0.2);">
            <div class="modal-header border-0">
                <h5 class="modal-title">Contact Support</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="mb-3">
                        <input type="text" name="name" class="form-control" style="background: rgba(255,255,255,0.1); border: none; color: white;" placeholder="Your Name" required>
                    </div>
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" style="background: rgba(255,255,255,0.1); border: none; color: white;" placeholder="Your Email" required>
                    </div>
                    <div class="mb-3">
                        <textarea name="message" class="form-control" style="background: rgba(255,255,255,0.1); border: none; color: white;" rows="4" placeholder="Write your problem..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-custom w-100">Submit Now</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>