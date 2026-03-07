<?php
include("includes/auth.php");
include("includes/conexao.php");

$user_id = (int)($_SESSION["user_id"] ?? 0);
$nome = $_SESSION["nome"] ?? "Júlia";
$semana = [];

$mapDias = [
    'Mon' => 'Seg',
    'Tue' => 'Ter',
    'Wed' => 'Qua',
    'Thu' => 'Qui',
    'Fri' => 'Sex',
    'Sat' => 'Sáb',
    'Sun' => 'Dom'
];

$sql = "
SELECT 
    dp.data,
    COUNT(pi.id) as total,
    SUM(pi.concluido) as done
FROM daily_plans dp
LEFT JOIN plan_items pi ON pi.daily_plan_id = dp.id
WHERE dp.user_id = $user_id
AND dp.data >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
GROUP BY dp.data
";

$res = $conn->query($sql);

$dados = [];
while ($row = $res->fetch_assoc()) {
    $dados[$row['data']] = [
        'total' => (int)$row['total'],
        'done' => (int)$row['done']
    ];
}

for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));

    $total = $dados[$d]['total'] ?? 0;
    $done = $dados[$d]['done'] ?? 0;

    $pct = $total > 0 ? (int)round(($done / $total) * 100) : 0;

    $diaNome = $mapDias[date('D', strtotime($d))] ?? date('D', strtotime($d));

    $semana[] = [
        'dia' => $diaNome,
        'pct' => $pct
    ];
}

?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>FitLife - Evolução</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-light">
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
                <h1 class="h4 mb-0">Evolução semanal</h1>
                <div class="text-muted small">Progresso calculado pelos últimos 7 dias</div>
            </div>
            <a class="btn btn-outline-secondary btn-sm" href="dashboard.php"><i class="bi bi-arrow-left"></i> Voltar</a>
        </header>

        <section class="card shadow-sm mb-3">
            <div class="card-body">
                <h2 class="h6 mb-3">Últimos 7 dias</h2>

                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th style="width: 90px;">Dia</th>
                                <th>Progresso</th>
                                <th style="width: 80px;" class="text-end">%</th>
                            </tr>
                        </thead>
                        <tbody id="tb"></tbody>
                    </table>
                </div>

                <canvas id="chart" height="100"></canvas>
            </div>
        </section>

        <section class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3">Resumo</h2>
                <ul class="list-inline mb-0">
                    <li class="list-inline-item me-4"><strong>Dias com rotina:</strong> <span id="diasComRotina">0</span>/7</li>
                    <li class="list-inline-item me-4"><strong>Média da semana:</strong> <span id="mediaSemana">0</span>%</li>
                    <li class="list-inline-item"><strong>Melhor dia:</strong> <span id="melhorDia">-</span></li>
                </ul>
            </div>
        </section>

    </div>

    <script>
        const semana = <?php echo json_encode($semana); ?>;

        const tb = document.getElementById("tb");

        function barra(pct) {
            return `
        <div class="progress" style="height: 10px;">
          <div class="progress-bar" role="progressbar" style="width: ${pct}%;" aria-valuenow="${pct}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
      `;
        }

        semana.forEach(r => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
        <td>${r.dia}</td>
        <td>${barra(r.pct)}</td>
        <td class="text-end">${r.pct}%</td>
      `;
            tb.appendChild(tr);
        });

        const diasComRotina = semana.filter(x => x.pct > 0).length;
        const media = semana.length ? Math.round(semana.reduce((a, x) => a + x.pct, 0) / semana.length) : 0;
        const melhor = semana.length ? semana.reduce((best, x) => x.pct > best.pct ? x : best, semana[0]) : {
            dia: '-',
            pct: 0
        };

        document.getElementById("diasComRotina").textContent = diasComRotina;
        document.getElementById("mediaSemana").textContent = media;
        document.getElementById("melhorDia").textContent = `${melhor.dia} (${melhor.pct}%)`;

        const ctx = document.getElementById('chart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: semana.map(x => x.dia),
                datasets: [{
                    label: 'Progresso (%)',
                    data: semana.map(x => x.pct),
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13,110,253,0.2)',
                    tension: 0.3
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    </script>
</body>

</html>