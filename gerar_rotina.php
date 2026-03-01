<?php
include("includes/conexao.php");
include("includes/auth.php");

$user_id = $_SESSION['user_id'];
$data = date("Y-m-d");

// verifica se já existe rotina hoje
$check = $conn->query("SELECT * FROM daily_plans WHERE user_id=$user_id AND data='$data'");

if ($check->num_rows == 0) {
    $conn->query("INSERT INTO daily_plans (user_id, data) VALUES ($user_id, '$data')");
    $plan_id = $conn->insert_id;

    $itens = ["Treino 30min", "Beber 2L água", "Alimentação saudável", "Dormir 8h", "Registrar humor"];

    foreach ($itens as $item) {
        $conn->query("INSERT INTO plan_items (daily_plan_id, titulo) VALUES ($plan_id, '$item')");
    }
}

header("Location: dashboard.php");
?>