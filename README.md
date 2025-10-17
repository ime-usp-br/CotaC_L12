# Sistema CotaC - Laravel 12

**Versão:** 0.1.3<br>
**Data:** 2025-10-17

[![Status da Build](https://github.com/ime-usp-br/laravel_12_starter_kit/actions/workflows/laravel.yml/badge.svg)](https://github.com/ime-usp-br/laravel_12_starter_kit/actions/workflows/laravel.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## 1. Introdução

O **Sistema CotaC** (Cota de Café) é um sistema de gestão de cotas mensais para consumo de produtos (café, lanches) pela comunidade acadêmica do IME-USP. Este projeto é uma modernização completa do sistema legado Java/JSF, reimplementado em **Laravel 12** utilizando o **Laravel 12 USP Starter Kit** como base.

**Propósito e Justificativa:** O sistema gerencia cotas mensais por vínculo USP (docentes, servidores, alunos) e cotas especiais individuais, oferecendo interfaces distintas para:
- **Balcão:** Realização de pedidos sem autenticação
- **Entrega:** Interface pública para atendentes gerenciarem pedidos
- **Administrativa:** Área restrita (perfis ADM e OPR) para gestão do sistema

A migração para Laravel 12 visa eliminar a dívida técnica do sistema legado, aproveitando as funcionalidades modernas do framework e as integrações pré-configuradas do Starter Kit com o ecossistema USP (Senha Única, Replicado).

## 2. Público-Alvo

Este sistema destina-se a:

*   **Usuários Finais:** Comunidade acadêmica do IME-USP (docentes, servidores, alunos, visitantes)
*   **Atendentes:** Funcionários do balcão que gerenciam a entrega dos pedidos
*   **Administradores:** Responsáveis pela configuração de cotas, produtos e usuários do sistema
*   **Desenvolvedores:** Equipe de desenvolvimento e manutenção do sistema (assume-se conhecimento de PHP, Laravel, Git e linha de comando)

## 3. Principais Funcionalidades

### 3.1. Funcionalidades do Sistema CotaC

**Status:** 🚧 Em Implementação

#### Área Pública (Sem Autenticação)

*   **Balcão (Interface de Pedidos):**
    *   Busca de consumidor por N° USP (integração com Replicado)
    *   Cálculo automático de saldo (cota mensal - pedidos do mês)
    *   Seleção de produtos com validação de saldo
    *   Geração de número de pedido
    *   Status: `PLANEJADO` ✅

*   **Entrega (Interface para Atendentes):**
    *   Visualização de pedidos pendentes (status `REALIZADO`)
    *   Atualização automática da lista (polling)
    *   Marcação de pedidos como entregues
    *   Status: `PLANEJADO` ✅

#### Área Administrativa (Autenticação Obrigatória)

*   **Gerenciamento de Cotas Regulares:** CRUD para cotas por vínculo USP (Status: `PLANEJADO` ✅)
*   **Gerenciamento de Cotas Especiais:** CRUD para cotas individuais (Status: `PLANEJADO` ✅)
*   **Gerenciamento de Produtos:** CRUD para produtos disponíveis (Status: `PLANEJADO` ✅)
*   **Gerenciamento de Usuários:** CRUD para usuários ADM/OPR (Status: `PLANEJADO` ✅)
*   **Consulta de Extratos:** Visualização de histórico de pedidos com filtros (Status: `PLANEJADO` ✅)
*   **Auditoria:** Log de alterações via `owen-it/laravel-auditing` (Status: `PLANEJADO` ✅)

**Perfis de Acesso:**
- `ADM` (Administrador): Acesso total
- `OPR` (Operador): Acesso somente-leitura a extratos

### 3.2. Infraestrutura do Starter Kit (Já Implementado)

*   **Base Laravel 12** com stack TALL (Tailwind, Alpine.js, Livewire 3, Laravel)
*   **Autenticação:** Laravel Breeze + Senha Única USP (`uspdev/senhaunica-socialite`)
*   **Integração com Replicado:** Biblioteca `uspdev/replicado` configurada
*   **Gerenciamento de Permissões:** `spatie/laravel-permission`
*   **Painel Administrativo:** Filament 4.x
*   **Sistema de Filas:** Driver `database` com Supervisor
*   **Logging de Email:** Model `EmailLog` + Filament Resource
*   **Infraestrutura Docker:** Produção-ready com multi-stage build
*   **Ferramentas de Qualidade:** Laravel Pint, Larastan, EditorConfig
*   **Testes Automatizados:** PHPUnit, Laravel Dusk

*Para arquitetura detalhada, consulte [`docs/arquitetura/06-desenho-arquitetura.md`](./docs/arquitetura/06-desenho-arquitetura.md).*
*Para modelo de dados, consulte [`docs/arquitetura/05-mapeamento-modelo-dados.md`](./docs/arquitetura/05-mapeamento-modelo-dados.md).*
*Para análise do sistema legado, consulte [`docs/analise/`](./docs/analise/).*

## 4. Stack Tecnológica

*   **Framework:** Laravel 12
*   **Linguagem:** PHP >= 8.2
*   **Frontend (Stack TALL via Laravel Breeze):**
    *   **Livewire 3 (Class API):** Componentes PHP interativos
    *   **Alpine.js 3:** Interatividade leve no frontend
    *   **Tailwind CSS 4:** Estilização utilitária com Dark Mode
    *   **Vite:** Compilação de assets
*   **Banco de Dados:** MySQL/MariaDB (produção), SQLite (testes)
*   **Integrações USP:**
    *   `uspdev/senhaunica-socialite` - Autenticação OAuth USP
    *   `uspdev/replicado` - Dados corporativos USP (vínculos, pessoas)
*   **Pacotes Principais:**
    *   `laravel/breeze` - Autenticação scaffolding
    *   `spatie/laravel-permission` - Gerenciamento de roles/permissões
    *   `filament/filament` - Painel administrativo
    *   `owen-it/laravel-auditing` - Auditoria de alterações
*   **Testes:** PHPUnit (Unit/Feature), Laravel Dusk (E2E)
*   **Qualidade:** Laravel Pint (PSR-12), Larastan (análise estática)

## 5. Arquitetura do Sistema

### 5.1. Separação de Camadas

O sistema segue uma arquitetura em camadas com separação clara de responsabilidades:

```
┌─────────────────────────┐
│   Controllers (HTTP)    │ ← Orquestração apenas
├─────────────────────────┤
│   Form Requests         │ ← Validação de entrada
├─────────────────────────┤
│   Services              │ ← Lógica de negócio
├─────────────────────────┤
│   Models (Eloquent)     │ ← Acesso a dados
└─────────────────────────┘
```

**Princípios Fundamentais:**
- **Controllers Enxutos:** Apenas orquestração, sem lógica de negócio
- **Services:** Toda lógica de negócio complexa (ex: `CotaService`, `PedidoService`, `ReplicadoService`)
- **Form Requests:** Validação obrigatória antes de chegar ao controller
- **Models:** Relacionamentos Eloquent e acesso a dados

### 5.2. Regras de Negócio Principais

**Cálculo de Cota Mensal (Prioridade):**
1. **Cota Especial** (se existir para o codpes) → FIM
2. **Cota Regular** (maior cota entre vínculos ativos do IME)
3. **Sem cota** → valor = 0

**Cálculo de Saldo:**
```
Saldo = Cota Mensal - Σ(Pedidos do Mês Atual)
```

**Validações Críticas:**
- Vínculos devem estar **ativos**
- Vínculos devem pertencer à **unidade IME**
- **BLOQUEAR** pedido se `valorTotal > saldo`

### 5.3. Integração com Replicado

**IMPORTANTE:** Dados de pessoas e vínculos **NÃO são armazenados** localmente. Todas as consultas são feitas em **tempo real** via `ReplicadoService`.

*Para arquitetura completa, consulte [`docs/arquitetura/06-desenho-arquitetura.md`](./docs/arquitetura/06-desenho-arquitetura.md).*

## 6. Instalação

O sistema já vem com Laravel Breeze (Stack TALL), Laravel Dusk e todas as integrações USP pré-configuradas. Você pode escolher entre instalação tradicional ou usando Docker com Laravel Sail.

### 6.1. Instalação com Laravel Sail (Recomendado)

Laravel Sail fornece um ambiente Docker completo com PHP, MySQL, Redis, Selenium e outras dependências pré-configuradas.

1.  **Pré-requisitos:**
    *   Docker e Docker Compose instalados
    *   Git

2.  **Clonar o Repositório:**
    ```bash
    git clone https://github.com/ime-usp-br/CotaC_L12.git cotac
    cd cotac
    ```

3.  **Configurar Ambiente:**
    *   Copie o arquivo de exemplo `.env`:
        ```bash
        cp .env.example .env
        ```
    *   **Edite o arquivo `.env`** e configure as variáveis essenciais para Sail:
        ```bash
        APP_NAME=Laravel
        APP_URL=http://localhost
        APP_PORT=8000

        # Configuração do banco de dados para Sail
        DB_CONNECTION=mysql
        DB_HOST=mysql
        DB_PORT=3306
        DB_DATABASE=laravel12_usp_starter_kit
        DB_USERNAME=sail
        DB_PASSWORD=password

        # Configuração de usuário Docker
        WWWUSER=1000
        WWWGROUP=1000
        ```
    *   **Credenciais USP:** Adicione e configure as variáveis para `uspdev/senhaunica-socialite` e `uspdev/replicado` (veja a seção 7).

4.  **Iniciar Containers Docker:**
    ```bash
    ./vendor/bin/sail up -d
    ```
    *(Na primeira execução, as imagens Docker serão construídas, o que pode levar alguns minutos)*

5.  **Gerar Chave da Aplicação:**
    ```bash
    ./vendor/bin/sail artisan key:generate
    ```

6.  **Instalar Dependências Frontend:**
    ```bash
    ./vendor/bin/sail npm install
    ```

7.  **Executar Migrações e Seeders:**
    ```bash
    ./vendor/bin/sail artisan migrate --seed
    ```

8.  **Compilar Assets Frontend:**
    ```bash
    ./vendor/bin/sail npm run dev
    ```
    *(Mantenha este comando rodando em um terminal separado durante o desenvolvimento)*

9.  **Configurar Usuário Admin:**

    Após a migração e seeding, você pode atribuir o perfil Admin a um usuário:
    ```bash
    ./vendor/bin/sail artisan tinker
    ```
    No tinker, execute:
    ```php
    $user = App\Models\User::where('email', 'seu-email@usp.br')->first();
    $user->assignRole('Admin');
    ```

**Atalho:** Para simplificar comandos, você pode criar um alias:
```bash
alias sail='./vendor/bin/sail'
```

Agora você pode usar `sail up -d`, `sail artisan migrate`, `sail npm run dev`, etc.

### 7.2. Instalação Tradicional (Sem Docker)

1.  **Pré-requisitos:**
    *   PHP >= 8.2 (com extensões comuns do Laravel: ctype, fileinfo, json, mbstring, openssl, PDO, tokenizer, xml, etc.)
    *   Composer
    *   Node.js (v18+) e NPM
    *   Git
    *   MySQL/MariaDB ou outro banco de dados compatível
    *   **Google Chrome** ou **Chromium** instalado (para testes Dusk)

2.  **Clonar o Repositório:**
    ```bash
    git clone https://github.com/ime-usp-br/CotaC_L12.git cotac
    cd cotac
    ```

3.  **Instalar Dependências PHP:**
    ```bash
    composer install
    ```

4.  **Instalar Dependências Frontend:**
    ```bash
    npm install
    ```

5.  **Configurar Ambiente:**
    *   Copie o arquivo de exemplo `.env`:
        ```bash
        cp .env.example .env
        ```
    *   Gere a chave da aplicação:
        ```bash
        php artisan key:generate
        ```
    *   **Edite o arquivo `.env`:** Configure as variáveis de ambiente, especialmente:
        *   `APP_NAME`: Nome da sua aplicação.
        *   `APP_URL`: URL base da sua aplicação (ex: `http://localhost:8000`).
        *   `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`: Credenciais do seu banco de dados.
        *   `MAIL_*`: Configurações de e-mail (importante para verificação de e-mail).
        *   **Credenciais USP:** Adicione e configure as variáveis para `uspdev/senhaunica-socialite` e `uspdev/replicado` (veja a seção 7).

6.  **Banco de Dados e Dados Iniciais:**
    *   Execute as migrações para criar todas as tabelas necessárias:
        ```bash
        php artisan migrate
        ```
    *   (Opcional, mas recomendado) Execute os seeders para popular o banco com dados iniciais (ex: usuário de teste local `test@example.com`):
        ```bash
        php artisan db:seed
        ```

7.  **Compilar Assets Frontend:**
    ```bash
    npm run build
    ```
    *(Ou use `npm run dev` durante o desenvolvimento para compilação automática).*

8.  **Configuração Inicial do Dusk (Importante):**
    *   **Verificar Instalação:** Confirme se o Dusk está instalado (já deveria estar no `composer.json`). Se necessário, rode `php artisan dusk:install`.
    *   **Instalar ChromeDriver:** Instale o driver correto para sua versão do Chrome/Chromium:
        ```bash
        php artisan dusk:chrome-driver --detect
        ```
    *   **Criar/Verificar `.env.dusk.local`:** Crie este arquivo na raiz do projeto (se não existir) e configure-o para o ambiente de teste do Dusk. Um exemplo (`.env.dusk.local`) já está incluído neste repositório. Preste atenção especial a:
        *   `APP_URL=http://127.0.0.1:8000` (ou a URL que `php artisan serve` usa)
        *   `DB_CONNECTION=sqlite` e `DB_DATABASE=database/testing/dusk.sqlite` (recomendado usar um banco de dados SQLite separado para testes Dusk)

Seu ambiente de desenvolvimento com o Starter Kit deve estar pronto para uso.

## 7. Uso Básico

### 7.1. Com Laravel Sail

1.  **Iniciar Containers (se não estiverem rodando):**
    ```bash
    ./vendor/bin/sail up -d
    ```

2.  **Acessar a Aplicação:**
    *   Abra seu navegador e acesse `http://localhost:8000` (ou a porta definida em `APP_PORT`).
    *   Páginas de autenticação: `/login` (Senha Única), `/login/local`, `/register`.
    *   Painel administrativo: `/admin` (requer autenticação e role Admin)

3.  **Parar Containers:**
    ```bash
    ./vendor/bin/sail down
    ```

4.  **Comandos Úteis:**
    ```bash
    # Executar comandos Artisan
    ./vendor/bin/sail artisan migrate

    # Executar npm
    ./vendor/bin/sail npm run dev

    # Acessar shell do container
    ./vendor/bin/sail shell

    # Ver logs
    ./vendor/bin/sail logs
    ```

### 7.2. Instalação Tradicional

1.  **Iniciar Servidores (Desenvolvimento):**
    *   Para o servidor web PHP embutido:
        ```bash
        php artisan serve
        ```
    *   Para o servidor de desenvolvimento Vite (compilação de assets em tempo real):
        ```bash
        npm run dev
        ```

2.  **Acessar a Aplicação:**
    *   Abra seu navegador e acesse a `APP_URL` definida no `.env` (geralmente `http://localhost:8000`).
    *   Páginas de autenticação: `/login` (Senha Única), `/login/local`, `/register`.
    *   Painel administrativo: `/admin` (requer autenticação e role Admin)

### 7.3. Credenciais Padrão

*   Se você rodou `php artisan db:seed` (ou `migrate --seed`) após a instalação, pode usar o usuário local criado:
    *   **Email:** `test@example.com`
    *   **Senha:** `password`

## 8. Configurações Específicas da USP

Para que as funcionalidades de integração com a USP funcionem corretamente, você **precisa** configurar as credenciais apropriadas no seu arquivo `.env`.

*   **Senha Única:** Adicione e preencha as variáveis `SENHAUNICA_CALLBACK`, `SENHAUNICA_KEY`, `SENHAUNICA_SECRET`. Consulte a [documentação do `uspdev/senhaunica-socialite`](https://github.com/uspdev/senhaunica-socialite) para detalhes sobre como obter essas credenciais.
*   **Replicado:** Adicione e preencha as variáveis `REPLICADO_HOST`, `REPLICADO_PORT`, `REPLICADO_DATABASE`, `REPLICADO_USERNAME`, `REPLICADO_PASSWORD`, `REPLICADO_CODUND`, `REPLICADO_CODBAS`. Consulte a [documentação do `uspdev/replicado`](https://github.com/uspdev/replicado) para detalhes.

*Instruções detalhadas sobre a configuração e uso dessas integrações podem ser encontradas na [Wiki do Projeto](https://github.com/ime-usp-br/laravel_12_starter_kit/wiki).*

## 9. Desenvolvimento

### 9.1. Workflow de Desenvolvimento

Este projeto segue uma metodologia **Ágil/Kanban** com Issues atômicas e rastreabilidade completa.

**Processo:**
1. **Issue** → Criar Issue atômica no GitHub (use templates em `templates/issue_bodies/`)
2. **Branch** → `feature/<ID>-descricao`, `fix/<ID>-descricao`, etc.
3. **Commits** → Atômicos e frequentes com `#<ID>` na mensagem
4. **PR** → Pull Request vinculado com `Closes #<ID>`
5. **Merge** → Após CI passar e auto-revisão

**Conventional Commits:**
```bash
feat(pedido): adiciona validação de saldo (#45)
fix(cota): corrige cálculo de vínculos múltiplos (#52)
chore(deps): atualiza Laravel para 12.1 (#60)
```

**Padrões Obrigatórios:**
- Controllers enxutos (sem lógica de negócio)
- Services para lógica complexa
- Form Requests para validação
- Todo texto visível usa `__()`
- DocBlocks em métodos públicos

*Para detalhes completos, consulte [`docs/guia_de_desenvolvimento.md`](./docs/guia_de_desenvolvimento.md) e [`docs/padroes_codigo_boas_praticas.md`](./docs/padroes_codigo_boas_praticas.md).*

### 9.2. Padrões de Nomenclatura

| Elemento | Convenção | Exemplo |
|----------|-----------|---------|
| Controller | Singular + `Controller` | `PedidoController` |
| Model | Singular | `Consumidor`, `Pedido` |
| Service | Substantivo + `Service` | `CotaService` |
| Form Request | `{Verbo}{Model}Request` | `StorePedidoRequest` |
| View | `kebab-case` | `show-pedido.blade.php` |
| Rota (URI) | `kebab-case`, Plural | `/pedidos`, `/cotas-regulares` |
| Rota (nome) | `dot.notation` | `pedidos.store` |

### 9.3. Estrutura de Diretórios Principal

```
app/
├── Http/
│   ├── Controllers/        # Controllers enxutos
│   │   └── Admin/          # Área administrativa
│   └── Requests/           # Form Requests (validação)
├── Models/                 # Modelos Eloquent
├── Services/               # Lógica de negócio
│   ├── CotaService.php
│   ├── PedidoService.php
│   └── ReplicadoService.php
└── ...

docs/
├── analise/                # Análise do sistema LEGADO
│   ├── 01-mapeamento-funcionalidades.md
│   ├── 02-modelo-de-dados.md
│   ├── 03-regras-de-negocio.md
│   └── 04-perfis-e-permissoes.md
└── arquitetura/            # Arquitetura do NOVO sistema
    ├── 05-mapeamento-modelo-dados.md
    └── 06-desenho-arquitetura.md
```

## 10. Ferramentas e Qualidade de Código

Este Starter Kit inclui ferramentas para ajudar a manter a qualidade e a consistência do código:

*   **Laravel Pint:** Formatador de código automático (PSR-12).
    *   Para formatar: `vendor/bin/pint`
    *   Para verificar (CI): `vendor/bin/pint --test`
*   **Larastan (PHPStan):** Ferramenta de análise estática para encontrar erros sem executar o código.
    *   Para analisar: `vendor/bin/phpstan analyse`
*   **EditorConfig:** Arquivo `.editorconfig` na raiz para padronizar configurações básicas do editor (indentação, fim de linha, etc.). Garanta que seu editor tenha o plugin EditorConfig instalado e ativado.

## 11. Testes

*   **Executando Testes PHPUnit (Unitários e Feature):** Use o comando Artisan:
    ```bash
    php artisan test
    ```
*   **Executando Testes Dusk (Browser / End-to-End):** Rodar testes Dusk requer que **o servidor da aplicação e o ChromeDriver estejam rodando simultaneamente** antes de executar o comando de teste.
    1.  **Terminal 1 - Servidor da Aplicação:**
        ```bash
        php artisan serve
        ```
        *(Mantenha este terminal rodando)*
    2.  **Terminal 2 - ChromeDriver:**
        *   **Problema Comum:** Em alguns ambientes, o comando `php artisan dusk:chrome-driver` pode *não* manter o processo rodando como esperado, saindo imediatamente após confirmar a instalação.
        *   **Solução Manual:** Se o comando acima sair imediatamente, inicie o ChromeDriver manualmente, **especificando a porta 9515** (ou a porta definida em `DUSK_DRIVER_URL` no seu `.env.dusk.local`). Encontre o executável correto para seu sistema operacional dentro de `./vendor/laravel/dusk/bin/` e execute-o com a flag `--port`:
          ```bash
          # Exemplo para Linux:
          ./vendor/laravel/dusk/bin/chromedriver-linux --port=9515

          # Exemplo para macOS (Intel):
          # ./vendor/laravel/dusk/bin/chromedriver-mac-x64 --port=9515

          # Exemplo para macOS (Apple Silicon):
          # ./vendor/laravel/dusk/bin/chromedriver-mac-arm64 --port=9515

          # Exemplo para Windows (use Git Bash ou similar):
          # ./vendor/laravel/dusk/bin/chromedriver-win.exe --port=9515
          ```
        *(Mantenha este terminal rodando. Você deve ver uma mensagem como "ChromeDriver was started successfully on port 9515.")*
    3.  **Terminal 3 - Executar Testes Dusk:**
        ```bash
        php artisan dusk
        ```
*   **Fakes para Dependências USP:** O kit inclui classes `Fake` (ex: `FakeReplicadoService`, `FakeSenhaUnicaSocialiteProvider`) para facilitar a escrita de testes que interagem com as funcionalidades da Senha Única ou Replicado sem depender dos serviços reais (Planejado).

## 12. Documentação

### 12.1. Estrutura da Documentação

**📁 Raiz:**
- `README.md` - Este arquivo (visão geral do projeto)
- `CLAUDE.md` - Instruções detalhadas para Claude Code
- `CHANGELOG.md` - Histórico de mudanças

**📁 docs/**
- **Guias de Desenvolvimento:**
  - `guia_de_desenvolvimento.md` - Workflow, metodologia, ferramentas
  - `padroes_codigo_boas_praticas.md` - Padrões obrigatórios
  - `versionamento_documentacao.md` - Estratégia de versionamento

**📁 docs/analise/** (Sistema Legado)
- `01-mapeamento-funcionalidades.md` - Funcionalidades do sistema Java/JSF
- `02-modelo-de-dados.md` - Estrutura de dados do sistema antigo
- `03-regras-de-negocio.md` - Regras de negócio identificadas
- `04-perfis-e-permissoes.md` - Perfis e permissões do legado

**📁 docs/arquitetura/** (Novo Sistema)
- `05-mapeamento-modelo-dados.md` - Estrutura de dados Laravel
- `06-desenho-arquitetura.md` - Arquitetura de software

**📁 docs/** (Infraestrutura)
- `DEPLOYMENT.md` - Guia de deployment
- `QUEUE_SETUP.md` - Configuração de filas
- `QUEUE_EXAMPLES.md` - Exemplos de uso de filas

### 12.2. Versionamento da Documentação

**IMPORTANTE:** Todos os arquivos `.md` (exceto `LICENSE` e `CHANGELOG.md`) **DEVEM** incluir cabeçalho:
```markdown
**Versão:** X.Y.Z
**Data:** YYYY-MM-DD
```

A versão deve ser atualizada no commit de preparação de release e reflete a tag SemVer do código.

*Para detalhes, consulte [`docs/versionamento_documentacao.md`](./docs/versionamento_documentacao.md).*

## 13. Como Contribuir

Contribuições são bem-vindas! Para garantir um desenvolvimento organizado e rastreável, siga o fluxo descrito no **[Guia de Estratégia de Desenvolvimento](./docs/guia_de_desenvolvimento.md)**.

Em resumo:

1.  Identifique ou crie uma **Issue** atômica no GitHub descrevendo a tarefa (bug, feature, chore).
2.  Crie um **Branch** específico para a Issue a partir do branch principal (`main` ou `develop`).
3.  Faça **Commits Atômicos** e frequentes, sempre referenciando a Issue ID na mensagem (`#<ID>`).
4.  Abra um **Pull Request (PR)** claro, vinculando-o à Issue (`Closes #<ID>`).
5.  Aguarde a revisão (mesmo que seja auto-revisão) e a passagem da CI.
6.  Faça o **Merge** do PR.

## 14. Roadmap

### Fase Atual: Implementação Inicial 🚧

**Concluído:**
- ✅ Análise completa do sistema legado
- ✅ Definição da arquitetura do novo sistema
- ✅ Mapeamento do modelo de dados
- ✅ Documentação de regras de negócio
- ✅ Instalação do Laravel 12 Starter Kit

**Em Progresso:**
- 🔄 Criação de migrations (models: `Consumidor`, `Pedido`, `ItemPedido`, `Produto`, `CotaRegular`, `CotaEspecial`)
- 🔄 Implementação dos Services (`CotaService`, `PedidoService`, `ReplicadoService`)
- 🔄 Desenvolvimento dos Controllers
- 🔄 Criação dos Form Requests

**Próximos Passos:**
- 📋 Desenvolvimento das interfaces TALL Stack (Balcão, Entrega, Admin)
- 📋 Implementação de testes (Unit, Feature, Dusk)
- 📋 Configuração de permissões (roles ADM/OPR)
- 📋 Integração com Filament Admin
- 📋 Deploy em ambiente de testes

## 15. Licença

Este projeto é licenciado sob a **Licença MIT**. Veja o arquivo [LICENSE](./LICENSE) para mais detalhes.
