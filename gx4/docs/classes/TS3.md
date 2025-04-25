## ☁️ `TS3::__construct(string $awsKey, string $awsSecretKey, string $endpoint = ..., bool $pathStyle = true, string $region = 'us-east-1')`

Inicializa a instância do cliente S3 com as credenciais e configurações especificadas.

### Parâmetros
- `string $awsKey`: Chave de acesso AWS.
- `string $awsSecretKey`: Chave secreta AWS.
- `string $endpoint`: Endpoint do serviço S3 (padrão: Amazon S3).
- `bool $pathStyle`: Define se o estilo de path será utilizado.
- `string $region`: Região AWS (padrão: `us-east-1`).

---

## 📤 `TS3::uploadFile(string $bucket, string $s3Key, string $localFile, bool $deleteAfterUpload = true): void`

Envia um arquivo local para um bucket S3.

### Parâmetros
- `string $bucket`: Nome do bucket.
- `string $s3Key`: Caminho completo do arquivo no S3.
- `string $localFile`: Caminho completo do arquivo local.
- `bool $deleteAfterUpload`: Se verdadeiro, exclui o arquivo local após o envio.

---

## 📥 `TS3::downloadFile(string $bucket, string $s3Key, string $saveAs, string $saveDir): void`

Baixa um arquivo do S3 e o salva localmente.

### Parâmetros
- `string $bucket`: Nome do bucket.
- `string $s3Key`: Caminho completo do arquivo no S3.
- `string $saveAs`: Nome do arquivo salvo localmente.
- `string $saveDir`: Diretório onde o arquivo será salvo.

---

## ❌ `TS3::deleteFile(string $bucket, string $s3Key): void`

Exclui um arquivo do bucket S3.

### Parâmetros
- `string $bucket`: Nome do bucket.
- `string $s3Key`: Caminho completo do arquivo no S3.

---

## 📂 `TS3::listDirectory(string $bucket, string $directory): array`

Lista os arquivos contidos em um diretório no S3.

### Parâmetros
- `string $bucket`: Nome do bucket.
- `string $directory`: Caminho do diretório a ser listado.

### Retorno
- `array`: Lista de arquivos encontrados.

---

## 🔎 `TS3::fileExists(string $bucket, string $filename): bool`

Verifica se um arquivo existe no bucket S3.

### Parâmetros
- `string $bucket`: Nome do bucket.
- `string $filename`: Caminho completo do arquivo.

### Retorno
- `bool`: `true` se o arquivo existir, `false` caso contrário.