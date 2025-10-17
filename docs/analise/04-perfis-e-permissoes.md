### **Visão Geral dos Perfis de Usuário**

O sistema `cotac` define quatro papéis distintos, cada um com um conjunto específico de responsabilidades e acesso a funcionalidades. Dois desses papéis (`Administrador` e `Operador`) são para usuários autenticados que gerenciam o sistema, enquanto os outros dois (`Consumidor` e `Atendente`) representam interações de usuários não autenticados nas interfaces públicas.

---

### **1. Descrição Detalhada dos Perfis e Permissões**

#### **1.1. Administrador (ADM)**

O Administrador é o superusuário do sistema, com acesso irrestrito a todas as funcionalidades de configuração, gerenciamento e auditoria. Este perfil é responsável por manter o sistema operacional e suas regras de negócio atualizadas.

**Análise dos Arquivos:** Acesso a todas as páginas restritas, verificado com `rendered="#{UsuarioLogado.adm}"` em `header-menu.xhtml` e nas respectivas páginas.

**Permissões:**
*   **Gestão de Cotas:**
    *   Criar, editar e excluir cotas regulares baseadas em vínculo (`cota.xhtml`).
    *   Criar, editar e excluir cotas especiais para usuários específicos (`cota_especial.xhtml`).
*   **Gestão de Produtos:**
    *   Cadastrar, editar e remover os produtos disponíveis para consumo (`produto.xhtml`).
*   **Gestão de Usuários e Acessos:**
    *   Criar, editar e excluir contas de usuários (ADM e OPR) (`usuario.xhtml`).
    *   Associar e desassociar papéis (perfis) aos usuários.
    *   Gerar novas senhas para os usuários do sistema.
    *   Gerenciar os papéis de acesso (`papel.xhtml`).
*   **Consulta e Auditoria:**
    *   Acessar o extrato completo de consumo, com filtros por pessoa, período ou geral (`extrato.xhtml`).
    *   Visualizar os logs de auditoria do sistema para rastrear todas as operações relevantes (`log.xhtml`).
*   **Ações de Outros Perfis:**
    *   Pode realizar todas as ações de um Operador.

#### **1.2. Operador (OPR)**

O Operador é um perfil com acesso limitado, focado primariamente em tarefas de consulta e monitoramento, sem permissão para alterar configurações críticas do sistema.

**Análise dos Arquivos:** Acesso verificado com `rendered="#{UsuarioLogado.adm || UsuarioLogado.opr}"` no arquivo `extrato.xhtml`.

**Permissões:**
*   **Consulta de Extrato:**
    *   Acessar e consultar o extrato de consumo de todos os usuários (`extrato.xhtml`).

O Operador **não tem acesso** ao gerenciamento de cotas, produtos, usuários ou logs.

#### **1.3. Consumidor (Usuário Não Autenticado)**

O Consumidor representa qualquer pessoa que utiliza a interface do balcão para realizar um pedido. Este papel não requer login.

**Análise dos Arquivos:** `pedido.xhtml`, `MbPedido.java`.

**Ações Permitidas:**
*   **Autoidentificação:** Informar o N° USP para iniciar um pedido.
*   **Consulta de Saldo:** Visualizar a cota e o saldo disponíveis para o mês corrente.
*   **Realizar Pedido:**
    *   Selecionar produtos da lista de itens disponíveis.
    *   Adicionar e remover itens do carrinho de compras.
    *   Finalizar o pedido, desde que o saldo seja suficiente.
*   **Cancelar Pedido:** Desistir do pedido a qualquer momento antes da finalização.

#### **1.4. Atendente (Usuário Não Autenticado)**

A Atendente utiliza a interface de entrega para gerenciar os pedidos que foram finalizados pelos consumidores. Este papel também não requer login.

**Análise dos Arquivos:** `entrega.xhtml`, `MbEntrega.java`.

**Ações Permitidas:**
*   **Visualizar Pedidos Pendentes:** Ver uma lista em tempo real de todos os pedidos com status "REALIZADO".
*   **Confirmar Entrega:** Marcar um pedido como "ENTREGUE", removendo-o da fila de pendências.

---

### **2. Matriz de Acesso a Funcionalidades**

A tabela abaixo resume as permissões de cada perfil, proporcionando uma visão clara das responsabilidades no sistema.

| Funcionalidade | Administrador (ADM) | Operador (OPR) | Consumidor / Atendente |
| :--- | :---: | :---: | :---: |
| **Fluxo de Balcão** | | | |
| Realizar Pedido | ➖ | ➖ | ✅ |
| Marcar Pedido como Entregue | ➖ | ➖ | ✅ |
| **Gestão de Cotas** | | | |
| Gerenciar Cotas Regulares (por Vínculo) | ✅ | ❌ | ❌ |
| Gerenciar Cotas Especiais (por N° USP) | ✅ | ❌ | ❌ |
| **Gestão do Sistema** | | | |
| Gerenciar Produtos | ✅ | ❌ | ❌ |
| Gerenciar Usuários e Senhas | ✅ | ❌ | ❌ |
| Gerenciar Papéis de Acesso | ✅ | ❌ | ❌ |
| **Consulta e Auditoria** | | | |
| Consultar Extrato de Consumo | ✅ | ✅ | ❌ |
| Visualizar Logs do Sistema | ✅ | ❌ | ❌ |