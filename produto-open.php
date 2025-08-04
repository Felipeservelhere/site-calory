<?php
$servername = "177.107.115.204";
$username = "root";
$password = "@@rOOt@cAlOry@1967@@";
$dbname = "calory_felipe";
$port = 30590;


$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$codigo = isset($_GET['codigo']) ? $_GET['codigo'] : '';

// Consulta SQL para obter os dados do produto
$sql = "SELECT produto, descricaosite, Vrvenda, codigo, foto, fotob2, fotob3, codsite 
        FROM tbproduto_site 
        WHERE codigo = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Erro na preparação da consulta: " . $conn->error);
}

$stmt->bind_param("s", $codigo);
if (!$stmt->execute()) {
    die("Erro na execução da consulta: " . $stmt->error);
}

$result = $stmt->get_result();
$produto = $result->fetch_assoc();

$conn->close();

// Processamento das imagens
$imagens = array();
if (!empty($produto['foto'])) {
    $imagens[] = "imagem.php?codigo=" . urlencode($produto['codigo']);
}
if (!empty($produto['fotob2'])) {
    $imagens[] = "imagem.php?codigo=" . urlencode($produto['codigo']) . "&campo=fotob2";
}
if (!empty($produto['fotob3'])) {
    $imagens[] = "imagem.php?codigo=" . urlencode($produto['codigo']) . "&campo=fotob3";
}

// Imagem padrão se não houver imagens
if (empty($imagens)) {
    $imagens[] = "img/sem-imagem.jpg";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($produto['produto'] ?? 'Produto'); ?> - Calory</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap">
    <style>
        :root {
            --primary: #3277d6;
            --text-dark: #000;
            --text-gray: #666;
            --green: #27ae60;
            --border: #ddd;
            --radius: 10px;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: #fff;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            color: #333;
        }

        header {
            background-color: var(--primary);
            padding: 80px 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            color: white;
            position: relative;
        }

        .logo-header {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
        }

        .logo-header img {
            height: 50px;
        }

        .header-nav {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }

        .menu-central {
            display: flex;
            justify-content: center;
            gap: 30px;
            font-size: 18px;
        }

        .menu-central a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            padding: 5px 10px;
            transition: all 0.3s;
        }

        .menu-central a:hover {
            background-color: rgba(255,255,255,0.2);
            border-radius: 5px;
        }

        .icons {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .search-header {
            position: relative;
        }

        .search-header input {
            padding: 8px 15px;
            padding-right: 35px;
            width: 200px;
            border: none;
            border-radius: var(--radius);
            font-size: 14px;
        }

        .search-header img {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            height: 18px;
        }

        .icons img {
            height: 30px;
            transition: transform 0.3s;
        }

        .icons img:hover {
            transform: scale(1.1);
        }

        .container {
            display: flex;
            max-width: 1100px;
            margin: 30px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            gap: 30px;
        }

        .imagens {
            flex: 1;
            display: flex;
            gap: 20px;
            max-width: 600px;
        }

        .miniaturas {
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-width: 100px;
        }

        .miniaturas img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border: 2px solid #ccc;
            border-radius: 6px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .miniaturas img:hover {
            transform: scale(1.05);
            border-color: var(--primary);
        }

        .principal-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f9f9f9;
            border-radius: 10px;
            border: 1px solid #ddd;
            height: 500px;
            overflow: hidden;
        }

        .principal-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .detalhes {
            flex: 1;
            max-width: 400px;
        }

        .codigo-produto {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        .detalhes h1 {
            margin: 0 0 15px 0;
            font-size: 28px;
            color: #111;
            font-weight: 600;
        }

        .descricao-produto {
            margin: 20px 0;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 8px;
            line-height: 1.6;
            font-size: 15px;
        }

        .preco {
            color: var(--green);
            font-size: 28px;
            font-weight: bold;
            margin: 25px 0;
        }

        .botao-comprar {
            display: block;
            width: 100%;
            padding: 15px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .botao-comprar:hover {
            background-color: #2a65b8;
        }

        footer {
            background-color: #3277d6;
            color: white;
            padding: 30px 20px;
            margin-top: auto;
            text-align: center;
        }

        footer img {
            height: 60px;
            margin-bottom: 10px;
        }

        footer p {
            margin: 5px 0;
            color: #ccc;
        }

        footer a {
            color: #ffffffff;
            text-decoration: none;
            margin: 0 10px;
        }

        footer a:hover {
            text-decoration: underline;
        }

        .mensagem-erro {
            text-align: center;
            margin: 50px 0;
            font-size: 20px;
            color: #d9534f;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                padding: 20px;
            }
            
            .imagens {
                flex-direction: column-reverse;
            }
            
            .miniaturas {
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .principal-container {
                height: 350px;
            }
            
            .detalhes {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="logo-header">
        <img src="img/logobranco.png" alt="Logo Calory" />
    </div>

    <div class="header-nav">
        <nav class="menu-central">
            <a href="index.php">Início</a>
            <a href="listaprodutos.php">Produtos</a>
        </nav>

        <div class="icons">
            <div class="search-header">
                <input type="text" placeholder="Buscar..." />
                <img src="img/Lupa.png" alt="Buscar" />
            </div>
            <a href="login.php"><img src="img/Login.png" alt="Login" /></a>
            <a href="carrinho.php"><img src="img/Carrinho.png" alt="Carrinho" /></a>
        </div>
    </div>
</header>

<?php if ($produto): ?>
    <div class="container">
        <div class="imagens">
            <?php if (count($imagens) > 1): ?>
                <div class="miniaturas">
                    <?php foreach ($imagens as $img): ?>
                        <img src="<?php echo $img; ?>" onclick="document.getElementById('imagem-principal').src = this.src">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="principal-container">
                <img id="imagem-principal" src="<?php echo $imagens[0]; ?>" alt="<?php echo htmlspecialchars($produto['produto']); ?>">
            </div>
        </div>
        
        <div class="detalhes">
            <div class="codigo-produto">COD: <?php echo htmlspecialchars($produto['codsite'] ?? ''); ?></div>
            <h1><?php echo htmlspecialchars($produto['produto']); ?></h1>
            
            <?php if (!empty($produto['descricaosite'])): ?>
                <div class="descricao-produto">
                    <?php echo nl2br(htmlspecialchars($produto['descricaosite'])); ?>
                </div>
            <?php endif; ?>
            
            <div class="preco">R$ <?php echo number_format($produto['Vrvenda'], 2, ',', '.'); ?></div>
            
            <button class="botao-comprar">Comprar</button>
        </div>
    </div>
<?php else: ?>
    <p class="mensagem-erro">Produto não encontrado.</p>
<?php endif; ?>

<footer>
    <img src="img/logobranco.png" alt="Logo Calory">
    <p>&copy; <?php echo date("Y"); ?> Calory Sistemas. Todos os direitos reservados.</p>
    <p>
        <a href="index.php">Início</a> | 
        <a href="listaprodutos.php">Produtos</a> | 
        <a href="contato.php">Contato</a>
    </p>
</footer>

</body>
</html>