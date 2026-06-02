# Sistema de Cadastro de Eventos e Inscricoes

API REST em Laravel para cadastro de eventos e controle de inscricoes, com regras de negocio e controle de vagas.

**Stack:** Laravel, PostgreSQL, Docker, Nginx, PHP 8.3.

## Estrutura

```
/
|-- devops/
|   |-- docker/
|   |   |-- nginx/
|   |   |   |-- conf.d/
|   |   |   |   |-- laravel.conf
|   |   |-- php/
|   |   |   |-- Dockerfile
|   |-- docker-compose.yml
|-- web/
|-- postman_collection.json
```

## Configuracao do banco

As variaveis de ambiente em `web/.env` ja estao alinhadas com o Docker:

```
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=app
DB_USERNAME=app
DB_PASSWORD=app
```

## Comandos

**Instalacao (local, se necessario):**

```
cd web
composer install
```

**Subir os containers:**

```
docker compose -f devops/docker-compose.yml up -d --build
```

**Executar migrations:**

```
docker compose -f devops/docker-compose.yml exec php php artisan migrate
```

## Endpoints principais

```
GET    /api/eventos
GET    /api/eventos/{id}
POST   /api/eventos
PUT    /api/eventos/{id}
DELETE /api/eventos/{id}
GET    /api/eventos/vagas-disponiveis
GET    /api/eventos/{id}/participantes

GET    /api/participantes
GET    /api/participantes/{id}
POST   /api/participantes
PUT    /api/participantes/{id}
DELETE /api/participantes/{id}

POST   /api/inscricoes
DELETE /api/inscricoes/{id}
```

## Exemplos de payload JSON

**Criar evento**

```json
{
  "titulo": "Oficina de Robotica",
  "descricao": "Introducao a robotica educacional.",
  "data": "2026-08-10",
  "horario": "14:00",
  "local": "Laboratorio 2",
  "quantidade_vagas": 30
}
```

**Atualizar evento**

```json
{
  "status": "encerrado"
}
```

**Criar participante**

```json
{
  "nome": "Maria Silva",
  "email": "maria.silva@email.com",
  "telefone": "11999998888"
}
```

**Criar inscricao**

```json
{
  "evento_id": 1,
  "participante_id": 1
}
```

## Postman

A collection esta em `postman_collection.json` com todos os endpoints e exemplos.
