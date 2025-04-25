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

---

### `debug(...$valores)`

Exibe um ou mais valores com `var_dump` formatado em HTML.

#### Parâmetros:
- **...$valores**: Um ou mais valores a serem exibidos.

#### Retorno:
- **void**: Não retorna nada. Apenas exibe os valores.

---

### `tratadt($var)`

Formata a data passada para o formato `Y-m-d H:i:s`.

#### Parâmetros:
- **$var** (string): Data a ser formatada.

#### Retorno:
- **string**: A data formatada no formato `Y-m-d H:i:s`.

---

### `validaArquivo($arquivo, $tipo)`

Valida e carrega o conteúdo XML da NFe conforme o tipo informado. Aceita como entrada o caminho para um arquivo local, uma URL ou diretamente o conteúdo XML como string.

#### Parâmetros:
- **$arquivo** (string): Caminho do arquivo, URL, ou conteúdo XML em string.
- **$tipo** (string): Tipo de entrada. Pode ser 'arquivo', 'url' ou 'string'.

#### Exceções:
- Lança uma exceção caso o arquivo não exista, o conteúdo seja inválido ou não contenha dados de autorização.

---

### `getProtocolo()`

Retorna as informações do protocolo de autorização da NFe, extraindo do XML os dados como número do protocolo, data/hora da autorização e o código de status da SEFAZ.

#### Retorno:
- **\stdClass**: Objeto contendo os seguintes campos:
  - **protocolo_autorizacao** (string): Número do protocolo de autorização.
  - **dt_autorizacao** (string): Data e hora da autorização, já formatada.
  - **cd_status** (string): Código de status da SEFAZ.

---

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

---

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

### `ICMS($var)`

#### Descrição:
Processa e formata as informações do ICMS de acordo com o tipo de tributação.

Este método verifica os diferentes tipos de ICMS presentes no objeto `$var` (ICMS00, ICMS10, ICMS20, ICMS30, ICMS40, ICMS51, ICMS60, ICMS70, ICMS90, ICMSSN101, ICMSSN102, ICMSSN201, ICMSSN202) e extrai as informações relevantes para cada tipo. Em seguida, essas informações são armazenadas em um objeto `stdClass` que é retornado para posterior uso.

#### Parâmetros:
- **$var** (`object`): Objeto contendo as informações de ICMS a serem processadas. Espera-se que o objeto tenha propriedades específicas como ICMS00, ICMS10, ICMS20, etc.

#### Retorno:
- Retorna um objeto `stdClass` com os dados processados do ICMS, onde cada tipo de ICMS é tratado de acordo com suas especificidades.

Detalhamento dos Tipos de ICMS:
- **ICMS00** ICMS normal.
- **ICMS10** ICMS com substituição tributária.
- **ICMS20** ICMS com redução de base de cálculo.
- **ICMS30** ICMS por substituição tributária com diferença de alíquota.
- **ICMS40** ICMS com isenção.
- **ICMS51** ICMS com diferimento.
- **ICMS60** ICMS substituição tributária por retenção.
- **ICMS70** ICMS com redução de base de cálculo e substituição tributária.
- **ICMS90** ICMS com redução de base de cálculo.
- **ICMSSN101** ICMS substituição tributária por sistema simplificado.
- **ICMSSN102** ICMS substituição tributária por sistema simplificado com isenção.
- **ICMSSN201** ICMS substituição tributária por sistema simplificado com redução de base.
- **ICMSSN202** ICMS substituição tributária por sistema simplificado com isenção e redução de base.

#### Exemplo de Uso:
```php

$icmsData = $this->ICMS($var);

```

---

### `IPI($var)`

#### Descrição:
Esse método processa as informações de IPI (Imposto sobre Produtos Industrializados) a partir de um objeto de entrada e retorna um objeto com as propriedades pertinentes ao IPI.

#### Parâmetros
- **$var**: Um objeto contendo a estrutura de dados do IPI. Pode ter duas possíveis estruturas:
  - `IPITrib`: Para informações de tributo do IPI.
  - `IPINT`: Para informações de IPI não tributado.

#### Retorno
Retorna um objeto do tipo `stdClass` com as seguintes propriedades:

- **cEnq**: Código da consulta, presente se existir em `IPITrib`.
- **CST**: Código da Situação Tributária do IPI, presente tanto em `IPITrib` quanto em `IPINT`.
- **vBC**: Valor da base de cálculo do IPI, presente se existir em `IPITrib`.
- **pIPI**: Percentual do IPI, presente se existir em `IPITrib`.
- **vIPI**: Valor do IPI, presente se existir em `IPITrib`.

---

### `II($var)`

Esse método processa as informações de II (Imposto de Importação) a partir de um objeto de entrada e retorna um objeto com as propriedades pertinentes ao II.

#### Parâmetros
- **$var**: Um objeto contendo a estrutura de dados do II.

#### Retorno
Retorna um objeto do tipo `stdClass` com as seguintes propriedades:

- **vBC**: Valor da base de cálculo do II, presente se existir no objeto de entrada.
- **vDespAdu**: Valor das despesas aduaneiras, presente se existir no objeto de entrada.
- **vII**: Valor do Imposto de Importação, presente se existir no objeto de entrada.
- **vIOF**: Valor do IOF (Imposto sobre Operações Financeiras) relacionado ao II, presente se existir no objeto de entrada.

#### Exemplo de Uso

```php
$var = new stdClass();
// Exemplo de estrutura de dados de II
$var->II = new stdClass();
$var->II->vBC = '1000';
$var->II->vDespAdu = '50';
$var->II->vII = '200';
$var->II->vIOF = '10';

$ii = new SeuObjeto();
$resultado = $ii->II($var);
print_r($resultado);

```

### `PIS($var)`

Esse método processa as informações de PIS (Programa de Integração Social) a partir de um objeto de entrada e retorna um objeto com as propriedades pertinentes ao PIS.

#### Parâmetros
- **$var**: Um objeto contendo a estrutura de dados do PIS.

#### Retorno
Retorna um objeto do tipo `stdClass` com as seguintes propriedades:

#### Para o tipo **PISAliq**:
- **CST**: Código da situação tributária do PIS, presente se existir no objeto de entrada.
- **vBC**: Valor da base de cálculo do PIS, presente se existir no objeto de entrada.
- **pPIS**: Percentual da alíquota do PIS, presente se existir no objeto de entrada.
- **vPIS**: Valor do PIS, presente se existir no objeto de entrada.

#### Para o tipo **PISNT**:
- **CST**: Código da situação tributária do PIS, presente se existir no objeto de entrada.

#### Para o tipo **PISOutr**:
- **CST**: Código da situação tributária do PIS, presente se existir no objeto de entrada.
- **vBC**: Valor da base de cálculo do PIS, presente se existir no objeto de entrada.
- **pPIS**: Percentual da alíquota do PIS, presente se existir no objeto de entrada.
- **vPIS**: Valor do PIS, presente se existir no objeto de entrada.

#### Para o tipo **PISQtde**:
- **CST**: Código da situação tributária do PIS, presente se existir no objeto de entrada.
- **vBC**: Valor da base de cálculo do PIS, presente se existir no objeto de entrada.
- **pPIS**: Percentual da alíquota do PIS, presente se existir no objeto de entrada.
- **vPIS**: Valor do PIS, presente se existir no objeto de entrada.
- **qBCProd_pis**: Quantidade de produto na base de cálculo do PIS, presente se existir no objeto de entrada.
- **vAliqProd_pis**: Valor da alíquota do PIS sobre o produto, presente se existir no objeto de entrada.

#### Exemplo de Uso

```php
$var = new stdClass();
// Exemplo de estrutura de dados de PIS
$var->PIS = new stdClass();
$var->PIS->PISAliq = new stdClass();
$var->PIS->PISAliq->CST = '01';
$var->PIS->PISAliq->vBC = '1000';
$var->PIS->PISAliq->pPIS = '1.65';
$var->PIS->PISAliq->vPIS = '16.50';

$pis = new SeuObjeto();
$resultado = $pis->PIS($var);
print_r($resultado);

```

### `COFINS($var)`

Esse método processa as informações de COFINS (Contribuição para o Financiamento da Seguridade Social) a partir de um objeto de entrada e retorna um objeto com as propriedades pertinentes ao COFINS.

#### Parâmetros
- **$var**: Um objeto contendo a estrutura de dados do COFINS.

#### Retorno
Retorna um objeto do tipo `stdClass` com as seguintes propriedades:

#### Para o tipo **COFINSAliq**:
- **CST**: Código da situação tributária do COFINS, presente se existir no objeto de entrada.
- **vBC**: Valor da base de cálculo do COFINS, presente se existir no objeto de entrada.
- **pCOFINS**: Percentual da alíquota do COFINS, presente se existir no objeto de entrada.
- **vCOFINS**: Valor do COFINS, presente se existir no objeto de entrada.

#### Para o tipo **COFINSNT**:
- **CST**: Código da situação tributária do COFINS, presente se existir no objeto de entrada.

#### Para o tipo **COFINSOutr**:
- **CST**: Código da situação tributária do COFINS, presente se existir no objeto de entrada.
- **vBC**: Valor da base de cálculo do COFINS, presente se existir no objeto de entrada.
- **pCOFINS**: Percentual da alíquota do COFINS, presente se existir no objeto de entrada.
- **vCOFINS**: Valor do COFINS, presente se existir no objeto de entrada.

#### Para o tipo **COFINSQtde**:
- **CST**: Código da situação tributária do COFINS, presente se existir no objeto de entrada.
- **vBC**: Valor da base de cálculo do COFINS, presente se existir no objeto de entrada.
- **pCOFINS**: Percentual da alíquota do COFINS, presente se existir no objeto de entrada.
- **vCOFINS**: Valor do COFINS, presente se existir no objeto de entrada.
- **qBCProd_cofins**: Quantidade de produto na base de cálculo do COFINS, presente se existir no objeto de entrada.
- **vAliqProd_cofins**: Valor da alíquota do COFINS sobre o produto, presente se existir no objeto de entrada.

#### Exemplo de Uso

```php
$var = new stdClass();
// Exemplo de estrutura de dados de COFINS
$var->COFINS = new stdClass();
$var->COFINS->COFINSAliq = new stdClass();
$var->COFINS->COFINSAliq->CST = '01';
$var->COFINS->COFINSAliq->vBC = '2000';
$var->COFINS->COFINSAliq->pCOFINS = '7.60';
$var->COFINS->COFINSAliq->vCOFINS = '152.00';

$cofins = new SeuObjeto();
$resultado = $cofins->COFINS($var);
print_r($resultado);

```

### `getProdutos()`

#### Descrição
O método `getProdutos()` é responsável por extrair e processar os detalhes dos produtos de uma nota fiscal eletrônica (NFe). Ele percorre os itens do XML da NFe e monta um objeto contendo informações sobre cada produto, como código, descrição, NCM, CFOP, valores de tributos (ICMS, IPI, PIS, COFINS, II) e outros dados adicionais de cada item.

#### Retorno
O método retorna um objeto contendo uma lista de itens, onde cada item é um objeto com os seguintes atributos:

- **nItem**: Número do item.
- **cProd**: Código do produto.
- **cEAN**: Código EAN (código de barras).
- **xProd**: Descrição do produto.
- **NCM**: Código NCM do produto.
- **nve**: Número de versão de exportação (se presente).
- **cest**: Código CEST (se presente).
- **CFOP**: Código Fiscal de Operações e Prestações.
- **uCom**: Unidade de comercialização do produto.
- **qCom**: Quantidade do produto.
- **vUnCom**: Valor unitário do produto.
- **vProd**: Valor total do produto.
- **cEANTrib**: Código EAN do produto para fins de tributação.
- **uTrib**: Unidade de tributação do produto.
- **qTrib**: Quantidade tributada do produto.
- **vUnTrib**: Valor unitário da tributação do produto.
- **vFrete**: Valor do frete (se presente).
- **vSeg**: Valor do seguro (se presente).
- **vDesc**: Valor do desconto (se presente).
- **vOutro**: Outros valores relacionados ao produto (se presente).
- **indTot**: Indicador de totalização do item.
- **xPed**: Código do pedido (se presente).
- **nItemPed**: Número do item no pedido (se presente).
- **nFCI**: Número do FCI (se presente).
- **tipoTributo**: Tipo de tributação do ICMS.
- **orig**: Origem do ICMS.
- **cst_icms**: Código do ICMS.
- **modBC**: Modalidade de cálculo da BC do ICMS.
- **pRedBC**: Percentual de redução da base de cálculo do ICMS.
- **vBC**: Valor da base de cálculo do ICMS.
- **pICMS**: Percentual do ICMS.
- **vICMS**: Valor do ICMS.
- **vBCSTRet**: Valor da base de cálculo do ICMS ST retido.
- **pST**: Percentual do ICMS ST.
- **vICMSSubstituto**: Valor do ICMS Substituto.
- **vICMSSTRet**: Valor do ICMS Substituto retido.
- **vBCSTDest**: Valor da base de cálculo do ICMS ST destinado.
- **vICMSSTDest**: Valor do ICMS ST destinado.
- **modBCST**: Modalidade de cálculo da BC do ICMS ST.
- **pMVAST**: Percentual de margem de valor agregado.
- **vBCST**: Valor da base de cálculo do ICMS ST.
- **pICMSST**: Percentual do ICMS ST.
- **vICMSST**: Valor do ICMS ST.
- **pCredSN**: Percentual de crédito do ICMS no Simples Nacional.
- **vCredICMSSN**: Valor do crédito do ICMS no Simples Nacional.
- **pRedBCST**: Percentual de redução da base de cálculo do ICMS ST.
- **vICMSDeson**: Valor do ICMS desonerado.
- **motDesICMS**: Motivo da desoneração do ICMS.
- **vICMSOp**: Valor do ICMS sobre operações internas.
- **pDif**: Percentual de diferença do ICMS.
- **vICMSDif**: Valor do ICMS de diferença.
- **vBCFCP**: Valor da base de cálculo do FCP.
- **pFCP**: Percentual do FCP.
- **vFCP**: Valor do FCP.
- **vBCFCPST**: Valor da base de cálculo do FCP ST.
- **pFCPST**: Percentual do FCP ST.
- **vFCPST**: Valor do FCP ST.
- **vBCFCPSTRet**: Valor da base de cálculo do FCP ST retido.
- **pFCPSTRet**: Percentual do FCP ST retido.
- **vFCPSTRet**: Valor do FCP ST retido.
- **pRedBCEfet**: Percentual de redução da base de cálculo do ICMS Efetivo.
- **vBCEfet**: Valor da base de cálculo do ICMS Efetivo.
- **pICMSEfet**: Percentual do ICMS Efetivo.
- **vICMSEfet**: Valor do ICMS Efetivo.
- **enq_ipi**: Código de enquadramento do IPI.
- **cst_ipi**: Código do IPI.
- **vBCIPI**: Valor da base de cálculo do IPI.
- **pIPI**: Percentual do IPI.
- **vIPI**: Valor do IPI.
- **vBCII**: Valor da base de cálculo do II.
- **vDespAdu**: Valor das despesas aduaneiras.
- **vII**: Valor do II.
- **vIOF**: Valor do IOF.
- **cst_pis**: Código do PIS.
- **vBCPIS**: Valor da base de cálculo do PIS.
- **pPIS**: Percentual do PIS.
- **vPIS**: Valor do PIS.
- **cst_cofins**: Código do COFINS.
- **vBCCOFINS**: Valor da base de cálculo do COFINS.
- **pCOFINS**: Percentual do COFINS.
- **vCOFINS**: Valor do COFINS.
- **nDI**: Número do DI (se presente).
- **dDI**: Data do DI (se presente).
- **xLocDesemb**: Local de desembarque (se presente).
- **UFDesemb**: UF do desembarque (se presente).
- **dDesemb**: Data de desembarque (se presente).
- **tpViaTransp**: Tipo de via de transporte (se presente).
- **vAFRMM**: Valor do AFRMM (se presente).
- **tpIntermedio**: Tipo de intermediário (se presente).
- **cExportador**: Código do exportador (se presente).
- **nAdicao**: Número de adição (se presente).
- **nSeqAdic**: Número de sequência de adição (se presente).
- **cFabricante**: Código do fabricante (se presente).
- **qBCProd_pis**: Quantidade da base de cálculo do PIS.
- **vAliqProd_pis**: Valor da alíquota do PIS.
- **qBCProd_cofins**: Quantidade da base de cálculo do COFINS.
- **vAliqProd_cofins**: Valor da alíquota do COFINS.

#### Exemplo de Uso

```php
$nfe = new NFeHandler($xml);
$produtos = $nfe->getProdutos();

foreach ($produtos as $produto) {
    echo "Código do Produto: " . $produto->cProd . "\n";
    echo "Descrição: " . $produto->xProd . "\n";
    // Outros detalhes do produto...
}

```

### `getParcelas()`

#### Descrição
O método `getParcelas()` é responsável por extrair informações sobre as parcelas de pagamento associadas à nota fiscal eletrônica (NFe). Ele recupera os dados do XML da NFe, incluindo o número da fatura, o número da parcela, a data de vencimento e o valor da parcela. Essas informações são retornadas em um objeto, onde cada parcela é representada por um objeto contendo os detalhes mencionados.

#### Retorno
O método retorna um objeto contendo uma lista de parcelas, onde cada parcela é representada por um objeto com os seguintes atributos:

- `fatura`: Número da fatura associada à parcela.
- `parcela`: Número da parcela.
- `dt_vencimento`: Data de vencimento da parcela.
- `vlr_parcela`: Valor da parcela.

#### Exemplo de Uso

```php
$nfe = new NFeHandler($xml);
$parcelas = $nfe->getParcelas();

foreach ($parcelas as $parcela) {
    echo "Número da Fatura: " . $parcela->fatura . "\n";
    echo "Número da Parcela: " . $parcela->parcela . "\n";
    echo "Data de Vencimento: " . $parcela->dt_vencimento . "\n";
    echo "Valor da Parcela: " . $parcela->vlr_parcela . "\n";
}

```

### `getObs()`

#### Descrição
O método `getObs()` é responsável por extrair informações adicionais de observação da nota fiscal eletrônica (NFe). Ele recupera os dados do XML da NFe relacionados às observações fiscais e complementares. O método verifica se as tags `<infAdFisco>` e `<infCpl>` estão presentes no XML e, se estiverem, retorna os valores desses campos. Se algum campo não estiver presente, o método retorna `NULL` para esse campo.

#### Retorno
O método retorna um objeto com os seguintes atributos:

- `infAdFisco`: Informações adicionais fiscais, se presentes no XML. Caso contrário, `NULL`.
- `infCpl`: Informações complementares, se presentes no XML. Caso contrário, `NULL`.

#### Exemplo de Uso

```php
$nfe = new NFeHandler($xml);
$observacoes = $nfe->getObs();

echo "Informações Adicionais Fiscais: " . $observacoes->infAdFisco . "\n";
echo "Informações Complementares: " . $observacoes->infCpl . "\n";

```

### `getNotaCab()`

#### Descrição
O método `getNotaCab()` é responsável por consolidar e retornar todas as informações principais de uma nota fiscal eletrônica (NFe). Ele coleta dados de várias seções da NFe, incluindo chave de acesso, protocolo, informações do emitente, destinatário, totais, transporte e observações adicionais, combinando todas essas informações em um único objeto. O método utiliza outros métodos da classe, como `getChave()`, `getProtocolo()`, `getIde()`, `getEmitente()`, `getDestinatario()`, `getTotal()`, `getTransporte()` e `getObs()`, para obter os dados necessários.

#### Retorno
O método retorna um objeto contendo as seguintes informações da NFe:

- `chave`: A chave de acesso da NFe, obtida pelo método `getChave()`.
- `protocolo`: Informações sobre o protocolo da NFe, obtidas pelo método `getProtocolo()`.
- `ide`: Informações sobre a identificação da NFe, obtidas pelo método `getIde()`.
- `emitente`: Dados sobre o emitente da NFe, obtidos pelo método `getEmitente()`.
- `destinatario`: Dados sobre o destinatário da NFe, obtidos pelo método `getDestinatario()`.
- `total`: Informações sobre os totais da NFe, obtidas pelo método `getTotal()`.
- `transporte`: Informações sobre o transporte da NFe, obtidas pelo método `getTransporte()`.
- `obs`: Informações adicionais e complementares sobre a NFe, obtidas pelo método `getObs()`.

#### Exemplo de Uso

```php
$nfe = new NFeHandler($xml);
$notaCabecalho = $nfe->getNotaCab();

echo "Chave de Acesso: " . $notaCabecalho->chave . "\n";
echo "Protocolo: " . $notaCabecalho->protocolo . "\n";
echo "Emitente: " . $notaCabecalho->emitente . "\n";
echo "Destinatário: " . $notaCabecalho->destinatario . "\n";

```