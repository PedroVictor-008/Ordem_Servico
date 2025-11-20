<?php
require_once 'conexao.php';

// Se for POST, processa a atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id             = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $cliente_id     = isset($_POST['cliente_id']) ? (int)$_POST['cliente_id'] : 0;
    $titulo         = trim($_POST['titulo'] ?? '');
    $descricao      = trim($_POST['descricao'] ?? '');
    $status         = $_POST['status'] ?? 'aberta';
    $data_prevista  = $_POST['data_prevista'] ?? '';
    $data_conclusao = $_POST['data_conclusao'] ?? '';
    $valor_previsto = $_POST['valor_previsto'] ?? '0';
    $valor_final    = $_POST['valor_final'] ?? '0';
    $observacoes    = trim($_POST['observacoes'] ?? '');

    if ($id <= 0) {
        header('Location: index.php');
        exit;
    }

    // Ajusta datas
    if ($data_prevista !== '') {
        $data_prevista = str_replace('T', ' ', $data_prevista) . ':00';
    } else {
        $data_prevista = null;
    }

    if ($data_conclusao !== '') {
        $data_conclusao = str_replace('T', ' ', $data_conclusao) . ':00';
    } else {
        $data_conclusao = null;
    }

    // Ajusta valores numéricos
    $valor_previsto = str_replace(',', '.', $valor_previsto);
    if ($valor_previsto === '' || !is_numeric($valor_previsto)) {
        $valor_previsto = 0;
    }

    $valor_final = str_replace(',', '.', $valor_final);
    if ($valor_final === '' || !is_numeric($valor_final)) {
        $valor_final = 0;
    }

    $sql = "UPDATE ordens_servico
               SET cliente_id      = :cliente_id,
                   titulo          = :titulo,
                   descricao       = :descricao,
                   status          = :status,
                   data_prevista   = :data_prevista,
                   data_conclusao  = :data_conclusao,
                   valor_previsto  = :valor_previsto,
                   valor_final     = :valor_final,
                   observacoes     = :observacoes,
                   atualizado_por  = :atualizado_por
             WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':cliente_id', $cliente_id, PDO::PARAM_INT);
    $stmt->bindValue(':titulo', $titulo);
    $stmt->bindValue(':descricao', $descricao);
    $stmt->bindValue(':status', $status);
    $stmt->bindValue(':data_prevista', $data_prevista);
    $stmt->bindValue(':data_conclusao', $data_conclusao);
    $stmt->bindValue(':valor_previsto', $valor_previsto);
    $stmt->bindValue(':valor_final', $valor_final);
    $stmt->bindValue(':observacoes', $observacoes);
    $stmt->bindValue(':atualizado_por', 1, PDO::PARAM_INT); // sem login, fixo 1
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    $stmt->execute();

    header('Location: index.php');
    exit;
}

// Se for GET, carrega os dados da OS e mostra o formulário
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Busca OS
$sqlOs = "SELECT * FROM ordens_servico WHERE id = :id";
$stmtOs = $pdo->prepare($sqlOs);
$stmtOs->bindValue(':id', $id, PDO::PARAM_INT);
$stmtOs->execute();
$os = $stmtOs->fetch();

if (!$os) {
    header('Location: index.php');
    exit;
}

// Busca clientes para o select
$sqlClientes = "SELECT id, nome FROM clientes ORDER BY nome";
$stmtClientes = $pdo->prepare($sqlClientes);
$stmtClientes->execute();
$clientes = $stmtClientes->fetchAll();

function formatDateTimeLocal($datetime) {
    if (!$datetime) return '';
    // Entrada: "2025-11-17 14:30:00" -> "2025-11-17T14:30"
    return substr(str_replace(' ', 'T', $datetime), 0, 16);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Ordem de Serviço</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1 class="alert alert-info text-center">Editar Ordem de Serviço #<?php echo (int)$os['id']; ?></h1>

    <form method="post" class="form-horizontal">
        <input type="hidden" name="id" value="<?php echo (int)$os['id']; ?>">

        <div class="form-group">
            <label class="col-sm-2 control-label">Cliente</label>
            <div class="col-sm-10">
                <select name="cliente_id" class="form-control" required>
                    <option value="">Selecione um cliente</option>
                    <?php foreach ($clientes as $cli): ?>
                        <option value="<?php echo (int)$cli['id']; ?>"
                            <?php echo ($cli['id'] == $os['cliente_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cli['nome'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Título</label>
            <div class="col-sm-10">
                <input type="text" name="titulo" class="form-control" required
                       value="<?php echo htmlspecialchars($os['titulo'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Descrição</label>
            <div class="col-sm-10">
                <textarea name="descricao" class="form-control" rows="3" required><?php
                    echo htmlspecialchars($os['descricao'], ENT_QUOTES, 'UTF-8');
                ?></textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Status</label>
            <div class="col-sm-4">
                <select name="status" class="form-control">
                    <option value="aberta"       <?php echo ($os['status'] === 'aberta') ? 'selected' : ''; ?>>Aberta</option>
                    <option value="em_andamento" <?php echo ($os['status'] === 'em_andamento') ? 'selected' : ''; ?>>Em andamento</option>
                    <option value="concluida"    <?php echo ($os['status'] === 'concluida') ? 'selected' : ''; ?>>Concluída</option>
                    <option value="cancelada"    <?php echo ($os['status'] === 'cancelada') ? 'selected' : ''; ?>>Cancelada</option>
                </select>
            </div>

            <label class="col-sm-2 control-label">Data prevista</label>
            <div class="col-sm-4">
                <input type="datetime-local" name="data_prevista" class="form-control"
                       value="<?php echo formatDateTimeLocal($os['data_prevista']); ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Data conclusão</label>
            <div class="col-sm-4">
                <input type="datetime-local" name="data_conclusao" class="form-control"
                       value="<?php echo formatDateTimeLocal($os['data_conclusao']); ?>">
            </div>

            <label class="col-sm-2 control-label">Valor previsto (R$)</label>
            <div class="col-sm-4">
                <input type="number" step="0.01" min="0" name="valor_previsto" class="form-control"
                       value="<?php echo number_format($os['valor_previsto'], 2, '.', ''); ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Valor final (R$)</label>
            <div class="col-sm-4">
                <input type="number" step="0.01" min="0" name="valor_final" class="form-control"
                       value="<?php echo number_format($os['valor_final'], 2, '.', ''); ?>">
            </div>

            <label class="col-sm-2 control-label">Observações</label>
            <div class="col-sm-4">
                <textarea name="observacoes" class="form-control" rows="3"><?php
                    echo htmlspecialchars($os['observacoes'], ENT_QUOTES, 'UTF-8');
                ?></textarea>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <a href="index.php" class="btn btn-default">Voltar</a>
                <button type="submit" class="btn btn-primary">Salvar alterações</button>
            </div>
        </div>
    </form>
</div>
</body>
</html>
