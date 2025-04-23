# 📚 TGx4 – Funções Úteis

A classe `TGx4` fornece métodos úteis conforme documentação a seguir.

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

## 📅 `TGx4::semana(int $semana): array`

Retorna um array contendo o número e a descrição do dia da semana correspondente ao número informado (0 a 6), onde 0 é domingo.

### Parâmetros
- `int $semana`: Número do dia da semana, sendo:
  - `0` para **Domingo**
  - `1` para **Segunda-feira**
  - `2` para **Terça-feira**
  - `3` para **Quarta-feira**
  - `4` para **Quinta-feira**
  - `5` para **Sexta-feira**
  - `6` para **Sábado**

### Retorno
- `array`: Um array associativo com:
  - `numero` → número do dia da semana (0 a 6)
  - `descricao` → nome do dia da semana por extenso

### Exceções
- Lança `Exception` com a mensagem `Dia da semana inválido! Deve ser um número entre 0 e 6.` se o parâmetro estiver fora do intervalo permitido.

### Exemplos
```php
TGx4::semana(0);
// Retorna: ['numero' => 0, 'descricao' => 'Domingo']

TGx4::semana(3);
// Retorna: ['numero' => 3, 'descricao' => 'Quarta-feira']

TGx4::semana(7);
// Exception: Dia da semana inválido! Deve ser um número entre 0 e 6.
```

## 🐞 `TGx4::debug(mixed ...$valores): void`

Exibe no navegador um ou mais valores usando `var_dump`, com formatação HTML (`<pre>`), facilitando a leitura durante o desenvolvimento.

### Parâmetros
- `mixed ...$valores`: Um ou mais valores de qualquer tipo (string, array, objeto, etc.) que serão exibidos no navegador.

### Retorno
- `void`: Não há retorno.

### Exemplos
```php
TGx4::debug("texto", [1, 2, 3], new stdClass());

// Saída no navegador:
// <pre>string(5) "texto"</pre>
// <pre>array(3) { [0]=> int(1) [1]=> int(2) [2]=> int(3) }</pre>
// <pre>object(stdClass)#1 (0) { }</pre>
```

## 🔠 `TGx4::normalizaTexto(string $valor, bool $maiusculas = true): string`

Normaliza uma string, removendo acentos e símbolos especiais, e ajusta a caixa (maiúscula ou minúscula).

### Parâmetros
- `string $valor`: Texto a ser normalizado.
- `bool $maiusculas`: (opcional) Se `true`, retorna o texto em caixa alta (padrão). Se `false`, retorna em caixa baixa.

### Retorno
- `string`: Texto limpo, padronizado e ajustado conforme a caixa escolhida.

### Exemplos
```php
TGx4::normalizaTexto('João da Silva');            // "JOAO DA SILVA"
TGx4::normalizaTexto('R$ 25,00 #promoção!', false); // "r 2500 promocao"
TGx4::normalizaTexto('ÇÃO!');                     // "CAO"
```

## 🔠 `TGx4::normalizaTexto(string $valor, bool $maiusculas = true): string`

Normaliza uma string, removendo acentos e símbolos especiais, e ajusta a caixa (maiúscula ou minúscula).

### Parâmetros
- `string $valor`: Texto a ser normalizado.
- `bool $maiusculas`: (opcional) Se `true`, retorna o texto em caixa alta (padrão). Se `false`, retorna em caixa baixa.

### Retorno
- `string`: Texto limpo, padronizado e ajustado conforme a caixa escolhida.

### Exemplos
```php
TGx4::normalizaTexto('João da Silva');            // "JOAO DA SILVA"
TGx4::normalizaTexto('R$ 25,00 #promoção!', false); // "r 2500 promocao"
TGx4::normalizaTexto('ÇÃO!');                     // "CAO"
```

## 🧩 `TGx4::applyMask(string $mask, string $value): string`

Aplica uma máscara ao valor informado, utilizando o caractere `#` como marcador de posição.

### Parâmetros
- `string $mask`: A máscara desejada, onde cada `#` será substituído por um caractere do valor.
- `string $value`: O valor que será inserido na máscara. Espaços são removidos automaticamente.

### Retorno
- `string`: Retorna o valor com a máscara aplicada.

### Exemplos
```php
TGx4::applyMask('###.###.###-##', '39053344705');      // "390.533.447-05"
TGx4::applyMask('##.###-###', '1234567');              // "12.345-67"
TGx4::applyMask('(##) #####-####', '51987654321');     // "(51) 98765-4321"
```