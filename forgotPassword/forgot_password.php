<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <script src="https://kit.fontawesome.com/7b1b8b2fa3.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>    
    <div class="container"> 
        <div class="form-box">
            <h1>Forgot Password</h1>        
            <form id="forgotPasswordForm" action="send_reset_password.php" method="post">
                <div class="input-group-login" >
                    <div class="input-field " >
                        <i class="fa-solid fa-user"></i>
                        <input type="email" placeholder="Email *" name="email" id="email" required>
                </div>    
                <div class="user-selects">
                    <div class="carrier-part">
                        <input type="radio" id="carrier" name="userselects" value="carrier" checked>
                        <label for="carrier">Carrier</label>
                    </div>
                    <div class="consignor-part">
                        <input type="radio" id="consignor" name="userselects" value="consignor">
                        <label for="consignor">Consignor</label>
                    </div>
                </div>
                <div class="btn-field">
                    <button type="submit" id="submit">Submit</button>
                    <a href="../login.php" class="back-button">Back to Login</a>
                </div>
            </div>
        </form>
    </div>
</div>    
</body>
</html>

    
