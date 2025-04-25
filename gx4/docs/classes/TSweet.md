# Classe `TSweet`

**Namespace:** `GX4\Util`

## Descrição

A classe `TSweet` encapsula interações visuais com a biblioteca SweetAlert2 dentro do Adianti Framework. Fornece métodos prontos para exibir mensagens, confirmações e notificações tipo toast, tornando o código mais limpo e reutilizável.

---

## Métodos

### showMessage
```php
public static function showMessage(
    string $title,
    string $text,
    string $icon = 'success',
    string $textButton = 'OK',
    TAction $action = null,
    string $colorButton = '#236BB0'
): void
```
Exibe uma mensagem padrão com SweetAlert2.

**Parâmetros:**
- `title`: Título da mensagem.
- `text`: Conteúdo da mensagem.
- `icon`: Tipo de ícone (ex: `success`, `error`, `info`, etc.).
- `textButton`: Texto do botão de confirmação.
- `action`: Ação executada ao confirmar.
- `colorButton`: Cor do botão de confirmação.

---

### confirm
```php
public static function confirm(
    string $title,
    string $text,
    TAction $actionConfirm,
    string $textButtonConfirm = 'Confirmar',
    string $textButtonCancel = 'Cancelar',
    string $icon = 'question',
    string $focusConfirm = 'false',
    TAction $actionCancel = null,
    string $colorButtonConfirm = '#236BB0',
    string $colorButtonCancel = '#d14529'
): void
```
Exibe uma caixa de confirmação com opções de aceitar ou cancelar.

**Parâmetros:**
- `title`: Título da confirmação.
- `text`: Texto explicativo.
- `actionConfirm`: Ação ao confirmar.
- `textButtonConfirm`: Texto do botão de confirmação.
- `textButtonCancel`: Texto do botão de cancelamento.
- `icon`: Ícone da caixa.
- `focusConfirm`: Define foco inicial no botão confirmar (`true` ou `false`).
- `actionCancel`: Ação ao cancelar.
- `colorButtonConfirm`: Cor do botão confirmar.
- `colorButtonCancel`: Cor do botão cancelar.

---

### toast
```php
public static function toast(
    string $title,
    string $icon = 'success',
    string $position = 'top-end',
    int $timer = 3000,
    TAction $actionClose = null
): void
```
Exibe um alerta do tipo toast.

**Parâmetros:**
- `title`: Título do toast.
- `icon`: Tipo do toast (`success`, `error`, `info`, etc.).
- `position`: Posição na tela (ex: `top`, `top-end`, etc.).
- `timer`: Duração (ms).
- `actionClose`: Ação ao fechar o toast.

---

## Exemplo de uso
```php
use GX4\Util\TSweet;
use Adianti\Control\TAction;

TSweet::showMessage('Olá', 'Mensagem de boas-vindas');
```

---

## Requisitos
- Adianti Framework
- SweetAlert2 via TScript
