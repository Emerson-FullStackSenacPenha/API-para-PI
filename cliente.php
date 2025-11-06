<?php
$API_BASE_URL = "http://localhost/API-para-PI/API_usuario.php";

function consumir_api($url, $method = 'GET', $data = null)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['status' => $status, 'body' => json_decode($response, true)];
}

$mensagem_status = "";
$status_class = "";
$usuarios = [];

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['acao'] === 'criar') {
    $dados = [
        "nome" => $_POST['nome'],
        "email" => $_POST['email'],
        "senha" => $_POST['senha'],
        "telefone" => $_POST['telefone']
    ];
    $res = consumir_api($API_BASE_URL, 'POST', $dados);
    $mensagem_status = $res['body']['message'] ?? 'Erro ao criar.';
    $status_class = $res['status'] == 201 ? "success" : "error";
}

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['acao'] === 'atualizar') {
    $dados = [
        "id" => $_POST['id'],
        "nome" => $_POST['nome'],
        "email" => $_POST['email'],
        "telefone" => $_POST['telefone'],
        "tipo_usuario" => $_POST['tipo_usuario']
    ];
    $res = consumir_api($API_BASE_URL, 'PUT', $dados);
    $mensagem_status = $res['body']['message'] ?? 'Erro ao atualizar.';
    $status_class = $res['status'] == 200 ? "success" : "error";
}

// DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['acao'] === 'deletar') {
    $dados = ["id" => $_POST['id']];
    $res = consumir_api($API_BASE_URL, 'DELETE', $dados);
    $mensagem_status = $res['body']['message'] ?? 'Erro ao deletar.';
    $status_class = $res['status'] == 200 ? "success" : "error";
}

// GET
$res_get = consumir_api($API_BASE_URL, 'GET');
if ($res_get['status'] == 200) {
    $usuarios = $res_get['body'];
} else {
    $mensagem_status = "Erro ao carregar usuários.";
    $status_class = "error";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Gerenciar Usuários - API</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        h1,
        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        .success {
            color: green;
            border: 1px solid green;
            padding: 10px;
            margin-bottom: 20px;
        }

        .error {
            color: red;
            border: 1px solid red;
            padding: 10px;
            margin-bottom: 20px;
        }

        form {
            margin-top: 10px;
        }

        input,
        button {
            padding: 8px;
            margin: 4px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Gerenciar Usuários</h1>

        <?php if ($mensagem_status): ?>
            <div class="<?= $status_class ?>"><?= htmlspecialchars($mensagem_status) ?></div>
        <?php endif; ?>

        <h2>Usuários</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Tipo</th>
                    <th>Cadastro</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($usuarios): foreach ($usuarios as $u): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td><?= htmlspecialchars($u['nome']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['telefone']) ?></td>
                            <td><?= htmlspecialchars($u['tipo_usuario']) ?></td>
                            <td><?= htmlspecialchars($u['data_cadastro']) ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="acao" value="deletar">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <button type="submit" onclick="return confirm('Deseja deletar este usuário?')">🗑️ Deletar</button>
                                </form>

                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="acao" value="atualizar">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <input type="text" name="nome" value="<?= htmlspecialchars($u['nome']) ?>" required>
                                    <input type="email" name="email" value="<?= htmlspecialchars($u['email']) ?>" required>
                                    <input type="text" name="telefone" value="<?= htmlspecialchars($u['telefone']) ?>">
                                    <input type="text" name="tipo_usuario" value="<?= htmlspecialchars($u['tipo_usuario']) ?>">
                                    <button type="submit">💾 Atualizar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="7">Nenhum usuário encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h2>Criar Novo Usuário</h2>
        <form method="POST">
            <input type="hidden" name="acao" value="criar">
            <input type="text" name="nome" placeholder="Nome" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <input type="text" name="telefone" placeholder="Telefone">
            <button type="submit">➕ Cadastrar</button>
        </form>
    </div>
</body>

</html>