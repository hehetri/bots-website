# TBOT HQ Website

Site moderno e responsivo para autenticação e painel do jogador, conectado ao banco de dados existente (`tbot_base`).

## Requisitos
- PHP 8.1+ com extensões `mysqli` e `argon2`
- Servidor web configurado para servir arquivos PHP (Apache/Nginx) ou `php -S`
- Banco MariaDB/MySQL com o schema do arquivo `tbot-base.sql`

## Configuração
1. Importe o schema: `mysql -u root -p tbot_base < tbot-base.sql` (ajuste o usuário/senha conforme seu ambiente).
2. Configure as credenciais de banco via variáveis de ambiente se necessário:
   - `DB_HOST` (padrão: `localhost`)
   - `DB_USER` (padrão: `root`)
   - `DB_PASS` (padrão: `ascent`)
   - `DB_NAME` (padrão: `tbot_base`)
3. Inicie o servidor PHP na raiz do projeto:
   ```bash
   php -S localhost:8000
   ```
4. Acesse `http://localhost:8000` para visualizar o site.

## Funcionalidades
- **Autenticação segura** com hashing Argon2id, validação e sessão.
- **Cadastro de usuário** com verificação de duplicidade e IP salvo.
- **Painel logado** exibindo cash, gigas e contagem de guilds do usuário.
- **Rankings** de jogadores (por nível/experiência) e guilds (por nível médio e membros) carregados diretamente do banco.
- **Rotas API** autenticadas:
  - `GET /api/user.php` — dados do usuário logado.
  - `GET /api/rankings.php` — ranking de jogadores e guilds.
- Layout dark mode inspirado em dashboards de MMO.

## Estrutura
```
assets/          # Estilos globais
includes/        # Configuração, conexão e utilitários de autenticação
api/             # Endpoints JSON protegidos
index.php        # Login
register.php     # Cadastro
dashboard.php    # Painel logado com sidebar e rankings
logout.php       # Encerrar sessão
```

## Download
O botão de download do painel pode apontar para o cliente oficial configurando uma rota `/download` no servidor ou servindo o executável diretamente.
