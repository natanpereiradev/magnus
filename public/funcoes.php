<?php

include_once 'sqli_block.php';

function dbConnect() {
    $servername = getenv('DB_HOST');
    $username = getenv('DB_USER');
    $password = getenv('DB_PASS');
    $dbname = getenv('DB_NAME');

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    return $conn;
}

function registrarLog($user_id = null, $action) {
    $conn = dbConnect();
    
    // Obter o IP do usuário
    $user_ip = $_SERVER['REMOTE_ADDR'];
    
    $stmt = $conn->prepare("INSERT INTO logs (user_id, user_ip, action) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $user_ip, $action);

    if (!$stmt->execute()) {
        // Se houver um erro ao inserir o log, você pode tratá-lo aqui
        error_log("Erro ao registrar log: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}

function cadastrarUsuario($first_name, $last_name, $email, $phone, $password) {
    // Validar inputs
    if (!isNotEmpty($first_name) || !isNotEmpty($last_name) || !isValidEmail($email) || !isNotEmpty($password)) {
        echo json_encode(['error' => 'Dados inválidos']);
        return;
    }

    // Verificar se o input é seguro
    if (!isSafeInput($first_name) || !isSafeInput($last_name) || !isSafeInput($email) || !isSafeInput($phone) || !isSafeInput($password)) {
        echo json_encode(['error' => 'Input inseguro detectado']);
        return;
    }

    // Criptografar a senha
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Conectar ao banco e preparar a instrução SQL
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        // Email já em uso
        echo json_encode(['error' => 'O email fornecido já está em uso']);
        $stmt->close();
        $conn->close();
        return;
    }
    
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone, password_hash) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $phone, $password_hash);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Usuário cadastrado com sucesso']);
         // Registrar o log
         registrarLog(null, "Usuário cadastrado: $first_name $last_name");
    } else {
        echo json_encode(['error' => 'Erro ao cadastrar usuário']);
    }

    $stmt->close();
    $conn->close();
}

function consultarUsuarios($filtroNome = '', $filtroEmail = '') {
    // Garantir que os filtros são seguros
    if (!isSafeInput($filtroNome) || !isSafeInput($filtroEmail)) {
        return json_encode(['error' => 'Input inseguro detectado']);
    }

    $conn = dbConnect();

    $sql = "SELECT * FROM users WHERE 1=1";
    if ($filtroNome) {
        $sql .= " AND first_name LIKE ?";
        $filtroNome = "%$filtroNome%";
    }
    if ($filtroEmail) {
        $sql .= " AND email LIKE ?";
        $filtroEmail = "%$filtroEmail%";
    }

    $stmt = $conn->prepare($sql);
    
    if ($filtroNome && $filtroEmail) {
        $stmt->bind_param("ss", $filtroNome, $filtroEmail);
    } elseif ($filtroNome) {
        $stmt->bind_param("s", $filtroNome);
    } elseif ($filtroEmail) {
        $stmt->bind_param("s", $filtroEmail);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    $stmt->close();
    $conn->close();

    return json_encode($users);
}


function atualizarUsuario($id, $first_name, $last_name, $email, $phone) {
    // Validar inputs
    if (!isNotEmpty($first_name) || !isNotEmpty($last_name) || !isValidEmail($email)) {
        echo json_encode(['error' => 'Dados inválidos']);
        return;
    }

    // Verificar se o input é seguro
    if (!isSafeInput($first_name) || !isSafeInput($last_name) || !isSafeInput($email) || !isSafeInput($phone)) {
        echo json_encode(['error' => 'Input inseguro detectado']);
        return;
    }

    $conn = dbConnect();

    // Verificar se o usuário existe
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows === 0) {
        echo json_encode(['error' => "Usuário com ID $id não encontrado"]);
        $stmt->close();
        $conn->close();
        return;
    }
    
    $stmt->close();

    // Atualizar o usuário
    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $first_name, $last_name, $email, $phone, $id);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Usuário atualizado com sucesso']);
         // Registrar o log
         registrarLog($id, "Usuário atualizado: $first_name $last_name");
    } else {
        echo json_encode(['error' => 'Erro ao atualizar usuário']);
    }

    $stmt->close();
    $conn->close();
}


function excluirUsuario($id) {
    $conn = dbConnect();

    // Verificar se o usuário existe
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows === 0) {
        echo json_encode(['error' => "Usuário com ID $id não encontrado"]);
        $stmt->close();
        $conn->close();
        return;
    }
    
    $stmt->close();

    // Excluir o usuário
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Usuário excluído com sucesso']);
        // Registrar o log
        registrarLog(0, "Usuário excluído: $id");
    } else {
        echo json_encode(['error' => 'Erro ao excluir usuário']);
    }

    $stmt->close();
    $conn->close();
}