# UX Patterns - CotaC

Este documento descreve os padrões de UX implementados no sistema CotaC para garantir consistência e melhor experiência do usuário.

## 📋 Índice

- [Toast Notifications](#toast-notifications)
- [Loading States](#loading-states)
- [Form Validation](#form-validation)
- [Animations](#animations)
- [Accessibility](#accessibility)

---

## 🔔 Toast Notifications

### Descrição
Sistema unificado de notificações temporárias que aparecem no canto superior direito da tela.

### Quando Usar
- Feedback de sucesso após ações (ex: "Pedido criado com sucesso!")
- Mensagens de erro (ex: "NUSP não encontrado")
- Avisos importantes
- Informações contextuais

### Como Usar

#### 1. No Layout
Adicione o componente toast-container no layout:

```blade
<!-- resources/views/layouts/app.blade.php -->
<body>
    <!-- Conteúdo -->
    
    <x-toast-container />
</body>
```

#### 2. Em Componentes Livewire
Dispatch um evento toast com tipo e mensagem:

```php
// Sucesso
$this->dispatch('toast', type: 'success', message: __('Operação realizada com sucesso!'));

// Erro
$this->dispatch('toast', type: 'error', message: __('Ocorreu um erro.'));

// Aviso
$this->dispatch('toast', type: 'warning', message: __('Atenção!'));

// Informação
$this->dispatch('toast', type: 'info', message: __('Informação importante.'));
```

### Tipos Disponíveis

| Tipo | Cor | Uso |
|------|-----|-----|
| `success` | Verde | Ações bem-sucedidas |
| `error` | Vermelho | Erros e falhas |
| `warning` | Amarelo | Avisos importantes |
| `info` | Azul | Informações gerais |

### Características
- ✅ Auto-dismiss após 5 segundos
- ✅ Suporte a dark mode
- ✅ ARIA live region para acessibilidade
- ✅ Animação slide-in-down
- ✅ Empilhamento de múltiplas notificações

---

## ⏳ Loading States

### 1. Spinners

#### Descrição
Indicadores visuais de carregamento para ações assíncronas.

#### Quando Usar
- Botões que executam ações assíncronas
- Durante requisições ao servidor
- Processamento de dados

#### Como Usar

```blade
<button wire:click="salvar" wire:loading.attr="disabled">
    <span wire:loading.remove wire:target="salvar">
        Salvar
    </span>
    <span wire:loading wire:target="salvar" class="flex items-center gap-2">
        <x-spinner size="sm" color="white" />
        Salvando...
    </span>
</button>
```

#### Tamanhos Disponíveis
- `sm` - 16x16px (para botões)
- `md` - 24x24px (padrão)
- `lg` - 32x32px
- `xl` - 48x48px

#### Cores Disponíveis
- `blue` (padrão)
- `white` (para botões coloridos)
- `gray`
- `green`
- `red`

### 2. Skeleton Loaders

#### Descrição
Placeholders animados que mostram a estrutura do conteúdo enquanto carrega.

#### Quando Usar
- Carregamento de listas
- Carregamento de cards
- Carregamento de dados do usuário
- Qualquer conteúdo que demore >200ms para carregar

#### Como Usar

```blade
<!-- Durante carregamento -->
<div wire:loading wire:target="buscar">
    <x-skeleton type="card" />
    <div class="grid grid-cols-3 gap-4 mt-4">
        <x-skeleton type="card" />
        <x-skeleton type="card" />
        <x-skeleton type="card" />
    </div>
</div>

<!-- Conteúdo real -->
<div wire:loading.remove wire:target="buscar">
    <!-- Seu conteúdo aqui -->
</div>
```

#### Tipos Disponíveis

| Tipo | Uso | Dimensões |
|------|-----|-----------|
| `text` | Linhas de texto | h-4 |
| `card` | Cards/blocos | h-32 |
| `avatar` | Avatares/ícones | 48x48px circular |
| `button` | Botões | 40x96px |

#### Múltiplas Linhas
```blade
<x-skeleton type="text" lines="3" />
```

---

## ✅ Form Validation

### Descrição
Validação em tempo real com feedback visual imediato.

### Quando Usar
- Campos de formulário importantes
- Validação de formato (ex: NUSP, email, CPF)
- Antes de submissão do formulário

### Como Usar

#### 1. No Componente Livewire

```php
class MeuComponente extends Component
{
    public string $campo = '';
    public ?bool $campoValid = null;

    public function validateCampo(): void
    {
        if (empty($this->campo)) {
            $this->campoValid = null;
            return;
        }

        // Sua lógica de validação
        if ($this->validarFormato($this->campo)) {
            $this->campoValid = true;
        } else {
            $this->campoValid = false;
        }
    }

    public function updatedCampo(): void
    {
        $this->campoValid = null; // Reset ao digitar
    }
}
```

#### 2. Na View

```blade
<x-text-input
    wire:model.live="campo"
    wire:blur="validateCampo"
    :validatable="true"
    :valid="$campoValid"
/>
<x-input-error :messages="$errors->get('campo')" />
```

### Estados Visuais

| Estado | Borda | Ícone | Quando |
|--------|-------|-------|--------|
| Neutro | Cinza | Nenhum | Não validado |
| Válido | Verde | ✓ | Validação passou |
| Inválido | Vermelho | ✗ | Validação falhou |

### Características
- ✅ Validação on-blur (ao sair do campo)
- ✅ Reset ao digitar
- ✅ Ícones visuais claros
- ✅ Mensagens de erro animadas
- ✅ Suporte a dark mode

---

## 🎨 Animations

### Animações Disponíveis

#### 1. fade-in
Aparecimento suave com opacidade.

```blade
<div class="animate-fade-in">
    Conteúdo
</div>
```

**Uso:** Empty states, conteúdo que aparece após carregamento

#### 2. slide-in-up
Desliza de baixo para cima.

```blade
<div class="animate-slide-in-up">
    Conteúdo
</div>
```

**Uso:** Cards, modais, conteúdo que entra na tela

#### 3. slide-in-down
Desliza de cima para baixo.

```blade
<div class="animate-slide-in-down">
    Conteúdo
</div>
```

**Uso:** Toasts, notificações, dropdowns

#### 4. slide-in-left
Desliza da direita para esquerda.

```blade
<div class="animate-slide-in-left">
    Conteúdo
</div>
```

**Uso:** Itens de lista, cards laterais

#### 5. scale-in
Cresce do centro.

```blade
<div class="animate-scale-in">
    Conteúdo
</div>
```

**Uso:** Modais, popups, elementos de destaque

#### 6. bounce-subtle
Bounce sutil.

```blade
<div class="animate-bounce-subtle">
    Conteúdo
</div>
```

**Uso:** Chamadas de atenção, badges, notificações

### Transitions Interativas

#### Hover Effects
```blade
<div class="transition-all hover:-translate-y-1 hover:shadow-lg">
    Card com hover
</div>
```

#### Active States
```blade
<button class="transition-all active:scale-95">
    Botão com feedback
</button>
```

### Boas Práticas
- ✅ Use animações sutis (200-300ms)
- ✅ Combine com `transition-all` para suavidade
- ✅ Evite animações em loops infinitos
- ✅ Teste em dispositivos mais lentos

---

## ♿ Accessibility

### Princípios Seguidos

#### 1. ARIA Attributes
Todos os componentes incluem atributos ARIA apropriados:

```blade
<!-- Spinner -->
<svg role="status" aria-label="{{ __('Loading...') }}">
    <!-- ... -->
</svg>

<!-- Toast Container -->
<div aria-live="polite" aria-atomic="true">
    <!-- ... -->
</div>

<!-- Input Error -->
<ul role="alert" aria-live="polite">
    <!-- ... -->
</ul>
```

#### 2. Keyboard Navigation
- ✅ Todos os botões são focáveis
- ✅ Estados de focus visíveis
- ✅ Tab order lógico

#### 3. Screen Readers
- ✅ Labels descritivos
- ✅ Live regions para mudanças dinâmicas
- ✅ Mensagens de erro anunciadas

#### 4. Color Contrast
- ✅ Contraste mínimo 4.5:1 para texto
- ✅ Contraste 3:1 para elementos UI
- ✅ Não depende apenas de cor para informação

### Checklist de Acessibilidade

- [ ] Todos os inputs têm labels
- [ ] Erros são anunciados para screen readers
- [ ] Loading states são anunciados
- [ ] Navegação por teclado funciona
- [ ] Contraste de cores adequado
- [ ] Foco visível em todos elementos interativos

---

## 📚 Exemplos Completos

### Exemplo 1: Formulário com Validação e Toast

```php
// Livewire Component
class FormularioExemplo extends Component
{
    public string $email = '';
    public ?bool $emailValid = null;

    public function validateEmail(): void
    {
        if (empty($this->email)) {
            $this->emailValid = null;
            return;
        }

        $this->emailValid = filter_var($this->email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function updatedEmail(): void
    {
        $this->emailValid = null;
    }

    public function salvar(): void
    {
        $this->validate(['email' => 'required|email']);

        // Processar...

        $this->dispatch('toast', type: 'success', message: __('Email salvo com sucesso!'));
    }
}
```

```blade
<!-- View -->
<form wire:submit="salvar">
    <div>
        <label for="email">Email</label>
        <x-text-input
            id="email"
            type="email"
            wire:model.live="email"
            wire:blur="validateEmail"
            :validatable="true"
            :valid="$emailValid"
        />
        <x-input-error :messages="$errors->get('email')" />
    </div>

    <button type="submit" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="salvar">
            Salvar
        </span>
        <span wire:loading wire:target="salvar" class="flex items-center gap-2">
            <x-spinner size="sm" color="white" />
            Salvando...
        </span>
    </button>
</form>
```

### Exemplo 2: Lista com Loading e Empty State

```blade
<!-- Loading -->
<div wire:loading wire:target="carregar">
    @for($i = 0; $i < 3; $i++)
        <x-skeleton type="card" class="mb-4" />
    @endfor
</div>

<!-- Conteúdo -->
<div wire:loading.remove wire:target="carregar">
    @forelse($items as $item)
        <div class="animate-slide-in-up">
            {{ $item->nome }}
        </div>
    @empty
        <div class="animate-fade-in text-center py-12">
            <p>Nenhum item encontrado</p>
        </div>
    @endforelse
</div>
```

---

## 🔄 Atualizações

**Última atualização:** 2025-11-25  
**Versão:** 1.0.0  
**Issue:** #37

Para sugestões ou melhorias nestes padrões, abra uma issue no repositório.
