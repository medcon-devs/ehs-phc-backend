<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        h2 {
            color: #333;
        }

        p {
            color: #555;
            margin-bottom: 10px;
        }

        strong {
            color: #333;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Contact Us Details</h2>
    <p><strong>Name:</strong> {{$name}}</p>
    <p><strong>Email:</strong> {{$email}}</p>
    <p><strong>Message:</strong> {{$message}}</p>
</div>
</body>
</html>
