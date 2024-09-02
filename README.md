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
     "first_name": "Natan",
     "last_name": "Borges",
     "email": "natanbp@live.com",
     "phone": "44997731674",
     "password": "senhaTeste"
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
2. **URL:** `http://localhost:8000/?metodo=consultarUsuarios&filtroNome=Natan`

**Resposta do sucesso:**

```json
[
  { 
    "id": 1,
    "first_name": "Natan",
    "last_name": "Borges",
    "email": "natanbp@live.com",
    "phone": "44997731674"
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
        "first_name": "Natan",
        "last_name": "Pereira",
        "email": "natanbp7@hotmail.com",
        "phone": "44997731674"
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

-  O arquivo do databse se encontra no repositório. `databse.php`

## Configuração do Ambiente Docker
- Esta aplicação é desenvolvida em PHP na versão 8.1 e utiliza Docker para facilitar a configuração e o gerenciamento do ambiente de desenvolvimento. O ambiente inclui PHP-FPM, Nginx, MySQL, Redis e Memcached.
### 1. Dockerfile
O Dockerfile é utilizado para construir a imagem do PHP-FPM com as extensões necessárias. Abaixo está o conteúdo do Dockerfile:
```dockerfile
FROM phpdockerio/php:8.1-fpm
WORKDIR "/application"

RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        php8.1-memcached \
        php8.1-mysql \
        php8.1-redis \
        php8.1-xdebug \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
```

### 2. Docker Compose
O arquivo docker-compose.yml define os serviços necessários para o ambiente de desenvolvimento. Aqui está o conteúdo do docker-compose.yml:

```yml
version: '3.1'
services:
    memcached:
        image: 'memcached:alpine'

    redis:
        image: redis:latest
        ports:
        - "6379:6379"

    mysql:
        image: 'mysql:8.0'
        working_dir: /application
        volumes:
            - mysql_data:/var/lib/mysql
            - '.:/application'
        environment:
            - MYSQL_ROOT_PASSWORD=root
            - MYSQL_DATABASE=projeto-magnus
            - MYSQL_USER=magnus-usr
            - MYSQL_PASSWORD=usr-magnus
        ports:
            - '8002:3306'

    webserver:
        image: 'nginx:alpine'
        working_dir: /application
        volumes:
            - '.:/application'
            - './phpdocker/nginx/nginx.conf:/etc/nginx/conf.d/default.conf'
        ports:
            - '8000:80'

    php-fpm:
        build: phpdocker/php-fpm
        working_dir: /application
        volumes:
            - '.:/application'
            - './phpdocker/php-fpm/php-ini-overrides.ini:/etc/php/8.1/fpm/conf.d/99-overrides.ini'
            - './phpdocker/php-fpm/php-ini-overrides.ini:/etc/php/8.1/cli/conf.d/99-overrides.ini'
        environment:
            - DB_HOST=mysql
            - DB_USER=magnus-usr
            - DB_PASS=usr-magnus
            - DB_NAME=projeto-magnus

volumes:
    mysql_data:
```

### 3. Instruções para Subir o Ambiente
Para iniciar o ambiente de desenvolvimento, siga estas etapas:

**Construa a Imagem PHP-FPM**
- Navegue até o diretório onde está localizado o Dockerfile e construa a imagem do PHP-FPM:
```bash
docker build -t php-fpm-image -f Dockerfile .
```

**Suba os Contêineres**
- Navegue até o diretório onde está localizado o arquivo docker-compose.yml e inicie os serviços:

```bash
docker-compose up
```

**Isso irá iniciar os seguintes serviços:**
- `Memcached`: Contêiner para caching.
- `Redis`: Contêiner para caching de dados.
- `MySQL`: Contêiner para o banco de dados.
- `Webserver` (Nginx): Contêiner para servir os arquivos da aplicação.
- `PHP-FPM`: Contêiner para executar o PHP.


### Acessar a Aplicação
- A aplicação estará disponível em http://localhost:8000.

**Parar os Contêineres**
```bash
docker-compose down
```

**Volumes Persistentes**
- Os dados do MySQL são persistidos em um volume nomeado mysql_data. Isso garante que os dados não sejam perdidos quando os contêineres forem recriados.

