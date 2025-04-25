<?php

namespace GX4\Util;

class TNfe
{
    protected $chave;
    protected $xml;
    protected $versao;

    /**
     * Construtor da classe TNfe.
     *
     * Inicializa a classe validando o conteúdo informado, que pode ser o caminho
     * de um arquivo XML ou uma string com o conteúdo do XML.
     *
     * @param string $arquivo Caminho do arquivo XML ou string com o conteúdo XML.
     * @param string $tipo Tipo de entrada: 'arquivo' para caminho de arquivo ou 'string' para conteúdo XML.
     *
     * @throws \Exception Caso o arquivo não seja válido ou não possa ser lido.
     */

    public function __construct($arquivo, $tipo = 'arquivo')
    {
        $this->validaArquivo($arquivo, $tipo);
    }

    /**
     * Exibe um ou mais valores com `var_dump` formatado em HTML.
     *
     * @param mixed ...$valores Um ou mais valores a serem exibidos.
     * @return void
     */
    public static function debug(...$valores): void
    {
        foreach ($valores as $valor) {
            echo '<pre>';
            var_dump($valor);
            echo '</pre>';
        }
    }

    public function tratadt($var)
    {
        if ($var != '') {
            $dt = new \DateTime($var);
            $var = $dt->format('Y-m-d H:i:s');
        }
        return $var;
    }

    /**
     * Valida e carrega o conteúdo XML da NFe conforme o tipo informado.
     *
     * Aceita como entrada o caminho para um arquivo local, uma URL ou diretamente
     * o conteúdo XML como string. O XML é carregado e verificado se contém dados
     * de autorização válidos.
     *
     * @param string $arquivo Caminho do arquivo, URL, ou conteúdo XML em string.
     * @param string $tipo Tipo de entrada: 'arquivo', 'url' ou 'string'.
     *
     * @throws \Exception Caso o arquivo não exista, o conteúdo seja inválido ou não contenha dados de autorização.
     */
    public function validaArquivo($arquivo, $tipo)
    {
        if ($tipo  == 'arquivo') {
            if (!file_exists($arquivo)) {
                throw new \Exception("Arquivo não encontrado!");
            }
            $this->xml = simplexml_load_file($arquivo);
        } else if ($tipo  == 'url') {
            $fileInfo  = pathinfo($arquivo);
            $arquivo   = rawurlencode($fileInfo['basename']);
            $arquivo   = @file_get_contents($fileInfo['dirname'].'/'.$arquivo);
            $this->xml = simplexml_load_string($arquivo);
        }
        else {
            $this->xml = simplexml_load_string($arquivo);
        }

        if (empty($this->xml->protNFe->infProt->nProt)) {
            throw new \Exception("Arquivo sem dados de autorização. " . $arquivo);
            return false;
        }

        $chave        = $this->xml->NFe->infNFe->attributes()->Id;
        $this->chave  = strtr(strtoupper($chave), array("NFE" => NULL));
        $this->versao = trim($this->xml->NFe->infNFe->attributes()->versao);
    }

    /**
     * Retorna as informações do protocolo de autorização da NFe.
     *
     * Extrai do XML os dados da autorização, como número do protocolo,
     * data/hora da autorização e o código de status da SEFAZ.
     *
     * @return \stdClass Objeto contendo:
     *  - protocolo_autorizacao (string): Número do protocolo de autorização.
     *  - dt_autorizacao (string): Data e hora da autorização, já formatada.
     *  - cd_status (string): Código de status da SEFAZ.
     */
    public function getProtocolo()
    {
        $prot                       = $this->xml->protNFe->infProt;
        $std                        = new \stdClass;
        $std->protocolo_autorizacao = trim($prot->nProt);
        $std->dt_autorizacao        = $this->tratadt($prot->dhRecbto);
        $std->cd_status             = trim($prot->cStat);
        return $std;
    }

    /**
     * Retorna as informações da seção <ide> da NFe.
     *
     * Essa seção contém dados de identificação da Nota Fiscal Eletrônica,
     * como número, série, data de emissão, tipo de operação, entre outros.
     *
     * @return \stdClass Objeto com os seguintes atributos:
     *                   - versao (string): Versão do layout da NFe.
     *                   - ide_natOp (string|null): Natureza da operação.
     *                   - ide_mod (string|null): Modelo do documento fiscal.
     *                   - ide_serie (string|null): Série do documento fiscal.
     *                   - ide_nNF (string|null): Número da nota fiscal.
     *                   - ide_dhEmi (string|null): Data/hora de emissão (formatada).
     *                   - ide_dhSaiEnt (string|null): Data/hora de saída ou entrada (formatada).
     *                   - ide_tpNF (string|null): Tipo da nota fiscal (entrada/saída).
     *                   - ide_idDEst (string|null): Identificador do destinatário.
     *                   - ide_cMunFG (string|null): Código do município de ocorrência do fato gerador.
     *                   - ide_tp_Imp (string|null): Formato de impressão do DANFE.
     *                   - ide_tpEmis (string|null): Tipo de emissão.
     *                   - ide_tpAmb (string|null): Tipo de ambiente (produção/homologação).
     *                   - ide_finNFe (string|null): Finalidade de emissão da NFe.
     *                   - ide_indFinal (string|null): Indicador de operação com consumidor final.
     *                   - ide_indPres (string|null): Indicador de presença do comprador.
     *                   - ide_procEmi (string|null): Processo de emissão.
     *                   - ide_verProc (string|null): Versão do processo de emissão.
     *                   - ide_dhCont (string|null): Data/hora do contingência (se houver).
     *                   - ide_xJust (string|null): Justificativa da contingência (se houver).
     */
    public function getIde()
    {
        $ide               = $this->xml->NFe->infNFe->ide;
        $std               = new \stdClass;
        $std->versao       = $this->versao;
        $std->ide_natOp    = (isset($ide->natOp)) ? trim($ide->natOp) : NULL;
        $std->ide_mod      = (isset($ide->mod)) ? trim($ide->mod) : NULL;
        $std->ide_serie    = (isset($ide->serie)) ? trim($ide->serie) : NULL;
        $std->ide_nNF      = (isset($ide->nNF)) ? trim($ide->nNF) : NULL;
        $std->ide_dhEmi    = (isset($ide->dhEmi)) ? $this->tratadt($ide->dhEmi) : NULL;
        $std->ide_dhSaiEnt = (isset($ide->dhSaiEnt)) ? $this->tratadt($ide->dhSaiEnt) : NULL;
        $std->ide_tpNF     = (isset($ide->tpNF)) ? trim($ide->tpNF) : NULL;
        $std->ide_idDEst   = (isset($ide->idDest)) ? trim($ide->idDest) : NULL;
        $std->ide_cMunFG   = (isset($ide->cMunFG)) ? trim($ide->cMunFG) : NULL;
        $std->ide_tp_Imp   = (isset($ide->tpImp)) ? trim($ide->tpImp) : NULL;
        $std->ide_tpEmis   = (isset($ide->tpEmis)) ? trim($ide->tpEmis) : NULL;
        $std->ide_tpAmb    = (isset($ide->tpAmb)) ? trim($ide->tpAmb) : NULL;
        $std->ide_finNFe   = (isset($ide->finNFe)) ? trim($ide->finNFe) : NULL;
        $std->ide_indFinal = (isset($ide->indFinal)) ? trim($ide->indFinal) : NULL;
        $std->ide_indPres  = (isset($ide->indPres)) ? trim($ide->indPres) : NULL;
        $std->ide_procEmi  = (isset($ide->procEmi)) ? trim($ide->procEmi) : NULL;
        $std->ide_verProc  = (isset($ide->verProc)) ? trim($ide->verProc) : NULL;
        $std->ide_dhCont   = (isset($ide->dhCont)) ? $this->tratadt($ide->dhCont) : NULL;
        $std->ide_xJust    = (isset($ide->xJust)) ? trim($ide->xJust) : NULL;
        return $std;
    }

    /**
     * Retorna a chave de acesso da NFe.
     *
     * A chave de acesso é um identificador único da nota fiscal eletrônica,
     * composta por diversas informações como CNPJ, data, modelo, série, número e código numérico.
     *
     * @return string Chave de acesso da NFe.
     */
    public function getChave()
    {
        return $this->chave;
    }

    /**
     * Retorna os dados do emitente da NFe.
     *
     * Extrai e organiza as informações do emitente (empresa ou pessoa que emitiu a nota fiscal),
     * incluindo dados de identificação, endereço, regime tributário e contato.
     *
     * @return \stdClass Objeto contendo os dados do emitente:
     *                   - emit_cpf: CPF do emitente (caso pessoa física)
     *                   - emit_cnpj: CNPJ do emitente (caso pessoa jurídica)
     *                   - emit_xNome: Nome ou razão social do emitente
     *                   - emit_xFant: Nome fantasia
     *                   - emit_IE: Inscrição estadual
     *                   - emit_CRT: Código do regime tributário
     *                   - emit_xLgr: Logradouro (rua/avenida)
     *                   - emit_nro: Número do endereço
     *                   - emit_xCpl: Complemento do endereço
     *                   - emit_xBairro: Bairro
     *                   - emit_cMun: Código do município
     *                   - emit_xMun: Nome do município
     *                   - emit_UF: Unidade federativa (estado)
     *                   - emit_CEP: CEP
     *                   - emit_cPais: Código do país
     *                   - emit_xPais: Nome do país
     *                   - emit_fone: Telefone de contato
     */
    public function getEmitente()
    {
        $emit              = $this->xml->NFe->infNFe->emit;
        $std               = new \stdClass;
        $std->emit_cpf     = (isset($emit->CPF)) ? trim($emit->CPF) : NULL;
        $std->emit_cnpj    = (isset($emit->CNPJ)) ? trim($emit->CNPJ) : NULL;
        $std->emit_xNome   = (isset($emit->xNome)) ? trim($emit->xNome) : NULL;
        $std->emit_xFant   = (isset($emit->xFant)) ? trim($emit->xFant) : NULL;
        $std->emit_IE      = (isset($emit->IE)) ? trim($emit->IE) : NULL;
        $std->emit_CRT     = (isset($emit->CRT)) ? trim($emit->CRT) : NULL;
        $std->emit_xLgr    = (isset($emit->enderEmit->emit_xLgr)) ? trim($emit->enderEmit->emit_xLgr) : NULL;
        $std->emit_xLgr    = (isset($emit->enderEmit->xLgr)) ? trim($emit->enderEmit->xLgr) : NULL;
        $std->emit_nro     = (isset($emit->enderEmit->nro)) ? trim($emit->enderEmit->nro) : NULL;
        $std->emit_xCpl    = (isset($emit->enderEmit->xCpl)) ? trim($emit->enderEmit->xCpl) : NULL;
        $std->emit_xBairro = (isset($emit->enderEmit->xBairro)) ? trim($emit->enderEmit->xBairro) : NULL;
        $std->emit_cMun    = (isset($emit->enderEmit->cMun)) ? trim($emit->enderEmit->cMun) : NULL;
        $std->emit_xMun    = (isset($emit->enderEmit->xMun)) ? trim($emit->enderEmit->xMun) : NULL;
        $std->emit_UF      = (isset($emit->enderEmit->UF)) ? trim($emit->enderEmit->UF) : NULL;
        $std->emit_CEP     = (isset($emit->enderEmit->CEP)) ? trim($emit->enderEmit->CEP) : NULL;
        $std->emit_cPais   = (isset($emit->enderEmit->cPais)) ? trim($emit->enderEmit->cPais) : NULL;
        $std->emit_xPais   = (isset($emit->enderEmit->xPais)) ? trim($emit->enderEmit->xPais) : ' ';
        $std->emit_fone    = (isset($emit->enderEmit->fone)) ? trim($emit->enderEmit->fone) : NULL;
        return $std;
    }

    /**
     * Retorna os dados do destinatário da NFe.
     *
     * Este método extrai e organiza as informações do destinatário da nota fiscal,
     * como CPF/CNPJ, nome, endereço, e dados complementares de identificação.
     *
     * @return \stdClass Objeto contendo os dados do destinatário:
     *                   - dest_idEstrangeiro: Identificação de estrangeiro (quando aplicável)
     *                   - dest_CPF: CPF do destinatário (caso pessoa física)
     *                   - dest_CNPJ: CNPJ do destinatário (caso pessoa jurídica)
     *                   - dest_xNome: Nome ou razão social do destinatário
     *                   - dest_xFant: Nome fantasia
     *                   - dest_IE: Inscrição estadual
     *                   - dest_indIEDest: Indicador de IE do destinatário
     *                   - dest_CRT: Código do regime tributário (quando presente)
     *                   - dest_xLgr: Logradouro (rua/avenida)
     *                   - dest_nro: Número do endereço
     *                   - dest_xCpl: Complemento do endereço
     *                   - dest_xBairro: Bairro
     *                   - dest_cMun: Código do município
     *                   - dest_xMun: Nome do município
     *                   - dest_UF: Unidade federativa (estado)
     *                   - dest_CEP: Código postal (CEP)
     *                   - dest_cPais: Código do país
     *                   - dest_xPais: Nome do país
     *                   - dest_fone: Telefone de contato
     *                   - dest_email: E-mail de contato (se fornecido)
     */
    public function getDestinatario()
    {
        $dest                    = $this->xml->NFe->infNFe->dest;
        $std                     = new \stdClass;
        $std->dest_idEstrangeiro = (isset($dest->idEstrangeiro)) ? trim($dest->idEstrangeiro) : NULL;
        $std->dest_CPF           = (isset($dest->CPF)) ? trim($dest->CPF) : NULL;
        $std->dest_CNPJ          = (isset($dest->CNPJ)) ? trim($dest->CNPJ) : NULL;
        $std->dest_xNome         = (isset($dest->xNome)) ? trim($dest->xNome) : NULL;
        $std->dest_xFant         = (isset($dest->xFant)) ? trim($dest->xFant) : NULL;
        $std->dest_IE            = (isset($dest->IE)) ? trim($dest->IE) : NULL;
        $std->dest_indIEDest     = (isset($dest->indIEDest)) ? trim($dest->indIEDest) : NULL;
        $std->dest_CRT           = (isset($dest->CRT)) ? trim($dest->CRT) : NULL;
        $std->dest_xLgr          = (isset($dest->enderDest->dest_xLgr)) ? trim($dest->enderDest->dest_xLgr) : NULL;
        $std->dest_xLgr          = (isset($dest->enderDest->xLgr)) ? trim($dest->enderDest->xLgr) : NULL;
        $std->dest_nro           = (isset($dest->enderDest->nro)) ? trim($dest->enderDest->nro) : NULL;
        $std->dest_xCpl          = (isset($dest->enderDest->xCpl)) ? trim($dest->enderDest->xCpl) : NULL;
        $std->dest_xBairro       = (isset($dest->enderDest->xBairro)) ? trim($dest->enderDest->xBairro) : NULL;
        $std->dest_cMun          = (isset($dest->enderDest->cMun)) ? trim($dest->enderDest->cMun) : NULL;
        $std->dest_xMun          = (isset($dest->enderDest->xMun)) ? trim($dest->enderDest->xMun) : NULL;
        $std->dest_UF            = (isset($dest->enderDest->UF)) ? trim($dest->enderDest->UF) : NULL;
        $std->dest_CEP           = (isset($dest->enderDest->CEP)) ? trim($dest->enderDest->CEP) : NULL;
        $std->dest_cPais         = (isset($dest->enderDest->cPais)) ? trim($dest->enderDest->cPais) : NULL;
        $std->dest_xPais         = (isset($dest->enderDest->xPais)) ? trim($dest->enderDest->xPais) : NULL;
        $std->dest_fone          = (isset($dest->enderDest->fone)) ? trim($dest->enderDest->fone) : NULL;
        $std->dest_email         = (isset($dest->enderDest->email)) ? trim($dest->enderDest->email) : NULL;
        return $std;
    }

    /**
     * Retorna os totais da nota fiscal.
     *
     * Este método extrai os valores totais da NFe a partir do grupo `ICMSTot`, incluindo
     * os valores de impostos e, quando presente, os dados do faturamento (grupo `fat`).
     *
     * @return \SimpleXMLElement Objeto contendo os totais da NFe, incluindo:
     *                           - vBC: Valor da base de cálculo do ICMS
     *                           - vICMS: Valor total do ICMS
     *                           - vICMSDeson: Valor do ICMS desonerado
     *                           - vFCP: Valor total do FCP
     *                           - vBCST: Valor da base de cálculo do ICMS ST
     *                           - vST: Valor total do ICMS ST
     *                           - vFCPST: Valor do FCP retido por ST
     *                           - vFCPSTRet: Valor do FCP retido anteriormente por ST
     *                           - vProd: Valor total dos produtos e serviços
     *                           - vFrete: Valor do frete
     *                           - vSeg: Valor do seguro
     *                           - vDesc: Valor do desconto
     *                           - vII: Valor do imposto de importação
     *                           - vIPI: Valor do IPI
     *                           - vIPIDevol: Valor do IPI devolvido
     *                           - vPIS: Valor do PIS
     *                           - vCOFINS: Valor do COFINS
     *                           - vOutro: Outros valores
     *                           - vNF: Valor total da nota fiscal
     *                           - fat_vOrig: Valor original da fatura (se existir)
     *                           - fat_vDesc: Valor do desconto na fatura (se existir)
     *                           - fat_vLiq: Valor líquido da fatura (se existir)
     */
    public function getTotal()
    {
        $retorno = $this->xml->NFe->infNFe->total->ICMSTot;
        if (isset($this->xml->NFe->infNFe->cobr->fat->vLiq)) {
            $retorno->fat_vOrig = $this->xml->NFe->infNFe->cobr->fat->vOrig;
            $retorno->fat_vDesc = $this->xml->NFe->infNFe->cobr->fat->vDesc;
            $retorno->fat_vLiq  = $this->xml->NFe->infNFe->cobr->fat->vLiq;
        }
        return $retorno;
    }

    /**
     * Retorna os dados de transporte da NFe.
     *
     * Este método extrai informações do grupo de transporte da nota fiscal, incluindo
     * o tipo de frete, dados do transportador, veículo e volumes transportados.
     *
     * @return \stdClass Objeto contendo as informações de transporte, com os seguintes campos:
     *                   - modFrete: Modalidade do frete (por conta do emitente, destinatário, etc.)
     *                   - transp_CNPJ: CNPJ do transportador (se houver)
     *                   - transp_CPF: CPF do transportador (se houver)
     *                   - transp_xNome: Nome do transportador
     *                   - transp_xEnder: Endereço do transportador
     *                   - transp_xMun: Município do transportador
     *                   - transp_UF: UF do transportador
     *                   - veic_placa: Placa do veículo
     *                   - veic_UF: UF do veículo
     *                   - veic_RNTC: Registro Nacional de Transportador de Carga
     *                   - vol_qVol: Quantidade de volumes
     *                   - vol_nVol: Identificação dos volumes
     *                   - vol_esp: Espécie dos volumes
     *                   - vol_marca: Marca dos volumes
     *                   - vol_pesoL: Peso líquido dos volumes
     *                   - vol_pesoB: Peso bruto dos volumes
     */
    public function getTransporte()
    {
        $transp             = $this->xml->NFe->infNFe->transp;
        $std                = new \stdClass;
        $std->modFrete      = trim($transp->modFrete);
        $std->transp_CNPJ   = (isset($transp->transporta->CNPJ)) ? trim($transp->transporta->CNPJ) : NULL;
        $std->transp_CPF    = (isset($transp->transporta->CPF)) ? trim($transp->transporta->CPF) : NULL;
        $std->transp_xNome  = (isset($transp->transporta->xNome)) ? trim($transp->transporta->xNome) : NULL;
        $std->transp_xEnder = (isset($transp->transporta->xEnder)) ? trim($transp->transporta->xEnder) : NULL;
        $std->transp_xMun   = (isset($transp->transporta->xMun)) ? trim($transp->transporta->xMun) : NULL;
        $std->transp_UF     = (isset($transp->transporta->UF)) ? trim($transp->transporta->UF) : NULL;
        $std->veic_placa    = (isset($transp->veicTransp->placa)) ? trim($transp->veicTransp->placa) : NULL;
        $std->veic_UF       = (isset($transp->veicTransp->UF)) ? trim($transp->veicTransp->UF) : NULL;
        $std->veic_RNTC     = (isset($transp->veicTransp->RNTC)) ? trim($transp->veicTransp->RNTC) : NULL;
        $std->vol_qVol      = (isset($transp->vol->qVol)) ? trim($transp->vol->qVol) : NULL;
        $std->vol_nVol      = (isset($transp->vol->nVol)) ? trim($transp->vol->nVol) : NULL;
        $std->vol_esp       = (isset($transp->vol->esp)) ? trim($transp->vol->esp) : NULL;
        $std->vol_marca     = (isset($transp->vol->marca)) ? trim($transp->vol->marca) : NULL;
        $std->vol_pesoL     = (isset($transp->vol->pesoL)) ? trim($transp->vol->pesoL) : NULL;
        $std->vol_pesoB     = (isset($transp->vol->pesoB)) ? trim($transp->vol->pesoB) : NULL;
        return $std;
    }

    /**
     * Processa e formata as informações do ICMS de acordo com o tipo de tributação.
     *
     * Este método verifica os diferentes tipos de ICMS presentes no objeto `$var` (ICMS00, ICMS10, ICMS20, ICMS30, ICMS40, ICMS51, ICMS60, ICMS70, ICMS90, ICMSSN101, ICMSSN102, ICMSSN201, ICMSSN202) e extrai as informações relevantes para cada tipo. Em seguida, essas informações são armazenadas em um objeto `stdClass` que é retornado para posterior uso.
     *
     * @param object $var Objeto contendo as informações de ICMS a serem processadas. Espera-se que o objeto tenha propriedades específicas como ICMS00, ICMS10, ICMS20, etc.
     *
     * @return object Retorna um objeto `stdClass` com os dados processados do ICMS, onde cada tipo de ICMS é tratado de acordo com suas especificidades.
     */
    public function ICMS($var)
    {
        $std = new \stdClass;

        if (isset($var->ICMS->ICMS00)) {
            $l                = $var->ICMS->ICMS00;
            $std->CST         = trim($l->CST);
            $std->orig        = trim($l->orig);
            $std->modBC       = trim($l->modBC);
            $std->vBC         = trim($l->vBC);
            $std->pICMS       = trim($l->pICMS);
            $std->vICMS       = trim($l->vICMS);
            $std->pFCP        = (isset($l->pFCP)) ? trim($l->pFCP) : 0;
            $std->vFCP        = (isset($l->vFCP)) ? trim($l->vFCP) : 0;
            $std->tipoTributo = 'T';
        }

        if (isset($var->ICMS->ICMS10)) {
            $l                = $var->ICMS->ICMS10;
            $std->orig        = trim($l->orig);
            $std->CST         = trim($l->CST);
            $std->modBC       = trim($l->modBC);
            $std->vBC         = trim($l->vBC);
            $std->pICMS       = trim($l->pICMS);
            $std->vICMS       = trim($l->vICMS);
            $std->modBCST     = trim($l->modBCST);
            $std->pMVAST      = trim($l->pMVAST);
            $std->pRedBCST    = trim($l->pRedBCST);
            $std->vBCST       = trim($l->vBCST);
            $std->pICMSST     = trim($l->pICMSST);
            $std->vICMSST     = trim($l->vICMSST);
            $std->vBCFCP      = (isset($l->vBCFCP)) ? trim($l->vBCFCP) : 0;
            $std->pFCP        = (isset($l->pFCP)) ? trim($l->pFCP) : 0;
            $std->vFCP        = (isset($l->vFCP)) ? trim($l->vFCP) : 0;
            $std->vBCFCPST    = (isset($l->vBCFCPST)) ? trim($l->vBCFCPST) : 0;
            $std->pFCPST      = (isset($l->pFCPST)) ? trim($l->pFCPST) : 0;
            $std->vFCPST      = (isset($l->vFCPST)) ? trim($l->vFCPST) : 0;
            $std->tipoTributo = 'T';
        }

        if (isset($var->ICMS->ICMS20)) {
            $l                = $var->ICMS->ICMS20;
            $std->CST         = trim($l->CST);
            $std->orig        = trim($l->orig);
            $std->modBC       = trim($l->modBC);
            $std->pRedBC      = trim($l->pRedBC);
            $std->vBC         = trim($l->vBC);
            $std->pICMS       = trim($l->pICMS);
            $std->vICMS       = trim($l->vICMS);
            $std->vBCFCP      = (isset($l->vBCFCP)) ? trim($l->vBCFCP) : 0;
            $std->pFCP        = (isset($l->pFCP)) ? trim($l->pFCP) : 0;
            $std->vFCP        = (isset($l->vFCP)) ? trim($l->vFCP) : 0;
            $std->vICMSDeson  = (isset($l->vICMSDeson)) ? trim($l->vICMSDeson) : 0;
            $std->motDesICMS  = (isset($l->motDesICMS)) ? trim($l->motDesICMS) : 0;
            $std->tipoTributo = 'T';
        }

        if (isset($var->ICMS->ICMS30)) {
            $l                = $var->ICMS->ICMS30;
            $std->CST         = trim($l->CST);
            $std->orig        = trim($l->orig);
            $std->modBCST     = trim($l->modBCST);
            $std->pMVAST      = trim($l->pMVAST);
            $std->pRedBCST    = trim($l->pRedBCST);
            $std->vBCST       = trim($l->vBCST);
            $std->pICMSST     = trim($l->pICMSST);
            $std->vICMSST     = trim($l->vICMSST);
            $std->vBCFCPST    = (isset($l->vBCFCPST)) ? trim($l->vBCFCPST) : 0;
            $std->pFCPST      = (isset($l->pFCPST)) ? trim($l->pFCPST) : 0;
            $std->vFCPST      = (isset($l->vFCPST)) ? trim($l->vFCPST) : 0;
            $std->vICMSDeson  = (isset($l->vICMSDeson)) ? trim($l->vICMSDeson) : 0;
            $std->motDesICMS  = (isset($l->motDesICMS)) ? trim($l->motDesICMS) : 0;
            $std->tipoTributo = 'I';
        }

        if (isset($var->ICMS->ICMS40)) {
            $l                = $var->ICMS->ICMS40;
            $std->CST         = trim($l->CST);
            $std->orig        = trim($l->orig);
            $std->vICMSDeson  = (isset($l->vICMSDeson)) ? trim($l->vICMSDeson) : 0;
            $std->motDesICMS  = (isset($l->motDesICMS)) ? trim($l->motDesICMS) : 0;
            $std->tipoTributo = 'I';
        }

        if (isset($var->ICMS->ICMS51)) {
            $l                = $var->ICMS->ICMS51;
            $std->CST         = trim($l->CST);
            $std->orig        = trim($l->orig);
            $std->modBC       = (isset($l->modBC)) ? trim($l->modBC) : 0;
            $std->pRedBC      = (isset($l->pRedBC)) ? trim($l->pRedBC) : 0;
            $std->vBC         = (isset($l->vBC)) ? trim($l->vBC) : 0;
            $std->pICMS       = (isset($l->pICMS)) ? trim($l->pICMS) : 0;
            $std->vICMSOp     = (isset($l->vICMSOp)) ? trim($l->vICMSOp) : 0;
            $std->pDif        = (isset($l->pDif)) ? trim($l->pDif) : 0;
            $std->vICMSDif    = (isset($l->vICMSDif)) ? trim($l->vICMSDif) : 0;
            $std->vICMS       = (isset($l->vICMS)) ? trim($l->vICMS) : 0;
            $std->vBCFCP      = (isset($l->vBCFCP)) ? trim($l->vBCFCP) : 0;
            $std->pFCP        = (isset($l->pFCP)) ? trim($l->pFCP) : 0;
            $std->vFCP        = (isset($l->vFCP)) ? trim($l->vFCP) : 0;
            $std->tipoTributo = 'O';
        }

        if (isset($var->ICMS->ICMS60)) {
            $l                    = $var->ICMS->ICMS60;
            $std->CST             = trim($l->CST);
            $std->orig            = trim($l->orig);
            $std->vBCSTRet        = trim($l->vBCSTRet);
            $std->pST             = trim($l->pST);
            $std->vICMSSubstituto = trim($l->vICMSSubstituto);
            $std->vICMSSTRet      = trim($l->vICMSSTRet);
            $std->vBCSTDest       = trim($l->vBCSTDest);
            $std->vICMSSTDest     = trim($l->vICMSSTDest);
            $std->vBCFCPSTRet     = (isset($l->vBCFCPSTRet)) ? trim($l->vBCFCPSTRet) : 0;
            $std->pFCPSTRet       = (isset($l->pFCPSTRet)) ? trim($l->pFCPSTRet) : 0;
            $std->vFCPSTRet       = (isset($l->vFCPSTRet)) ? trim($l->vFCPSTRet) : 0;
            $std->pRedBCEfet      = (isset($l->pRedBCEfet)) ? trim($l->pRedBCEfet) : 0;
            $std->vBCEfet         = (isset($l->vBCEfet)) ? trim($l->vBCEfet) : 0;
            $std->pICMSEfet       = (isset($l->pICMSEfet)) ? trim($l->pICMSEfet) : 0;
            $std->vICMSEfet       = (isset($l->vICMSEfet)) ? trim($l->vICMSEfet) : 0;
            $std->tipoTributo     = 'I';
        }

        if (isset($var->ICMS->ICMS70)) {
            $l                = $var->ICMS->ICMS70;
            $std->orig        = trim($l->orig);
            $std->CST         = trim($l->CST);
            $std->modBC       = trim($l->modBC);
            $std->pRedBC      = trim($l->pRedBC);
            $std->vBC         = trim($l->vBC);
            $std->pICMS       = trim($l->pICMS);
            $std->vICMS       = trim($l->vICMS);
            $std->vBCFCP      = (isset($l->vBCFCP)) ? $l->vBCFCP : 0;
            $std->pFCP        = (isset($l->pFCP)) ? $l->pFCP : 0;
            $std->vFCP        = (isset($l->vFCP)) ? $l->vFCP : 0;
            $std->modBCST     = trim($l->modBCST);
            $std->pMVAST      = trim($l->pMVAST);
            $std->pRedBCST    = trim($l->pRedBCST);
            $std->vBCST       = trim($l->vBCST);
            $std->pICMSST     = trim($l->pICMSST);
            $std->vICMSST     = trim($l->vICMSST);
            $std->vBCFCPST    = (isset($l->vBCFCPST)) ? trim($l->vBCFCPST) : 0;
            $std->pFCPST      = (isset($l->pFCPST)) ? trim($l->pFCPST) : 0;
            $std->vFCPST      = (isset($l->vFCPST)) ? trim($l->vFCPST) : 0;
            $std->vICMSDeson  = (isset($l->vICMSDeson)) ? trim($l->vICMSDeson) : 0;
            $std->motDesICMS  = (isset($l->motDesICMS)) ? trim($l->motDesICMS) : 0;
            $std->tipoTributo = 'T';
        }

        if (isset($var->ICMS->ICMS90)) {
            $l                = $var->ICMS->ICMS90;
            $std->orig        = trim($l->orig);
            $std->CST         = trim($l->CST);
            $std->modBC       = trim($l->modBC);
            $std->modBC       = (isset($l->modBC)) ? trim($l->modBC) : 0;
            $std->vBC         = (isset($l->vBC)) ? trim($l->vBC) : 0;
            $std->pRedBC      = (isset($l->pRedBC)) ? trim($l->pRedBC) : 0;
            $std->pICMS       = (isset($l->pICMS)) ? trim($l->pICMS) : 0;
            $std->vICMS       = (isset($l->vICMS)) ? trim($l->vICMS) : 0;
            $std->vBCFCP      = (isset($l->vBCFCP)) ? trim($l->vBCFCP) : 0;
            $std->pFCP        = (isset($l->pFCP)) ? trim($l->pFCP) : 0;
            $std->vFCP        = (isset($l->vFCP)) ? trim($l->vFCP) : 0;
            $std->modBCST     = (isset($l->modBCST)) ? trim($l->modBCST) : 0;
            $std->pMVAST      = (isset($l->pMVAST)) ? trim($l->pMVAST) : 0;
            $std->pRedBCST    = (isset($l->pRedBCST)) ? trim($l->pRedBCST) : 0;
            $std->vBCST       = (isset($l->vBCST)) ? trim($l->vBCST) : 0;
            $std->pICMSST     = (isset($l->pICMSST)) ? trim($l->pICMSST) : 0;
            $std->vICMSST     = (isset($l->vICMSST)) ? trim($l->vICMSST) : 0;
            $std->vBCFCPST    = (isset($l->vBCFCPST)) ? trim($l->vBCFCPST) : 0;
            $std->pFCPST      = (isset($l->pFCPST)) ? trim($l->pFCPST) : 0;
            $std->vFCPST      = (isset($l->vFCPST)) ? trim($l->vFCPST) : 0;
            $std->vICMSDeson  = (isset($l->vICMSDeson)) ? trim($l->vICMSDeson) : 0;
            $std->motDesICMS  = (isset($l->motDesICMS)) ? trim($l->motDesICMS) : 0;
            $std->tipoTributo = 'T';
        }

        if (isset($var->ICMS->ICMSSN101)) {
            $l                = $var->ICMS->ICMSSN101;
            $std->CST         = trim($l->CSOSN);
            $std->orig        = trim($l->orig);
            $std->pCredSN     = trim($l->pCredSN);
            $std->vCredICMSSN = trim($l->vCredICMSSN);
            $std->tipoTributo = 'T';
        }

        if (isset($var->ICMS->ICMSSN102)) {
            $l                = $var->ICMS->ICMSSN102;
            $std->CST         = trim($l->CSOSN);
            $std->orig        = trim($l->orig);
            $std->tipoTributo = 'O';
        }

        if (isset($var->ICMS->ICMSSN201)) {
            $l                = $var->ICMS->ICMSSN201;
            $std->CST         = trim($l->CSOSN);
            $std->orig        = trim($l->orig);
            $std->modBCST     = (isset($l->modBCST)) ? trim($l->modBCST) : 0;
            $std->pMVAST      = (isset($l->pMVAST)) ? trim($l->pMVAST) : 0;
            $std->pRedBCST    = (isset($l->pRedBCST)) ? trim($l->pRedBCST) : 0;
            $std->vBCST       = (isset($l->vBCST)) ? trim($l->vBCST) : 0;
            $std->pICMSST     = (isset($l->pICMSST)) ? trim($l->pICMSST) : 0;
            $std->vICMSST     = (isset($l->vICMSST)) ? trim($l->vICMSST) : 0;
            $std->vBCFCPST    = (isset($l->vBCFCPST)) ? trim($l->vBCFCPST) : 0;
            $std->pFCPST      = (isset($l->pFCPST)) ? trim($l->pFCPST) : 0;
            $std->vFCPST      = (isset($l->vFCPST)) ? trim($l->vFCPST) : 0;
            $std->pCredSN     = (isset($l->pCredSN)) ? trim($l->pCredSN) : 0;
            $std->vCredICMSSN = (isset($l->vCredICMSSN)) ? trim($l->vCredICMSSN) : 0;
            $std->tipoTributo = 'T';
        }

        if (isset($var->ICMS->ICMSSN202)) {
            $l                = $var->ICMS->ICMSSN202;
            $std->CST         = trim($l->CSOSN);
            $std->orig        = trim($l->orig);
            $std->modBCST     = (isset($l->modBCST)) ? trim($l->modBCST) : 0;
            $std->pMVAST      = (isset($l->pMVAST)) ? trim($l->pMVAST) : 0;
            $std->pRedBCST    = (isset($l->pRedBCST)) ? trim($l->pRedBCST) : 0;
            $std->vBCST       = (isset($l->vBCST)) ? trim($l->vBCST) : 0;
            $std->pICMSST     = (isset($l->pICMSST)) ? trim($l->pICMSST) : 0;
            $std->vICMSST     = (isset($l->vICMSST)) ? trim($l->vICMSST) : 0;
            $std->vBCFCPST    = (isset($l->vBCFCPST)) ? trim($l->vBCFCPST) : 0;
            $std->pFCPST      = (isset($l->pFCPST)) ? trim($l->pFCPST) : 0;
            $std->vFCPST      = (isset($l->vFCPST)) ? trim($l->vFCPST) : 0;
            $std->tipoTributo = 'O';
        }

        if (isset($var->ICMS->ICMSSN500)) {
            $l                    = $var->ICMS->ICMSSN500;
            $std->CST             = trim($l->CSOSN);
            $std->orig            = trim($l->orig);
            $std->vBCSTRet        = trim($l->vBCSTRet);
            $std->pST             = trim($l->pST);
            $std->vICMSSubstituto = (isset($l->vICMSSubstituto)) ? trim($l->vICMSSubstituto) : 0;
            $std->vICMSSTRet      = trim($l->vICMSSTRet);
            $std->vBCFCPSTRet     = (isset($l->vBCFCPSTRet)) ? trim($l->vBCFCPSTRet) : 0;
            $std->pFCPSTRet       = (isset($l->pFCPSTRet)) ? trim($l->pFCPSTRet) : 0;
            $std->vFCPSTRet       = (isset($l->vFCPSTRet)) ? trim($l->vFCPSTRet) : 0;
            $std->pRedBCEfet      = (isset($l->pRedBCEfet)) ? trim($l->pRedBCEfet) : 0;
            $std->vBCEfet         = (isset($l->vBCEfet)) ? trim($l->vBCEfet) : 0;
            $std->pICMSEfet       = (isset($l->pICMSEfet)) ? trim($l->pICMSEfet) : 0;
            $std->vICMSEfet       = (isset($l->vICMSEfet)) ? trim($l->vICMSEfet) : 0;
            $std->tipoTributo     = 'O';
        }

        if (isset($var->ICMS->ICMSSN900)) {
            $l                = $var->ICMS->ICMSSN900;
            $std->orig        = trim($l->orig);
            $std->CST         = trim($l->CSOSN);
            $std->modBC       = trim($l->modBC);
            $std->vBC         = (isset($l->vBC)) ? trim($l->vBC) : 0;
            $std->pRedBC      = (isset($l->pRedBC)) ? trim($l->pRedBC) : 0;
            $std->pICMS       = (isset($l->pICMS)) ? trim($l->pICMS) : 0;
            $std->vICMS       = (isset($l->vICMS)) ? trim($l->vICMS) : 0;
            $std->modBCST     = (isset($l->modBCST)) ? trim($l->modBCST) : 0;
            $std->pMVAST      = (isset($l->pMVAST)) ? trim($l->pMVAST) : 0;
            $std->pRedBCST    = (isset($l->pRedBCST)) ? trim($l->pRedBCST) : 0;
            $std->vBCST       = (isset($l->vBCST)) ? trim($l->vBCST) : 0;
            $std->pICMSST     = (isset($l->pICMSST)) ? trim($l->pICMSST) : 0;
            $std->vICMSST     = (isset($l->vICMSST)) ? trim($l->vICMSST) : 0;
            $std->vBCFCPST    = (isset($l->vBCFCPST)) ? trim($l->vBCFCPST) : 0;
            $std->pFCPST      = (isset($l->pFCPST)) ? trim($l->pFCPST) : 0;
            $std->vFCPST      = (isset($l->vFCPST)) ? trim($l->vFCPST) : 0;
            $std->pCredSN     = (isset($l->pCredSN)) ? trim($l->pCredSN) : 0;
            $std->vCredICMSSN = (isset($l->vCredICMSSN)) ? trim($l->vCredICMSSN) : 0;
            $std->tipoTributo = 'T';
        }

        if (isset($var->ICMS->ICMSST)) {
            $l                = $var->ICMS->ICMSST;
            $std->orig        = trim($l->orig);
            $std->CST         = trim($l->CST);
            $std->modBC       = trim($l->modBC);
            $std->modBC       = (isset($l->modBC)) ? trim($l->modBC) : 0;
            $std->vBC         = (isset($l->vBC)) ? trim($l->vBC) : 0;
            $std->pRedBC      = (isset($l->pRedBC)) ? trim($l->pRedBC) : 0;
            $std->pICMS       = (isset($l->pICMS)) ? trim($l->pICMS) : 0;
            $std->vICMS       = (isset($l->vICMS)) ? trim($l->vICMS) : 0;
            $std->vBCFCP      = (isset($l->vBCFCP)) ? trim($l->vBCFCP) : 0;
            $std->pFCP        = (isset($l->pFCP)) ? trim($l->pFCP) : 0;
            $std->vFCP        = (isset($l->vFCP)) ? trim($l->vFCP) : 0;
            $std->modBCST     = (isset($l->modBCST)) ? trim($l->modBCST) : 0;
            $std->pMVAST      = (isset($l->pMVAST)) ? trim($l->pMVAST) : 0;
            $std->pRedBCST    = (isset($l->pRedBCST)) ? trim($l->pRedBCST) : 0;
            $std->vBCST       = (isset($l->vBCST)) ? trim($l->vBCST) : 0;
            $std->pICMSST     = (isset($l->pICMSST)) ? trim($l->pICMSST) : 0;
            $std->vICMSST     = (isset($l->vICMSST)) ? trim($l->vICMSST) : 0;
            $std->vBCFCPST    = (isset($l->vBCFCPST)) ? trim($l->vBCFCPST) : 0;
            $std->pFCPST      = (isset($l->pFCPST)) ? trim($l->pFCPST) : 0;
            $std->vFCPST      = (isset($l->vFCPST)) ? trim($l->vFCPST) : 0;
            $std->vICMSDeson  = (isset($l->vICMSDeson)) ? trim($l->vICMSDeson) : 0;
            $std->motDesICMS  = (isset($l->motDesICMS)) ? trim($l->motDesICMS) : 0;
            $std->tipoTributo = 'O';
        }

        return $std;
    }

    /**
     * Retorna os dados do imposto IPI (Imposto sobre Produtos Industrializados) da nota fiscal.
     *
     * Este método extrai os dados do grupo `IPI` da NF-e, podendo considerar tanto o grupo
     * `IPITrib` (IPI tributado) quanto o `IPINT` (IPI não tributado). Os campos retornados
     * variam conforme a presença de cada grupo, e valores ausentes são preenchidos com zero.
     *
     * @param object $var Objeto contendo a estrutura XML da NF-e com o grupo `IPI`.
     * @return \stdClass Objeto contendo os dados extraídos do IPI, incluindo:
     *                   - cEnq: Código de enquadramento legal do IPI (quando houver)
     *                   - CST: Código de situação tributária do IPI
     *                   - vBC: Valor da base de cálculo do IPI (quando houver)
     *                   - pIPI: Alíquota do IPI, em percentual (quando houver)
     *                   - vIPI: Valor do IPI (quando houver)
     */
    public function IPI($var)
    {
        $std = new \stdClass;

        if (isset($var->IPI->IPITrib)) {
            $l         = $var->IPI->IPITrib;
            $std->cEnq = (isset($l->cEnq)) ? trim($l->cEnq) : 0;
            $std->CST  = (isset($l->CST)) ? trim($l->CST) : 0;
            $std->vBC  = (isset($l->vBC)) ? trim($l->vBC) : 0;
            $std->pIPI = (isset($l->pIPI)) ? trim($l->pIPI) : 0;
            $std->vIPI = (isset($l->vIPI)) ? trim($l->vIPI) : 0;
        }

        if (isset($var->IPI->IPINT)) {
            $l        = $var->IPI->IPINT;
            $std->CST = (isset($l->CST)) ? trim($l->CST) : 0;
        }

        return $std;
    }

    /**
     * Retorna os dados do imposto II (Imposto de Importação) da nota fiscal.
     *
     * Este método extrai os valores do grupo `II` da NF-e, referentes ao imposto de importação.
     * Os campos são retornados quando presentes e, caso ausentes, são preenchidos com zero.
     *
     * @param object $var Objeto contendo a estrutura XML da NF-e com o grupo `II`.
     * @return \stdClass Objeto contendo os dados extraídos do II, incluindo:
     *                   - vBC: Valor da base de cálculo do imposto de importação
     *                   - vDespAdu: Valor das despesas aduaneiras
     *                   - vII: Valor do imposto de importação
     *                   - vIOF: Valor do IOF (Imposto sobre Operações Financeiras)
     */
    public function II($var)
    {
        $std = new \stdClass;

        if (isset($var->II)) {
            $l             = $var->II;
            $std->vBC      = (isset($l->vBC)) ? trim($l->vBC) : 0;
            $std->vDespAdu = (isset($l->vDespAdu)) ? trim($l->vDespAdu) : 0;
            $std->vII      = (isset($l->vII)) ? trim($l->vII) : 0;
            $std->vIOF     = (isset($l->vIOF)) ? trim($l->vIOF) : 0;
        }

        return $std;
    }

    /**
     * Retorna os dados do imposto PIS (Programa de Integração Social) da nota fiscal.
     *
     * Este método extrai os dados do grupo `PIS` da NF-e, identificando o tipo de tributação
     * utilizado: alíquota percentual (`PISAliq`), quantidade (`PISQtde`), outras (`PISOutr`)
     * ou não tributado (`PISNT`). Os campos retornados variam conforme o tipo de tributação presente.
     *
     * @param object $var Objeto contendo a estrutura XML da NF-e com o grupo `PIS`.
     * @return \stdClass Objeto contendo os dados extraídos do PIS, incluindo:
     *                   - CST: Código de situação tributária do PIS
     *                   - vBC: Valor da base de cálculo do PIS (quando aplicável)
     *                   - pPIS: Alíquota do PIS em percentual (quando aplicável)
     *                   - vPIS: Valor do PIS
     *                   - qBCProd_pis: Quantidade vendida para base de cálculo (quando aplicável)
     *                   - vAliqProd_pis: Valor da alíquota por unidade do produto (quando aplicável)
     */
    public function PIS($var)
    {
        $std = new \stdClass;

        if (isset($var->PIS->PISAliq)) {
            $l         = $var->PIS->PISAliq;
            $std->CST  = (isset($l->CST)) ? trim($l->CST) : 0;
            $std->vBC  = (isset($l->vBC)) ? trim($l->vBC) : 0;
            $std->pPIS = (isset($l->pPIS)) ? trim($l->pPIS) : 0;
            $std->vPIS = (isset($l->vPIS)) ? trim($l->vPIS) : 0;
        }

        if (isset($var->PIS->PISNT)) {
            $l        = $var->PIS->PISNT;
            $std->CST = (isset($l->CST)) ? trim($l->CST) : 0;
        }

        if (isset($var->PIS->PISOutr)) {
            $l         = $var->PIS->PISOutr;
            $std->CST  = (isset($l->CST)) ? trim($l->CST) : 0;
            $std->vBC  = (isset($l->vBC)) ? trim($l->vBC) : 0;
            $std->pPIS = (isset($l->pPIS)) ? trim($l->pPIS) : 0;
            $std->vPIS = (isset($l->vPIS)) ? trim($l->vPIS) : 0;
        }

        if (isset($var->PIS->PISQtde)) {
            $l                  = $var->PIS->PISQtde;
            $std->CST           = (isset($l->CST)) ? trim($l->CST) : 0;
            $std->vBC           = (isset($l->vBC)) ? trim($l->vBC) : 0;
            $std->pPIS          = (isset($l->pPIS)) ? trim($l->pPIS) : 0;
            $std->vPIS          = (isset($l->vPIS)) ? trim($l->vPIS) : 0;
            $std->qBCProd_pis   = (isset($l->qBCProd)) ? trim($l->qBCProd) : 0;
            $std->vAliqProd_pis = (isset($l->vAliqProd)) ? trim($l->vAliqProd) : 0;
        }

        return $std;
    }

    /**
     * Retorna os dados do imposto COFINS (Contribuição para o Financiamento da Seguridade Social) da nota fiscal.
     *
     * Este método extrai os dados do grupo `COFINS` da NF-e, considerando os diferentes tipos de tributação:
     * alíquota percentual (`COFINSAliq`), por quantidade (`COFINSQtde`), outras formas (`COFINSOutr`) ou
     * não tributado (`COFINSNT`). Os campos retornados variam de acordo com o grupo presente.
     *
     * @param object $var Objeto contendo a estrutura XML da NF-e com o grupo `COFINS`.
     * @return \stdClass Objeto contendo os dados extraídos do COFINS, incluindo:
     *                   - CST: Código de situação tributária do COFINS
     *                   - vBC: Valor da base de cálculo do COFINS (quando aplicável)
     *                   - pCOFINS: Alíquota do COFINS em percentual (quando aplicável)
     *                   - vCOFINS: Valor do COFINS
     *                   - qBCProd_cofins: Quantidade vendida para base de cálculo (quando aplicável)
     *                   - vAliqProd_cofins: Valor da alíquota por unidade do produto (quando aplicável)
     */
    public function COFINS($var)
    {
        $std = new \stdClass;

        if (isset($var->COFINS->COFINSAliq)) {
            $l            = $var->COFINS->COFINSAliq;
            $std->CST     = (isset($l->CST)) ? trim($l->CST) : 0;
            $std->vBC     = (isset($l->vBC)) ? trim($l->vBC) : 0;
            $std->pCOFINS = (isset($l->pCOFINS)) ? trim($l->pCOFINS) : 0;
            $std->vCOFINS = (isset($l->vCOFINS)) ? trim($l->vCOFINS) : 0;
        }

        if (isset($var->COFINS->COFINSNT)) {
            $l        = $var->COFINS->COFINSNT;
            $std->CST = (isset($l->CST)) ? trim($l->CST) : 0;
        }

        if (isset($var->COFINS->COFINSOutr)) {
            $l            = $var->COFINS->COFINSOutr;
            $std->CST     = (isset($l->CST)) ? trim($l->CST) : 0;
            $std->vBC     = (isset($l->vBC)) ? trim($l->vBC) : 0;
            $std->pCOFINS = (isset($l->pCOFINS)) ? trim($l->pCOFINS) : 0;
            $std->vCOFINS = (isset($l->vCOFINS)) ? trim($l->vCOFINS) : 0;
        }

        if (isset($var->COFINS->COFINSQtde)) {
            $l                     = $var->COFINS->COFINSQtde;
            $std->CST              = (isset($l->CST)) ? trim($l->CST) : 0;
            $std->vBC              = (isset($l->vBC)) ? trim($l->vBC) : 0;
            $std->pCOFINS          = (isset($l->pCOFINS)) ? trim($l->pCOFINS) : 0;
            $std->vCOFINS          = (isset($l->vCOFINS)) ? trim($l->vCOFINS) : 0;
            $std->qBCProd_cofins   = (isset($l->qBCProd)) ? trim($l->qBCProd) : 0;
            $std->vAliqProd_cofins = (isset($l->vAliqProd)) ? trim($l->vAliqProd) : 0;
        }

        return $std;
    }

    /**
     * Retorna os dados dos produtos e seus impostos associados na nota fiscal eletrônica (NF-e).
     *
     * Este método extrai os dados do grupo `det` da NF-e, que contém as informações dos itens de venda
     * (produtos ou serviços), incluindo os dados do produto como código, descrição, quantidade, valor unitário,
     * entre outros. Além disso, também são extraídos os impostos relacionados ao produto, como ICMS, IPI, II, PIS
     * e COFINS. Para cada produto, os dados são retornados em um objeto com os campos relevantes para a tributação.
     *
     * @return object Objeto contendo uma lista de produtos, cada um representado como um objeto com os campos:
     *                - nItem: Número do item na nota fiscal
     *                - cProd: Código do produto
     *                - cEAN: Código de barras do produto
     *                - xProd: Descrição do produto
     *                - NCM: Código NCM do produto
     *                - vProd: Valor total do produto
     *                - ICMS, IPI, II, PIS, COFINS: Dados relacionados aos impostos do produto, conforme a tributação
     *                - vFrete, vSeg, vDesc, vOutro: Valores adicionais como frete, seguro, desconto e outros
     *                - Dados adicionais relacionados ao DI (Declaração de Importação), quando aplicável.
     */
    public function getProdutos()
    {
        $Produtos = $this->xml->NFe->infNFe->det;
        $Itens    = [];

        foreach ($Produtos as $l) {
            $ICMS   = $this->ICMS($l->imposto);
            $IPI    = $this->IPI($l->imposto);
            $II     = $this->II($l->imposto);
            $PIS    = $this->PIS($l->imposto);
            $COFINS = $this->COFINS($l->imposto);

            $std                   = new \stdClass;
            $std->nItem            = $l->attributes()['nItem'];
            $std->cProd            = trim($l->prod->cProd);
            $std->cEAN             = trim($l->prod->cEAN);
            $std->xProd            = trim($l->prod->xProd);
            $std->NCM              = trim($l->prod->NCM);
            $std->nve              = (isset($l->prod->NVE)) ? trim($l->prod->NVE) : NULL;
            $std->cest             = (isset($l->prod->CEST)) ? trim($l->prod->CEST) : NULL;;
            $std->CFOP             = trim($l->prod->CFOP);
            $std->uCom             = trim($l->prod->uCom);
            $std->qCom             = trim($l->prod->qCom);
            $std->vUnCom           = trim($l->prod->vUnCom);
            $std->vProd            = trim($l->prod->vProd);
            $std->cEANTrib         = trim($l->prod->cEANTrib);
            $std->uTrib            = trim($l->prod->uTrib);
            $std->qTrib            = trim($l->prod->qTrib);
            $std->vUnTrib          = trim($l->prod->vUnTrib);
            $std->vFrete           = (isset($l->prod->vFrete)) ? trim($l->prod->vFrete) : 0;
            $std->vSeg             = (isset($l->prod->vSeg)) ? trim($l->prod->vSeg) : 0;
            $std->vDesc            = (isset($l->prod->vDesc)) ? trim($l->prod->vDesc) : 0;
            $std->vOutro           = (isset($l->prod->vOutro)) ? trim($l->prod->vOutro) : 0;
            $std->indTot           = trim($l->prod->indTot);
            $std->xPed             = trim($l->prod->xPed);
            $std->nItemPed         = trim($l->prod->nItemPed);
            $std->nFCI             = trim($l->prod->nFCI);
            $std->tipoTributo      = (isset($ICMS->tipoTributo)) ? $ICMS->tipoTributo : 'T';
            $std->orig             = (isset($ICMS->orig)) ? $ICMS->orig : NULL;
            $std->cst_icms         = (isset($ICMS->CST)) ? $ICMS->CST : NULL;
            $std->modBC            = (isset($ICMS->modBC)) ? $ICMS->modBC : '3';
            $std->pRedBC           = (isset($ICMS->pRedBC)) ? $ICMS->pRedBC : 0;
            $std->vBC              = (isset($ICMS->vBC)) ?  $ICMS->vBC : 0;
            $std->pICMS            = (isset($ICMS->pICMS)) ? $ICMS->pICMS : 0;
            $std->vICMS            = (isset($ICMS->vICMS)) ? $ICMS->vICMS : 0;
            $std->vBCSTRet         = (isset($ICMS->vBCSTRet)) ? $ICMS->vBCSTRet : 0;
            $std->pST              = (isset($ICMS->pST)) ? $ICMS->pST : 0;
            $std->vICMSSubstituto  = (isset($ICMS->vICMSSubstituto)) ? $ICMS->vICMSSubstituto : 0;
            $std->vICMSSTRet       = (isset($ICMS->vICMSSTRet)) ? $ICMS->vICMSSTRet : 0;
            $std->vBCSTDest        = (isset($ICMS->vBCSTDest)) ? $ICMS->vBCSTDest : 0;
            $std->vICMSSTDest      = (isset($ICMS->vICMSSTDest)) ? $ICMS->vICMSSTDest : 0;
            $std->modBCST          = (isset($ICMS->modBCST)) ? $ICMS->modBCST : 0;
            $std->pMVAST           = (isset($ICMS->pMVAST)) ? $ICMS->pMVAST : 0;
            $std->vBCST            = (isset($ICMS->vBCST)) ? $ICMS->vBCST : 0;
            $std->pICMSST          = (isset($ICMS->pICMSST)) ? $ICMS->pICMSST : 0;
            $std->vICMSST          = (isset($ICMS->vICMSST)) ? $ICMS->vICMSST : 0;
            $std->pCredSN          = (isset($ICMS->pCredSN)) ? $ICMS->pCredSN : 0;
            $std->vCredICMSSN      = (isset($ICMS->vCredICMSSN)) ? $ICMS->vCredICMSSN : 0;
            $std->pRedBCST         = (isset($ICMS->pRedBCST)) ? $ICMS->pRedBCST : 0;
            $std->vICMSDeson       = (isset($ICMS->vICMSDeson)) ? $ICMS->vICMSDeson : 0;
            $std->motDesICMS       = (isset($ICMS->motDesICMS)) ? $ICMS->motDesICMS : 0;
            $std->vICMSOp          = (isset($ICMS->vICMSOp)) ? $ICMS->vICMSOp : 0;
            $std->pDif             = (isset($ICMS->pDif)) ? $ICMS->pDif : 0;
            $std->vICMSDif         = (isset($ICMS->vICMSDif)) ? $ICMS->vICMSDif : 0;
            $std->vBCFCP           = (isset($ICMS->vBCFCP)) ? $ICMS->vBCFCP : 0;
            $std->pFCP             = (isset($ICMS->pFCP)) ? $ICMS->pFCP : 0;
            $std->vFCP             = (isset($ICMS->vFCP)) ? $ICMS->vFCP : 0;
            $std->vBCFCPST         = (isset($ICMS->vBCFCPST)) ? $ICMS->vBCFCPST : 0;
            $std->pFCPST           = (isset($ICMS->pFCPST)) ? $ICMS->pFCPST : 0;
            $std->vFCPST           = (isset($ICMS->vFCPST)) ? $ICMS->vFCPST : 0;
            $std->vBCFCPSTRet      = (isset($ICMS->vBCFCPSTRet)) ? $ICMS->vBCFCPSTRet : 0;
            $std->pFCPSTRet        = (isset($ICMS->pFCPSTRet)) ? $ICMS->pFCPSTRet : 0;
            $std->vFCPSTRet        = (isset($ICMS->vFCPSTRet)) ? $ICMS->vFCPSTRet : 0;
            $std->pRedBCEfet       = (isset($ICMS->pRedBCEfet)) ? $ICMS->pRedBCEfet : 0;
            $std->vBCEfet          = (isset($ICMS->vBCEfet)) ? $ICMS->vBCEfet : 0;
            $std->pICMSEfet        = (isset($ICMS->pICMSEfet)) ? $ICMS->pICMSEfet : 0;
            $std->vICMSEfet        = (isset($ICMS->vICMSEfet)) ? $ICMS->vICMSEfet : 0;
            $std->enq_ipi          = (isset($IPI->cEnq)) ? trim($IPI->cEnq) : '999';
            $std->cst_ipi          = (isset($IPI->CST)) ? trim($IPI->CST) : '99';
            $std->vBCIPI           = (isset($IPI->vBC)) ? trim($IPI->vBC) : 0;
            $std->pIPI             = (isset($IPI->pIPI)) ? trim($IPI->pIPI) : 0;
            $std->vIPI             = (isset($IPI->vIPI)) ? trim($IPI->vIPI) : 0;
            $std->vBCII            = (isset($II->vBC)) ? trim($II->vBC) : 0;
            $std->vDespAdu         = (isset($II->vDespAdu)) ? trim($II->vDespAdu) : 0;
            $std->vII              = (isset($II->vII)) ? trim($II->vII) : 0;
            $std->vIOF             = (isset($II->vIOF)) ? trim($II->vIOF) : 0;
            $std->cst_pis          = (isset($PIS->CST)) ? trim($PIS->CST) : 0;
            $std->vBCPIS           = (isset($PIS->vBC)) ? trim($PIS->vBC) : 0;
            $std->pPIS             = (isset($PIS->pPIS)) ? trim($PIS->pPIS) : 0;
            $std->vPIS             = (isset($PIS->vPIS)) ? trim($PIS->vPIS) : 0;
            $std->cst_cofins       = (isset($COFINS->CST)) ? trim($COFINS->CST) : 0;
            $std->vBCCOFINS        = (isset($COFINS->vBC)) ? trim($COFINS->vBC) : 0;
            $std->pCOFINS          = (isset($COFINS->pCOFINS)) ? trim($COFINS->pCOFINS) : 0;
            $std->vCOFINS          = (isset($COFINS->vCOFINS)) ? trim($COFINS->vCOFINS) : 0;
            $std->nDI              = (isset($l->prod->DI->nDI)) ? trim($l->prod->DI->nDI) : NULL;
            $std->dDI              = (isset($l->prod->DI->dDI)) ? trim($l->prod->DI->dDI) : NULL;
            $std->xLocDesemb       = (isset($l->prod->DI->xLocDesemb)) ? trim($l->prod->DI->xLocDesemb) : NULL;
            $std->UFDesemb         = (isset($l->prod->DI->UFDesemb)) ? trim($l->prod->DI->UFDesemb) : NULL;
            $std->dDesemb          = (isset($l->prod->DI->dDesemb)) ? trim($l->prod->DI->dDesemb) : NULL;
            $std->tpViaTransp      = (isset($l->prod->DI->tpViaTransp)) ? trim($l->prod->DI->tpViaTransp) : NULL;
            $std->vAFRMM           = (isset($l->prod->DI->vAFRMM)) ? trim($l->prod->DI->vAFRMM) : 0;
            $std->tpIntermedio     = (isset($l->prod->DI->tpIntermedio)) ? trim($l->prod->DI->tpIntermedio) : NULL;
            $std->cExportador      = (isset($l->prod->DI->cExportador)) ? trim($l->prod->DI->cExportador) : NULL;
            $std->nAdicao          = (isset($l->prod->DI->adi->nAdicao)) ? trim($l->prod->DI->adi->nAdicao) : NULL;
            $std->nSeqAdic         = (isset($l->prod->DI->adi->nSeqAdic)) ? trim($l->prod->DI->adi->nSeqAdic) : NULL;
            $std->cFabricante      = (isset($l->prod->DI->adi->cFabricante)) ? trim($l->prod->DI->adi->cFabricante) : NULL;
            $std->qBCProd_pis      = (isset($PIS->qBCProd_pis)) ? trim($PIS->qBCProd_pis) : 0;
            $std->vAliqProd_pis    = (isset($PIS->vAliqProd_pis)) ? trim($PIS->vAliqProd_pis) : 0;
            $std->qBCProd_cofins   = (isset($COFINS->qBCProd_cofins)) ? trim($COFINS->qBCProd_cofins) : 0;
            $std->vAliqProd_cofins = (isset($COFINS->vAliqProd_cofins)) ? trim($COFINS->vAliqProd_cofins) : 0;

            $Itens[] = $std;
        }

        return (object)$Itens;
    }

    /**
     * Retorna os dados das parcelas de cobrança da nota fiscal eletrônica (NF-e).
     *
     * Este método extrai os dados do grupo `cobr` da NF-e, especificamente as informações da fatura (`fat`)
     * e das duplicatas (`dup`). Para cada parcela encontrada, são retornados os dados relevantes como número
     * da fatura, número da parcela, data de vencimento e valor da parcela.
     *
     * @return object Objeto contendo uma lista de parcelas, cada uma representada como um objeto com os campos:
     *                - fatura: Número da fatura associada às parcelas
     *                - parcela: Identificador da parcela (número da duplicata)
     *                - dt_vencimento: Data de vencimento da parcela
     *                - vlr_parcela: Valor da parcela
     */
    public function getParcelas()
    {
        $parcelas = [];

        if (isset($this->xml->NFe->infNFe->cobr)) {
            $par      = $this->xml->NFe->infNFe->cobr;
            $fatura   = trim($this->xml->NFe->infNFe->cobr->fat->nFat);
            $parcelas = [];

            foreach ($par->dup as $p) {
                $std                = new \stdClass;
                $std->fatura        = $fatura;
                $std->parcela       = trim($p->nDup);
                $std->dt_vencimento = trim($p->dVenc);
                $std->vlr_parcela   = trim($p->vDup);
                $parcelas[]         = $std;
            }
        }

        return (object) $parcelas;
    }

    /**
     * Retorna as observações adicionais da nota fiscal eletrônica (NF-e).
     *
     * Este método extrai os dados do grupo `infAdic` da NF-e, que contém informações complementares sobre a nota
     * fiscal. São retornadas as observações fiscais (`infAdFisco`) e as informações complementares (`infCpl`), se
     * disponíveis, relacionadas à nota fiscal.
     *
     * @return object Objeto contendo as observações, com os campos:
     *                - infAdFisco: Observação fiscal específica para a NF-e (se presente)
     *                - infCpl: Informações complementares da NF-e (se presente)
     */
    public function getObs()
    {
        $obs             = $this->xml->NFe->infNFe->infAdic;
        $std             = new \stdClass;
        $std->infAdFisco = (isset($obs->infAdFisco)) ? (trim($obs->infAdFisco)) : NULL;
        $std->infCpl     = (isset($obs->infCpl)) ? (trim($obs->infCpl)) : NULL;

        return $std;
    }

    /**
     * Retorna os dados principais da nota fiscal eletrônica (NF-e).
     *
     * Este método coleta as informações essenciais da NF-e, combinando dados de diferentes seções da nota fiscal,
     * incluindo a chave de acesso, protocolo, informações do emitente, destinatário, total, transporte e observações
     * adicionais. O resultado é um objeto contendo essas informações de forma consolidada.
     *
     * @return object Objeto contendo os dados da NF-e, com os seguintes campos:
     *                - chave: Chave de acesso da NF-e
     *                - protocolo: Protocolo de autorização da NF-e
     *                - ide: Informações de identificação da NF-e
     *                - emitente: Dados do emitente da NF-e
     *                - destinatario: Dados do destinatário da NF-e
     *                - total: Informações sobre o total da NF-e
     *                - transporte: Dados de transporte da NF-e
     *                - obs: Observações adicionais da NF-e
     */
    public function getNotaCab()
    {
        $nota          = [];
        $nota['chave'] = $this->getChave();
        $nota          = array_merge($nota, (array)$this->getProtocolo());
        $nota          = array_merge($nota, (array)$this->getIde());
        $nota          = array_merge($nota, (array)$this->getEmitente());
        $nota          = array_merge($nota, (array)$this->getDestinatario());
        $nota          = array_merge($nota, (array)$this->getTotal());
        $nota          = array_merge($nota, (array)$this->getTransporte());
        $nota          = array_merge($nota, (array)$this->getObs());

        return (object)$nota;
    }
}