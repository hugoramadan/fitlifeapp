<?php include("includes/auth.php"); ?>

<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>FitLife - Evolução</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-4" style="max-width: 900px;">

    <header class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h1 class="h4 mb-0">Evolução semanal</h1>
        <div class="text-muted small">(Front-end) dados simulados</div>
      </div>
      <a class="btn btn-outline-secondary btn-sm" href="dashboard.php">Voltar</a>
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
      </div>
    </section>

    <section class="card shadow-sm">
      <div class="card-body">
        <h2 class="h6 mb-3">Resumo</h2>
        <ul class="mb-0">
          <li><strong>Dias com rotina:</strong> <span id="diasComRotina">0</span>/7</li>
          <li><strong>Média da semana:</strong> <span id="mediaSemana">0</span>%</li>
          <li><strong>Melhor dia:</strong> <span id="melhorDia">-</span></li>
        </ul>
      </div>
    </section>

  </div>

  <script>
    // Dados simulados (0 a 100)
    const semana = [
      { dia: "Seg", pct: 60 },
      { dia: "Ter", pct: 40 },
      { dia: "Qua", pct: 80 },
      { dia: "Qui", pct: 20 },
      { dia: "Sex", pct: 70 },
      { dia: "Sáb", pct: 50 },
      { dia: "Dom", pct: 90 }
    ];

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
    const media = Math.round(semana.reduce((a, x) => a + x.pct, 0) / semana.length);
    const melhor = semana.reduce((best, x) => x.pct > best.pct ? x : best, semana[0]);

    document.getElementById("diasComRotina").textContent = diasComRotina;
    document.getElementById("mediaSemana").textContent = media;
    document.getElementById("melhorDia").textContent = `${melhor.dia} (${melhor.pct}%)`;
  </script>
</body>
</html>