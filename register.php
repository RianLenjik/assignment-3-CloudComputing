<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
</head>
<?php
include "navbar.php";
?>

<body>
    <h1>Register</h1>
    <br>
    <form action="register.php" method="POST">
        <div class="form-group">
            Email: <input type="email" name="email" placeholder="Enter an Email">
        </div>
        <div class="form-group">
            Username: <input type="text" name="username" placeholder="Enter a Username">
        </div>
        <div class="form-group">
            Password: <input type="password" name="password" placeholder="Enter a Password">
        </div>
        <br /><input type="submit" name="submit" value="Register">
        <br>
        <br />Already have an account? <a href="login.php">Login</a>
        <br />
    </form>
    <br>
</body>

</html>

<?php
require("connect.php");

date_default_timezone_set('Australia/Melbourne');

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;

$dynamodb = $sdk->createDynamoDb();
$marshaler = new Marshaler();
$tableName = "Login";
$params = [
    'TableName' => $tableName,
    'ProjectionExpression' => 'email, password, user_name'
];

$email = @$_POST['email'];
$username = @$_POST['username'];
$password = @$_POST['password'];


if (isset($_POST['submit'])) {
    if ($email && $username && $password) {
        $emailExist = false;
        try {
            $result = $dynamodb->scan($params);
            foreach ($result['Items'] as $i) {
                $user = $marshaler->unmarshalItem($i);
                if ($user['email'] == $email) {
                    $emailExist = true;
                }
            }
            if ($emailExist) {
                echo "<strong>";
                echo "Email already exists";
                echo "</strong>";
            } else {
                $json = json_encode([
                    'email' => $email,
                    'password' => $password,
                    'user_name' => $username
                ]);

                $params2 = [
                    'TableName' => $tableName,
                    'Item' => $marshaler->marshalJson($json)
                ];

                try {
                    $result = $dynamodb->putItem($params2);
                    echo "<script type='text/javascript'>window.location.href = 'login.php';</script>";
                    exit();
                } catch (DynamoDbException $e) {
                    echo "Unable to add user\n";
                    echo $e->getMessage() . "\n";
                }
            }
        } catch (DynamoDbException $e) {
            echo "Unable to scan:\n";
            echo $e->getMessage() . "\n";
        }
    } else {
        echo "<strong>";
        echo "Please fill in all fields";
        echo "</strong>";
    }
}

?>