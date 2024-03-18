<?php
/**
 * Sanitizes the given data.
 * @param string $data The data to sanitize.
 * @return string The sanitized data.
 */
function sanitizeData(string $data): string
{
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data);
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Проверка, что все поля заполнены
    if (empty($_POST['login']) || empty($_POST['password'])) {
        $errors[] = "Необходимо заполнить все поля";
    } else {
        $data = [
            'login' => sanitizeData($_POST['login']),
            'password' => sanitizeData($_POST['password']),
        ];

        // Проверка данных
        $log = fopen("users.txt", "r") or die("Недоступный файл!");
        $ifExist = false;

        while (!feof($log)) {
            $line = trim(fgets($log));
            if (strpos($line, $data['login']) !== false) {
                $ifExist = true;
                $line = explode(":", $line);
                if (md5($data['password']) === $line[1]) {
                    fclose($log);
                    header("Location: images.php"); // Перенаправление пользователя на страницу с изображениями
                    exit;
                } else {
                    $errors[] = "Неверный логин или пароль";
                }
                break;
            }
        }

        // Действия, если пользователь не найден
        if (!$ifExist) {
            $errors[] = "Пользователь не найден";
        }

        fclose($log);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
</head>
<body>
    <h2>Форма авторизации</h2>
    <?php if (!empty($errors)) : ?>
        <ul>
            <?php foreach ($errors as $error) : ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form method="POST" action="">
        <label for="login">Логин:</label>
        <input type="text" id="login" name="login"><br><br>
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password"><br><br>
        <input type="submit" value="Войти">
    </form>
</body>
</html>
