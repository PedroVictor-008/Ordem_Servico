# 📌 Sistema de Gestão de Ordens de Serviço

## 📘 Descrição

Este projeto é um Sistema de Gestão de Ordens de Serviço desenvolvido para organizar e controlar solicitações internas, permitindo cadastrar, visualizar, editar e excluir ordens de maneira prática e eficiente.

O sistema foi pensado para ser simples, rápido e funcional.

## 🚀 Tecnologias Utilizadas

- PHP
- MySQL / MariaDB
- HTML5
- CSS3
- JavaScript
- XAMPP (ambiente local)
- PDO para conexão segura

## 🖥️ Instalação e Configuração
### 1️⃣ Instalar o XAMPP

1. Baixe e instale em: https://www.apachefriends.org
2. Abra o XAMPP Control Panel.
3. Inicie os serviços:
  - Apache
  - MySQL

### 2️⃣ Adicionar o Projeto ao Servidor

1. Abra a pasta:
C:\xampp\htdocs\ 
   
Coloque dentro dela a pasta do projeto.

2. Acesse no navegador usando:
http://localhost/nome_da_pasta

### 3️⃣ Criar o Banco de Dados

1. Acesse o phpMyAdmin:
http://localhost/phpmyadmin

- Clique em Novo.
- Crie um banco (ex.: os_db).
- Na aba Importar, selecione o arquivo .sql fornecido.
- Clique em Executar.

### 4️⃣ Configurar a Conexão com o Banco

1. No arquivo db.php, verifique:

- Host: localhost
- Banco: os_db
- Usuário: root
- Senha: (senha do seu MySQl Workbench)

### 5️⃣ Executar o Sistema

1. Com Apache e MySQL ativos:
Acesse:
http://localhost/nome_da_pasta

O sistema será carregado.

## 🛠️ Funcionalidades

- Cadastro de ordens de serviço
- Edição e atualização
- Exclusão de registros
- Listagem completa
- Controle de status
- Interface intuitiva
- Conexão segura via PDO

## ⚠️ Problemas Comuns
- Erro de conexão
- MySQL desligado
- Nome do banco diferente do db.php
- Usuário ou senha incorretos
- Coluna/Tabela não existe
- Banco não importado corretamente
- Refaça a importação do arquivo .sql
- Página não abre
- Projeto fora da pasta htdocs
- Caminho errado na URL
