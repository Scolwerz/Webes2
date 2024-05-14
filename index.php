<?php

function color($color) {
switch ($color) {
	case "piros":  return "red";    break;
	case "zold":   return "green";  break;
	case "sarga":  return "yellow"; break;
	case "kek":    return "blue";   break;
	case "fekete": return "black";  break;
	case "feher":  return "white";  break;
	default:       return "white"; break;
	}
}


function decrypt($data) {
	$keys = [5, -14, 31, -9 ,3];
	$decrypted_data = '';
	$key_length = count($keys);
	$key_index = 0;

	for ($i = 0; $i < strlen($data); $i++) {
		if ($data[$i] !== "\x0A") {
			$offset = $keys[$key_index % $key_length];
			$decoded_char = chr(ord($data[$i]) - $offset);
			// Ha kisebb, mint 32 (nem nyomtatható) - nem módosítjuk
			if (ord($decoded_char) < 32) { $decrypted_data .= $data[$i]; }
			else 						 { $decrypted_data .= $decoded_char; }
    $key_index++;
		}
		else {
			// Ha EOL karakter
			$decrypted_data .= $data[$i];
			$key_index = 0;
		}
	}
	return $decrypted_data;
}





if ($_SERVER["REQUEST_METHOD"] == "POST" &&
		isset($_POST['username']) && !empty($_POST['username']) &&
		isset($_POST['password']) && !empty($_POST['password'])) {

	// password.txt
    $coded_passwords = file_get_contents("password.txt");
    $passwords = decrypt($coded_passwords);
	$login_dict = array();

    $lines = explode("\n", $passwords);
    foreach ($lines as $line) {
        $parts = explode("*", $line);
		if (count($parts) == 2) {
			$stored_username = $parts[0];
			$stored_password = $parts[1];
			$login_dict[$stored_username] = $stored_password;
		}
	}

	// Form adatai
	$username = trim($_POST["username"]);
	$password = trim($_POST["password"]);

	// Mysql
	if (array_key_exists($username, $key_and_value)) {
		if ($password == $login_dict[$username]) {
			$server = "localhost";
			$dbusername = "id22163654_webes2_adatok";
			$dbpassword = "Webes2_adatok_jelszo";
			$dbname = "id22163654_webes2";

			try {
			$conn = new mysqli($server, $dbusername, $dbpassword, $dbname);
			// if ($conn->connect_error) { die("Kapcsolódási hiba: " . $conn->connect_error); }
			} catch (Exception $e) { die("Kapcsolódási hiba: " . $e->getMessage()); }

			$sql = "SELECT Titkos FROM tabla WHERE Username = '$username'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $favorite_color = color($row["Titkos"]);
            }
			$background_color = "var fav_color = '$favorite_color';";
		}
		else {
			echo "Helytelen jelszó!";
   		    echo '<meta http-equiv="refresh" content="3;url=http://police.hu">';
    		exit();
		}
	}
	else { echo "Nem található ilyen nevű felhasználó!"; }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel ="stylesheet" href="styles.css">
	<title>LogIn</title>
</head>
<body>
	<div class="mydata">
		<p><b>Szabolcsi Daniel - KITW5W</b></p>
	</div>
	<div class="form-box">
		<form method="post" action="index.php">
			<input type="text" name="username" placeholder="Email" required>
			<input type="password" name="password" placeholder="Password" required>
			<input type="submit" name="signin" class="signin" value="Sign In">
		</form>
	</div>
	<script>
		<?phpif (isset($background_color)) { echo $background_color; } ?>
		document.addEventListener('DOMContentLoaded', function() {
			if (typeof bodyColor !== 'undefined') {
				document.body.style.backgroundColor = fav_color;
			}
		});
	</script>
</body>
</html>
