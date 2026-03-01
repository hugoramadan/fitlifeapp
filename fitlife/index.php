<?php
session_start();
include("includes/conexao.php");

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"] ?? "";
    $senha = $_POST["senha"] ?? "";

    $email = $conn->real_escape_string($email);
    $senha = $conn->real_escape_string($senha);

    $sql = "SELECT id, nome FROM users WHERE email='$email' AND senha='$senha' LIMIT 1";
    $res = $conn->query($sql);

    if ($res && $res->num_rows === 1) {
        $u = $res->fetch_assoc();
        $_SESSION["logado"] = true;
        $_SESSION["user_id"] = (int)$u["id"];
        $_SESSION["nome"] = $u["nome"];
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
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>FitLife - Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height:100vh;">
  <main class="container" style="max-width: 420px;">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h1 class="h4 text-center mb-1">FitLife</h1>
        <p class="text-center text-muted mb-4">Organize sua rotina do dia</p>

        <?php if ($erro): ?>
          <div class="alert alert-danger"><?php echo $erro; ?></div>
        <?php endif; ?>

        <form method="POST">
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input class="form-control" type="email" name="email" placeholder="julia@fitlife.com" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Senha</label>
            <input class="form-control" type="password" name="senha" placeholder="1234" required>
          </div>

          <!-- por enquanto é só navegação front-end -->
          <button class="btn btn-primary w-100" type="submit">Entrar</button>


        </form>
      </div>
    </div>
  </main>
</body>
</html>