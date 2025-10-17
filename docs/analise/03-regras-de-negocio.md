### **1. Lógica de Cálculo de Cota e Saldo**

Esta é a regra central do sistema, definindo o poder de consumo de cada pessoa. A lógica é executada sempre que um consumidor se identifica no balcão.

**Análise dos Arquivos:** `MbPedido.java`, `DaoCota.java`, `DaoCotaEspecial.java`, `DaoPedido.java`

#### **1.1. Cálculo da Cota Mensal (`calcularCota()`)**

A cota é o limite máximo de unidades que uma pessoa pode consumir no mês corrente. O cálculo segue uma ordem de prioridade:

1.  **Verificação de Cota Especial:** O sistema primeiro verifica na tabela `COTA_ESPECIAL` se existe um registro para o `codpes` (N° USP) da pessoa.
    *   **Se uma cota especial for encontrada**, seu valor (`valor`) é definido como a cota mensal da pessoa, e o cálculo termina aqui. Esta regra tem precedência sobre qualquer outra.

2.  **Cálculo por Cota Regular (Vínculo):** Se não houver cota especial, o sistema determina a cota com base nos vínculos da pessoa com a universidade.
    *   O sistema obtém a lista de todos os vínculos ativos da pessoa (ex: "DOCENTE", "SERVIDOR", "ALUNOPOS") a partir dos dados importados do Replicado.
    *   Em seguida, consulta a tabela `COTA` para obter todas as cotas-padrão definidas.
    *   Ele compara os vínculos da pessoa com os vínculos da tabela `COTA`.
    *   **Regra da Maior Cota:** Se uma pessoa possui múltiplos vínculos (ex: é servidor e aluno de pós-graduação), o sistema atribui a ela a **maior cota** entre todos os seus vínculos correspondentes. Por exemplo, se a cota para "SERVIDOR" é 15 e para "ALUNOPOS" é 10, a pessoa receberá uma cota de 15.
    *   Se a pessoa não possui nenhum vínculo que corresponda a uma cota definida, sua cota será 0.

#### **1.2. Cálculo do Saldo Disponível (`calcularSaldo()`)**

O saldo representa o que a pessoa ainda pode consumir no mês corrente.

1.  O sistema consulta a tabela `PEDIDO` para obter todos os pedidos realizados pela pessoa no mês e ano atuais (`buscarPedidosMesAtual`).
2.  Ele soma o valor total (em unidades) de todos os itens de todos esses pedidos.
3.  O saldo é então calculado pela fórmula: **`Saldo = Cota Mensal - Consumo Total do Mês`**.

Este saldo é recalculado a cada nova interação para garantir que a informação esteja sempre atualizada.

---

### **2. Lógica de Validação e Registro de Pedidos**

Este fluxo descreve como o sistema gerencia a criação e finalização de um pedido no balcão.

**Análise dos Arquivos:** `MbPedido.java`

#### **2.1. Adição de Itens ao Carrinho (`adicionarCarrinho()`)**

*   Quando um consumidor seleciona um produto, o sistema verifica se o item já existe no pedido atual.
*   Se sim, apenas incrementa a quantidade.
*   Se não, cria um novo `ItemPedido` e o adiciona à lista de itens do pedido.

#### **2.2. Finalização do Pedido (`finalizar()`)**

1.  **Validação de Saldo (Regra Crítica):** Antes de salvar o pedido, o sistema executa a validação mais importante do fluxo:
    *   `if (pessoa.getSaldo() < pedido.getTotalPedido())`
    *   Ele compara o **saldo disponível** da pessoa com o **valor total do pedido atual**.
    *   Se o valor do pedido for maior que o saldo, a operação é bloqueada, e uma mensagem de erro ("Sua cota é insuficiente para esse pedido") é exibida. O pedido não é registrado.

2.  **Registro do Pedido:** Se o saldo for suficiente, o pedido é persistido no banco de dados com todos os seus itens, a data/hora atual e o status inicial **`REALIZADO`**.

---

### **3. Lógica de Autenticação e Segurança**

Esta lógica se aplica ao acesso à área administrativa do sistema por operadores e administradores.

**Análise dos Arquivos:** `AutenticadorLocal.java`, `PasswordGenerator.java`, `pom.xml (commons-codec)`

1.  **Recuperação de Credenciais:** O sistema busca o `Usuario` na base de dados local pelo N° USP informado.
2.  **Geração do Hash:** A senha informada pelo usuário no formulário de login é combinada com o `salt` (uma string aleatória e única) armazenado para aquele usuário no banco de dados.
3.  **Algoritmo de Hash:** A combinação `senha + salt` é então processada pelo algoritmo **SHA-256**, usando a biblioteca `org.apache.commons.codec.digest.DigestUtils`.
    *   `DigestUtils.sha256Hex(senhaFornecida + usuario.getSalt())`
4.  **Comparação:** O hash resultante é comparado com o hash que está armazenado na coluna `senha` da tabela `USUARIO`.
5.  **Validação:** Se os hashes forem idênticos, a autenticação é bem-sucedida. Caso contrário, o acesso é negado. O uso de `salt` impede que duas senhas iguais resultem no mesmo hash e protege contra ataques de *rainbow table*.

---

### **4. Lógica de Gerenciamento de Usuários**

Esta lógica descreve como os administradores do sistema são criados e como suas senhas são gerenciadas.

**Análise dos Arquivos:** `MbUsuario.java`, `PasswordGenerator.java`

#### **4.1. Criação de Novos Usuários (`criarUsuario()`)**

1.  **Atribuição de Papel Padrão:** Por padrão, todo novo usuário criado recebe o papel **`OPR` (Operador)**. O administrador pode posteriormente promover o usuário para `ADM` na mesma interface.
2.  **Geração de Senha Aleatória:** Uma senha inicial segura e aleatória é gerada através da classe `PasswordGenerator.generatePassword()`.
3.  **Geração de Salt e Hash:** Um novo `salt` é gerado (`PasswordGenerator.generateSalt()`) e, juntamente com a senha recém-criada, é usado para gerar o hash SHA-256 que será armazenado.
4.  **Feedback para o Administrador:** Após a criação, o sistema exibe uma mensagem de sucesso na tela contendo a **senha gerada em texto plano**. Isso é feito para que o administrador possa comunicá-la ao novo usuário, que deverá trocá-la em um momento oportuno (embora o sistema não force a troca no primeiro login).

#### **4.2. Alteração de Senha (`changePassword()`)**

*   A lógica é idêntica à da criação de usuário: uma nova senha aleatória é gerada, um **novo salt** é criado e um novo hash é calculado e salvo, substituindo os dados antigos. A senha alterada também é exibida na tela para o administrador.