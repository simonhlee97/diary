<?php
//Connect to the database
    $host = "127.0.0.1";
    $user = "simonhlee97";                     //Your Cloud 9 username
    $pass = "";                                  //Remember, there is NO password by default!
    $db = "diary";                                  //Your database name you want to connect to
    $port = 3306;                                //The port #. It is always 3306

    session_start();

    $error = "";    

    if (array_key_exists("logout", $_GET)) {
        
        unset($_SESSION);
        setcookie("id", "", time() - 60*60);
        $_COOKIE["id"] = "";  
        
    } else if ((array_key_exists("id", $_SESSION) AND $_SESSION['id']) OR (array_key_exists("id", $_COOKIE) AND $_COOKIE['id'])) {
        
        header("Location: loggedinpage.php");
        
    }

    if (array_key_exists("submit", $_POST)) {
        
        $link = mysqli_connect($host, $user, $pass, $db, $port);
        
        if (mysqli_connect_error()) {
            
            die ("Database Connection Error");
            
        }
        
        
        
        if (!$_POST['email']) {
            
            $error .= "An email address is required<br>";
            
        } 
        
        if (!$_POST['password']) {
            
            $error .= "A password is required<br>";
            
        } 
        
        if ($error != "") {
            
            $error = "<p>There were error(s) in your form:</p>".$error;
            
        } else {
            
            if ($_POST['signUp'] == '1') {
            
                $query = "SELECT id FROM `users` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."' LIMIT 1";

                $result = mysqli_query($link, $query);

                if (mysqli_num_rows($result) > 0) {

                    $error = "That email address is taken.";

                } else {

                    $query = "INSERT INTO `users` (`email`, `password`) VALUES ('".mysqli_real_escape_string($link, $_POST['email'])."', '".mysqli_real_escape_string($link, $_POST['password'])."')";

                    if (!mysqli_query($link, $query)) {

                        $error = "<p>Could not sign you up - please try again later.</p>";

                    } else {

                        $query = "UPDATE `users` SET password = '".md5(md5(mysqli_insert_id($link)).$_POST['password'])."' WHERE id = ".mysqli_insert_id($link)." LIMIT 1";

                        mysqli_query($link, $query);

                        $_SESSION['id'] = mysqli_insert_id($link);

                        if ($_POST['stayLoggedIn'] == '1') {

                            setcookie("id", mysqli_insert_id($link), time() + 60*60*24*365);

                        } 

                        header("Location: loggedinpage.php");
                    }
                } 
                
            } else {
                    
                    $query = "SELECT * FROM `users` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."'";
                
                    $result = mysqli_query($link, $query);
                
                    $row = mysqli_fetch_array($result);
                
                    if (isset($row)) {
                        
                        $hashedPassword = md5(md5($row['id']).$_POST['password']);
                        
                        if ($hashedPassword == $row['password']) {
                            
                            $_SESSION['id'] = $row['id'];
                            
                            if ($_POST['stayLoggedIn'] == '1') {

                                setcookie("id", $row['id'], time() + 60*60*24*365);

                            } 

                            header("Location: loggedinpage.php");
                                
                        } else {
                            
                            $error = "That email/password combination could not be found.";
                            
                        }
                        
                    } else {
                        
                        $error = "That email/password combination could not be found.";
                    }
                }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<style>
.container{
    text-align: center;
    width: 400px;
}
</style>
</head>
    <body>
        <div class="container">
            <h1>Secret Diary</h1>
        <form method="post" id = "signUpForm">
            <p>Interested? Sign up now.</p>
            <fieldset class="form-group">
                <input class="form-control" type="email" name="email" placeholder="Your Email">
            </fieldset>
            <fieldset class="form-group">
                <input class="form-control" type="password" name="password" placeholder="Password">
            </fieldset>
    
            <div class="checkbox">
                <label>
                <input type="checkbox" name="stayLoggedIn" value=1> Stay logged in
                </label>
            </div>
    
            <fieldset class="form-group">
                <input type="hidden" name="signUp" value="1">
                <input class="btn btn-success" type="submit" name="submit" value="Sign Up!">
            </fieldset>
        </form>

        <form method="post" id = "logInForm">
            <fieldset class="form-group">
                <input class="form-control" type="email" name="email" placeholder="Your Email">
                
            </fieldset>
            
            <fieldset class="form-group">
                <input class="form-control"type="password" name="password" placeholder="Password">
            </fieldset>
            
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="stayLoggedIn" value=1> Stay logged in
                </label>
            </div>
                <input type="hidden" name="signUp" value="0">
            <fieldset class="form-group">
                <input class="btn btn-success" type="submit" name="submit" value="Log In!">
            </fieldset>
        </form>
      </div>

    <!-- jQuery first, then Tether, then Bootstrap JS. -->
    <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
  </body>
</html>

