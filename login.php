<?php
session_start();
include "navbar.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
    <h1>Login</h1>
    <br>
    <form action="login.php" method="POST">
        <div class="form-group">
            Email: <input type="email" name="email" placeholder="Enter Email">
        </div>
        <div class="form-group">
            Password: <input type="password" name="password" placeholder="Enter Password">
        </div>
        <br /><input type="submit" name="submit" value="Login">
        <br>
        <br />Don't have an account yet? <a href="register.php">Register</a>
        <br />


    </form>
    <br>


    <?php
    require("connect.php");

    date_default_timezone_set('Australia/Melbourne');

    use Aws\DynamoDb\Exception\DynamoDbException;
    use Aws\DynamoDb\Marshaler;

    $dynamodb = $sdk->createDynamoDb();
    $marshaler = new Marshaler();

    $params = [
        'TableName' => 'Login',
        'ProjectionExpression' => 'email, password, user_name'
    ];

    $email = @$_POST['email'];
    $password = @$_POST['password'];

    if (isset($_POST['submit'])) {
        $username = "";
        if ($email && $password) {
            $userExist = false;
            try {
                $result = $dynamodb->scan($params);
                foreach ($result['Items'] as $i) {
                    $user = $marshaler->unmarshalItem($i);
                    if ($user['email'] == $email && $user['password'] == $password) {
                        $userExist = true;
                        $username = $user['user_name'];
                    }
                }
                if ($userExist) {
                    @$_SESSION['username'] = $username;
                    @$_SESSION['email'] = $email;

                    echo "<script type='text/javascript'>window.location.href = 'index.php';</script>";
                } else {
                    echo "<strong>";
                    echo "Email or password is invalid";
                    echo "</strong>";
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

</body>

</html>