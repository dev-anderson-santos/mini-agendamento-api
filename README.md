# Mini Agendamento - API

API REST de agendamento (serviços, profissionais e agendamentos) em Laravel, com autenticação por token. O projeto está em desenvolvimento: a autenticação já está pronta e testada, e o domínio de agendamento é a próxima etapa.

## Stack

- PHP 8.3+
- Laravel 13
- Laravel Sanctum (autenticação por token)
- Pest (testes)
- SQLite (desenvolvimento e testes)

## Status

- [x] Autenticação: registro, login e logout com tokens (Sanctum)
- [x] Testes automatizados da autenticação
- [ ] Serviços, profissionais e agendamentos
- [ ] Regras de negócio: horário duplicado, antecedência mínima para cancelar e horário fora do expediente
- [ ] Lembrete de agendamento por fila
- [ ] Docker, CI e documentação OpenAPI

## Como rodar localmente

Pré-requisitos: PHP 8.3+ e Composer.

```bash
git clone git@github.com:dev-anderson-santos/mini-agendamento-api.git
cd mini-agendamento-api

composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

No `migrate`, confirme a criação do arquivo SQLite quando o Laravel perguntar. A API fica em `http://localhost:8000/api`.

## Como rodar os testes

```bash
php artisan test
```

Os testes usam SQLite em memória e não tocam no banco de desenvolvimento.

## Endpoints

Envie sempre o header `Accept: application/json`. As rotas protegidas exigem `Authorization: Bearer <token>`.

| Método | Rota | Autenticação | Descrição |
|---|---|---|---|
| POST | `/api/register` | não | Cadastra um usuário |
| POST | `/api/login` | não | Devolve o token (limite de 5 tentativas por minuto) |
| POST | `/api/logout` | Bearer | Revoga o token atual |
| GET | `/api/user` | Bearer | Dados do usuário autenticado |

### Exemplo: login

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email": "maria@example.com", "password": "senha-segura-123"}'
```

Resposta (200):

```json
{
  "user": { "id": 1, "name": "Maria Silva", "email": "maria@example.com" },
  "token": "1|abc123..."
}
```

Credenciais inválidas e dados com erro de validação respondem 422 com a lista de erros por campo.

### Exemplo: rota protegida

```bash
curl http://localhost:8000/api/user \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|abc123..."
```

## Decisões técnicas

- **Service para a autenticação.** A lógica de registro, login e logout fica em `AuthService`. O controller só recebe a requisição já validada e devolve a resposta.
- **Validação em Form Requests**, com `FailOnUnknownFields` para recusar campos inesperados no corpo da requisição.
- **Mensagem genérica no login.** A resposta não diz se o erro foi no email ou na senha, para não revelar quais emails estão cadastrados.
- **Limite de tentativas no login** (`throttle:5,1`) contra força bruta.
- **Respostas de erro sempre em JSON** nas rotas `/api/*`, sem redirecionar visitantes não autenticados.
- **Testes de feature com Pest**, cobrindo o caminho feliz e as recusas (dados inválidos, email duplicado, senha fraca, token revogado).
