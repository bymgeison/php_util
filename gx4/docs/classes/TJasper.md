
# Classe `TJasper`

A classe `TJasper` é uma utilitária para integração com o JasperReports Server via REST. Ela permite gerar relatórios remotamente, autenticando com credenciais e passando parâmetros dinâmicos.

---

## 🧩 Namespace

```php
namespace GX4\Util;
```

---

## 🏷️ Propriedades

| Nome           | Tipo     | Descrição                                                  |
|----------------|----------|------------------------------------------------------------|
| `jasperUrl`    | `string` | URL base do servidor JasperReports.                        |
| `reportPath`   | `string` | Caminho do relatório no servidor Jasper.                   |
| `type`         | `string` | Tipo de saída esperada (por exemplo, `pdf`, `html`, `xls`).|
| `user`         | `string` | Nome de usuário para autenticação.                         |
| `password`     | `string` | Senha do usuário para autenticação.                        |
| `status_code`  | `int`    | Código HTTP retornado após a execução do relatório.        |
| `parameters`   | `array`  | Lista de parâmetros passados ao relatório.                 |

---

## 🔧 Construtor

```php
__construct(
    string $jasperUrl,
    string $reportPath,
    string $type,
    string $user,
    string $password,
    array $parameters
)
```

### Descrição
Inicializa a instância da classe com os dados necessários para conexão e execução do relatório.

---

## 🔒 Método Privado

### `getQueryString() : string`

Gera a query string com os parâmetros definidos no array `parameters`.

#### Exemplo de saída:
```text
?param1=value1&param2=value2
```

---

## 🚀 Método Público

### `execute() : string`

Executa o relatório no JasperReports Server e retorna seu conteúdo bruto.

#### Fluxo:
1. Monta a URL final com base no caminho, tipo e parâmetros.
2. Realiza a requisição via `cURL` com autenticação básica.
3. Verifica o código de retorno HTTP:
   - Se **200**, retorna o conteúdo do relatório.
   - Caso contrário, lança uma `Exception` contendo código e mensagem de erro do Jasper.

#### Exceções:
- Lança `Exception` personalizada se a resposta não for bem-sucedida (HTTP diferente de 200).
- Adiciona as propriedades `errorCode` e `errorMessage` ao objeto de exceção.

---

## 📌 Exemplo de Uso

```php
use GX4\Util\TJasper;

$jasper = new TJasper(
    'http://localhost:8080/jasperserver',
    'relatorios/financeiro/faturamento',
    'pdf',
    'usuario',
    'senha',
    ['ID' => 123, 'EMPRESA' => 456]
);

try {
    $pdfContent = $jasper->execute();
    file_put_contents('relatorio.pdf', $pdfContent);
} catch (Exception $e) {
    echo "Erro ao gerar relatório: " . $e->getMessage();
}
```

---

## 📎 Dependências

- Extensão `cURL` habilitada no PHP.
- JasperReports Server com API REST (`/rest_v2/reports/`).
