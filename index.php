<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LogIn</title>
	<link rel ="stylesheet" href="styles.css">
</head>
<body>

    <?php
	
	function color($color) {
		switch ($color) {
			case "piros":  return "red";    break;
			case "zold":   return "green";  break; 
			case "sarga":  return "yellow"; break;
			case "kek":    return "blue";   break;
			case "fekete": return "black";  break;
			case "feher":  return "white";  break;
			default:       return "orange"; break;
		}
	}
	

    function decrypt($data, $keys) {
		$decrypted_data = '';
		$key_length = count($keys);
		$key_index = 0;

		for ($i = 0; $i < strlen($data); $i++) {
			if ($data[$i] !== "\x0A") {
				$offset = $keys[$key_index % $key_length];
				$decoded_char = chr(ord($data[$i]) - $offset);

				// Ha kisebb, mint 32 (nem nyomtatható) - nem módosítjuk
				if (ord($decoded_char) < 32) {
					$decrypted_data .= $data[$i];
				} else {
					$decrypted_data .= $decoded_char;
				}
			} else {
				// Ha EOL karakter, nem kell módosítani
				$decrypted_data .= $data[$i];
				$key_index = 0;
			}
			$key_index++;
		}

		return $decrypted_data;
	}
	
	
	
	$favorite_color = "orange";
	$error = '';
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $password = $_POST["password"];

        $passwords = file_get_contents("password.txt");
        $passwords = decrypt($passwords, [5, -14, 31, -9, 3]);

        $lines = explode("\n", $passwords);
        $found = false;
        foreach ($lines as $line) {
            $parts = explode("*", $line);
            $stored_username = $parts[0];
            $stored_password = $parts[1];
            if ($stored_username === $username) {
                $found = true;
                if ($stored_password === $password) {
                    // Sokeres bejelentkezés, ellenőrizzük, hogy van-e ilyen felhasználónk az adatbázisban
                    $conn = new mysqli("localhost", "root", "root", "webes2_adatok	");
                    if ($conn->connect_error) { die("Kapcsolódási hiba: " . $conn->connect_error); }
					// else
                    $sql = "SELECT Titkos FROM tabla WHERE Username = '$username'";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        // Helyes felhasználónév, jelszó és mehtalálható az adatbázisban
                        $row = $result->fetch_assoc();
                        $favorite_color = color($row["Titkos"]);
                    }
					else {
						$error = "Nincs ilyen felhasználó az adatbázisban";
					}
                    $conn->close();
                }
				else {
                    $error = "Hibás jelszó.";
					sleep(3);
                    header("Location: http://police.hu");
                    exit();
                }
				if ($found) { break; }
            }
        }
        if (!$found) {            
            $error = "Nincs ilyen felhasználó";
        }
    }

    ?>

	<h1><b>Szabolcsi Daniel - KITW5W</b></h1>
	<div class="form-box">
		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
			<div>
				<br>
				<input type="text" name="username" placeholder="Email" required>
				<br><br>
				<input type="password" name="password" placeholder="Password">
				<br><br>
				<input class="signin" type="submit" name="signin" value="Sign In">
				<br>
			</div>
		</form>
		<br>
		<div><?php echo $error; ?></div>
	</div>
</body>
</html>