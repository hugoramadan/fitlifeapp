<?php
// dashboard.php
include("includes/conexao.php");
include("includes/auth.php");

$user_id = (int)$_SESSION['user_id'];
$data = date("Y-m-d");

// busca o usuário (só para exibir nome)
$userRes = $conn->query("SELECT nome FROM users WHERE id = $user_id LIMIT 1");
$user = $userRes ? $userRes->fetch_assoc() : null;
$nome = $user ? $user['nome'] : "Usuário";

// busca (ou não) a rotina de hoje
$planRes = $conn->query("SELECT id FROM daily_plans WHERE user_id = $user_id AND data = '$data' LIMIT 1");
$plan = $planRes ? $planRes->fetch_assoc() : null;

$items = [];
$progressText = "0%";
$hasPlan = false;

if ($plan && isset($plan['id'])) {
    $hasPlan = true;
    $plan_id = (int)$plan['id'];

    // itens da rotina
    $itemsRes = $conn->query("SELECT id, titulo, concluido FROM plan_items WHERE daily_plan_id = $plan_id ORDER BY id ASC");
    if ($itemsRes) {
        while ($row = $itemsRes->fetch_assoc()) {
            $items[] = $row;
        }
    }

    // calcula progresso
    $total = count($items);
    $done = 0;
    foreach ($items as $it) {
        if ((int)$it['concluido'] === 1) $done++;
    }
    $percent = ($total > 0) ? (int)round(($done / $total) * 100) : 0;
    $progressText = $percent . "%";
}

?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>FitLife - Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap (CDN) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Seu CSS opcional -->
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light">

<div class="container py-4" style="max-width: 820px;">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-0">Bem-vinda(o), <?php echo htmlspecialchars($nome); ?>!</h3>
      <small class="text-muted">Rotina de hoje — <?php echo date("d/m/Y"); ?></small>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary" href="logout.php">Sair</a>
    </div>
  </div>

  <?php if (!$hasPlan): ?>
    <div class="alert alert-info">
      Você ainda não tem uma rotina gerada para hoje.
    </div>
    <a class="btn btn-primary" href="gerar_rotina.php">Gerar rotina do dia</a>

  <?php else: ?>
    <div class="card shadow-sm mb-3">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Sua rotina de hoje</h5>
          <span class="badge text-bg-success">Progresso: <?php echo $progressText; ?></span>
        </div>

        <hr>

        <?php if (count($items) === 0): ?>
          <p class="text-muted mb-0">Nenhum item encontrado.</p>
        <?php else: ?>
          <div class="list-group">
            <?php foreach ($items as $it): ?>
              <?php
                $item_id = (int)$it['id'];
                $titulo = $it['titulo'];
                $concluido = ((int)$it['concluido'] === 1);
              ?>
              <div class="list-group-item d-flex justify-content-between align-items-center">
                <div class="form-check">
                  <!-- Quando clicar, envia para marcar_item.php -->
                  <input
                    class="form-check-input"
                    type="checkbox"
                    id="item_<?php echo $item_id; ?>"
                    <?php echo $concluido ? "checked" : ""; ?>
                    onchange="window.location.href='marcar_item.php?id=<?php echo $item_id; ?>&v=<?php echo $concluido ? 0 : 1; ?>';"
                  >
                  <label class="form-check-label" for="item_<?php echo $item_id; ?>">
                    <?php echo htmlspecialchars($titulo); ?>
                  </label>
                </div>

                <?php if ($concluido): ?>
                  <span class="badge text-bg-secondary">Concluído</span>
                <?php else: ?>
                  <span class="badge text-bg-warning">Pendente</span>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between mt-3">
          <a class="btn btn-outline-primary" href="gerar_rotina.php">Regerar (se não existir)</a>
          <a class="btn btn-outline-secondary" href="dashboard.php">Atualizar</a>
        </div>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <h6 class="mb-2">Observação (POC)</h6>
        <p class="text-muted mb-0">
          Esta é a prova de conceito: gerar a rotina diária, marcar itens como concluídos e calcular progresso.
        </p>
      </div>
    </div>
  <?php endif; ?>
</div>

</body>
</html>