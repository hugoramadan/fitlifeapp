<?php
include("includes/auth.php");
include("includes/conexao.php");

$user_id = (int)($_SESSION["user_id"] ?? 0);
$nome = $_SESSION["nome"] ?? "Usuário";

$data = date("Y-m-d");

if (isset($_GET['delete_item']) && ctype_digit($_GET['delete_item'])) {
  $delId = (int)$_GET['delete_item'];
  $conn->query("DELETE pi FROM plan_items pi
        JOIN daily_plans dp ON pi.daily_plan_id = dp.id
        WHERE pi.id=$delId AND dp.user_id=$user_id AND dp.data='$data'");
  header('Location: dashboard.php');
  exit;
}

if (
  $_SERVER['REQUEST_METHOD'] === 'POST' &&
  !empty($_POST['new_item'])
) {
  $titulo = $conn->real_escape_string(trim($_POST['new_item']));
  if ($titulo !== '') {
    $planRes2 = $conn->query("SELECT id FROM daily_plans WHERE user_id=$user_id AND data='$data' LIMIT 1");
    if ($planRes2 && $planRes2->num_rows > 0) {
      $plan2 = $planRes2->fetch_assoc();
      $plan2_id = (int)$plan2['id'];
      $conn->query("INSERT INTO plan_items (daily_plan_id, titulo) VALUES ($plan2_id, '$titulo')");
    }
  }
  header('Location: dashboard.php');
  exit;
}

$plan_id = 0;
$planRes = $conn->query("SELECT id FROM daily_plans WHERE user_id = $user_id AND data = '$data' LIMIT 1");
if ($planRes && $planRes->num_rows === 1) {
  $plan = $planRes->fetch_assoc();
  $plan_id = (int)$plan["id"];
}

$items = [];
if ($plan_id > 0) {
  $itRes = $conn->query("SELECT id, titulo, concluido FROM plan_items WHERE daily_plan_id = $plan_id ORDER BY id ASC");
  if ($itRes) {
    while ($row = $itRes->fetch_assoc()) $items[] = $row;
  }
}

$total = count($items);
$done = 0;
foreach ($items as $it) {
  if ((int)$it["concluido"] === 1) $done++;
}
$percent = ($total > 0) ? (int)round(($done / $total) * 100) : 0;

$dataBR = date("d/m/Y");
?>

<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>FitLife - Rotina do Dia</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      color: #343a40;
    }

    .card {
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
      border-radius: 12px;
    }

    .list-group-item {
      border-radius: 8px;
      margin-bottom: 0.5rem;
    }

    .btn-primary {
      background-color: #0d6efd;
      border-color: #0d6efd;
    }

    .navbar {
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand {
      font-weight: 700;
      color: #0d6efd;
    }

    .card-header {
      background-color: #f1f3f5;
      font-weight: 600;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4">
    <div class="container-fluid">
      <a class="navbar-brand" href="dashboard.php">FitLife</a>
      <div class="d-flex">
        <span class="navbar-text me-3 text-muted small">Olá, <?php echo htmlspecialchars($nome); ?></span>
        <a class="btn btn-outline-secondary btn-sm" href="logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a>
      </div>
    </div>
  </nav>
  <div class="container py-4">

    <header class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h1 class="h4 mb-0">Rotina do Dia</h1>
        <div class="text-muted small">Hoje: <?php echo $dataBR; ?></div>
      </div>
    </header>

    <section class="card shadow-sm mb-3">
      <div class="card-body">
        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
          <div>
            <h2 class="h6 mb-1">Sua rotina</h2>
            <div class="text-muted small">
              <?php if ($plan_id === 0): ?>
                Você ainda não gerou a rotina de hoje.
              <?php else: ?>
                Marque os itens conforme concluir.
              <?php endif; ?>
            </div>
          </div>
          <div class="d-flex gap-2">
            <a class="btn btn-primary btn-sm" href="gerar_rotina.php"><i class="bi bi-plus-circle"></i>Gerar rotina</a>
            <a class="btn btn-outline-secondary btn-sm" href="evolucao.php"><i class="bi bi-bar-chart"></i>Evolução</a>
          </div>
        </div>

        <hr>

        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="text-muted small">Progresso do dia</span>
          <span class="badge text-bg-success"><?php echo $percent; ?>%</span>
        </div>
        <div class="progress mb-3">
          <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $percent; ?>%;" aria-valuenow="<?php echo $percent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
        </div>

        <div class="list-group mb-3">
          <?php if ($plan_id === 0): ?>
            <div class="text-muted small">Nenhuma rotina gerada ainda.</div>
          <?php else: ?>
            <?php foreach ($items as $it): ?>
              <?php
              $item_id = (int)$it['id'];
              $titulo = $it['titulo'];
              $concluido = ((int)$it['concluido'] === 1);
              ?>
              <div class="list-group-item d-flex justify-content-between align-items-center">
                <div class="form-check d-flex align-items-center gap-2">
                  <input class="form-check-input" type="checkbox"
                    <?php echo $concluido ? "checked" : ""; ?>
                    onchange="window.location.href='marcar_item.php?id=<?php echo $item_id; ?>&v=<?php echo $concluido ? 0 : 1; ?>';">
                  <label class="form-check-label mb-0">
                    <?php echo htmlspecialchars($titulo); ?>
                  </label>
                </div>

                <div class="d-flex gap-2 align-items-center">
                  <?php if ($concluido): ?>
                    <span class="badge text-bg-success">Concluído</span>
                  <?php else: ?>
                    <span class="badge text-bg-warning">Pendente</span>
                  <?php endif; ?>

                  <a href="dashboard.php?delete_item=<?php echo $item_id; ?>"
                    class="text-danger small"
                    onclick="return confirm('Tem certeza que deseja excluir esta tarefa?');">
                    <i class="bi bi-trash"></i>
                  </a>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <?php if ($plan_id !== 0): ?>
          <form method="post" class="input-group mb-3">
            <input type="text" name="new_item" class="form-control" placeholder="Adicionar nova tarefa" required>
            <button class="btn btn-primary" type="submit"><i class="bi bi-plus-circle"></i> Adicionar</button>
          </form>
        <?php endif; ?>

      </div>
    </section>

  </div>
</body>
</html>