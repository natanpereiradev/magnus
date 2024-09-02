<?php

include 'sqli_block.php';  // Incluir a função de segurança
include 'funcoes.php';  // Incluir o arquivo com as funções para cada método

header('Content-Type: application/json');

// Obtém os dados da requisição (POST ou GET)
$rawPostData = file_get_contents('php://input');
$postData = json_decode($rawPostData, true);

if ($postData === null && json_last_error() !== JSON_ERROR_NONE) {
    // Se a decodificação JSON falhar, trata como GET
    $postData = $_GET;
}

// Verifica se os inputs são seguros
foreach ($postData as $key => $value) {
    if (!isSafeInput($value)) {
        echo json_encode(['error' => 'Input inseguro detectado']);
        exit;
    }
}

// Extrai variáveis a partir do array $postData
foreach ($postData as $key => $value) {
    $$key = $value;
}

// Obtém o endpoint da URL
$requestUri = $_SERVER['REQUEST_URI'];
$endpoint = trim(parse_url($requestUri, PHP_URL_PATH), '/');

// Roteamento baseado no endpoint da URL
try {
    switch ($endpoint) {
        case 'cadastrarUsuario':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                cadastrarUsuario($first_name, $last_name, $email, $phone, $password);
            } else {
                throw new Exception('Método HTTP não permitido para cadastrarUsuario');
            }
            break;
        
        case 'consultarUsuarios':
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                echo consultarUsuarios($filtroNome, $filtroEmail);
            } else {
                throw new Exception('Método HTTP não permitido para consultarUsuarios');
            }
            break;
        
        case 'atualizarUsuario':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                atualizarUsuario($id, $first_name, $last_name, $email, $phone);
            } else {
                throw new Exception('Método HTTP não permitido para atualizarUsuario');
            }
            break;

        case 'excluirUsuario':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                excluirUsuario($id);
            } else {
                throw new Exception('Método HTTP não permitido para excluirUsuario');
            }
            break;

        default:
            throw new Exception("Endpoint desconhecido: $endpoint");
    }   
} catch (Exception $e) {
    // Retorna a mensagem de erro em formato JSON
    echo json_encode(['error' => $e->getMessage()]);
}
