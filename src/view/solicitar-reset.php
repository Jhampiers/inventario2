<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitar nuevo enlace</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background: white;
            padding: 30px 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }
        .form-container input {
            padding: 10px;
            margin-top: 10px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .form-container button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #900C3F;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>¿Olvidaste tu contraseña?</h2>
        <p>Ingresa tu correo electrónico para enviarte un nuevo enlace.</p>
        <form action="src/control/Usuario.php?tipo=sent_email_password" method="POST">
            <input type="email" name="correo" placeholder="Correo electrónico" required>
            <input type="hidden" name="sesion" value="SISTEMA">
            <input type="hidden" name="token" value="SISTEMA">
            <button type="submit">Enviar enlace</button>
        </form>
    </div>
</body>
</html>
