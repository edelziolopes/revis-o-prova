<?php
function conectar() {
    $mysqli = new mysqli(
        "srv1664.hstgr.io", 
        "u344105464_aluno", 
        "Ind-2025", 
        "u344105464_chat"
    );       
    return $mysqli;
}
function inserir($nome, $mensagem) {
    $mysqli = conectar();
    $stmt = $mysqli->prepare("
        INSERT INTO tb_mensagem 
        (nome, mensagem) 
        VALUES (?, ?)
    ");
    $stmt->bind_param("ss", $nome, $mensagem);
    $stmt->execute();
    $stmt->close();
    $mysqli->close();
}
function listar() {
    $mysqli = conectar();
    $sql = "SELECT * FROM tb_mensagem";
    $result = $mysqli->query($sql);
    $categorias = [];
    while ($row = $result->fetch_assoc()) {$categorias[] = $row;}
    $mysqli->close();
    return $categorias;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? null;
    $mensagem = $_POST['mensagem'] ?? null;
    if ($nome && $mensagem) {
        inserir($nome, $mensagem);
    } 
    header('Location: index.php');
}

$mensagens = listar();
?>


<form method="post">
    <label for="nome">nome:</label>
    <input type="text" name="nome">
    <label for="mensagem">mensagem:</label>
    <input type="text" name="mensagem">
    <button type="submit">Enviar</button>
    <button type="reset">Limpar</button>
</form>
<?php foreach ($mensagens as $mensagem): ?>
<p><b><?=$mensagem['nome'];?> -> </b>Mensagem: <?=$mensagem['mensagem'];?></p>
<?php endforeach; ?>
