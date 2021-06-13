<!DOCTYPE html>
<html lang="en">

<?php include "connect.php"; ?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>

    <style type="text/css">
        * {
            padding: 0;
            margin: 0;
        }

        .navbar {
            width: 100%;
            background: #005350;
        }

        .navbar ul {
            list-style-type: none;
            text-align: center;
        }

        .navbar li {
            display: inline-block;
            padding: 10px;
            color: #fff;
        }

        .navbar a {
            float: left;
            text-align: center;
            padding: 12px;
            color: white;
            text-decoration: none;
            font-size: 17px;
        }

        /* Navbar links on mouse-over */
        .navbar a:hover {
            background-color: #fff;
            color: black;
            transition: ease .5s;
        }

        /* Current/active navbar link */
        .active {
            background-color: #04AA6D;

        }
    </style>
</head>

<body style="background-color: #d9d9d9">
    <div class="navbar">
        <ul>
            <li><a href="index.php">Now Showing</a></li>
            <li><a href="#">Theaters</a></li>
            <li><a href="comingsoon.php">Coming Soon</a></li>
            <li><a href="#">Playing at ....</a></li>
            <li><a href="#">About Us</a></li>
            <li><a href="#">Logout</a></li>
        </ul>
    </div>
</body>

</html>