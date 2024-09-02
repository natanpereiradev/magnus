<?php

include 'sqli_block.php';  // Incluir a função de segurança
include 'funcoes.php';  // Incluir o arquivo com as funções para cada método

header('Content-Type: application/json');

// Captura o endpoint da URL
$requestUri = $_SERVER['REQUEST_URI'];

// Remove o caminho da base da URL
$endpoint = trim(parse_url($requestUri, PHP_URL_PATH), '/');

// Captura os parâmetros de consulta (GET)
$queryParams = [];
parse_str($_SERVER['QUERY_STRING'], $queryParams);

// Verifica se a requisição é um POST ou GET
$rawPostData = file_get_contents('php://input');
$postData = json_decode($rawPostData, true);

if ($postData === null && json_last_error() !== JSON_ERROR_NONE) {
    // Se a decodificação JSON falhar, continua a processar como GET
    $postData = $_GET;
}

// Verifica se os inputs são seguros
foreach ($postData as $key => $value) {
    if (!isSafeInput($value)) {
        echo json_encode(['error' => 'Input inseguro detectado']);
        exit;
    }
}

// Captura o método a partir dos parâmetros de consulta ou POST
$metodo = $queryParams['metodo'] ?? $postData['metodo'] ?? $endpoint;

// Extrai variáveis a partir do array de parâmetros (POST ou GET)
foreach ($postData as $key => $value) {
    $$key = $value;
}

try {
    switch ($metodo) {
        case 'cadastrarUsuario':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                cadastrarUsuario($first_name, $last_name, $email, $phone, $password);
            } else {
                throw new Exception("Método inválido para cadastrarUsuario");
            }
            break;
        
        case 'consultarUsuarios':
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                echo consultarUsuarios($filtroNome, $filtroEmail);
            } else {
                throw new Exception("Método inválido para consultarUsuarios");
            }
            break;
        
        case 'atualizarUsuario':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                atualizarUsuario($id, $first_name, $last_name, $email, $phone);
            } else {
                throw new Exception("Método inválido para atualizarUsuario");
            }
            break;

        case 'excluirUsuario':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                excluirUsuario($id);
            } else {
                throw new Exception("Método inválido para excluirUsuario");
            }
            break;

        default:
            throw new Exception("Método desconhecido: $metodo");
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}



// include 'sqli_block.php';  // Incluir a função de segurança
// include 'funcoes.php';  // Incluir o arquivo com as funções para cada método

// header('Content-Type: application/json');

// // Verifica se a requisição é um POST ou GET
// $rawPostData = file_get_contents('php://input');
// $postData = json_decode($rawPostData, true);

// if ($postData === null && json_last_error() !== JSON_ERROR_NONE) {
//     // Se a decodificação JSON falhar, continua a processar como GET
//     $postData = $_GET;
// }

// // Verifica se os inputs são seguros
// foreach ($postData as $key => $value) {
//     if (!isSafeInput($value)) {
//         echo json_encode(['error' => 'Input inseguro detectado']);
//         exit;
//     }
// }

// // Extrai variáveis a partir do array
// foreach ($postData as $key => $value) {
//     $$key = $value;
// }

// try {
//     switch ($metodo) {
//         case 'cadastrarUsuario':
//             cadastrarUsuario($first_name, $last_name, $email, $phone, $password);
//             break;
        
//         case 'consultarUsuarios':
//             echo consultarUsuarios($filtroNome, $filtroEmail);
//             break;
        
//         case 'atualizarUsuario':
//             atualizarUsuario($id, $first_name, $last_name, $email, $phone);
//             break;

//         case 'excluirUsuario':
//             excluirUsuario($id);
//             break;

//         default:
//             throw new Exception("Método desconhecido: $metodo");
//     }	
// } catch (Exception $e) {
//     echo json_encode(['error' => $e->getMessage()]);
// }
