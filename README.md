# Slim PHP Restful
Projeto de Exemplo utilizando o microframework Slim (PHP) e o ORM Eloquent que registra usuários, endereços e tarefas.

### Instruções para inicialização:
---
1. Instale o PHP e o Composer
2. execute  'composer install' para instalar as dependências
3. execute composer start para executar a api
4. abra o arquivo API Tasks.postman_collection.json no postman para testar os endpoints

### Estrutura do Projeto:
---
``
/api

├── Middlewares          → usados nas rotas
|
├── Models               → modelos de dados ORM Eloquent
|
├── routes               → roteamento para os controllers e uso de middleware de autenticação
|
├── Services             → ações e regras de negócio chamadas pelos controllers
|
├── source               → conexão com banco de dados, carregamento de rotas, middlewares, startup da API
|
└── public               → front controller para a pasta pública da hospedagem

``
