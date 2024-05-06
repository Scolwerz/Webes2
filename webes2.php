<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LogIn</title>
</head>
<body>
	<style>
        body {
			display: flex;
			flex-direction: column;
			align-items: center;
            font-family: Arial, sans-serif;
            text-align: center;
			background: linear-gradient(to bottom, <?php echo $favorite_color; ?>, #000000);
            background-attachment: fixed;
			font-size: 30px;
        }
		.form-box {
			width: 444px;
			height: 333px;
			display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px
		}

		form {
			width: 444px;
			height: 333px;
			display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
		}
		background: transparent;
		border: 2px solid rgba(255, 0, 0, 0.5);
		backdrop-filter: blur(22px);
		box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
		border-radius: 8px;
		padding: 40px 40px;
		}

		h1 {
			position: absolute;
			font-weight: bold;
			font-size: 36px;
			color: #FFFFFF;
		}
		h2 {
			position: absolute;
			font-size: 20px;
			color: #FFFFFF;
		}
		input {
			font-size: 16px;
			width: 100%;
			height: 44px;
			border: 2px solid;
			border-radius: 4px;
			padding: 15px 40px 15px 15px;
			background-color: rgba(255, 255, 255, 0.2);
		}
		.signin {
			padding: 0;
			cursor: pointer;
		}
    </style>
		
    <?php
	
	function color($color) {
		switch ($color) {
			case "piros":  return "#FF0000"; break;
			case "zold":   return "#00FF00"; break; 
			case "sarga":  return "#FFFF00"; break;
			case "kek":    return "#0000FF"; break;
			case "fekete": return "#000000"; break;
			case "feher":  return "#FFFFFF"; break;
			default:       return "#FFA500"; break;
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
	
	
	
	$favorite_color = "#FFA500";
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
                    $conn = new mysqli("localhost", "scolwerz@gmail.com", "VPGASolimer77", "webes2_adatok	");
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
                } else {
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
