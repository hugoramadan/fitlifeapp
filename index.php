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
</head>
<body class="bg-light d-flex align-items-center" style="height:100vh;">

<div class="container" style="max-width:400px;">
  <div class="card shadow">
    <div class="card-body">
      <h4 class="text-center mb-3">FitLife</h4>

      <?php if ($erro): ?>
        <div class="alert alert-danger"><?php echo $erro; ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="mb-3">
          <label>Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
          <label>Senha</label>
          <input type="password" name="senha" class="form-control" required>
        </div>

        <button class="btn btn-primary w-100">Entrar</button>
      </form>

      <div class="mt-3 text-center text-muted small">
        Login padrão:<br>
        julia@fitlife.com / 1234
      </div>

    </div>
  </div>
</div>

</body>
</html>