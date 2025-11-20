<?php
require_once 'conexao.php';

/**
 * 1) Buscar todas as ordens de serviço com o nome do cliente
 */
$sqlOs = "SELECT 
            os.id,
            os.titulo,
            os.descricao,
            os.status,
            os.data_abertura,
            os.data_prevista,
            os.data_conclusao,
            os.valor_previsto,
            os.valor_final,
            c.nome AS cliente_nome
          FROM ordens_servico AS os
          INNER JOIN clientes AS c ON c.id = os.cliente_id
          ORDER BY os.data_abertura DESC";

$stmtOs = $pdo->prepare($sqlOs);
$stmtOs->execute();
$ordens = $stmtOs->fetchAll(PDO::FETCH_ASSOC);

/**
 * 2) Contagem por status para o gráfico
 */
$totAbertas      = 0;
$totEmAndamento  = 0;
$totConcluidas   = 0;
$totCanceladas   = 0;

foreach ($ordens as $os) {
    switch ($os['status']) {
        case 'aberta':
            $totAbertas++;
            break;
        case 'em_andamento':
            $totEmAndamento++;
            break;
        case 'concluida':
            $totConcluidas++;
            break;
        case 'cancelada':
            $totCanceladas++;
            break;
    }
}

/**
 * 3) Buscar clientes para o <select> do formulário de nova OS
 */
$sqlClientes = "SELECT id, nome FROM clientes ORDER BY nome";
$stmtClientes = $pdo->prepare($sqlClientes);
$stmtClientes->execute();
$clientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Ordens de Serviço</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.11/css/jquery.dataTables.min.css">

    <style>
        body {
            margin-top: 20px;
        }
        .espaco-top {
            margin-top: 20px;
        }
    </style>

    <!-- Google Charts -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Status', 'Quantidade'],
                ['Abertas',        <?php echo (int)$totAbertas; ?>],
                ['Em andamento',   <?php echo (int)$totEmAndamento; ?>],
                ['Concluídas',     <?php echo (int)$totConcluidas; ?>],
                ['Canceladas',     <?php echo (int)$totCanceladas; ?>]
            ]);

            var options = {
                title: 'Situação das Ordens de Serviço'
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart'));
            chart.draw(data, options);
        }
    </script>
</head>
<body>

<div class="container">
    <h1 class="alert alert-info text-center">GESTÃO DE ORDENS DE SERVIÇO</h1>

    <!-- FORMULÁRIO NOVA OS -->
    <div class="row">
        <div class="col-md-12">
            <h3>Nova Ordem de Serviço</h3>
            <form method="post" action="insert.php" class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Cliente</label>
                    <div class="col-sm-6">
                        <select name="cliente_id" class="form-control" required>
                            <option value="">Selecione um cliente</option>
                            <?php foreach ($clientes as $cli): ?>
                                <option value="<?php echo (int)$cli['id']; ?>">
                                    <?php echo htmlspecialchars($cli['nome'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <a href="clientes.php" class="btn btn-default">
                            Gerenciar Clientes
                        </a>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Título</label>
                    <div class="col-sm-10">
                        <input type="text" name="titulo" class="form-control" required
                               placeholder="Ex.: Manutenção em computador, instalação de impressora...">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Descrição</label>
                    <div class="col-sm-10">
                        <textarea name="descricao" class="form-control" rows="3" required
                                  placeholder="Descreva o problema ou serviço a ser realizado"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Data prevista</label>
                    <div class="col-sm-4">
                        <input type="datetime-local" name="data_prevista" class="form-control">
                    </div>

                    <label class="col-sm-2 control-label">Valor previsto (R$)</label>
                    <div class="col-sm-4">
                        <input type="number" step="0.01" min="0" name="valor_previsto" class="form-control" value="0.00">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-success">Cadastrar OS</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <hr>

        <!-- ABAS: RESUMO / LISTA -->
    <div class="espaco-top">

        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#tab-resumo" aria-controls="tab-resumo" role="tab" data-toggle="tab">
                    Resumo / Gráfico
                </a>
            </li>
            <li role="presentation">
                <a href="#tab-lista" aria-controls="tab-lista" role="tab" data-toggle="tab">
                    Lista de Ordens
                </a>
            </li>
        </ul>

        <div class="tab-content" style="margin-top: 20px;">

            <!-- ABA RESUMO -->
            <div role="tabpanel" class="tab-pane fade in active" id="tab-resumo">
                <div class="row">
                    <div class="col-md-6">
                        <div id="piechart" style="width: 100%; height: 320px;"></div>
                    </div>

                    <div class="col-md-6">
                        <h3>Resumo das OS</h3>
                        <div class="list-group">
                            <span class="list-group-item">
                                <strong>Abertas:</strong> <?php echo (int)$totAbertas; ?>
                            </span>
                            <span class="list-group-item">
                                <strong>Em andamento:</strong> <?php echo (int)$totEmAndamento; ?>
                            </span>
                            <span class="list-group-item">
                                <strong>Concluídas:</strong> <?php echo (int)$totConcluidas; ?>
                            </span>
                            <span class="list-group-item">
                                <strong>Canceladas:</strong> <?php echo (int)$totCanceladas; ?>
                            </span>
                            <span class="list-group-item">
                                <strong>Total de OS:</strong> 
                                <?php echo (int)($totAbertas + $totEmAndamento + $totConcluidas + $totCanceladas); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ABA LISTA -->
            <div role="tabpanel" class="tab-pane fade" id="tab-lista">
                <h3>Ordens de Serviço</h3>
                <table id="table" class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Título</th>
                        <th>Status</th>
                        <th>Abertura</th>
                        <th>Prevista</th>
                        <th>Conclusão</th>
                        <th>Valor (R$)</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($ordens as $os): ?>
                        <tr>
                            <td><?php echo (int)$os['id']; ?></td>
                            <td><?php echo htmlspecialchars($os['cliente_nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($os['titulo'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <?php
                                switch ($os['status']) {
                                    case 'aberta':
                                        echo 'Aberta';
                                        break;
                                    case 'em_andamento':
                                        echo 'Em andamento';
                                        break;
                                    case 'concluida':
                                        echo 'Concluída';
                                        break;
                                    case 'cancelada':
                                        echo 'Cancelada';
                                        break;
                                }
                                ?>
                            </td>
                            <td><?php echo $os['data_abertura']; ?></td>
                            <td><?php echo $os['data_prevista']; ?></td>
                            <td><?php echo $os['data_conclusao']; ?></td>
                            <td><?php echo number_format($os['valor_final'], 2, ',', '.'); ?></td>
                            <td>
                                <a href="update.php?id=<?php echo (int)$os['id']; ?>" class="btn btn-xs btn-primary">
                                    Editar
                                </a>
                                <a href="delete.php?id=<?php echo (int)$os['id']; ?>"
                                   class="btn btn-xs btn-danger"
                                   onclick="return confirm('Tem certeza que deseja excluir esta OS?');">
                                    Excluir
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

<!-- jQuery -->
<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>

<!-- Bootstrap JS (necessário para as abas) -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

<!-- Script Datatables -->
<script src="https://cdn.datatables.net/1.10.11/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#table').DataTable({
            language: {
                url:'http://cdn.datatables.net/plug-ins/1.10.9/i18n/Portuguese-Brasil.json'
            }
        });
    });
</script>
</body>
</html>