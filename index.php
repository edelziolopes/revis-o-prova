<?php
class Database {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli(
            "srv1664.hstgr.io", 
            "u344105464_aluno", 
            "Ind-2025", 
            "u344105464_chat"
        );
        if ($this->conn->connect_error) {
            die("Erro de conexão: " . $this->conn->connect_error);
        }
    }

    public function inserir($nome, $mensagem) {
        $stmt = $this->conn->prepare("
            INSERT INTO tb_mensagem (nome, mensagem) 
            VALUES (?, ?)
        ");
        $stmt->bind_param("ss", $nome, $mensagem);
        $stmt->execute();
        $stmt->close();
    }

    public function listar() {
        $result = $this->conn->query("SELECT * FROM tb_mensagem ORDER BY id DESC");
        $mensagens = [];
        while ($row = $result->fetch_assoc()) {
            $mensagens[] = $row;
        }
        return $mensagens;
    }

    public function excluir($id) {
        $stmt = $this->conn->prepare("DELETE FROM tb_mensagem WHERE id = ?");
        $stmt->bind_param("i", $id);    
        $stmt->execute();   
        $stmt->close();
    }

    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
            $this->conn = null;
        }
    }

    public function __destruct() {
        $this->closeConnection();
    }
}

// Instância da classe
$db = new Database();

// Inserção
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? null;
    $mensagem = $_POST['mensagem'] ?? null;
    if ($nome && $mensagem) {
        $db->inserir($nome, $mensagem);
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Exclusão
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    if ($id > 0) {
        $db->excluir($id);
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Listar
$mensagens = $db->listar();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Mensagens</title>
</head>
<body>
    <h2>Enviar Mensagem</h2>
    <form method="post">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" required>
        <label for="mensagem">Mensagem:</label>
        <input type="text" name="mensagem" required>
        <button type="submit">Enviar</button>
        <button type="reset">Limpar</button>
    </form>

    <h2>Mensagens Recebidas</h2>
    <?php foreach ($mensagens as $mensagem): ?>
        <p>
            <strong><?= htmlspecialchars($mensagem['nome']) ?>:</strong>
            <?= htmlspecialchars($mensagem['mensagem']) ?>
            <a href="?id=<?= $mensagem['id'] ?>" onclick="return confirm('Deseja excluir?')">Excluir</a>
        </p>
    <?php endforeach; ?>
</body>
</html>
