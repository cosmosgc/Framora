# 📸 Framora

**Framora** é uma aplicação desenvolvida em [Laravel](https://laravel.com) para venda e gestão de fotografias.  
O projeto utiliza **Breeze** para autenticação, **Blade** como sistema de templates, e **Pest** para testes automatizados.

---

## 🚀 Tecnologias

- [Laravel 11](https://laravel.com) — Framework principal  
- [Blade](https://laravel.com/docs/blade) — Engine de templates nativo  
- [Laravel Breeze](https://laravel.com/docs/starter-kits#breeze) — Autenticação simples e elegante  
- [Pest](https://pestphp.com) — Framework de testes em PHP  

---

## ⚙️ Setup do Projeto

### 1. Clonar o repositório
```bash
git clone https://github.com/seu-usuario/framora.git
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

Quer que eu já prepare também os **comandos para instalar Breeze e Pest** dentro do `README` (ex: `php artisan breeze:install blade`, `composer require pestphp/pest`), ou prefere deixar o guia mais limpo e só citar as tecnologias?
