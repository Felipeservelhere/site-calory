<?php
$paginaRetorno = isset($_GET['voltar']) ? $_GET['voltar'] : 'pesquisa.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Ler Código de Barras</title>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        #leitor {
            width: 100%;
            max-width: 400px;
            margin: 40px auto;
        }
        body {
            font-family: sans-serif;
            text-align: center;
            padding: 20px;
        }
        .btn-cancelar {
            margin-top: 20px;
            font-size: 20px;
            background-color: #ff3333;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
        }
        .btn-cancelar:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>
    <h1>Ler Código</h1>
    <div id="leitor"></div>

    <button class="btn-cancelar" onclick="window.location.href='<?php echo $paginaRetorno; ?>'">Cancelar</button>

    <script>
        const html5QrCode = new Html5Qrcode("leitor");

        Html5Qrcode.getCameras().then(cameras => {
            if (cameras && cameras.length) {
                html5QrCode.start(
                    { facingMode: "environment" }, 
                    {
                        fps: 10,
                        qrbox: 250
                    },
                    qrCodeMessage => {
                        html5QrCode.stop().then(() => {
                            window.location.href = "<?php echo $paginaRetorno; ?>?qrcode=" + encodeURIComponent(qrCodeMessage);
                        });
                    },
                    errorMessage => {
                        // Pode ignorar erros frequentes
                        console.warn("Leitura falhou:", errorMessage);
                    }
                ).catch(err => {
                    alert("Erro ao iniciar a câmera: " + err);
                });
            } else {
                alert("Nenhuma câmera detectada.");
            }
        }).catch(err => {
            alert("Erro ao acessar as câmeras: " + err);
        });
    </script>
</body>
</html>
