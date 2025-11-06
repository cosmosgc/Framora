# 📸 Framora

**Framora** é uma aplicação desenvolvida em [Laravel](https://laravel.com) para venda e gestão de fotografias.  
O projeto utiliza **Breeze** para autenticação, **Blade** como sistema de templates, e **Pest** para testes automatizados.

---

## 🚀 Tecnologias

- [Laravel  ](https://laravel.com) — Framework principal  
- [Blade](https://laravel.com/docs/blade) — Engine de templates nativo  
- [Laravel Breeze](https://laravel.com/docs/starter-kits#breeze) — Autenticação simples e elegante  
- [Pest](https://pestphp.com) — Framework de testes em PHP  

---

## 🛠️ Requisitos

Antes de rodar o projeto, certifique-se de ter instalado:

- [PHP 8.2+](https://www.php.net/downloads) (com extensões `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`)
- [Composer](https://getcomposer.org/download/) (gerenciador de dependências do PHP)
- [Node.js 18+](https://nodejs.org) e [NPM](https://www.npmjs.com) (para compilar assets front-end)
- [MySQL](https://dev.mysql.com/downloads/) ou [SQLite](https://www.sqlite.org/download.html) (banco de dados)

### Windows

1. Instale o [XAMPP](https://www.apachefriends.org/pt_br/index.html) ou [Laragon](https://laragon.org/) (vem com PHP e MySQL).
2. Instale o [Composer](https://getcomposer.org/Composer-Setup.exe).
3. Instale o [Node.js](https://nodejs.org/en/download/).

### Linux (Ubuntu/Debian)

```bash
sudo apt update
sudo apt install php php-cli php-mbstring php-xml php-bcmath php-curl unzip curl
sudo apt install composer
sudo apt install nodejs npm
```

## ⚙️ Setup do Projeto

### 1. Clonar o repositório
```bash
git clone https://github.com/cosmosgc/framora.git
cd framora
```
### 2. Instalar dependências
```bash
composer install
npm install && npm run build
```
### 3. Configurar variáveis de ambiente

Crie o arquivo .env copiando o exemplo:
```bash
cp .env.example .env
```

Gere a chave da aplicação:
```bash
php artisan key:generate
```

Configure o banco de dados no .env (MySQL, SQLite ou outro de sua escolha).

### 4. Executar migrações
```bash
php artisan migrate
```

### 5. Rodar o servidor
```bash
php artisan serve
```


Acesse em http://localhost:8000

## 🧪 Testes

O projeto usa Pest para rodar os testes unitários e de feature:
```bash
php artisan test
```

ou
```bash
./vendor/bin/pest
```

## 🔑 Autenticação

O Laravel Breeze fornece:

Registro de usuário

Login e logout

Recuperação de senha

Sessões seguras

Todas as views estão em Blade, permitindo fácil personalização.

## 📂 Estrutura Principal

app/Models → Modelos da aplicação

app/Http/Controllers → Controladores

resources/views → Views Blade

tests/Feature → Testes de funcionalidades (Pest)

tests/Unit → Testes unitários (Pest)

## 🖼️ Objetivo do Projeto

Criar uma plataforma simples e elegante para fotógrafos venderem suas fotos, permitindo gerenciamento de usuários, compras e catálogo.

## 📜 Licença

Este projeto está sob a licença MIT
.


---
