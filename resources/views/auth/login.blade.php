<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* Gaya tetap sama */
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: url('/images/WhatsApp-Image-2024-07-11-at-9.21.17-PM-1024x575.jpeg') no-repeat center center fixed;
            background-size: cover;
        }
        .login-container {
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }
        .login-box {
            background-color: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            text-align: center;
            width: 350px;
        }
        .login-box img {
            width: 100px;
            margin-bottom: 15px;
        }
        .login-box h3 {
            margin-bottom: 20px;
            color: #333;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        .btn-group {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .btn-group button {
            width: 48%;
            padding: 10px;
            border: none;
            border-radius: 8px;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-login {
            background-color: #28a745;
        }
        .btn-cancel {
            background-color: #dc3545;
        }
        footer {
            margin-top: 15px;
            font-size: 0.9em;
            color: #777;
        }
        #error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <img src="/images/Screenshot 2025-05-20 002803.png" alt="Logo">
            <h3>Admin Dashboard</h3>

            <form onsubmit="loginWithFirebase(event)">
                <input type="email" id="email" placeholder="Email Address" required>
                <input type="password" id="password" placeholder="Password" required>
                <div class="btn-group">
                    <button type="submit" class="btn-login">Login</button>
                    <button type="button" class="btn-cancel" onclick="window.location.href='/logout'">Cancel</button>
                </div>
            </form>

            <p id="error-message"></p>

            <footer>
                ©2025. Engkos Kosasih. All rights reserved.
            </footer>
        </div>
    </div>

    <!-- Firebase Auth Modular SDK -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.7.3/firebase-app.js";
        import { getAuth, signInWithEmailAndPassword } from "https://www.gstatic.com/firebasejs/11.7.3/firebase-auth.js";

        // Firebase configuration
        const firebaseConfig = {
            apiKey: "AIzaSyC4gqxOGLjcnZlWcHnp6BCa53MWY9kf5kU",
            authDomain: "celengan-7c473.firebaseapp.com",
            projectId: "celengan-7c473",
            storageBucket: "celengan-7c473.firebasestorage.app",
            messagingSenderId: "880232833106",
            appId: "1:880232833106:web:7080b4a845e62555c13f8b"
        };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);

        // Fungsi login
        window.loginWithFirebase = async function(event) {
            event.preventDefault();

            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;
            const errorMsg = document.getElementById("error-message");

            try {
                const userCredential = await signInWithEmailAndPassword(auth, email, password);
                console.log("Login berhasil:", userCredential.user);

                // Simpan token Firebase ke session Laravel jika ingin
                // atau langsung redirect
                window.location.href = "/dashboard";
            } catch (error) {
                errorMsg.textContent = "Login gagal: " + error.message;
            }
        };
    </script>
</body>
</html>
