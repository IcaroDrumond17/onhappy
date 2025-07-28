# 🚀 Projeto Fullstack com Docker: Laravel (API) + Vue.js (Frontend)

Este projeto oferece uma estrutura completa para iniciar aplicações com **API em Laravel** e **Frontend em Vue.js** utilizando **Docker**, sem a necessidade de instalar PHP, Composer, Node.js ou MySQL localmente.

---

## 🐳 Requisitos

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

---

## 📁 Estrutura dos Serviços (docker-compose.yml)

| Serviço    | Função                         |
| ---------- | ------------------------------ |
| `api`      | Container PHP com Laravel      |
| `mysql`    | Banco de dados MySQL           |
| `composer` | Composer para instalar pacotes |
| `vue`      | Vite + Vue                     |

---

### 1. 🐙 Clone ou baixe o projeto

```bash
git clone https://github.com/seu-usuario/seu-projeto.git
cd onhappy-project
```

#### 🏗️ Estrutura do projeto:

- 📁 root

```
/onhappy-project/
├── backend/      # Laravel (via Docker)
├── frontend/     # Vue 3 + Vite (via Docker)
├── docker/       # Dockerfiles
├── docker-compose.yml
├── .env.example
├── README.md
```

- 📁 backend (API - Laravel)

```
/onhappy-project/backend/
├── app/
├── bootstrap/
├── config/
├── database/
├── routes/
├── storage/
├── tests/
├── vendor/
├── .editorconfig
├── .env.example
├── .gitattributes
├── .gitignore
├── artisan
├── composer.json
├── package.json
├── phpunit.xml
```

- 📁 front (VueJs)

```
/onhappy-project/frontend/
├── .vscode/
├── public/
├── src/
├── .editorconfig
├── .env.example
├── .gitattributes
├── .gitignore
├── .prettierrc.json
├── auto-imports.d.ts
├── components.d.ts
├── components.d.ts
├── .env.d.json
├── package.json
├── eslint.config.ts
├── index.html
├── tsconfig.app.json
├── tsconfig.json
├── tsconfig.node.json
├── vite.config.ts
```

#### 📝 Observação para usuários Windows (com WSL2)

- Ao instalar as dependências do projeto no Windows, é comum enfrentar problemas de permissão relacionados à criação de pastas e arquivos pelo Docker.

- Para evitar esses problemas, configurei o docker-compose para usar duas variáveis de ambiente: UID (ID do usuário) e GID (ID do grupo). Essas variáveis devem ser definidas em um arquivo .env localizado no mesmo nível do arquivo docker-compose.yml (raiz do projeto).

- Para facilitar, basta copiar o arquivo .env.example para .env e ajustar os valores conforme seu ambiente.

- Essa configuração é recomendada mesmo se você não estiver usando Docker Desktop no Windows, pois ajuda a manter as permissões corretas nos volumes compartilhados.

#### 🖥️ Como descobrir seu UID e GID no Linux ou WSL2:

```bash
echo $UID      # mostra o ID do usuário
id -g         # mostra o ID do grupo
```

- Valores Padrão

UID=1000
GID=1000

> Copie esses valores e cole no arquivo .env (na raiz do projeto) que você vai criar baseado no .env.example.

#### 🖥️ No Windows (sem WSL)

- O Windows não tem UID/GID, pois não usa sistema de permissões Unix.

- Mesmo assim, manter o .env com valores padrões (UID=1000 e GID=1000) geralmente funciona.

- Se usar WSL2, deve pegar os valores pelo terminal WSL (como acima).

- Essa configuração ajuda o Docker a rodar os containers com as permissões corretas no sistema de arquivos compartilhado.

- Após criar seu .env (na raiz do projeto) com os valores corretos, rode seus containers normalmente com docker compose up ou os comandos que já está acostumado.

### 2. 🐳 Docker

#### 📦 Containers

```bash
# Prieiro copie o env da raiz do projeto para validar as questões de permissões do docker
cp .env.example .env

# Subir (Primeira vez)
docker compose up -d --build

# Subir
docker compose up

# Verificar status
docker compose ps

# Finalizar containers
docker compose down -v

```

### 3. 🔥 Laravel

```bash
# Instalar as dependências (vendor)
docker compose run --rm api composer install

# Ou: Para ajudar em casos de conflitos ao baixar dependências de arquivos/diretórios com letras maiúsculas/minúsculas diferentes
docker compose run --rm api composer install --prefer-dist

# Caso necessário, sobe o container do VueJs
docker compose up api

# Replicar o .env
docker compose exec api cp .env.example .env

# Gera a chave da aplicação
docker compose exec api php artisan key:generate

```

#### 🔐 Instalação do JWT (Autenticação)

Instala a biblioteca JWT para autenticação via tokens.

> 💡 _Geralmente, essa dependência já estará instalada ao rodar `composer install`, mas incluí este comando por garantia:_

```bash
docker compose run --rm composer require tymon/jwt-auth
```

- Chave secreta

```bash
# publica o arquivo de configuração config/jwt.php da biblioteca tymon/jwt-aut
docker compose exec api php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"

# Gera a chave secreta (secret) e salva no .env
docker compose exec api php artisan jwt:secret
```

#### 🗄️ Gerar Base de Dados com Dados Simulados

Use os comandos abaixo para criar ou reiniciar o banco de dados da API:

> 💡 Observação: Pode ser necessário aguardar alguns segundos após subir os containers para que o banco de dados MySQL esteja totalmente pronto e as tabelas possam ser criadas com sucesso. Caso ocorra erro de conexão, tente executar o comando novamente após alguns segundos.

```bash
# Executa as migrations normalmente (cria as tabelas)
docker compose exec api php artisan migrate

# OU: Remove todas as tabelas, recria e popula com dados fictícios (seeds)
docker compose run --rm api php artisan migrate:fresh --seed
```

#### 🧪 Rotina de testes (Laravel)

- Escolhi fazer testes de Feature em vez de testes unitários para garantir que o sistema funcione de forma integrada, além de testar regras de negócio, validar rotas, permissões, respostas HTTP, interações com o banco de dados e autenticação.

```bash
# lista de testes
docker compose run --rm api php artisan test --list-tests

# Roda todos os testes
docker compose run --rm api php artisan test

# Testar orders
docker compose run --rm api php artisan test tests/Feature/OrderControllerTest.php

# Testar atualização de status do pedido
docker compose run --rm api php artisan test tests/Feature/OrderStatusTest.php

# Testar notificações
docker compose run --rm api php artisan test tests/Feature/NotificationControllerTest.php
```

### 4. 🟢 VueJS

```bash
# Instalar as dependências (node_modules)
docker compose run --rm vue npm install

# Replicar o .env
docker compose exec vue cp .env.example .env

# Caso necessário, sobe o container do VueJs
docker compose up vue

```

### 5. ⚡ Comandos úteis

5.1 ➡️ Criar usuários default

| E-mail            | Password | Tipo  |
| ----------------- | -------- | ----- |
| admin@teste.com   | `1234`   | ADMIN |
| default@teste.com | `1234`   | COMUM |

```bash
# Gerar tabelas
docker compose run --rm api php artisan db:seed

# Apaga todas as tabelas e recria (roda o migrate fresh + seed)
docker compose run --rm api php artisan migrate:fresh --seed
```

5.2 📊 Testar conexão com a base de dados MySQL

```bash
# logs 
docker compose logs mysql

# Entrar no terminal do container
docker compose exec mysql bash

# Acessar
mysql -u laravel -p
# Enter password: laravel
```

5.3 📊 Ver tabelas

```bash
# shell do container.
docker exec -it laravel-mysql bash

# Acessar
mysql -uroot -p
# Enter password: root

# Visualizar tabelas (aqui pode se dar comandos SQL)
SHOW DATABASES;

# Exemplos:

USE laravel;

SHOW TABLES;

DESCRIBE users;

SHOW COLUMNS FROM users;

SELECT * FROM users LIMIT 10;
```

| Serviço | URL/Host         |
| ------- | ---------------- |
| MySQL   | `127.0.0.1:3306` |
| Usuário | `laravel`        |
| Senha   | `laravel`        |

5.4 ⚡ Limpar caches da API

```bash
docker compose run --rm api php artisan config:clear
docker compose run --rm api php artisan cache:clear
docker compose run --rm api php artisan config:cache
docker compose run --rm api php artisan route:clear
```

5.5 🔑 Listar rotas da API

```bash
docker compose run --rm api php artisan route:list
```

5.6 Verificar variáveis de ambiente da API

```bash
docker compose run --rm api php artisan env
```

#### 📝 Observações Finais

- ⚠️ao instalar as dependencias do laravel, em sistemas com filesystem case-insensitive (como Windows ou WSL), o Composer pode apresentar erros ao instalar dependências, devido a conflitos de nomes com letras maiúsculas/minúsculas (ex: Ramsey/Uuid vs ramsey/uuid) e permissões de arquivos.

Para evitar problemas:

- Use o comando composer install --prefer-dist para evitar extrações problemáticas com unzip.

- Garanta permissões corretas nas pastas vendor, storage e bootstrap/cache.

- Em ambiente Docker, recomenda-se configurar a execução do container com o mesmo UID/GID do host para evitar conflitos de permissões em volumes.

```bash
docker compose run --rm api composer install --prefer-dist
```

- ⚠️ Para testar a responsividade no navegador, ao utilizar a ferramenta de inspeção para simular dispositivos móveis, lembre-se de atualizar a página. Isso garante que os scripts JavaScript responsáveis por ajustar elementos conforme o tamanho da tela funcionem corretamente.

#### Comandos padrão Docker

| Ação                      | Comando                                |
| ------------------------- | -------------------------------------- |
| Subir containers          | `docker compose up -d`                 |
| Derrubar containers       | `docker compose down`                  |
| Executar Artisan          | `docker compose exec api php artisan`  |
| Rodar Composer            | `docker compose run --rm composer`     |
| Acessar container do PHP  | `docker compose exec api bash`         |
| Rodar scripts npm         | `docker compose exec node npm run dev` |
| Acessar container do Node | `docker compose exec node bash`        |

#### Documentações Oficiais

| Tecnologia  | Link para a Documentação Oficial 📘                        |
| ----------- | ---------------------------------------------------------- |
| 🔥 Laravel  | [https://laravel.com/docs](https://laravel.com/docs)       |
| 🟢 Vue.js   | [https://vuejs.org/guide](https://vuejs.org/guide)         |
| 🐳 Docker   | [https://docs.docker.com](https://docs.docker.com)         |
| 📦 Composer | [https://getcomposer.org/doc](https://getcomposer.org/doc) |
| 🧰 Node.js  | [https://nodejs.org/en/docs](https://nodejs.org/en/docs)   |
| ⚡ Vite     | [https://vitejs.dev/guide](https://vitejs.dev/guide)       |
