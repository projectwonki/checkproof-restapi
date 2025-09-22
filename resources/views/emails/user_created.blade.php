<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Account Has Been Created Successfully</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .header {
            background: #4f46e5;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
            line-height: 1.6;
        }
        .button {
            display: inline-block;
            background: #4f46e5;
            color: #fff;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            margin-top: 20px;
        }
        .footer {
            background: #f1f1f1;
            color: #666;
            font-size: 12px;
            padding: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Welcome, {{ $name }} 🎉</h1>
    </div>
    <div class="content">
        <p>Your account has been created successfully.</p>

        <p><strong>Name:</strong> {{ $name }}<br>
           <strong>Email:</strong> {{ $email }}</p>
    </div>
</div>
</body>
</html>