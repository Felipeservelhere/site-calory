<?php
// Recebe o código do produto e o campo da imagem (padrão: 'foto')
$codigo = $_GET['codigo'] ?? '';
$campo = $_GET['campo'] ?? 'foto';

// Valida se o campo é permitido
$camposPermitidos = ['foto', 'fotob2', 'fotob3'];
if (!in_array($campo, $camposPermitidos)) {
    header("HTTP/1.1 400 Bad Request");
    exit("Campo inválido.");
}

// Conexão com o banco
$conn = new mysqli("177.107.115.204", "root", "@@rOOt@cAlOry@1967@@", "calory_felipe", "30590");
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Prepara e executa a consulta
$sql = "SELECT $campo FROM tbproduto_site WHERE codigo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $codigo);
$stmt->execute();
$stmt->store_result();

// Se encontrou algum registro
if ($stmt->num_rows > 0) {
    $stmt->bind_result($imagem);
    $stmt->fetch();

    if (!empty($imagem)) {
        $info = getimagesizefromstring($imagem);
        if ($info !== false) {
            header("Content-Type: " . $info['mime']);
            echo $imagem;
        } else {
            // Imagem inválida, mostra a padrão
            header("Content-Type: image/jpeg");
            readfile("imagens/sem-foto.jpg");
        }
    } else {
        // Imagem vazia, mostra a padrão
        header("Content-Type: image/jpeg");
        readfile("imagens/sem-foto.jpg");
    }
} else {
    // Produto não encontrado, mostra a padrão
    header("Content-Type: image/jpeg");
    readfile("imagens/sem-foto.jpg");
}

$stmt->close();
$conn->close();
?>
