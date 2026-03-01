<?php
include("includes/auth.php");
include("includes/conexao.php");

$user_id = (int)($_SESSION["user_id"] ?? 0);
if ($user_id <= 0) {
  header("Location: index.php");
  exit();
}

$data = date("Y-m-d");

// 1) verifica se já existe rotina hoje
$sql = "SELECT id FROM daily_plans WHERE user_id = $user_id AND data = '$data' LIMIT 1";
$res = $conn->query($sql);

if ($res && $res->num_rows === 1) {
  // já existe, só volta
  header("Location: dashboard.php");
  exit();
}

// 2) cria a rotina do dia
$conn->query("INSERT INTO daily_plans (user_id, data) VALUES ($user_id, '$data')");
$plan_id = (int)$conn->insert_id;

// 3) itens padrão (rotina automática)
$itens = [
  "Treino 30min",
  "Beber 2L de água",
  "Alimentação saudável",
  "Dormir 8h",
  "Registrar humor"
];

foreach ($itens as $titulo) {
  $t = $conn->real_escape_string($titulo);
  $conn->query("INSERT INTO plan_items (daily_plan_id, titulo, concluido) VALUES ($plan_id, '$t', 0)");
}

header("Location: dashboard.php");
exit();
?>