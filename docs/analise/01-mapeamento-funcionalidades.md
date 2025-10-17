### **Visão Geral do Sistema**

O `cotac` é um sistema de gestão para a cota de café, projetado para controlar o consumo de produtos (como café) por membros da comunidade acadêmica (docentes, funcionários, etc.). Ele opera com base em cotas mensais atribuídas aos usuários de acordo com seu vínculo com a instituição ou por meio de cotas especiais.

O sistema possui três fluxos principais:
1.  **Fluxo do Consumidor:** Realizado no balcão para fazer um pedido.
2.  **Fluxo da Atendente:** Uma interface para visualizar e dar baixa nos pedidos realizados.
3.  **Fluxo Administrativo:** Uma área restrita para gerenciar as regras, produtos, usuários e consultar dados do sistema.

---

### **1. Autenticação e Perfis de Usuário**

O acesso à área administrativa do sistema é protegido e requer autenticação. Existem dois métodos de login:

*   **Autenticação Local (`AutenticadorLocal.java`):** Destinada a operadores e administradores que possuem um cadastro interno no sistema. O acesso é feito através do N° USP e uma senha própria, gerenciada pelo `cotac`.
*   **Autenticação via Senha Única USP (`AutenticadorUSP.java`):** Permite que usuários com credenciais da universidade acessem o sistema. Após a autenticação via OAuth da USP, o sistema verifica se o usuário (identificado pelo seu N° USP) possui um cadastro e permissões na base de dados local.

O sistema opera com os seguintes perfis (papéis) de usuário, que determinam o acesso às funcionalidades:

*   **`ADM` (Administrador):** Possui acesso total a todas as funcionalidades de gestão, incluindo configuração de cotas, gerenciamento de produtos, administração de usuários e visualização de logs e extratos.
*   **`OPR` (Operador):** Perfil com acesso limitado, focado na consulta de extratos de consumo.

---

### **2. Fluxo do Consumidor (Balcão)**

Este fluxo é realizado na interface pública (`pedido.xhtml`) e não exige login. Ele permite que qualquer pessoa com um N° USP válido faça um pedido, utilizando sua cota mensal.

**Análise dos Arquivos:** `pedido.xhtml`, `MbPedido.java`, `DaoReplicado.java`

**Etapas do Fluxo:**

1.  **Identificação:**
    *   O consumidor informa seu N° USP em um campo de busca.
    *   O sistema consulta a base de dados replicada da USP (`DaoReplicado`) para validar o número e obter os dados da pessoa (nome e vínculos, como "Docente", "Servidor", etc.).

2.  **Cálculo de Cota e Saldo:**
    *   Uma vez identificado, o sistema calcula a cota do usuário para o mês corrente:
        *   Primeiro, verifica se existe uma "cota especial" definida para aquele N° USP. Se sim, esse valor tem prioridade.
        *   Caso contrário, o sistema verifica os vínculos da pessoa e atribui a maior cota correspondente definida nas "cotas regulares".
    *   Em seguida, o sistema calcula o **saldo**, subtraindo da cota total o valor de todos os pedidos já realizados pela pessoa no mês corrente.

3.  **Seleção de Produtos:**
    *   Se o saldo for maior que zero, a interface exibe os produtos disponíveis (ex: "Café", "Pão de Queijo").
    *   O consumidor clica nos produtos que deseja, e cada clique adiciona uma unidade do item ao seu pedido. É possível adicionar múltiplos itens e remover itens já adicionados.

4.  **Finalização do Pedido:**
    *   O sistema valida se o valor total do pedido não ultrapassa o saldo disponível.
    *   Ao confirmar, o pedido é salvo no banco de dados com o status "REALIZADO" e um número de identificação é gerado e exibido na tela.
    *   O saldo do consumidor é implicitamente atualizado para os próximos pedidos no mesmo mês.

5.  **Sem Saldo:**
    *   Se, no momento da identificação, o saldo do consumidor for zero ou negativo, o sistema exibe uma mensagem informando que não há cota disponível e encerra o fluxo.

---

### **3. Fluxo da Atendente (Entrega)**

Este fluxo ocorre em uma interface pública (`entrega.xhtml`) projetada para ser exibida em uma tela no balcão da cafeteria, permitindo que a atendente gerencie os pedidos pendentes.

**Análise dos Arquivos:** `entrega.xhtml`, `MbEntrega.java`

**Funcionalidades:**

*   **Visualização de Pedidos:** A tela exibe uma lista de todos os pedidos com o status "REALIZADO".
*   **Atualização Automática:** A lista é atualizada automaticamente a cada segundo (`p:poll`), garantindo que novos pedidos apareçam em tempo real.
*   **Marcação de Entrega:** Para cada pedido na lista, há um botão "Entregue". Ao ser clicado, o status do pedido é alterado para "ENTREGUE", e ele desaparece da lista de pendências.

---

### **4. Fluxo Administrativo (Área Restrita)**

A área administrativa é composta por um conjunto de telas para a gestão completa do sistema. O acesso a cada funcionalidade é determinado pelo perfil do usuário logado (`ADM` ou `OPR`).

| Funcionalidade | Descrição | Arquivos Relacionados | Perfil de Acesso |
| :--- | :--- | :--- | :--- |
| **Gerenciamento de Cotas Regulares** | Permite criar, editar e excluir as cotas-padrão associadas aos vínculos da USP (ex: Docentes recebem 20 unidades, Servidores recebem 15). | `cota.xhtml`, `MbCota.java` | `ADM` |
| **Gerenciamento de Cotas Especiais** | Permite atribuir uma cota mensal específica para um N° USP, que sobrescreve qualquer cota regular. Útil para casos excepcionais. | `cota_especial.xhtml`, `MbCotaEspecial.java` | `ADM` |
| **Gerenciamento de Produtos** | Permite criar, editar e excluir os produtos que podem ser consumidos (ex: Café), definindo seu nome e valor em "unidades" de cota (geralmente 1). | `produto.xhtml`, `MbProduto.java` | `ADM` |
| **Gerenciamento de Usuários** | Permite criar, editar e excluir usuários do sistema (`ADM` e `OPR`), associar papéis (perfis) e redefinir senhas para o acesso local. | `usuario.xhtml`, `MbUsuario.java` | `ADM` |
| **Gerenciamento de Papéis** | Permite criar ou remover os perfis de acesso do sistema (ex: ADM, OPR). | `papel.xhtml`, `MbPapel.java` | `ADM` |
| **Consulta de Extrato** | Permite visualizar o histórico de pedidos. O administrador pode filtrar por todos os pedidos do mês, todos os pedidos históricos ou buscar os pedidos de uma pessoa específica (por N° USP, nome ou e-mail). | `extrato.xhtml`, `MbExtrato.java` | `ADM`, `OPR` |
| **Visualização de Logs** | Exibe um log de auditoria detalhado de todas as operações importantes realizadas no sistema, como autenticações, erros e alterações de dados, incluindo quem realizou a ação e quando. | `log.xhtml`, `MbLog.java` | `ADM` |