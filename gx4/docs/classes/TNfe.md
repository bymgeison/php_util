# Classe TNfe

A classe `TNfe` é responsável por realizar o tratamento de arquivos XML da Nota Fiscal Eletrônica (NFe), validando e extraindo informações como o protocolo de autorização e outros dados específicos. Ela oferece métodos para carregar o XML de diferentes fontes, como arquivos locais, URLs ou diretamente como strings, e também para exibir dados de maneira formatada para debug.

## Propriedades

- **$chave**: Armazena a chave de acesso da NFe.
- **$xml**: Contém o conteúdo do XML da NFe carregado.
- **$versao**: Versão da NFe extraída do XML.

## Métodos

### `__construct($arquivo, $tipo = 'arquivo')`

Construtor da classe. Inicializa a classe validando o conteúdo informado, que pode ser o caminho de um arquivo XML ou uma string com o conteúdo do XML.

#### Parâmetros:
- **$arquivo** (string): Caminho do arquivo XML ou string com o conteúdo XML.
- **$tipo** (string): Tipo de entrada. Pode ser 'arquivo' para caminho de arquivo ou 'string' para conteúdo XML. O padrão é 'arquivo'.

#### Exceções:
- Lança uma exceção caso o arquivo não seja válido ou não possa ser lido.

### `debug(...$valores)`

Exibe um ou mais valores com `var_dump` formatado em HTML.

#### Parâmetros:
- **...$valores**: Um ou mais valores a serem exibidos.

#### Retorno:
- **void**: Não retorna nada. Apenas exibe os valores.

### `tratadt($var)`

Formata a data passada para o formato `Y-m-d H:i:s`.

#### Parâmetros:
- **$var** (string): Data a ser formatada.

#### Retorno:
- **string**: A data formatada no formato `Y-m-d H:i:s`.

### `validaArquivo($arquivo, $tipo)`

Valida e carrega o conteúdo XML da NFe conforme o tipo informado. Aceita como entrada o caminho para um arquivo local, uma URL ou diretamente o conteúdo XML como string.

#### Parâmetros:
- **$arquivo** (string): Caminho do arquivo, URL, ou conteúdo XML em string.
- **$tipo** (string): Tipo de entrada. Pode ser 'arquivo', 'url' ou 'string'.

#### Exceções:
- Lança uma exceção caso o arquivo não exista, o conteúdo seja inválido ou não contenha dados de autorização.

### `getProtocolo()`

Retorna as informações do protocolo de autorização da NFe, extraindo do XML os dados como número do protocolo, data/hora da autorização e o código de status da SEFAZ.

#### Retorno:
- **\stdClass**: Objeto contendo os seguintes campos:
  - **protocolo_autorizacao** (string): Número do protocolo de autorização.
  - **dt_autorizacao** (string): Data e hora da autorização, já formatada.
  - **cd_status** (string): Código de status da SEFAZ.

### `getIde()`

Retorna as informações da seção `<ide>` da NFe. Essa seção contém dados de identificação da Nota Fiscal Eletrônica, como número, série, data de emissão, tipo de operação, entre outros.

#### Retorno:
- **\stdClass**: Objeto contendo os seguintes atributos:
  - **versao** (string): Versão do layout da NFe.
  - **ide_natOp** (string|null): Natureza da operação.
  - **ide_mod** (string|null): Modelo do documento fiscal.
  - **ide_serie** (string|null): Série do documento fiscal.
  - **ide_nNF** (string|null): Número da nota fiscal.
  - **ide_dhEmi** (string|null): Data/hora de emissão (formatada).
  - **ide_dhSaiEnt** (string|null): Data/hora de saída ou entrada (formatada).
  - **ide_tpNF** (string|null): Tipo da nota fiscal (entrada/saída).
  - **ide_idDEst** (string|null): Identificador do destinatário.
  - **ide_cMunFG** (string|null): Código do município de ocorrência do fato gerador.
  - **ide_tp_Imp** (string|null): Formato de impressão do DANFE.
  - **ide_tpEmis** (string|null): Tipo de emissão.
  - **ide_tpAmb** (string|null): Tipo de ambiente (produção/homologação).
  - **ide_finNFe** (string|null): Finalidade de emissão da NFe.
  - **ide_indFinal** (string|null): Indicador de operação com consumidor final.
  - **ide_indPres** (string|null): Indicador de presença do comprador.
  - **ide_procEmi** (string|null): Processo de emissão.
  - **ide_verProc** (string|null): Versão do processo de emissão.
  - **ide_dhCont** (string|null): Data/hora do contingência (se houver).
  - **ide_xJust** (string|null): Justificativa da contingência (se houver).

### `getChave()`

Retorna a chave de acesso da NFe.

**Descrição**:
A chave de acesso é um identificador único da nota fiscal eletrônica, composta por diversas informações como CNPJ, data, modelo, série, número e código numérico.

**Retorno**:
- `string` Chave de acesso da NFe.

---

### `getEmitente()`

Retorna os dados do emitente da NFe.

**Descrição**:
Extrai e organiza as informações do emitente (empresa ou pessoa que emitiu a nota fiscal), incluindo dados de identificação, endereço, regime tributário e contato.

**Retorno**:
- **\stdClass** Objeto contendo os dados do emitente:
  - **emit_cpf**: CPF do emitente (caso pessoa física)
  - **emit_cnpj**: CNPJ do emitente (caso pessoa jurídica)
  - **emit_xNome**: Nome ou razão social do emitente
  - **emit_xFant**: Nome fantasia
  - **emit_IE**: Inscrição estadual
  - **emit_CRT**: Código do regime tributário
  - **emit_xLgr**: Logradouro (rua/avenida)
  - **emit_nro**: Número do endereço
  - **emit_xCpl**: Complemento do endereço
  - **emit_xBairro**: Bairro
  - **emit_cMun**: Código do município
  - **emit_xMun**: Nome do município
  - **emit_UF**: Unidade federativa (estado)
  - **emit_CEP**: CEP
  - **emit_cPais**: Código do país
  - **emit_xPais**: Nome do país
  - **emit_fone**: Telefone de contato

---

### `getDestinatario()`

Retorna os dados do destinatário da NFe.

**Descrição**:
Este método extrai e organiza as informações do destinatário da nota fiscal, como CPF/CNPJ, nome, endereço, e dados complementares de identificação.

**Retorno**:
- **\stdClass** Objeto contendo os dados do destinatário:
  - **dest_idEstrangeiro**: Identificação de estrangeiro (quando aplicável)
  - **dest_CPF**: CPF do destinatário (caso pessoa física)
  - **dest_CNPJ**: CNPJ do destinatário (caso pessoa jurídica)
  - **dest_xNome**: Nome ou razão social do destinatário
  - **dest_xFant**: Nome fantasia
  - **dest_IE**: Inscrição estadual
  - **dest_indIEDest**: Indicador de IE do destinatário
  - **dest_CRT**: Código do regime tributário (quando presente)
  - **dest_xLgr**: Logradouro (rua/avenida)
  - **dest_nro**: Número do endereço
  - **dest_xCpl**: Complemento do endereço
  - **dest_xBairro**: Bairro
  - **dest_cMun**: Código do município
  - **dest_xMun**: Nome do município
  - **dest_UF**: Unidade federativa (estado)
  - **dest_CEP**: Código postal (CEP)
  - **dest_cPais**: Código do país
  - **dest_xPais**: Nome do país
  - **dest_fone**: Telefone de contato
  - **dest_email**: E-mail de contato (se fornecido)

---

### `getTotal()`

Retorna os totais da nota fiscal.

**Descrição**:
Este método extrai os valores totais da NFe a partir do grupo `ICMSTot`, incluindo os valores de impostos e, quando presente, os dados do faturamento (grupo `fat`).

**Retorno**:
- **\SimpleXMLElement** Objeto contendo os totais da NFe, incluindo:
  - **vBC**: Valor da base de cálculo do ICMS
  - **vICMS**: Valor total do ICMS
  - **vICMSDeson**: Valor do ICMS desonerado
  - **vFCP**: Valor total do FCP
  - **vBCST**: Valor da base de cálculo do ICMS ST
  - **vST**: Valor total do ICMS ST
  - **vFCPST**: Valor do FCP retido por ST
  - **vFCPSTRet**: Valor do FCP retido anteriormente por ST
  - **vProd**: Valor total dos produtos e serviços
  - **vFrete**: Valor do frete
  - **vSeg**: Valor do seguro
  - **vDesc**: Valor do desconto
  - **vII**: Valor do imposto de importação
  - **vIPI**: Valor do IPI
  - **vIPIDevol**: Valor do IPI devolvido
  - **vPIS**: Valor do PIS
  - **vCOFINS**: Valor do COFINS
  - **vOutro**: Outros valores
  - **vNF**: Valor total da nota fiscal
  - **fat_vOrig**: Valor original da fatura (se existir)
  - **fat_vDesc**: Valor do desconto na fatura (se existir)
  - **fat_vLiq**: Valor líquido da fatura (se existir)

---

### `getTransporte()`

Retorna os dados de transporte da NFe.

**Descrição**:
Este método extrai informações do grupo de transporte da nota fiscal, incluindo o tipo de frete, dados do transportador, veículo e volumes transportados.

**Retorno**:
- **\stdClass** Objeto contendo as informações de transporte, com os seguintes campos:
  - **modFrete**: Modalidade do frete (por conta do emitente, destinatário, etc.)
  - **transp_CNPJ**: CNPJ do transportador (se houver)
  - **transp_CPF**: CPF do transportador (se houver)
  - **transp_xNome**: Nome do transportador
  - **transp_xEnder**: Endereço do transportador
  - **transp_xMun**: Município do transportador
  - **transp_UF**: UF do transportador
  - **veic_placa**: Placa do veículo
  - **veic_UF**: UF do veículo
  - **veic_RNTC**: Registro Nacional de Transportador de Carga
  - **vol_qVol**: Quantidade de volumes
  - **vol_nVol**: Identificação dos volumes
  - **vol_esp**: Espécie dos volumes
  - **vol_marca**: Marca dos volumes
  - **vol_pesoL**: Peso líquido dos volumes
  - **vol_pesoB**: Peso bruto dos volumes

---

### `ICMS()`

#### Descrição:
Processa e formata as informações do ICMS de acordo com o tipo de tributação.

Este método verifica os diferentes tipos de ICMS presentes no objeto `$var` (ICMS00, ICMS10, ICMS20, ICMS30, ICMS40, ICMS51, ICMS60, ICMS70, ICMS90, ICMSSN101, ICMSSN102, ICMSSN201, ICMSSN202) e extrai as informações relevantes para cada tipo. Em seguida, essas informações são armazenadas em um objeto `stdClass` que é retornado para posterior uso.

#### Parâmetros:
- **$var** (`object`): Objeto contendo as informações de ICMS a serem processadas. Espera-se que o objeto tenha propriedades específicas como ICMS00, ICMS10, ICMS20, etc.

#### Retorno:
- Retorna um objeto `stdClass` com os dados processados do ICMS, onde cada tipo de ICMS é tratado de acordo com suas especificidades.

Detalhamento dos Tipos de ICMS:
             ICMS00           : ICMS normal.

**ICMS10** ICMS com substituição tributária.
**ICMS20** ICMS com redução de base de cálculo.
**ICMS30** ICMS por substituição tributária com diferença de alíquota.
**ICMS40** ICMS com isenção.
**ICMS51** ICMS com diferimento.
**ICMS60** ICMS substituição tributária por retenção.
**ICMS70** ICMS com redução de base de cálculo e substituição tributária.
**ICMS90** ICMS com redução de base de cálculo.
**ICMSSN101** ICMS substituição tributária por sistema simplificado.
**ICMSSN102** ICMS substituição tributária por sistema simplificado com isenção.
**ICMSSN201** ICMS substituição tributária por sistema simplificado com redução de base.
**ICMSSN202** ICMS substituição tributária por sistema simplificado com isenção e redução de base.

#### Exemplo de Uso:
```php
$icmsData = $this->ICMS($var);

## Exemplo de Uso

```php
use GX4\Util\TNfe;

try {
    // Carregar NFe de um arquivo XML
    $nfe = new TNfe('/caminho/para/nfe.xml');

    // Obter o protocolo de autorização
    $protocolo = $nfe->getProtocolo();

    echo "Protocolo: " . $protocolo->protocolo_autorizacao;
    echo "Data de Autorização: " . $protocolo->dt_autorizacao;
    echo "Status: " . $protocolo->cd_status;

    // Obter as informações da seção <ide>
    $ide = $nfe->getIde();

    echo "Número da NFe: " . $ide->ide_nNF;
    echo "Modelo: " . $ide->ide_mod;
    echo "Data de Emissão: " . $ide->ide_dhEmi;
} catch (\Exception $e) {
    echo "Erro: " . $e->getMessage();
}
