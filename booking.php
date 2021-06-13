<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking</title>
</head>
<?php
session_start();
include 'navbar.php';
?>

<body>
    <h1>
        <center>This is where booking takes place</center>
        <?php echo $_SESSION['postcode'] ?>
    </h1>
</body>

</html>