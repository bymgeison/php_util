# `Ghost`

**Namespace:** `GX4\Util`

## Descrição

A classe `Ghost` fornece métodos utilitários para conversão de arquivos PDF em imagens JPEG utilizando a ferramenta GhostScript.

---

## Métodos

### `convertPDFtoImage($pdf, $image, $folder_pdf, $folder_image)`

Converte um arquivo PDF em imagem JPEG usando o GhostScript.

#### Parâmetros:
- **`$pdf`** (`string`): Nome do arquivo PDF a ser convertido.
- **`$image`** (`string`): Nome do arquivo de imagem de saída.
- **`$folder_pdf`** (`string`): Caminho da pasta onde está localizado o PDF.
- **`$folder_image`** (`string`): Caminho da pasta onde a imagem gerada será salva.

#### Exemplo de uso:
```php
Ghost::convertPDFtoImage('documento.pdf', 'documento.jpg', '/tmp/pdfs/', '/tmp/imagens/');
```

#### Detalhes:
- Usa a resolução de 250x250 DPI e qualidade JPEG 10.
- Em caso de erro, lança uma exceção com a mensagem do erro.

---

### `convertPDFCatalogo($imagem, $pdf)`

Converte um PDF de catálogo em imagem com configurações específicas para maior qualidade.

#### Parâmetros:
- **`$imagem`** (`string`): Caminho do arquivo de imagem de saída (incluindo nome e extensão).
- **`$pdf`** (`string`): Caminho do arquivo PDF de entrada.

#### Exemplo de uso:
```php
Ghost::convertPDFCatalogo('/tmp/imagens/catalogo.jpg', '/tmp/pdfs/catalogo.pdf');
```

#### Detalhes:
- Utiliza qualidade JPEG 80, resolução 300x300 DPI e opções adicionais para qualidade de impressão.
- Em caso de erro, lança uma exceção com a mensagem do erro.

---

## Requisitos
- GhostScript instalado e disponível no sistema (comando `gs`).

---

## Observações
- O uso de aspas simples ou duplas nos caminhos e nomes de arquivos pode ser importante dependendo do sistema operacional.
- Certifique-se de que os caminhos fornecidos existam e sejam graváveis.
