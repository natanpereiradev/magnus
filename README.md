# API de Gerenciamento de Usuários

## Introdução
Esta API foi desenvolvida para gerenciar usuários em um banco de dados MySQL. As funcionalidades incluem cadastrar, consultar, atualizar e excluir usuários. A API foi construída com ênfase na segurança, integrando proteções contra injeções SQL e validações robustas de dados.

## Funcionalidades
- Cadastrar novos usuários
- Consultar usuários com filtros opcionais
- Atualizar informações de usuários existentes
- Excluir usuários do banco de dados

## Endpoints Disponíveis
### `cadastrarUsuario`
**Método:** POST
**Descrição:** Cadastra um novo usuário no banco de dados.

**Parâmetros (Body em JSON):**
- `first_name`: Nome do usuário (string, obrigatório).
- `last_name`: Sobrenome do usuário (string, obrigatório).
- `email`: Email do usuário (string, obrigatório, deve ser único).
- `phone`: Telefone do usuário (string, opcional).
- `password`: Senha do usuário (string, obrigatório).

**Exemplo no Postman:**
1. **Método:** POST
2. **URL:** `http://localhost:8000/cadastrarUsuario`
3. **Header:**
    - Content-Type: `application/json`
4. **Body (Raw, JSON):**
   ```json
   {
     "first_name": "John",
     "last_name": "Doe",
     "email": "john.doe@example.com",
     "phone": "123456789",
     "password": "senha123"
   }

**Resposta de Sucesso:**
```json
{ "message": "Usuário cadastrado com sucesso" }
```
**Resposta de Sucesso:**
```json
{ "error": "Descrição do erro" }
```

### `consultarUsuarios`
**Método:** GET
**Descrição:** Retorna uma lista de usuários com base em filtros opcionais de nome e email.

**Parâmetros (Query Params):**

- `filtroNome`: Filtro opcional pelo nome do usuário (string).
- `filtroEmail`: Filtro opcional pelo email do usuário (string).

**Exemplo no Postman:**
1. **Método:** GET
2. **URL:** `http://localhost:8000/?metodo=consultarUsuarios&filtroNome=sdad`

**Resposta do sucesso:**

```json
[
  { 
    "id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "email": "john.doe@example.com",
    "phone": "123456789"
  },
  ...
]

```

**Resposta de Erro:**
```json
{ "error": "Descrição do erro" }
```

### `atualizarUsuario`
**Descrição:** Atualiza as informações de um usuário existente.

**Parâmetros (Body em JSON):**
- `id`: ID do usuário a ser atualizado (int, obrigatório).
- `first_name`: Nome atualizado do usuário (string, obrigatório).
- `last_name`: Sobrenome atualizado do usuário (string, obrigatório).
- `email`: Email atualizado do usuário (string, obrigatório).
- `phone`: Telefone atualizado do usuário (string, opcional).

**Exemplo no Postman:**
1. **Método:** POST
2. **URL:** `http://localhost:8000/atualizarUsuario`
3. **Header:**
    - Content-Type: `application/json`
4. **Body (Raw, JSON):**
   ```json
    {
        "id": 1,
        "first_name": "Jane",
        "last_name": "Doe",
        "email": "jane.doe@example.com",
        "phone": "987654321"
    }


**Resposta do sucesso:**

```json
{ "message": "Usuário atualizado com sucesso" }
```

**Resposta de Erro:**
```json
{ "error": "Descrição do erro" }
```

### `excluirUsuario`
**Descrição:** Exclui um usuário do banco de dados.

- `id`: ID do usuário a ser excluído (int, obrigatório).

1. **Método:** POST
2. **URL:** `http://localhost:8000/excluirUsuario`
3. **Header:**
    - Content-Type: `application/json`
4. **Body (Raw, JSON):**
   ```json
    {
    "id": 1
    }


**Resposta do sucesso:**

```json
{ "message": "Usuário excluído com sucesso" }
```

**Resposta de Erro:**
```json
{ "error": "Descrição do erro" }
```

## Segurança
A segurança é um aspecto central desta API, implementada através de funções de validação e sanitização que protegem contra injeções SQL e garantem a integridade dos dados.

### Proteção contra Injeções SQL
- A função isSafeInput($input) verifica se o input contém padrões que possam indicar uma tentativa de injeção SQL, como palavras-chave (SELECT, INSERT, etc.). 
- Se algum padrão perigoso for detectado, a função retorna false, bloqueando o processamento desse input.

### Validação de Email
- A função isValidEmail($email) valida se o formato do email fornecido é válido, utilizando o filtro FILTER_VALIDATE_EMAIL do PHP.

### Verificação de Campos Vazios
- A função isNotEmpty($value) garante que os campos não estejam vazios, após remover espaços em branco.

### Garantia de Codificação UTF-8
- A função ensureUtf8($string) assegura que o texto seja codificado em UTF-8, convertendo-o se necessário, para evitar problemas de codificação que possam comprometer a integridade dos dados.

## Conexão com o Banco de Dados
- A função dbConnect() realiza a conexão com o banco de dados MySQL utilizando as credenciais configuradas nas variáveis de ambiente.

- Nota: Para a correta utilização da API, certifique-se de que as variáveis de ambiente DB_HOST, DB_USER, DB_PASS, e DB_NAME estão configuradas corretamente no ambiente de execução.
