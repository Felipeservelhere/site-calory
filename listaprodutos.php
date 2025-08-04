<?php 
$pageTitle = "Produtos - Calory Sistemas";

// Conexão com o banco de dados
$conn = new mysqli("177.107.115.204", "root", "@@rOOt@cAlOry@1967@@", "calory_felipe", "30590");
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Processar filtros
$order = isset($_GET['order']) ? $_GET['order'] : 'newest';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Construir a query SQL
$sql = "SELECT produto, descricaosite, Vrvenda, codigo, categoriasite FROM tbproduto_site";

// Aplicar filtro de categoria se existir
if (!empty($category)) {
    $sql .= " WHERE categoriasite = '" . $conn->real_escape_string($category) . "'";
}

// Aplicar ordenação
switch ($order) {
    case 'oldest':
        $sql .= " ORDER BY codigo ASC";
        break;
    case 'lowest':
        $sql .= " ORDER BY Vrvenda ASC";
        break;
    case 'highest':
        $sql .= " ORDER BY Vrvenda DESC";
        break;
    default:
        $sql .= " ORDER BY codigo DESC"; // Mais novo primeiro (padrão)
}

$sql .= " LIMIT 9";
$res = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
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
            font-family: 'Segoe UI', sans-serif;
            background-color: #fff;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* HEADER */
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

        /* MAIN CONTENT */
        .main-content {
            flex: 1;
            padding: 20px 40px;
        }

        .breadcrumb {
            margin-bottom: 20px;
            font-size: 16px;
            color: var(--text-gray);
        }

        .breadcrumb a {
            color: var(--text-gray);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .titulo {
            font-size: 48px;
            font-weight: bold;
            margin: 0 0 20px 0;
        }

        .container {
            display: flex;
            gap: 40px;
        }

        /* SIDEBAR */
        .sidebar {
            width: 220px;
        }

        .sidebar h3 {
            font-size: 24px;
            margin-bottom: 15px;
        }

        .sidebar select {
            width: 100%;
            padding: 8px;
            font-size: 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            cursor: pointer;
        }

        .categorias {
            margin-top: 30px;
        }

        .categorias h4 {
            font-size: 22px;
            margin-bottom: 10px;
        }

        .categoria-item {
            margin: 5px 0;
            color: var(--text-gray);
            cursor: pointer;
            padding: 5px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .categoria-item:hover, .categoria-item.active {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        /* PRODUCTS */
        .content {
            flex: 1;
        }

        .top-bar {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .search {
            position: relative;
        }

        .search input {
            padding: 10px 15px;
            padding-right: 35px;
            width: 250px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 14px;
        }

        .search img {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            height: 18px;
            cursor: pointer;
        }

        .produtos {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
        }

        .produto {
            width: calc(33.33% - 20px);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 15px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
            box-sizing: border-box;
        }

        .produto:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }

        .produto img {
            height: 150px;
            width: 100%;
            object-fit: contain;
            transition: 0.3s;
            margin-bottom: 15px;
        }

        .produto:hover img {
            transform: scale(1.05);
        }

        .produto h2 {
            font-size: 16px;
            margin: 10px 0 5px;
            color: var(--text-dark);
        }

        .produto p {
            margin: 5px 0;
            color: var(--text-gray);
            font-size: 14px;
        }

        .produto .preco {
            font-weight: bold;
            color: var(--green);
            font-size: 18px;
            margin-top: 10px;
        }

        /* FOOTER */
        footer {
            background-color: #3277d6;
            color: white;
            padding: 30px 20px;
            margin-top: 60px;
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

        /* RESPONSIVIDADE */
        @media (max-width: 992px) {
            .produto {
                width: calc(50% - 15px);
            }
        }

        @media (max-width: 768px) {
            .header-nav {
                flex-direction: column;
                gap: 20px;
            }

            .menu-central {
                flex-direction: column;
                align-items: center;
            }

            .container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
            }

            .footer-column {
                min-width: 150px;
            }
        }

        @media (max-width: 576px) {
            .main-content {
                padding: 20px;
            }

            .produto {
                width: 100%;
            }

            .search input {
                width: 100%;
            }

            .footer-column {
                min-width: 100%;
                text-align: center;
            }

            .social-icons {
                justify-content: center;
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

<div class="main-content">
    <p class="breadcrumb">
        <a href="index.php">Início</a> | <a href="listaprodutos.php">Produtos</a>
    </p>
    <h1 class="titulo">PRODUTOS</h1>

    <div class="container">
        <aside class="sidebar">
            <h3>ORDENAR POR</h3>
            <select onchange="location = this.value;">
                <option value="listaprodutos.php?order=newest<?php echo !empty($category) ? '&category='.$category : ''; ?>" <?php echo ($order == 'newest') ? 'selected' : ''; ?>>Mais novo ao mais antigo</option>
                <option value="listaprodutos.php?order=oldest<?php echo !empty($category) ? '&category='.$category : ''; ?>" <?php echo ($order == 'oldest') ? 'selected' : ''; ?>>Mais antigo ao mais novo</option>
                <option value="listaprodutos.php?order=lowest<?php echo !empty($category) ? '&category='.$category : ''; ?>" <?php echo ($order == 'lowest') ? 'selected' : ''; ?>>Menor preço</option>
                <option value="listaprodutos.php?order=highest<?php echo !empty($category) ? '&category='.$category : ''; ?>" <?php echo ($order == 'highest') ? 'selected' : ''; ?>>Maior preço</option>
            </select>

            <div class="categorias">
                <h4>CATEGORIAS</h4>
                <div class="categoria-item <?php echo (empty($category)) ? 'active' : ''; ?>" onclick="location.href='listaprodutos.php'">Todas as categorias</div>
                <div class="categoria-item <?php echo ($category == 'computadores') ? 'active' : ''; ?>" onclick="location.href='listaprodutos.php?category=computadores'">Computadores</div>
                <div class="categoria-item <?php echo ($category == 'impressora') ? 'active' : ''; ?>" onclick="location.href='listaprodutos.php?category=impressora'">Materiais de impressora</div>
                <div class="categoria-item <?php echo ($category == 'perifericos') ? 'active' : ''; ?>" onclick="location.href='listaprodutos.php?category=perifericos'">Periféricos</div>
            </div>
        </aside>

        <main class="content">
            <div class="top-bar">
                <div class="search">
                    <input type="text" placeholder="Buscar produtos..." />
                    <img src="img/Lupa preta.png" alt="Buscar" />
                </div>
            </div>

            <div class="produtos">
                <?php
                if ($res->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) {
                        $codigo = urlencode($row["codigo"]);
                        echo "<div class='produto' onclick=\"location.href='produto-open.php?codigo={$codigo}'\">";
                        echo "<img src='imagem.php?codigo={$codigo}' alt='" . htmlspecialchars($row["produto"]) . "' />";
                        echo "<h2>" . htmlspecialchars($row["produto"]) . "</h2>";
                        echo "<p>" . htmlspecialchars($row["descricaosite"]) . "</p>";
                        echo "<p class='preco'>R$ " . number_format($row["Vrvenda"], 2, ',', '.') . "</p>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Nenhum produto encontrado.</p>";
                }
                $conn->close();
                ?>
            </div>
        </main>
    </div>
</div>

<footer>
    <img src="img/logobranco.png" alt="Logo Calory">
    <p>&copy; <?php echo date("Y"); ?> Calory Sistemas. Todos os direitos reservados.</p>
    <p><a href="index.php">Início</a> | <a href="listaprodutos.php">Produtos</a> | <a href="contato.php">Contato</a></p>
</footer>

</body>
</html>