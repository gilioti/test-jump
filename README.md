# Test Jump - Sistema de Ordens de Serviço

Este é um projeto Laravel que gerencia ordens de serviço para veículos, permitindo o controle de entrada, saída e preços de estacionamento.

## 📋 Pré-requisitos

- PHP 8.2 ou superior
- Composer
- Node.js e npm
- MySQL (banco de dados principal do projeto)

## 🚀 Como clonar e configurar o projeto

### 1. Clone o repositório
```bash
git clone <url-do-repositorio>
cd test-jump
```

### 2. Instale as dependências PHP
```bash
composer install
```

### 3. Instale as dependências JavaScript
```bash
npm install
```

### 4. Configure o ambiente
```bash
# Copie o arquivo de exemplo de ambiente
cp .env.example .env

# Gere a chave da aplicação
php artisan key:generate
```

### 5. Configure o banco de dados
O projeto usa **MySQL** como banco de dados principal. Configure o arquivo `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=test_jump
DB_USERNAME=root
DB_PASSWORD=
```

**Importante**: Certifique-se de que o banco de dados `test_jump` existe no MySQL antes de executar as migrações.

### 6. Execute as migrações
```bash
php artisan migrate
```

### 7. Inicie o servidor de desenvolvimento
```bash
# Terminal 1 - Servidor PHP
php artisan serve

# Terminal 2 - Vite (para assets)
npm run dev
```

## 📚 Regras de Negócio

### Contexto do Projeto
Este projeto foi desenvolvido seguindo orientações específicas para criar uma API de gerenciamento de ordens de serviço. Os modelos foram espelhados nas tabelas presentes no arquivo `db-structure.sql` e implementados conforme as seguintes orientações:

### Estrutura do Sistema

O sistema possui duas entidades principais baseadas no banco MySQL:

1. **Users (Usuários)**
   - Campo obrigatório: `name` (string, máximo 255 caracteres)
   - ID auto-incremento

2. **Service Orders (Ordens de Serviço)**
   - `vehiclePlate`: Placa do veículo (exatamente 7 caracteres)
   - `entryDateTime`: Data/hora de entrada (obrigatório)
   - `exitDateTime`: Data/hora de saída (opcional)
   - `priceType`: Tipo de preço (opcional, máximo 55 caracteres)
   - `price`: Preço (obrigatório, decimal)
   - `userId`: ID do usuário (obrigatório, deve existir na tabela users)

### Relacionamentos
- Uma Ordem de Serviço pertence a um Usuário (relacionamento 1:N)
- Se um usuário for deletado, suas ordens de serviço também serão removidas (cascade)

### Endpoints da API

#### Usuários
- `GET /api/users` - Lista todos os usuários
- `POST /api/users` - Cria um novo usuário
  ```json
  {
    "name": "Nome do Usuário"
  }
  ```

#### Ordens de Serviço

**Criação (POST /api/service-orders)**:
- Deve receber como parâmetro **todas as colunas** da tabela `service_orders`
- Implementa validações rigorosas nos parâmetros de entrada para evitar erros de inserção e inconsistência de dados
- Retorna código 200 com corpo de sucesso quando parâmetros estão corretos

**Listagem (GET /api/service-orders)**:
- Lista **todas as colunas** presentes na tabela `service_orders`
- A coluna `userId` é relacionada na tabela `users` e retorna o nome do usuário
- Formato de resposta: JSON

**Exemplo de criação**:
```json
{
  "vehiclePlate": "ABC1234",
  "entryDateTime": "2024-01-15T10:30:00",
  "exitDateTime": "2024-01-15T12:30:00",
  "priceType": "hora",
  "price": 100.50,
  "userId": 1
}
```

## ⚠️ Regras Importantes para Desenvolvimento

### 1. Criação de Usuários
**IMPORTANTE**: Você DEVE criar um usuário antes de tentar criar uma ordem de serviço. O campo `userId` é obrigatório e deve referenciar um usuário existente.

### 2. Validações
- A placa do veículo deve ter exatamente 7 caracteres
- O preço deve ser um número válido
- O `userId` deve existir na tabela `users`
- As datas devem estar em formato válido

### 3. Testes
Os testes funcionam melhor quando há pelo menos um usuário no banco de dados. O teste de criação de ordem de serviço cria automaticamente um usuário usando a factory.

Para executar os testes:
```bash
php artisan test
```

### 4. Banco de Dados
- O projeto usa **MySQL** como banco de dados principal
- As migrações criam automaticamente as tabelas necessárias baseadas no `db-structure.sql`
- O relacionamento entre `service_orders` e `users` é obrigatório
- Todas as colunas da tabela `service_orders` são mapeadas nos modelos

### 5. Desenvolvimento
Para desenvolvimento com hot-reload completo:
```bash
composer run dev
```
Este comando inicia:
- Servidor PHP (Laravel)
- Queue listener
- Logs em tempo real
- Vite para assets

## 🧪 Testando a API

### Exemplo de criação de usuário:
```bash
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{"name": "João Silva"}'
```

### Exemplo de criação de ordem de serviço:
```bash
curl -X POST http://localhost:8000/api/service-orders \
  -H "Content-Type: application/json" \
  -d '{
    "vehiclePlate": "ABC1234",
    "entryDateTime": "2024-01-15T10:30:00",
    "exitDateTime": "2024-01-15T12:30:00",
    "priceType": "hora",
    "price": 100.50,
    "userId": 1
  }'
```

## 📁 Estrutura do Projeto

```
test-jump/
├── app/
│   ├── Http/Controllers/
│   │   ├── Controller.php (Classe base abstrata)
│   │   ├── ServiceOrderController.php
│   │   └── UserController.php
│   └── Models/
│       ├── ServiceOrder.php
│       └── User.php
├── database/
│   ├── factories/
│   │   └── UserFactory.php
│   ├── migrations/
│   │   ├── create_users_table.php
│   │   └── create_service_orders_table.php
│   └── seeders/
├── routes/
│   └── api.php
└── tests/
    ├── TestCase.php (Classe base para testes)
    ├── Feature/
    │   └── ServiceOrderTest.php
    └── Unit/
        └── ExampleTest.php

## 🎯 Controllers

### Controller.php (Base)
- Classe abstrata base para todos os controllers
- Localização: `app/Http/Controllers/Controller.php`

### UserController.php
**Localização**: `app/Http/Controllers/UserController.php`

**Métodos**:
- `index()`: Retorna todos os usuários (GET /api/users)
- `store(Request $request)`: Cria um novo usuário (POST /api/users)
  - Validação: `name` obrigatório, string, máximo 255 caracteres
  - Retorna: JSON com status 201

### ServiceOrderController.php
**Localização**: `app/Http/Controllers/ServiceOrderController.php`

**Métodos**:
- `index()`: Retorna todas as ordens de serviço com dados do usuário (GET /api/service-orders)
- `store(Request $request)`: Cria uma nova ordem de serviço (POST /api/service-orders)
  - Validações:
    - `vehiclePlate`: obrigatório, string, exatamente 7 caracteres
    - `entryDateTime`: obrigatório, formato data
    - `exitDateTime`: opcional, formato data
    - `priceType`: opcional, string
    - `price`: obrigatório, numérico
    - `userId`: obrigatório, deve existir na tabela users
  - Retorna: JSON com status 200 e mensagem de sucesso

## 🗃️ Models

### User.php
**Localização**: `app/Models/User.php`

**Características**:
- Estende `Authenticatable` (preparado para autenticação)
- Usa traits: `HasFactory`, `Notifiable`
- Campos preenchíveis: `['name']`
- Relacionamento: Uma ordem de serviço pertence a um usuário

### ServiceOrder.php
**Localização**: `app/Models/ServiceOrder.php`

**Características**:
- Estende `Model`
- Campos preenchíveis: `['vehiclePlate', 'entryDateTime', 'exitDateTime', 'priceType', 'price', 'userId']`
- Relacionamento: `belongsTo(User::class, 'userId')`

## 🛣️ Rotas

**Arquivo**: `routes/api.php`

### Prefixo: `/api/service-orders`
- `GET /` → `ServiceOrderController@index`
- `POST /` → `ServiceOrderController@store`

### Prefixo: `/api/users`
- `GET /` → `UserController@index`
- `POST /` → `UserController@store`

### Rota raiz
- `GET /api/` → Retorna "Welcome to the API!"

## 🧪 Testes

### Estrutura de Testes
O projeto usa **Pest PHP** como framework de testes, conforme especificação do projeto. Pest oferece uma sintaxe mais expressiva que o PHPUnit tradicional.

### TestCase.php
**Localização**: `tests/TestCase.php`
- Classe base abstrata para todos os testes
- Estende `Illuminate\Foundation\Testing\TestCase`
- Fornece funcionalidades base do Laravel para testes

### ServiceOrderTest.php
**Localização**: `tests/Feature/ServiceOrderTest.php`

**Tipo**: Teste de Feature (testa funcionalidades completas da aplicação)

**Testes implementados** (conforme especificação do projeto):

1. **Teste de criação bem-sucedida**:
   - Cria um usuário usando factory
   - Envia payload válido para criar ordem de serviço
   - Verifica resposta **código 200** com corpo esperado de sucesso
   - Verifica se dados foram salvos no banco

2. **Teste de validação de erros**:
   - Envia dados inválidos (placa incorreta, data inválida, etc.)
   - Verifica resposta **NÃO recebendo código 200** e corpo esperado de erro
   - Verifica se status é 'error'

**Dependências**:
- Usa `RefreshDatabase` trait (limpa banco antes de cada teste)
- Usa `UserFactory` para criar dados de teste
- Requer usuário existente para funcionar corretamente

### ExampleTest.php
**Localização**: `tests/Unit/ExampleTest.php`
- Teste unitário básico de exemplo
- Usa PHPUnit tradicional (não Pest)
- Verifica se `true` é `true`

### Organização dos Testes
- **Feature Tests** (`tests/Feature/`): Testam funcionalidades completas, incluindo rotas, controllers e models
- **Unit Tests** (`tests/Unit/`): Testam unidades específicas de código isoladamente

## 🏭 Factories

### UserFactory.php
**Localização**: `database/factories/UserFactory.php`

**Funcionalidade**:
- Gera dados fake para usuários
- Campo `name`: usa `$this->faker->name()`
- Usado nos testes para criar usuários de exemplo

## 🔧 Comandos Úteis

```bash
# Limpar cache
php artisan config:clear
php artisan cache:clear

# Recriar banco de dados
php artisan migrate:fresh

# Executar testes
php artisan test

# Ver rotas disponíveis
php artisan route:list
```

## 📝 Notas Adicionais

- O projeto usa Laravel Sanctum para autenticação (configurado mas não implementado nos endpoints)
- Frontend usa Tailwind CSS v4 e Vite
- **Framework**: Laravel PHP (versão mais atualizada possível)
- **Banco de Dados**: MySQL (conforme especificação)
- **Testes**: Pest PHP (conforme especificação)
- **Formato**: Todas as requisições e respostas em JSON
- **Requisitos atendidos**: Criação e listagem de ordens de serviço com validações rigorosas
