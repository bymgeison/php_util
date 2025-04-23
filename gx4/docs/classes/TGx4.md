# 📚 TGx4 – Utilitários de Documento (CPF/CNPJ)

A classe `TGx4` fornece métodos utilitários para validação e formatação de documentos brasileiros: **CPF** e **CNPJ**.

---

## 🔍 `TGx4::validaDocumento(string $valor): bool`

Valida se o valor informado é um CPF ou CNPJ válido com base em seus dígitos verificadores.

### Parâmetros
- `string $valor`: Número do CPF ou CNPJ (com ou sem formatação).

### Retorno
- `bool`: Retorna `true` se o documento for válido.

### Exceções
- Lança `Exception` com a mensagem `CPF inválido!` ou `CNPJ inválido!` em caso de erro.

### Exemplos
```php
TGx4::validaDocumento('390.533.447-05');       // true
TGx4::validaDocumento('11.222.333/0001-81');   // true
TGx4::validaDocumento('000.000.000-00');       // Exception: CPF inválido!
TGx4::validaDocumento('12345678000100');       // Exception: CNPJ inválido!
```

## 🔍 `TGx4::formataDocumento(string $valor): string`

Formata um número de **CPF** ou **CNPJ** de acordo com a máscara correspondente, baseado no número de dígitos informados.

### Parâmetros
- `string $valor`: Número do CPF ou CNPJ (com ou sem formatação).

### Retorno
- `string`: Retorna o CPF ou CNPJ formatado:
  - CPF: `000.000.000-00`
  - CNPJ: `00.000.000/0000-00`

### Exceções
- Lança `Exception("Documento inválido!")` se o valor não tiver 11 (CPF) ou 14 (CNPJ) dígitos numéricos após a limpeza.

### Exemplos
```php
TGx4::formataDocumento('39053344705');        // "390.533.447-05"
TGx4::formataDocumento('11222333000181');     // "11.222.333/0001-81"
TGx4::formataDocumento('123');                // Exception: Documento inválido!
TGx4::formataDocumento('11.222.333/0001-81'); // "11.222.333/0001-81"
```
