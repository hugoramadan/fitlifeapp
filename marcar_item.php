<?php
// marcar_item.php
include("includes/conexao.php");
include("includes/auth.php");

$item_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$val = isset($_GET['v']) ? (int)$_GET['v'] : 0;
$val = ($val === 1) ? 1 : 0;

if ($item_id > 0) {
    // segurança extra: garante que o item pertence ao usuário logado
    $user_id = (int)$_SESSION['user_id'];

    $sql = "
      UPDATE plan_items pi
      JOIN daily_plans dp ON dp.id = pi.daily_plan_id
      SET pi.concluido = $val
      WHERE pi.id = $item_id AND dp.user_id = $user_id
      LIMIT 1
    ";
    $conn->query($sql);
}

header("Location: dashboard.php");
exit();
?>