### **`06-desenho-arquitetura.md`**

**Versão:** 1.0
**Data:** 17/10/2025

### **1. Introdução**

Este documento define a arquitetura de software para a nova implementação do sistema `cotac` em Laravel 12. O objetivo é estabelecer uma estrutura de código limpa, organizada e testável, que separe claramente as responsabilidades e siga as melhores práticas do ecossistema Laravel.

A arquitetura foi projetada para ser modular, facilitando a manutenção e futuras expansões, ao mesmo tempo em que garante a integridade e a segurança dos dados e das regras de negócio.

---

### **2. Visão Geral da Arquitetura**

A aplicação seguirá uma arquitetura em camadas para garantir a separação de responsabilidades. O fluxo de uma requisição HTTP passará pelas seguintes camadas:

1.  **Controladores (Controllers):** Atuam como o ponto de entrada. São responsáveis por receber a requisição, chamar a validação e orquestrar as chamadas para as classes de serviço. Devem ser "enxutos" (*lean*), sem conter regras de negócio.
2.  **Form Requests:** Interceptam a requisição antes do controlador para validar todos os dados de entrada. Garantem que apenas dados válidos cheguem à camada de negócio.
3.  **Serviços (Services):** Onde reside a lógica de negócio principal e complexa. Serviços são agnósticos ao HTTP e podem ser reutilizados em diferentes contextos (ex: Controladores, Comandos Artisan).
4.  **Modelos (Models):** Representam as entidades do banco de dados e definem seus relacionamentos Eloquent. Interagem diretamente com a base de dados.

---

### **3. Detalhamento das Camadas**

#### **3.1. Controladores (Controllers)**

*   **Responsabilidade:** Orquestrar o fluxo da requisição.
*   **Localização:** `app/Http/Controllers/`

| Controlador | Namespace | Responsabilidade |
| :--- | :--- | :--- |
| `PedidoController` | `App\Http\Controllers` | Gerencia o fluxo do consumidor no balcão: busca de consumidor, cálculo de saldo e criação de pedido. |
| `EntregaController` | `App\Http\Controllers` | Gerencia a interface da atendente: exibe pedidos pendentes e os marca como entregues. |
| `CotaRegularController` | `App\Http\Controllers\Admin` | CRUD para o gerenciamento de cotas por vínculo. (Área restrita) |
| `CotaEspecialController`| `App\Http\Controllers\Admin` | CRUD para o gerenciamento de cotas especiais por consumidor. (Área restrita) |
| `ProdutoController` | `App\Http\Controllers\Admin` | CRUD para o gerenciamento de produtos. (Área restrita) |
| `UsuarioController` | `App\Http\Controllers\Admin` | CRUD para usuários administrativos (`users`). (Área restrita) |
| `ExtratoController` | `App\Http\Controllers\Admin` | Lógica para consulta de extratos de consumo. (Área restrita) |

#### **3.2. Serviços (Services)**

*   **Responsabilidade:** Implementar a lógica de negócio.
*   **Localização:** `app/Services/`

| Serviço | Responsabilidade |
| :--- | :--- |
| `CotaService` | **Lógica central do sistema.** Responsável por calcular a cota e o saldo de um consumidor para o mês corrente, consultando o `ReplicadoService`, `CotaRegular`, `CotaEspecial` e os pedidos já realizados. |
| `PedidoService` | Responsável por toda a lógica de criação de um pedido. Recebe os dados validados e executa a criação do `Pedido` e seus `ItemPedido` dentro de uma transação de banco de dados. |
| `ReplicadoService` | Encapsula todas as chamadas ao pacote `uspdev/replicado`. Responsável por buscar dados de uma pessoa (nome, e-mail) e seus vínculos ativos no IME. |

#### **3.3. Form Requests**

*   **Responsabilidade:** Validar todos os dados de entrada das requisições HTTP.
*   **Localização:** `app/Http/Requests/`

| Form Request | Rota Associada | Validações Principais |
| :--- | :--- | :--- |
| `StorePedidoRequest` | `POST /pedidos` | Valida o N° USP (`codpes`) e a lista de produtos (`produtos`). |
| `StoreCotaRegularRequest`| `POST /admin/cotas-regulares` | Valida se `vinculo` é único e se `valor` é numérico. |
| `UpdateEntregaRequest` | `PUT /entregas/{pedido}` | Valida se o `Pedido` existe e pode ser marcado como entregue. |

---

### **4. Autenticação e Autorização**

#### **4.1. Autenticação**
A área administrativa será protegida pelo guard `web` padrão do Laravel. A autenticação dos perfis `ADM` e `OPR` utilizará o scaffolding de autenticação do `laravel_12_starter_kit`, que gerencia login, logout e reset de senha para a tabela `users`.

#### **4.2. Autorização**
A autorização será implementada com o pacote `spatie/laravel-permission`.

*   **Papéis (Roles):** `ADM`, `OPR`.
*   **Permissões (Permissions):** Serão criadas permissões granulares para cada ação (ex: `gerenciar_cotas`, `gerenciar_produtos`, `ver_extratos`).
*   **Implementação:** As rotas administrativas serão protegidas por middleware do `spatie/laravel-permission`.

| Recurso | Ação | Rota | Perfil Autorizado |
| :--- | :--- | :--- | :--- |
| Cotas Regulares | Listar, Criar, Editar, Excluir | `admin/cotas-regulares/*` | `ADM` |
| Cotas Especiais | Listar, Criar, Editar, Excluir | `admin/cotas-especiais/*` | `ADM` |
| Produtos | Listar, Criar, Editar, Excluir | `admin/produtos/*` | `ADM` |
| Usuários | Listar, Criar, Editar, Excluir | `admin/usuarios/*` | `ADM` |
| Extratos | Consultar | `admin/extratos` | `ADM`, `OPR` |
| Auditoria | Visualizar | `admin/auditoria` | `ADM` |

---

### **5. Implementação das Regras de Negócio (Fluxos Chave)**

#### **5.1. Lógica de Cálculo de Cota e Saldo (`CotaService`)**

A `CotaService` terá um método principal, `calcularSaldoParaConsumidor(Consumidor $consumidor)`, que seguirá os seguintes passos:

1.  **Obter Cota Mensal:**
    *   Primeiro, verifica se o `$consumidor` possui uma `CotaEspecial` ativa. Se sim, seu valor é a cota do mês.
    *   Se não houver cota especial, invoca o `ReplicadoService` para obter os vínculos da pessoa associada ao `codpes` do consumidor.
    *   Filtra os vínculos para considerar apenas os **ativos** e que pertencem à **unidade do IME** (a verificação de unidade é um requisito novo para aumentar a robustez).
    *   Com os vínculos válidos, consulta o `Model CotaRegular` e encontra a maior cota correspondente.
    *   Se nenhum vínculo corresponder, a cota é zero.

2.  **Calcular Gasto Mensal:**
    *   Consulta o `Model Pedido` para somar o valor de todos os pedidos (`ItemPedido->quantidade * Produto->valor`) realizados pelo consumidor no mês corrente.

3.  **Calcular Saldo:**
    *   Retorna o resultado de `Cota Mensal - Gasto Mensal`.

#### **5.2. Fluxo de Pedido no Balcão (`PedidoController`)**

O método `store(StorePedidoRequest $request)` do `PedidoController` orquestrará a criação de um pedido:

1.  **Validação:** A validação dos dados de entrada (N° USP e produtos) será feita automaticamente pela `StorePedidoRequest`.
2.  **Obtenção do Consumidor:**
    *   O controlador chama `ReplicadoService::buscarPessoa($request->codpes)` para validar se o N° USP existe e obter o nome. Se não existir, retorna um erro de validação.
    *   Usa `Consumidor::firstOrCreate(['codpes' => $request->codpes], ['nome' => $nomeDoReplicado])` para garantir que o consumidor exista na base de dados local.
3.  **Verificação de Saldo:**
    *   Invoca `CotaService::calcularSaldoParaConsumidor($consumidor)`.
    *   A `StorePedidoRequest` terá uma regra de validação `after()` que usa este serviço para verificar se o valor total do pedido (`$request->produtos`) é menor ou igual ao saldo disponível. Se não for, a validação falha.
4.  **Criação do Pedido:**
    *   Se a validação passar, o controlador chama `PedidoService::criarPedido($consumidor, $request->validated('produtos'))`.
    *   O `PedidoService` executa, dentro de uma **transação de banco de dados**, a criação do `Pedido` com status `REALIZADO` e dos seus respectivos `ItemPedido`.
5.  **Resposta:** Retorna uma resposta de sucesso (JSON) com o número do pedido gerado.

---

### **6. Auditoria e Logs**

#### **6.1. Auditoria de Alterações de Dados**

Para rastrear quem alterou o quê e quando, o pacote `owen-it/laravel-auditing` será utilizado. Os seguintes modelos serão configurados com a *trait* `OwenIt\Auditing\Contracts\Auditable`:

*   `App\Models\CotaRegular`
*   `App\Models\CotaEspecial`
*   `App\Models\Produto`
*   `App\Models\User` (para rastrear mudanças em permissões ou dados de operadores/admins)

Qualquer alteração (create, update, delete) nesses modelos será registrada automaticamente na tabela `audits`.

#### **6.2. Logs de Sistema**

Eventos importantes de sistema, que não são alterações de dados em modelos, serão registrados usando o *logging* padrão do Laravel (`Log` facade). Exemplos:
*   Falhas de autenticação na área administrativa.
*   Erros de comunicação com o `Replicado`.
*   Exceções não tratadas.