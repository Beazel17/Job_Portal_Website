<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal Website</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to bottom right, #4CAF50, #2c3e50);
            color: #fff;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        /* Navbar Styles */
        .navbar {
            background: linear-gradient(to right, #4CAF50, #2c3e50);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar .navbar-brand {
            color: #fff;
            font-size: 2rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);
        }

        .navbar .navbar-nav .btn {
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
            padding: 10px 20px;
            border: 2px solid #fff;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .navbar .navbar-nav .btn:hover {
            background-color: #fff;
            color: #2c3e50;
            transform: scale(1.1);
        }

        /* Main Content */
        .main-title {
            font-size: 3rem;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
            margin-top: 100px;
            margin-bottom: 20px;
            transition: transform 0.3s ease, text-shadow 0.3s ease;
        }

        .main-title:hover {
            transform: scale(1.1);
            text-shadow: 4px 4px 8px rgba(0, 0, 0, 0.9), 0 0 20px #4CAF50; /* Adds a glowing shadow effect */
        }

        .typing-text {
            font-size: 1.2rem;
            font-weight: lighter;
            text-align: center;
            margin-top: 20px;
        }

        .title-container {
            text-align: center;
            margin-left: 200px;
            margin-right: 200px;
        }

        .btn-login {
            background-color: #4CAF50;
            color: #fff;
            border: none;
            transition: background-color 0.3s ease;
        }

        .btn-login:hover {
            background-color: #3e8e41;
        }

        /* Media Query for Responsive Design */
        @media (max-width: 768px) {
            .main-title {
                font-size: 2.5rem;
            }

            .navbar .navbar-brand {
                font-size: 1.5rem;
                letter-spacing: 1px;
            }

            .navbar .navbar-nav .btn {
                font-size: 1rem;
            }
        }

    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="#">Job Portal</a>
        <div class="ml-auto">
            <button class="btn btn-login mr-2" onclick="window.location.href='login.php'"><i class="fas fa-sign-in-alt"></i> Login</button>
            <button class="btn btn-login" onclick="window.location.href='admin_login.php'"><i class="fas fa-user-shield"></i> Admin Login</button>
        </div>
    </nav>

    <h1 class="main-title">Job Portal Website</h1>

    <div class="title-container">
        <p class="typing-text" id="typing-text"></p>
    </div>

    <script>
        const text = "Your gateway to the best opportunities, where endless possibilities await to help you unlock your true potential, achieve your dreams, and pave the way for a brighter future filled with success and fulfillment.";
        const typingTextElement = document.getElementById("typing-text");
        let index = 0;

        function typeText() {
            if (index < text.length) {
                typingTextElement.textContent += text.charAt(index);
                index++;
                setTimeout(typeText, 20); 
            }
        }

        window.onload = typeText;
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
