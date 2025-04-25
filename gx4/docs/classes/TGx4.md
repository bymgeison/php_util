# Classe `TGx4`

**Namespace:** `GX4\Util`

## Descrição

A classe `TGx4` fornece métodos úteis conforme documentação a seguir.

---

## 🔍 `TGx4::validaDocumento(string $valor, string $tipoDocumento): array`

Valida diversos tipos de documentos, como CPF, CNPJ, RG, Título de Eleitor, NIS/PIS/PASEP, CNH e Passaporte. O método verifica a estrutura e os dígitos verificadores para garantir a validade do número do documento.

### Parâmetros
- `string $valor`: Número do documento a ser validado (pode ser CPF, CNPJ, RG, Título de Eleitor, NIS/PIS/PASEP, CNH ou Passaporte), com ou sem formatação.
- `string $tipoDocumento`: O tipo do documento a ser validado (por exemplo, 'CPF', 'CNPJ', 'RG', 'TITULO_ELEITOR', 'NIS_PIS_PASEP', 'CNH', 'PASSAPORTE').

### Retorno
- `array`: Retorna um array com as chaves:
  - `'valido'`: Um booleano indicando se o documento é válido.
  - `'tipo'`: O tipo do documento ('CPF', 'CNPJ', 'RG', 'TITULO_ELEITOR', 'NIS_PIS_PASEP', 'CNH', 'PASSAPORTE').

### Exceções
- Lança `Exception` com a mensagem apropriada caso o documento seja inválido, como "CPF inválido: dígito verificador incorreto" ou "CNPJ inválido: dígito verificador incorreto".

### Exemplos
```php
// CPF válido
TGx4::validaDocumento('390.533.447-05', 'CPF');       // ['valido' => true, 'tipo' => 'CPF']

// CNPJ válido
TGx4::validaDocumento('11.222.333/0001-81', 'CNPJ');  // ['valido' => true, 'tipo' => 'CNPJ']

// RG válido
TGx4::validaDocumento('123456789', 'RG');             // ['valido' => true, 'tipo' => 'RG']

// Título de Eleitor válido
TGx4::validaDocumento('123456789012', 'TITULO_ELEITOR'); // ['valido' => true, 'tipo' => 'Título de Eleitor']

// NIS/PIS/PASEP válido
TGx4::validaDocumento('12345678901', 'NIS_PIS_PASEP');  // ['valido' => true, 'tipo' => 'NIS/PIS/PASEP']

// CNH válida
TGx4::validaDocumento('12345678901', 'CNH');           // ['valido' => true, 'tipo' => 'CNH']

// Passaporte válido
TGx4::validaDocumento('ABC123456', 'PASSAPORTE');      // ['valido' => true, 'tipo' => 'Passaporte']

// CPF inválido
TGx4::validaDocumento('000.000.000-00', 'CPF');        // Exception: CPF inválido: repetição de dígitos

// CNPJ inválido
TGx4::validaDocumento('12345678000100', 'CNPJ');       // Exception: CNPJ inválido: dígito verificador incorreto

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
TGx4::normalizaTexto('João da Silva');              // "JOAO DA SILVA"
TGx4::normalizaTexto('R$ 25,00 #promoção!', false); // "r 2500 promocao"
TGx4::normalizaTexto('ÇÃO!');                       // "CAO"
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
TGx4::applyMask('###.###.###-##', '39053344705');  // "390.533.447-05"
TGx4::applyMask('##.###-###', '1234567');          // "12.345-67"
TGx4::applyMask('(##) #####-####', '51987654321'); // "(51) 98765-4321"
```

## 🔐 `TGx4::generatePassword(int $length): string`

Gera uma senha aleatória contendo letras maiúsculas, minúsculas, números e símbolos especiais.

### Parâmetros
- `int $length`: Tamanho desejado da senha. Deve ser maior que 0.

### Retorno
- `string`: Senha gerada aleatoriamente com o número de caracteres especificado.

### Exceções
- Lança `InvalidArgumentException` se o tamanho for menor que 1.

### Exemplos
```php
TGx4::generatePassword(8);   // Ex: "kJ8$2bR@"
TGx4::generatePassword(12);  // Ex: "1aD@eL!7uW#%"
```

## 🧽 `TGx4::removeMask(string $value): string`

Remove todos os caracteres não alfanuméricos de uma string, útil para limpar dados como CPF, CNPJ, telefones e códigos.

### Parâmetros
- `string $value`: Valor de entrada com possíveis máscaras ou símbolos.

### Retorno
- `string`: String contendo apenas letras e números, sem espaços ou caracteres especiais.

### Exemplos
```php
TGx4::removeMask('123.456.789-00');     // "12345678900"
TGx4::removeMask('(51) 99999-0000');    // "51999990000"
TGx4::removeMask('AB#123-CD!');         // "AB123CD"
```

## 🔠 `TGx4::mbStrPad(string $str, int $len, string $pad, int $align = STR_PAD_RIGHT): string`

Preenche uma string multibyte até o tamanho desejado, respeitando o alinhamento. Alternativa ao `str_pad` para strings com acentuação ou caracteres multibyte.

### Parâmetros
- `string $str`: String original a ser preenchida.
- `int $len`: Tamanho total desejado após o preenchimento.
- `string $pad`: Caracter(es) usado(s) para preencher.
- `int $align`: Tipo de alinhamento. Pode ser:
  - `STR_PAD_RIGHT` (padrão)
  - `STR_PAD_LEFT`
  - `STR_PAD_BOTH`

### Retorno
- `string`: String resultante com o comprimento desejado.

### Exemplos
```php
TGx4::mbStrPad('Olá', 10, '-');                  // "Olá-------"
TGx4::mbStrPad('Olá', 10, '-', STR_PAD_LEFT);    // "-------Olá"
TGx4::mbStrPad('Olá', 10, '-', STR_PAD_BOTH);    // "---Olá----"
TGx4::mbStrPad('çã', 5, '*');                    // "çã***"
```

## 📝 `TGx4::saveIniFile(array $data, string $file, bool $hasSections = false): bool`

Grava um array associativo em um arquivo `.ini` de forma organizada, com ou sem seções.

### Parâmetros
- `array $data`: Dados a serem gravados no formato INI.
- `string $file`: Caminho do arquivo a ser gerado.
- `bool $hasSections`: Define se o array possui seções. Se `true`, usa chaves de primeiro nível como nomes de seções.

### Retorno
- `bool`: Retorna `true` em caso de sucesso, `false` em caso de erro.

### Exemplo
```php
$data = [
    'app' => [
        'debug' => 'true',
        'version' => '1.0.0'
    ],
    'database' => [
        'host' => 'localhost',
        'port' => 3306
    ]
];

TGx4::saveIniFile($data, '/caminho/config.ini', true);

```

## 🧪 `TGx4::isAValidEAN13(string $ean): bool`

Valida se um código EAN-13 é válido com base no cálculo do dígito verificador.

### Parâmetros
- `string $ean`: Código EAN-13 a ser validado.

### Retorno
- `bool`: `true` se o código for válido, `false` caso contrário.

### Exemplo
```php
$isValid = TGx4::isAValidEAN13('7891234567895');
```

---

## 🧮 `TGx4::has13Numbers(array $ean): bool`

Verifica se o array contém exatamente 13 dígitos, conforme exigido pelo padrão EAN-13.

### Parâmetros
- `array $ean`: Array contendo os dígitos do EAN.

### Retorno
- `bool`: `true` se o array tiver 13 números, `false` caso contrário.

### Exemplo
```php
$tem13 = TGx4::has13Numbers([7,8,9,1,2,3,4,5,6,7,8,9,5]);
```

---

## 📦 `TGx4::isValidBarcode(string $barcode): bool`

Valida códigos de barras compatíveis com os padrões GTIN-8, GTIN-12, GTIN-13, GTIN-14, GSIN e SSCC.

### Parâmetros
- `string $barcode`: Código de barras numérico a ser validado.

### Retorno
- `bool`: `true` se o código for válido de acordo com o dígito verificador, `false` caso contrário.

### Exemplo
```php
$isValid = TGx4::isValidBarcode('1234567890128');
```