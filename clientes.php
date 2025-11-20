<?php
require_once 'conexao.php';

// Inserir novo cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome      = trim($_POST['nome'] ?? '');
    $telefone  = trim($_POST['telefone'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $documento = trim($_POST['documento'] ?? '');
    $endereco  = trim($_POST['endereco'] ?? '');

    if ($nome !== '') {
        $sql = "INSERT INTO clientes (nome, telefone, email, documento, endereco)
                VALUES (:nome, :telefone, :email, :documento, :endereco)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':telefone', $telefone);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':documento', $documento);
        $stmt->bindValue(':endereco', $endereco);
        $stmt->execute();
    }

    header('Location: clientes.php');
    exit;
}

// Excluir cliente (se não tiver OS vinculada)
if (isset($_GET['del'])) {
    $idDel = (int)$_GET['del'];

    if ($idDel > 0) {
        // Verifica se tem OS
        $sqlCheck = "SELECT COUNT(*) AS total FROM ordens_servico WHERE cliente_id = :id";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->bindValue(':id', $idDel, PDO::PARAM_INT);
        $stmtCheck->execute();
        $rowCheck = $stmtCheck->fetch();

        if ($rowCheck && $rowCheck['total'] == 0) {
            $sqlDel = "DELETE FROM clientes WHERE id = :id";
            $stmtDel = $pdo->prepare($sqlDel);
            $stmtDel->bindValue(':id', $idDel, PDO::PARAM_INT);
            $stmtDel->execute();
        } else {
            // Você pode tratar aqui uma mensagem de erro (cliente com OS vinculada)
            // Por simplicidade, só ignora a exclusão
        }
    }

    header('Location: clientes.php');
    exit;
}

// Listar clientes
$sqlCli = "SELECT * FROM clientes ORDER BY nome";
$stmtCli = $pdo->prepare($sqlCli);
$stmtCli->execute();
$clientes = $stmtCli->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Clientes - Gestão de OS</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1 class="alert alert-info text-center">Clientes</h1>

    <a href="index.php" class="btn btn-default">Voltar para Ordens de Serviço</a>

    <hr>

    <h3>Novo Cliente</h3>
    <form method="post" class="form-horizontal">

        <div class="form-group">
            <label class="col-sm-2 control-label">Nome*</label>
            <div class="col-sm-10">
                <input type="text" name="nome" class="form-control" required placeholder="Nome do cliente">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Telefone</label>
            <div class="col-sm-4">
                <input type="text" name="telefone" class="form-control" placeholder="(xx) xxxxx-xxxx">
            </div>

            <label class="col-sm-2 control-label">E-mail</label>
            <div class="col-sm-4">
                <input type="email" name="email" class="form-control" placeholder="email@exemplo.com">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Documento</label>
            <div class="col-sm-4">
                <input type="text" name="documento" class="form-control" placeholder="CPF/CNPJ (opcional)">
            </div>

            <label class="col-sm-2 control-label">Endereço</label>
            <div class="col-sm-4">
                <input type="text" name="endereco" class="form-control" placeholder="Rua, número, bairro...">
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-success">Cadastrar Cliente</button>
            </div>
        </div>

    </form>

    <hr>

    <h3>Lista de Clientes</h3>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>#</th>
            <th>Nome</th>
            <th>Telefone</th>
            <th>E-mail</th>
            <th>Documento</th>
            <th>Endereço</th>
            <th>Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($clientes as $cli): ?>
            <tr>
                <td><?php echo (int)$cli['id']; ?></td>
                <td><?php echo htmlspecialchars($cli['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($cli['telefone'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($cli['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($cli['documento'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($cli['endereco'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <a href="clientes.php?del=<?php echo (int)$cli['id']; ?>"
                       class="btn btn-xs btn-danger"
                       onclick="return confirm('Tem certeza que deseja excluir este cliente?');">
                        Excluir
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>
</body>
</html>
