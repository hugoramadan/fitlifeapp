<?php
include("includes/auth.php");
include("includes/conexao.php");

$user_id = (int)($_SESSION["user_id"] ?? 0);
$nome = $_SESSION["nome"] ?? "Usuário";

$data = date("Y-m-d");

// Busca plano de hoje
$plan_id = 0;
$planRes = $conn->query("SELECT id FROM daily_plans WHERE user_id = $user_id AND data = '$data' LIMIT 1");
if ($planRes && $planRes->num_rows === 1) {
  $plan = $planRes->fetch_assoc();
  $plan_id = (int)$plan["id"];
}

// Busca itens (se tiver plano)
$items = [];
if ($plan_id > 0) {
  $itRes = $conn->query("SELECT id, titulo, concluido FROM plan_items WHERE daily_plan_id = $plan_id ORDER BY id ASC");
  if ($itRes) {
    while ($row = $itRes->fetch_assoc()) $items[] = $row;
  }
}

// Progresso
$total = count($items);
$done = 0;
foreach ($items as $it) {
  if ((int)$it["concluido"] === 1) $done++;
}
$percent = ($total > 0) ? (int)round(($done / $total) * 100) : 0;

// Data BR
$dataBR = date("d/m/Y");
?>

<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>FitLife - Rotina do Dia</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-4" style="max-width: 900px;">

    <header class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h1 class="h4 mb-0">Rotina do Dia</h1>
        <div class="text-muted small">Bem-vinda(o), <?php echo htmlspecialchars($nome); ?> — Hoje: <?php echo $dataBR; ?></div>
      </div>
      <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary btn-sm" href="logout.php">Sair</a>
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
            <a class="btn btn-primary btn-sm" href="gerar_rotina.php">Gerar rotina do dia</a>
            <a class="btn btn-outline-secondary btn-sm" href="evolucao.php">Ver evolução</a>
          </div>
        </div>

        <hr>

        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="text-muted small">Progresso do dia</span>
          <span class="badge text-bg-success"><?php echo $percent; ?>%</span>
        </div>

        <div class="list-group">
          <?php if ($plan_id === 0): ?>
            <div class="text-muted small">Nenhuma rotina gerada ainda.</div>
          <?php else: ?>
            <?php foreach ($items as $it): ?>
              <label class="list-group-item d-flex gap-2 align-items-center">
                <input class="form-check-input m-0" type="checkbox"
                <?php echo ((int)$it["concluido"] === 1) ? "checked" : ""; ?>
                onchange="window.location.href='marcar_item.php?id=<?php echo (int)$it['id']; ?>&v=<?php echo ((int)$it['concluido'] === 1) ? 0 : 1; ?>';"
/>
                <span><?php echo htmlspecialchars($it["titulo"]); ?></span>
              </label>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

      </div>
    </section>

  </div>
</body>
</html>