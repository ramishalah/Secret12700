<?php

	session_start();

	$error = "";
	
	if(array_key_exists("logout", $_GET)){
		
		session_unset();
		setcookie("id","",time()-60*60);
		$_COOKIE['id'] = "";
		
	}else if((array_key_exists('id', $_SESSION) AND $_SESSION['id']) OR (array_key_exists('id', $_COOKIE) AND $_COOKIE['id'])){
		
		header("Location: loggedInPage.php");
		
	}
	if (array_key_exists('submit', $_POST)){
		
		include("connection.php");
		
		
		if(!$_POST['email']){
			
			$error .= "Please enter your email.<br>";
		}
		else if($_POST["email"] &&(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) === false)) {
			$error .="the email is not in the correct form<br>";
		} 
		if(!$_POST['password']){
			
			$error .= "Please enter your password.<br>";
		}
		else {
			
			if(strlen($_POST['password']) < 8) {
				
				$error .= "Please enter at least 8 characters.<br>";
			}
			if(!preg_match('`[A-Z]`',$_POST['password'] )){
				
				$error .= "please include at least one capital letter.<br>";
				
			}
		}
		
		if($error != ""){
			
			$error = "<p>There are error(s) in your form:</p>".$error;
		}
		else{
			
			if($_POST['signUp'] == 1){
			
				$query = "SELECT id FROM `users` WHERE email='".mysqli_real_escape_string($link, $_POST['email'])."' LIMIT 1";
				
				$result = mysqli_query($link, $query);
					
				if(mysqli_num_rows($result) > 0){
					
					$error =  "you are already signed up. Do you want to log in?";
				}
				else{
					$query = "INSERT INTO `users` (`email`, `password`) VALUES('".mysqli_real_escape_string($link, $_POST['email'])."','".mysqli_real_escape_string($link,$_POST['password'])."')";
					
					if(!mysqli_query($link, $query)){
						
						$error = "Could sign you up. Please try again later";
					}else{
						
						$query = "UPDATE `users` SET password = '".md5(md5(mysqli_insert_id($link)).$_POST["password"])."' WHERE id='".mysqli_insert_id($link)."' LIMIT 1";
						
						mysqli_query($link, $query);
						
						$_SESSION['id'] = mysqli_insert_id($link);
						
						if($_POST['stayLoggedIn'] == "1"){
							
							setcookie('id',mysqli_insert_id($link), time()+60*60*24*365);
						}
						
						header("Location: loggedInPage.php");
					}
					
					
				}
			}else{
				
				$query = "SELECT * FROM `users` WHERE email= '".mysqli_real_escape_string($link, $_POST['email'])."'";
				
				$result = mysqli_query($link, $query);
				
				$row = mysqli_fetch_array($result);
				
				if(isset($row)){
					
					$hashedPassword = md5(md5($row['id']).$_POST['password']);
					
					if($hashedPassword == $row['password']){
						
						$_SESSION['id'] = $row['id'];
						
						if($_POST['stayLoggedIn'] == "1"){
							
							setcookie('id',$row['id'], time()+60*60*24*365);
						}
						
						header("Location: loggedInPage.php");
						
					}else{
						
						$error = "That email/password could not be found.";
						
					}
				}else{
					
					$error = "That email/password could not be found.";
					
				}
				
			}
		}
		
	}



?>


<?php include("header.php"); ?>
	
	<div class="container">
	
	
		<h1 id="title" class="whiteColor">Secret Diary</h1>
		
		<p class="whiteColor"><strong>Store your thoughts permenantly and securely.</strong></p>
		
		<div id="error"><?php echo $error ?></div>

		<form method="POST" id="signUpForm">
			
			<p class="whiteColor">Intersted? Sign up now!</p>
			<div class="form-group">
			
				<input type="text" name="email" class="form-control" id="email" placeholder = 'email'>
				
			</div>
			
			<div class="form-group">
			
				<input type="password" name="password" class="form-control" id="password" placeholder = 'password'>
				
			</div>
			
			<div class="form-check">
				<label class="form-check-label">
				
					<input type="checkbox" class="form-check-input" name="stayLoggedIn" id="checkBox" value = "1">
					
					Stay Logged In
					<input type="hidden" name="signUp" value="1">
				
				</label>
			</div>
			<div class="form-group">
			
				<input type="submit" class="btn btn-success" name="submit" value="Sign Up">
				
			</div>	
			
			<p><a class="toggleForms">Log In</a></p>

		</form>

		<form method="POST" id="logInForm">
			
			<p class="whiteColor">Log in with your email and your password!</p>
			<div class="form-group">
			
				<input type="text" class="form-control" name="email" id="email" placeholder = 'email'>
				
			</div>
			
			<div class="form-group">
			
				<input type="password" class="form-control" name="password" id="password" placeholder = 'password'>
				
			</div>
			
			<div class="form-check">
				<label class="form-check-label">
				
					<input type="checkbox" name="stayLoggedIn" id="checkBox" value = "1">
						Stay Logged In
					<input type="hidden" name="signUp" value="0">
			
				</label>
			</div>
			
			<div class="form-group">
			<input type="submit" class="btn btn-success" name="submit" value="Log In">
			</div>

			<p><a class="toggleForms">Log In</a></p>
		</form>
	</div>
    <?php include("footer.php"); ?>



