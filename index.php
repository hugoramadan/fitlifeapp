<?php
session_start();
include("includes/conexao.php");

$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM users WHERE email='$email' AND senha='$senha' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        header("Location: dashboard.php");
        exit();
    } else {
        $erro = "Login inválido.";
    }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>FitLife - Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
min-height:100vh;
background: linear-gradient(135deg, #ffffff, #a1d7fd);
display:flex;
align-items:center;
justify-content:center;
font-family: Arial, Helvetica, sans-serif;
}

.login-card{
border:none;
border-radius:15px;
box-shadow:0 10px 25px rgba(0,0,0,0.2);
}

.logo{
font-size:28px;
font-weight:bold;
color:#0d6efd;
}

.btn-login{
border-radius:8px;
font-weight:600;
}

input{
border-radius:8px !important;
}

</style>

</head>

<body>

<div class="container" style="max-width:420px;">

<div class="card login-card p-4">

<div class="text-center mb-3">
<div class="logo">💪 FitLife</div>
<p class="text-muted">Acesse sua conta</p>
</div>

<?php if ($erro): ?>
<div class="alert alert-danger">
<?php echo $erro; ?>
</div>
<?php endif; ?>

<form method="POST">

<div class="mb-3">
<label class="form-label">Email</label>
<input type="email" name="email" class="form-control" placeholder="Digite seu email" required>
</div>

<div class="mb-3">
<label class="form-label">Senha</label>
<input type="password" name="senha" class="form-control" placeholder="Digite sua senha" required>
</div>

<button class="btn btn-primary btn-login w-100">
Entrar
</button>

</form>

<div class="text-center mt-3 small text-muted">
Login padrão:<br>
<strong>julia@fitlife.com</strong> / <strong>1234</strong>
</div>

</div>
</div>

</body>
</html>