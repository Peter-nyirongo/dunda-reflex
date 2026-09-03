<?php
require_once 'config/session.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['user_type'])) {
        if ($_SESSION['user_type'] === 'retailer') {
            header('Location: retailer/dashboard.php');
        } else if ($_SESSION['user_type'] === 'rider') {
            header('Location: rider/dashboard.php');
        }
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dunda Reflex - Delivery Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 50px;
            color: white;
        }

        .header h1 {
            font-size: 3rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .portal-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .portal-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .portal-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .portal-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
        }

        .portal-card h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.8rem;
        }

        .portal-card p {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #f0f0f0;
            color: #333;
        }

        .btn-secondary:hover {
            background: #e0e0e0;
        }

        .features {
            background: white;
            border-radius: 15px;
            padding: 40px;
            margin-top: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }

        .features h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .feature-item {
            padding: 20px;
            text-align: center;
        }

        .feature-item h3 {
            color: #667eea;
            margin-bottom: 10px;
        }

        .feature-item p {
            color: #666;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }
            
            .portal-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚚 Dunda Reflex</h1>
            <p>Delivery Management System</p>
        </div>

        <div class="portal-grid">
            <!-- Retailer Portal -->
            <div class="portal-card">
                <div class="portal-icon">🏪</div>
                <h2>Retailer Portal</h2>
                <p>Manage your deliveries, assign riders, and track your orders in real-time.</p>
                <div class="btn-group">
                    <a href="auth/login.php?type=retailer" class="btn btn-primary">Retailer Login</a>
                    <a href="auth/register.php?type=retailer" class="btn btn-secondary">Register as Retailer</a>
                </div>
            </div>

            <!-- Rider Portal -->
            <div class="portal-card">
                <div class="portal-icon">🚴</div>
                <h2>Rider Portal</h2>
                <p>Accept delivery assignments, update delivery status, and manage your routes.</p>
                <div class="btn-group">
                    <a href="auth/login.php?type=rider" class="btn btn-primary">Rider Login</a>
                    <a href="auth/register.php?type=rider" class="btn btn-secondary">Register as Rider</a>
                </div>
            </div>
        </div>

        <div class="features">
            <h2>Why Choose Dunda Reflex?</h2>
            <div class="feature-grid">
                <div class="feature-item">
                    <h3>⚡ Fast Delivery</h3>
                    <p>Efficient rider assignment and real-time tracking for quick deliveries.</p>
                </div>
                <div class="feature-item">
                    <h3>📊 Real-Time Tracking</h3>
                    <p>Monitor delivery status and location updates instantly.</p>
                </div>
                <div class="feature-item">
                    <h3>🔒 Secure Platform</h3>
                    <p>Your data is protected with industry-standard security measures.</p>
                </div>
                <div class="feature-item">
                    <h3>💼 Easy Management</h3>
                    <p>Simple dashboard to manage all your deliveries and riders.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>