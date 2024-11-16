# Sistema de Posts API

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white">
  <img src="https://img.shields.io/badge/Laravel-10-FF2D20?style=for-the-badge&logo=laravel&logoColor=white">
  <img src="https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white">
  <img src="https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white">
  <img src="https://img.shields.io/badge/JWT-000000?style=for-the-badge&logo=json-web-tokens&logoColor=white">
  <img src="https://img.shields.io/badge/SQLite-07405E?style=for-the-badge&logo=sqlite&logoColor=white">
  <img src="https://img.shields.io/badge/GitHub_Actions-2088FF?style=for-the-badge&logo=github-actions&logoColor=white">
</p>

## 🛠️ Tecnologias

- **PHP 8.3** 
- **Laravel 11** 
- **JWT Auth** 
- **SQLite** - Banco de dados para ambiente de testes
- **MySQL** - Banco de dados para ambiente de produção
- **Docker** - Containerização da aplicação
- **GitHub Actions** - Pipeline de CI/CD Executando Laravel Envoy


## 🚀 Como Executar

### Pré-requisitos
- Docker
- Docker Compose
- Git

### Passo a passo

1. Clone o repositório:
   ```bash
   git clone https://github.com/RuanSerraAzul/back-posts
   cd back-posts
   ```

2. Configure o ambiente:
   ```bash
   cp .env.example .env
   ```
   Altere os valores das variáveis de ambiente no arquivo .env referentes aos diretórios e portas.

3. Inicie os containers:
   ```bash
   docker-compose --env-file sistema/.env up -d --build
   ```

4. Instale as dependências:
   ```bash
   docker-compose exec php bash
   composer install
   php artisan key:generate
   php artisan jwt:secret
   php artisan migrate
   ```

5. Execute os testes:
   ```bash
   php artisan test --coverage-text
   ```

## 📂 Estrutura do Projeto

```plaintext
sistema/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       └── V1/      # Controladores da API V1
│   │   ├── Middleware/      # Middlewares
│   │   └── Requests/        # Form Requests para validação
│   ├── Models/              # Modelos do Eloquent
│   ├── Repositories/        # Implementações dos repositórios
│   │   └── Contracts/       # Interfaces dos repositórios
│   ├── Services/            # Camada de serviços
│   └── Traits/              # Traits compartilhados
├── config/                  # Arquivos de configuração
├── database/
│   ├── factories/           # Factories para testes
│   ├── migrations/          # Migrações do banco
│   └── seeders/             # Seeders para popular o banco
├── routes/
│   └── api.php              # Rotas da API
├── storage/
│   └── api-docs/            # Documentação OpenAPI/Swagger
└── tests/
    └── Feature/             # Testes de funcionalidades
        ├── Auth/            # Testes de autenticação
        └── Post/            # Testes de posts
```

## 🔑 Autenticação

A API utiliza JWT (JSON Web Token) para autenticação. Para acessar as rotas protegidas:

1. Registre um usuário.
2. Faça login para obter o token.
3. Inclua o token no header das requisições:
   ```
   Authorization: Bearer {seu-token}
   ```

## 📝 Endpoints Disponíveis

### Autenticação
- **POST** `/api/v1/auth/register` - Registro de usuário
- **POST** `/api/v1/auth/login` - Login
- **POST** `/api/v1/auth/logout` - Logout
- **POST** `/api/v1/auth/refresh` - Atualizar token
- **GET** `/api/v1/auth/me` - Dados do usuário

### Posts
- **GET** `/api/v1/posts` - Listar posts
- **POST** `/api/v1/posts` - Criar post
- **PUT** `/api/v1/posts/{id}` - Atualizar post
- **DELETE** `/api/v1/posts/{id}` - Deletar post

## 🧪 Testes

O projeto possui testes automatizados cobrindo as principais funcionalidades. Para executá-los, utilize:
```bash
php artisan test --coverage-text
```

## 🔧 Configuração do GitHub Actions

Para que o pipeline de CI/CD funcione corretamente, é necessário configurar os seguintes *secrets* no GitHub:

- `SSH_PRIVATE_KEY`: Chave SSH privada para acesso ao servidor
- `DEPLOY_USER`: Usuário do servidor para deploy
- `DEPLOY_HOST`: Endereço IP ou hostname do servidor
- `DEPLOY_REPOSITORY`: URL do repositório Git
- `DEPLOY_RELEASES_DIR`: Diretório de releases no servidor
- `DEPLOY_APP_DIR`: Diretório base da aplicação no servidor

### Configuração SSH no Servidor

1. Gere um par de chaves SSH com o comando:
   ```bash
   ssh-keygen -t rsa -b 4096 -C "seu-email@dominio.com"
   ```

## 🚀 Continuous Deployment

O projeto utiliza Laravel Envoy para automatizar o processo de deploy. O deploy é acionado automaticamente após os testes passarem no GitHub Actions.

### Variáveis de Ambiente para Deploy

Configure as seguintes variáveis no arquivo `.env`:

- `DEPLOY_USER`: Usuário do servidor
- `DEPLOY_HOST`: Endereço IP ou hostname do servidor
- `DEPLOY_REPOSITORY`: URL do repositório Git
- `DEPLOY_RELEASES_DIR`: Diretório de releases (/opt/app/releases)
- `DEPLOY_APP_DIR`: Diretório base da aplicação (/opt/app)

### Processo de Deploy

O Envoy executa as seguintes etapas:

1. **Restore Branch**: Restaura o estado do branch
2. **Pull Master**: Atualiza o código do repositório
3. **Enter Container**: Instala dependências do Composer
4. **Run Migrations**: Executa as migrações do banco
5. **Update Symlinks**: Atualiza os links simbólicos

### Estrutura de Diretórios no Servidor

```plaintext
/back-posts/sistema/
├── current/ # Link simbólico para o release atual
├── releases/ # Diretório com todos os releases
├── storage/ # Arquivos de armazenamento persistente
└── .env # Arquivo de configuração do ambiente
```