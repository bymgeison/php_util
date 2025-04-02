<?php

namespace GX4\Util;
class LeituraXmlNFe
{
    protected $chave;
    protected $xml;
    protected $versao;

    public function __construct($arquivo, $tipo = 'arquivo')
    {
        $this->ValidaArquivo($arquivo, $tipo);
    }
    public function debug($var)
    {
        echo "<pre>";
        print_r($var);
        //var_dump($var);
        echo "</pre>";
    }
    public function tratadt($var)
    {
        if ($var != '') {
            $dt = new \DateTime($var);
            $var = $dt->format('Y-m-d H:i:s');
        }
        return $var;
    }
    public function ValidaArquivo($arquivo, $tipo)
    {
        if ($tipo  == 'arquivo') {
            if (!file_exists($arquivo)) {
                throw new \Exception("Arquivo não encontrado!");
            }
            $this->xml = simplexml_load_file($arquivo);
        } else if ($tipo  == 'url') {
            $fileInfo = pathinfo($arquivo);
            $arquivo = rawurlencode($fileInfo['basename']);
            $arquivo = @file_get_contents($fileInfo['dirname'].'/'.$arquivo);
            $this->xml = simplexml_load_string($arquivo);
        }
        else {
            $this->xml = simplexml_load_string($arquivo);
        }
        //$this->debug($this->xml);
        if (empty($this->xml->protNFe->infProt->nProt)) {
            throw new \Exception("Arquivo sem dados de autorização. " . $arquivo);
            return false;
        }
        //$chave = $this->xml->NFe->infNFe->attributes->Id;
        $chave = $this->xml->NFe->infNFe->attributes()->Id;
        $this->chave = strtr(strtoupper($chave), array("NFE" => NULL));
        //file_put_contents('tmp/lidos/' . $this->chave . ".xml", file_get_contents($arquivo));
        //$this->versao = $this->xml->NFe->infNFe->attributes->versao;
        $this->versao = trim($this->xml->NFe->infNFe->attributes()->versao);
    }
    public function getProtocolo()
    {
        $prot = $this->xml->protNFe->infProt;
        $std = new \stdClass;
        $std->protocolo_autorizacao = trim($prot->nProt);
        $std->dt_autorizacao = $this->tratadt($prot->dhRecbto);
        $std->cd_status = trim($prot->cStat);
        return $std;
    }
    public function getIde()
    {
        $ide = $this->xml->NFe->infNFe->ide;
        $std = new \stdClass;
        $std->versao = $this->versao;
        $std->ide_natOp = (isset($ide->natOp)) ? trim($ide->natOp) : NULL;
        $std->ide_mod = (isset($ide->mod)) ? trim($ide->mod) : NULL;
        $std->ide_serie = (isset($ide->serie)) ? trim($ide->serie) : NULL;
        $std->ide_nNF = (isset($ide->nNF)) ? trim($ide->nNF) : NULL;
        $std->ide_dhEmi = (isset($ide->dhEmi)) ? $this->tratadt($ide->dhEmi) : NULL;
        $std->ide_dhSaiEnt = (isset($ide->dhSaiEnt)) ? $this->tratadt($ide->dhSaiEnt) : NULL;
        $std->ide_tpNF = (isset($ide->tpNF)) ? trim($ide->tpNF) : NULL;
        $std->ide_idDEst = (isset($ide->idDest)) ? trim($ide->idDest) : NULL;
        $std->ide_cMunFG = (isset($ide->cMunFG)) ? trim($ide->cMunFG) : NULL;
        $std->ide_tp_Imp = (isset($ide->tpImp)) ? trim($ide->tpImp) : NULL;
        $std->ide_tpEmis = (isset($ide->tpEmis)) ? trim($ide->tpEmis) : NULL;
        $std->ide_tpAmb = (isset($ide->tpAmb)) ? trim($ide->tpAmb) : NULL;
        $std->ide_finNFe = (isset($ide->finNFe)) ? trim($ide->finNFe) : NULL;
        $std->ide_indFinal = (isset($ide->indFinal)) ? trim($ide->indFinal) : NULL;
        $std->ide_indPres = (isset($ide->indPres)) ? trim($ide->indPres) : NULL;
        $std->ide_procEmi = (isset($ide->procEmi)) ? trim($ide->procEmi) : NULL;
        $std->ide_verProc = (isset($ide->verProc)) ? trim($ide->verProc) : NULL;
        $std->ide_dhCont = (isset($ide->dhCont)) ? $this->tratadt($ide->dhCont) : NULL;
        $std->ide_xJust = (isset($ide->xJust)) ? trim($ide->xJust) : NULL;
        return $std;
    }
    public function getChave()
    {
        return $this->chave;
    }
    public function getEmitente()
    {
        $emit = $this->xml->NFe->infNFe->emit;
        $std = new \stdClass;
        $std->emit_cpf = (isset($emit->CPF)) ? trim($emit->CPF) : NULL;
        $std->emit_cnpj = (isset($emit->CNPJ)) ? trim($emit->CNPJ) : NULL;
        $std->emit_xNome = (isset($emit->xNome)) ? trim($emit->xNome) : NULL;
        $std->emit_xFant = (isset($emit->xFant)) ? trim($emit->xFant) : NULL;
        $std->emit_IE = (isset($emit->IE)) ? trim($emit->IE) : NULL;
        $std->emit_CRT = (isset($emit->CRT)) ? trim($emit->CRT) : NULL;
        $std->emit_xLgr = (isset($emit->enderEmit->emit_xLgr)) ? trim($emit->enderEmit->emit_xLgr) : NULL;
        $std->emit_xLgr = (isset($emit->enderEmit->xLgr)) ? trim($emit->enderEmit->xLgr) : NULL;
        $std->emit_nro = (isset($emit->enderEmit->nro)) ? trim($emit->enderEmit->nro) : NULL;
        $std->emit_xCpl = (isset($emit->enderEmit->xCpl)) ? trim($emit->enderEmit->xCpl) : NULL;
        $std->emit_xBairro = (isset($emit->enderEmit->xBairro)) ? trim($emit->enderEmit->xBairro) : NULL;
        $std->emit_cMun = (isset($emit->enderEmit->cMun)) ? trim($emit->enderEmit->cMun) : NULL;
        $std->emit_xMun = (isset($emit->enderEmit->xMun)) ? trim($emit->enderEmit->xMun) : NULL;
        $std->emit_UF = (isset($emit->enderEmit->UF)) ? trim($emit->enderEmit->UF) : NULL;
        $std->emit_CEP = (isset($emit->enderEmit->CEP)) ? trim($emit->enderEmit->CEP) : NULL;
        $std->emit_cPais = (isset($emit->enderEmit->cPais)) ? trim($emit->enderEmit->cPais) : NULL;
        $std->emit_xPais = (isset($emit->enderEmit->xPais)) ? trim($emit->enderEmit->xPais) : ' ';
        $std->emit_fone = (isset($emit->enderEmit->fone)) ? trim($emit->enderEmit->fone) : NULL;
        return $std;
    }
    public function getDestinatario()
    {
        $dest = $this->xml->NFe->infNFe->dest;
        $std = new \stdClass;
        $std->dest_idEstrangeiro = (isset($dest->idEstrangeiro)) ? trim($dest->idEstrangeiro) : NULL;
        $std->dest_CPF = (isset($dest->CPF)) ? trim($dest->CPF) : NULL;
        $std->dest_CNPJ = (isset($dest->CNPJ)) ? trim($dest->CNPJ) : NULL;
        $std->dest_xNome = (isset($dest->xNome)) ? trim($dest->xNome) : NULL;
        $std->dest_xFant = (isset($dest->xFant)) ? trim($dest->xFant) : NULL;
        $std->dest_IE = (isset($dest->IE)) ? trim($dest->IE) : NULL;
        $std->dest_indIEDest = (isset($dest->indIEDest)) ? trim($dest->indIEDest) : NULL;
        $std->dest_CRT = (isset($dest->CRT)) ? trim($dest->CRT) : NULL;
        $std->dest_xLgr = (isset($dest->enderDest->dest_xLgr)) ? trim($dest->enderDest->dest_xLgr) : NULL;
        $std->dest_xLgr = (isset($dest->enderDest->xLgr)) ? trim($dest->enderDest->xLgr) : NULL;
        $std->dest_nro = (isset($dest->enderDest->nro)) ? trim($dest->enderDest->nro) : NULL;
        $std->dest_xCpl = (isset($dest->enderDest->xCpl)) ? trim($dest->enderDest->xCpl) : NULL;
        $std->dest_xBairro = (isset($dest->enderDest->xBairro)) ? trim($dest->enderDest->xBairro) : NULL;
        $std->dest_cMun = (isset($dest->enderDest->cMun)) ? trim($dest->enderDest->cMun) : NULL;
        $std->dest_xMun = (isset($dest->enderDest->xMun)) ? trim($dest->enderDest->xMun) : NULL;
        $std->dest_UF = (isset($dest->enderDest->UF)) ? trim($dest->enderDest->UF) : NULL;
        $std->dest_CEP = (isset($dest->enderDest->CEP)) ? trim($dest->enderDest->CEP) : NULL;
        $std->dest_cPais = (isset($dest->enderDest->cPais)) ? trim($dest->enderDest->cPais) : NULL;
        $std->dest_xPais = (isset($dest->enderDest->xPais)) ? trim($dest->enderDest->xPais) : NULL;
        $std->dest_fone = (isset($dest->enderDest->fone)) ? trim($dest->enderDest->fone) : NULL;
        $std->dest_email = (isset($dest->enderDest->email)) ? trim($dest->enderDest->email) : NULL;
        return $std;
    }
    public function getTotal()
    {
        $retorno = $this->xml->NFe->infNFe->total->ICMSTot;
        if (isset($this->xml->NFe->infNFe->cobr->fat->vLiq)) {
            $retorno->fat_vOrig = $this->xml->NFe->infNFe->cobr->fat->vOrig;
            $retorno->fat_vDesc = $this->xml->NFe->infNFe->cobr->fat->vDesc;
            $retorno->fat_vLiq = $this->xml->NFe->infNFe->cobr->fat->vLiq;
        }
        return $retorno;
    }
    public function getTransporte()
    {
        $transp = $this->xml->NFe->infNFe->transp;
        $std = new \stdClass;
        $std->modFrete = trim($transp->modFrete);
        $std->transp_CNPJ = (isset($transp->transporta->CNPJ)) ? trim($transp->transporta->CNPJ) : NULL;
        $std->transp_CPF = (isset($transp->transporta->CPF)) ? trim($transp->transporta->CPF) : NULL;
        $std->transp_xNome = (isset($transp->transporta->xNome)) ? trim($transp->transporta->xNome) : NULL;
        $std->transp_xEnder = (isset($transp->transporta->xEnder)) ? trim($transp->transporta->xEnder) : NULL;
        $std->transp_xMun = (isset($transp->transporta->xMun)) ? trim($transp->transporta->xMun) : NULL;
        $std->transp_UF = (isset($transp->transporta->UF)) ? trim($transp->transporta->UF) : NULL;
        $std->veic_placa = (isset($transp->veicTransp->placa)) ? trim($transp->veicTransp->placa) : NULL;
        $std->veic_UF = (isset($transp->veicTransp->UF)) ? trim($transp->veicTransp->UF) : NULL;
        $std->veic_RNTC = (isset($transp->veicTransp->RNTC)) ? trim($transp->veicTransp->RNTC) : NULL;
        $std->vol_qVol = (isset($transp->vol->qVol)) ? trim($transp->vol->qVol) : NULL;
        $std->vol_nVol = (isset($transp->vol->nVol)) ? trim($transp->vol->nVol) : NULL;
        $std->vol_esp = (isset($transp->vol->esp)) ? trim($transp->vol->esp) : NULL;
        $std->vol_marca = (isset($transp->vol->marca)) ? trim($transp->vol->marca) : NULL;
        $std->vol_pesoL = (isset($transp->vol->pesoL)) ? trim($transp->vol->pesoL) : NULL;
        $std->vol_pesoB = (isset($transp->vol->pesoB)) ? trim($transp->vol->pesoB) : NULL;
        return $std;
    }
    public function ICMS($var)
    {
        $std = new \stdClass;
        if (isset($var->ICMS->ICMS00)) {
            $l = $var->ICMS->ICMS00;
            $std->CST  = trim($l->CST);
            $std->orig = trim($l->orig);
            $std->modBC = trim($l->modBC);
            $std->vBC = trim($l->vBC);
            $std->pICMS = trim($l->pICMS);
            $std->vICMS = trim($l->vICMS);
            $std->pFCP = (isset($l->pFCP)) ? trim($l->pFCP) : 0;
            $std->vFCP = (isset($l->vFCP)) ? trim($l->vFCP) : 0;
            $std->tipoTributo = 'T';
        }
        if (isset($var->ICMS->ICMS10)) {
            $l = $var->ICMS->ICMS10;
            $std->orig = trim($l->orig);
            $std->CST = trim($l->CST);
            $std->modBC = trim($l->modBC);
            $std->vBC = trim($l->vBC);
            $std->pICMS = trim($l->pICMS);
            $std->vICMS = trim($l->vICMS);
            $std->modBCST = trim($l->modBCST);
            $std->pMVAST = trim($l->pMVAST);
            $std->pRedBCST = trim($l->pRedBCST);
            $std->vBCST = trim($l->vBCST);
            $std->pICMSST = trim($l->pICMSST);
            $std->vICMSST = trim($l->vICMSST);
            $std->vBCFCP = (isset($l->vBCFCP)) ? trim($l->vBCFCP) : 0;
            $std->pFCP = (isset($l->pFCP)) ? trim($l->pFCP) : 0;
            $std->vFCP = (isset($l->vFCP)) ? trim($l->vFCP) : 0;
            $std->vBCFCPST = (isset($l->vBCFCPST)) ? trim($l->vBCFCPST) : 0;
            $std->pFCPST = (isset($l->pFCPST)) ? trim($l->pFCPST) : 0;
            $std->vFCPST = (isset($l->vFCPST)) ? trim($l->vFCPST) : 0;
            $std->tipoTributo = 'T';
        }
        if (isset($var->ICMS->ICMS20)) {
            $l = $var->ICMS->ICMS20;
            $std->CST   = trim($l->CST);
            $std->orig   = trim($l->orig);
            $std->modBC = trim($l->modBC);
            $std->pRedBC = trim($l->pRedBC);
            $std->vBC = trim($l->vBC);
            $std->pICMS = trim($l->pICMS);
            $std->vICMS = trim($l->vICMS);
            $std->vBCFCP = (isset($l->vBCFCP)) ? trim($l->vBCFCP) : 0;
            $std->pFCP = (isset($l->pFCP)) ? trim($l->pFCP) : 0;
            $std->vFCP = (isset($l->vFCP)) ? trim($l->vFCP) : 0;
            $std->vICMSDeson = (isset($l->vICMSDeson)) ? trim($l->vICMSDeson) : 0;
            $std->motDesICMS = (isset($l->motDesICMS)) ? trim($l->motDesICMS) : 0;
            $std->tipoTributo = 'T';
        }
        if (isset($var->ICMS->ICMS30)) {
            $l = $var->ICMS->ICMS30;
            $std->CST   = trim($l->CST);
            $std->orig   = trim($l->orig);
            $std->modBCST = trim($l->modBCST);
            $std->pMVAST = trim($l->pMVAST);
            $std->pRedBCST = trim($l->pRedBCST);
            $std->vBCST = trim($l->vBCST);
            $std->pICMSST = trim($l->pICMSST);
            $std->vICMSST = trim($l->vICMSST);
            $std->vBCFCPST = (isset($l->vBCFCPST)) ? trim($l->vBCFCPST) : 0;
            $std->pFCPST = (isset($l->pFCPST)) ? trim($l->pFCPST) : 0;
            $std->vFCPST = (isset($l->vFCPST)) ? trim($l->vFCPST) : 0;
            $std->vICMSDeson = (isset($l->vICMSDeson)) ? trim($l->vICMSDeson) : 0;
            $std->motDesICMS = (isset($l->motDesICMS)) ? trim($l->motDesICMS) : 0;
            $std->tipoTributo = 'I';
        }
        if (isset($var->ICMS->ICMS40)) {
            $l = $var->ICMS->ICMS40;
            $std->CST   = trim($l->CST);
            $std->orig   = trim($l->orig);
            $std->vICMSDeson = (isset($l->vICMSDeson)) ? trim($l->vICMSDeson) : 0;
            $std->motDesICMS = (isset($l->motDesICMS)) ? trim($l->motDesICMS) : 0;
            $std->tipoTributo = 'I';
        }
        if (isset($var->ICMS->ICMS51)) {
            $l = $var->ICMS->ICMS51;
            $std->CST   = trim($l->CST);
            $std->orig   = trim($l->orig);
            $std->modBC = (isset($l->modBC)) ? trim($l->modBC) : 0;
            $std->pRedBC = (isset($l->pRedBC)) ? trim($l->pRedBC) : 0;
            $std->vBC = (isset($l->vBC)) ? trim($l->vBC) : 0;
            $std->pICMS = (isset($l->pICMS)) ? trim($l->pICMS) : 0;
            $std->vICMSOp = (isset($l->vICMSOp)) ? trim($l->vICMSOp) : 0;
            $std->pDif = (isset($l->pDif)) ? trim($l->pDif) : 0;
            $std->vICMSDif = (isset($l->vICMSDif)) ? trim($l->vICMSDif) : 0;
            $std->vICMS = (isset($l->vICMS)) ? trim($l->vICMS) : 0;
            $std->vBCFCP = (isset($l->vBCFCP)) ? trim($l->vBCFCP) : 0;
            $std->pFCP = (isset($l->pFCP)) ? trim($l->pFCP) : 0;
            $std->vFCP = (isset($l->vFCP)) ? trim($l->vFCP) : 0;
            $std->tipoTributo = 'O';
        }
        if (isset($var->ICMS->ICMS60)) {
            $l = $var->ICMS->ICMS60;
            $std->CST   = trim($l->CST);
            $std->orig   = trim($l->orig);
            $std->vBCSTRet = trim($l->vBCSTRet);
            $std->pST = trim($l->pST);
            $std->vICMSSubstituto = trim($l->vICMSSubstituto);
            $std->vICMSSTRet = trim($l->vICMSSTRet);
            $std->vBCSTDest = trim($l->vBCSTDest);
            $std->vICMSSTDest = trim($l->vICMSSTDest);
            $std->vBCFCPSTRet = (isset($l->vBCFCPSTRet)) ? trim($l->vBCFCPSTRet) : 0;
            $std->pFCPSTRet = (isset($l->pFCPSTRet)) ? trim($l->pFCPSTRet) : 0;
            $std->vFCPSTRet = (isset($l->vFCPSTRet)) ? trim($l->vFCPSTRet) : 0;
            $std->pRedBCEfet = (isset($l->pRedBCEfet)) ? trim($l->pRedBCEfet) : 0;
            $std->vBCEfet = (isset($l->vBCEfet)) ? trim($l->vBCEfet) : 0;
            $std->pICMSEfet = (isset($l->pICMSEfet)) ? trim($l->pICMSEfet) : 0;
            $std->vICMSEfet = (isset($l->vICMSEfet)) ? trim($l->vICMSEfet) : 0;
            $std->tipoTributo = 'I';
        }
        if (isset($var->ICMS->ICMS70)) {
            $l = $var->ICMS->ICMS70;
            $std->orig = trim($l->orig);
            $std->CST = trim($l->CST);
            $std->modBC = trim($l->modBC);
            $std->pRedBC = trim($l->pRedBC);
            $std->vBC = trim($l->vBC);
            $std->pICMS = trim($l->pICMS);
            $std->vICMS = trim($l->vICMS);
            $std->vBCFCP = (isset($l->vBCFCP)) ? $l->vBCFCP : 0;
            $std->pFCP = (isset($l->pFCP)) ? $l->pFCP : 0;
            $std->vFCP = (isset($l->vFCP)) ? $l->vFCP : 0;
            $std->modBCST = trim($l->modBCST);
            $std->pMVAST = trim($l->pMVAST);
            $std->pRedBCST = trim($l->pRedBCST);
            $std->vBCST = trim($l->vBCST);
            $std->pICMSST = trim($l->pICMSST);
            $std->vICMSST = trim($l->vICMSST);
            $std->vBCFCPST = (isset($l->vBCFCPST)) ? trim($l->vBCFCPST) : 0;
            $std->pFCPST = (isset($l->pFCPST)) ? trim($l->pFCPST) : 0;
            $std->vFCPST = (isset($l->vFCPST)) ? trim($l->vFCPST) : 0;
            $std->vICMSDeson = (isset($l->vICMSDeson)) ? trim($l->vICMSDeson) : 0;
            $std->motDesICMS = (isset($l->motDesICMS)) ? trim($l->motDesICMS) : 0;
            $std->tipoTributo = 'T';
        }
        if (isset($var->ICMS->ICMS90)) {
            $l = $var->ICMS->ICMS90;
            $std->orig = trim($l->orig);
            $std->CST = trim($l->CST);
            $std->modBC = trim($l->modBC);
            $std->modBC = (isset($l->modBC)) ? trim($l->modBC) : 0;
            $std->vBC = (isset($l->vBC)) ? trim($l->vBC) : 0;
            $std->pRedBC = (isset($l->pRedBC)) ? trim($l->pRedBC) : 0;
            $std->pICMS = (isset($l->pICMS)) ? trim($l->pICMS) : 0;
            $std->vICMS = (isset($l->vICMS)) ? trim($l->vICMS) : 0;
            $std->vBCFCP = (isset($l->vBCFCP)) ? trim($l->vBCFCP) : 0;
            $std->pFCP = (isset($l->pFCP)) ? trim($l->pFCP) : 0;
            $std->vFCP = (isset($l->vFCP)) ? trim($l->vFCP) : 0;
            $std->modBCST = (isset($l->modBCST)) ? trim($l->modBCST) : 0;
            $std->pMVAST = (isset($l->pMVAST)) ? trim($l->pMVAST) : 0;
            $std->pRedBCST = (isset($l->pRedBCST)) ? trim($l->pRedBCST) : 0;
            $std->vBCST = (isset($l->vBCST)) ? trim($l->vBCST) : 0;
            $std->pICMSST = (isset($l->pICMSST)) ? trim($l->pICMSST) : 0;
            $std->vICMSST = (isset($l->vICMSST)) ? trim($l->vICMSST) : 0;
            $std->vBCFCPST = (isset($l->vBCFCPST)) ? trim($l->vBCFCPST) : 0;
            $std->pFCPST = (isset($l->pFCPST)) ? trim($l->pFCPST) : 0;
            $std->vFCPST = (isset($l->vFCPST)) ? trim($l->vFCPST) : 0;
            $std->vICMSDeson = (isset($l->vICMSDeson)) ? trim($l->vICMSDeson) : 0;
            $std->motDesICMS = (isset($l->motDesICMS)) ? trim($l->motDesICMS) : 0;
            $std->tipoTributo = 'T';
        }
        if (isset($var->ICMS->ICMSSN101)) {
            $l = $var->ICMS->ICMSSN101;
            $std->CST   = trim($l->CSOSN);
            $std->orig   = trim($l->orig);
            $std->pCredSN = trim($l->pCredSN);
            $std->vCredICMSSN = trim($l->vCredICMSSN);
            $std->tipoTributo = 'T';
        }
        if (isset($var->ICMS->ICMSSN102)) {
            $l = $var->ICMS->ICMSSN102;
            $std->CST   = trim($l->CSOSN);
            $std->orig   = trim($l->orig);
            $std->tipoTributo = 'O';
        }
        if (isset($var->ICMS->ICMSSN201)) {
            $l = $var->ICMS->ICMSSN201;
            $std->CST   = trim($l->CSOSN);
            $std->orig   = trim($l->orig);
            $std->modBCST = (isset($l->modBCST)) ? trim($l->modBCST) : 0;
            $std->pMVAST = (isset($l->pMVAST)) ? trim($l->pMVAST) : 0;
            $std->pRedBCST = (isset($l->pRedBCST)) ? trim($l->pRedBCST) : 0;
            $std->vBCST = (isset($l->vBCST)) ? trim($l->vBCST) : 0;
            $std->pICMSST = (isset($l->pICMSST)) ? trim($l->pICMSST) : 0;
            $std->vICMSST = (isset($l->vICMSST)) ? trim($l->vICMSST) : 0;
            $std->vBCFCPST = (isset($l->vBCFCPST)) ? trim($l->vBCFCPST) : 0;
            $std->pFCPST = (isset($l->pFCPST)) ? trim($l->pFCPST) : 0;
            $std->vFCPST = (isset($l->vFCPST)) ? trim($l->vFCPST) : 0;
            $std->pCredSN = (isset($l->pCredSN)) ? trim($l->pCredSN) : 0;
            $std->vCredICMSSN = (isset($l->vCredICMSSN)) ? trim($l->vCredICMSSN) : 0;
            $std->tipoTributo = 'T';
        }
        if (isset($var->ICMS->ICMSSN202)) {
            $l = $var->ICMS->ICMSSN202;
            $std->CST   = trim($l->CSOSN);
            $std->orig   = trim($l->orig);
            $std->modBCST = (isset($l->modBCST)) ? trim($l->modBCST) : 0;
            $std->pMVAST = (isset($l->pMVAST)) ? trim($l->pMVAST) : 0;
            $std->pRedBCST = (isset($l->pRedBCST)) ? trim($l->pRedBCST) : 0;
            $std->vBCST = (isset($l->vBCST)) ? trim($l->vBCST) : 0;
            $std->pICMSST = (isset($l->pICMSST)) ? trim($l->pICMSST) : 0;
            $std->vICMSST = (isset($l->vICMSST)) ? trim($l->vICMSST) : 0;
            $std->vBCFCPST = (isset($l->vBCFCPST)) ? trim($l->vBCFCPST) : 0;
            $std->pFCPST = (isset($l->pFCPST)) ? trim($l->pFCPST) : 0;
            $std->vFCPST = (isset($l->vFCPST)) ? trim($l->vFCPST) : 0;
            $std->tipoTributo = 'O';
        }
        if (isset($var->ICMS->ICMSSN500)) {
            $l = $var->ICMS->ICMSSN500;
            $std->CST   = trim($l->CSOSN);
            $std->orig   = trim($l->orig);
            $std->vBCSTRet = trim($l->vBCSTRet);
            $std->pST = trim($l->pST);
            $std->vICMSSubstituto = (isset($l->vICMSSubstituto)) ? trim($l->vICMSSubstituto) : 0;
            $std->vICMSSTRet = trim($l->vICMSSTRet);
            $std->vBCFCPSTRet = (isset($l->vBCFCPSTRet)) ? trim($l->vBCFCPSTRet) : 0;
            $std->pFCPSTRet = (isset($l->pFCPSTRet)) ? trim($l->pFCPSTRet) : 0;
            $std->vFCPSTRet = (isset($l->vFCPSTRet)) ? trim($l->vFCPSTRet) : 0;
            $std->pRedBCEfet = (isset($l->pRedBCEfet)) ? trim($l->pRedBCEfet) : 0;
            $std->vBCEfet = (isset($l->vBCEfet)) ? trim($l->vBCEfet) : 0;
            $std->pICMSEfet = (isset($l->pICMSEfet)) ? trim($l->pICMSEfet) : 0;
            $std->vICMSEfet = (isset($l->vICMSEfet)) ? trim($l->vICMSEfet) : 0;
            $std->tipoTributo = 'O';
        }
        if (isset($var->ICMS->ICMSSN900)) {
            $l = $var->ICMS->ICMSSN900;
            $std->orig = trim($l->orig);
            $std->CST = trim($l->CSOSN);
            $std->modBC = trim($l->modBC);
            $std->vBC = (isset($l->vBC)) ? trim($l->vBC) : 0;
            $std->pRedBC = (isset($l->pRedBC)) ? trim($l->pRedBC) : 0;
            $std->pICMS = (isset($l->pICMS)) ? trim($l->pICMS) : 0;
            $std->vICMS = (isset($l->vICMS)) ? trim($l->vICMS) : 0;
            $std->modBCST = (isset($l->modBCST)) ? trim($l->modBCST) : 0;
            $std->pMVAST = (isset($l->pMVAST)) ? trim($l->pMVAST) : 0;
            $std->pRedBCST = (isset($l->pRedBCST)) ? trim($l->pRedBCST) : 0;
            $std->vBCST = (isset($l->vBCST)) ? trim($l->vBCST) : 0;
            $std->pICMSST = (isset($l->pICMSST)) ? trim($l->pICMSST) : 0;
            $std->vICMSST = (isset($l->vICMSST)) ? trim($l->vICMSST) : 0;
            $std->vBCFCPST = (isset($l->vBCFCPST)) ? trim($l->vBCFCPST) : 0;
            $std->pFCPST = (isset($l->pFCPST)) ? trim($l->pFCPST) : 0;
            $std->vFCPST = (isset($l->vFCPST)) ? trim($l->vFCPST) : 0;
            $std->pCredSN = (isset($l->pCredSN)) ? trim($l->pCredSN) : 0;
            $std->vCredICMSSN = (isset($l->vCredICMSSN)) ? trim($l->vCredICMSSN) : 0;
            $std->tipoTributo = 'T';
        }
        if (isset($var->ICMS->ICMSST)) {
            $l = $var->ICMS->ICMSST;
            $std->orig = trim($l->orig);
            $std->CST = trim($l->CST);
            $std->modBC = trim($l->modBC);
            $std->modBC = (isset($l->modBC)) ? trim($l->modBC) : 0;
            $std->vBC = (isset($l->vBC)) ? trim($l->vBC) : 0;
            $std->pRedBC = (isset($l->pRedBC)) ? trim($l->pRedBC) : 0;
            $std->pICMS = (isset($l->pICMS)) ? trim($l->pICMS) : 0;
            $std->vICMS = (isset($l->vICMS)) ? trim($l->vICMS) : 0;
            $std->vBCFCP = (isset($l->vBCFCP)) ? trim($l->vBCFCP) : 0;
            $std->pFCP = (isset($l->pFCP)) ? trim($l->pFCP) : 0;
            $std->vFCP = (isset($l->vFCP)) ? trim($l->vFCP) : 0;
            $std->modBCST = (isset($l->modBCST)) ? trim($l->modBCST) : 0;
            $std->pMVAST = (isset($l->pMVAST)) ? trim($l->pMVAST) : 0;
            $std->pRedBCST = (isset($l->pRedBCST)) ? trim($l->pRedBCST) : 0;
            $std->vBCST = (isset($l->vBCST)) ? trim($l->vBCST) : 0;
            $std->pICMSST = (isset($l->pICMSST)) ? trim($l->pICMSST) : 0;
            $std->vICMSST = (isset($l->vICMSST)) ? trim($l->vICMSST) : 0;
            $std->vBCFCPST = (isset($l->vBCFCPST)) ? trim($l->vBCFCPST) : 0;
            $std->pFCPST = (isset($l->pFCPST)) ? trim($l->pFCPST) : 0;
            $std->vFCPST = (isset($l->vFCPST)) ? trim($l->vFCPST) : 0;
            $std->vICMSDeson = (isset($l->vICMSDeson)) ? trim($l->vICMSDeson) : 0;
            $std->motDesICMS = (isset($l->motDesICMS)) ? trim($l->motDesICMS) : 0;
            $std->tipoTributo = 'O';
        }
        return $std;
    }
    public function IPI($var)
    {
        $std = new \stdClass;
        if (isset($var->IPI->IPITrib)) {
            $l = $var->IPI->IPITrib;
            $std->cEnq = (isset($l->cEnq)) ? trim($l->cEnq) : 0;
            $std->CST = (isset($l->CST)) ? trim($l->CST) : 0;
            $std->vBC = (isset($l->vBC)) ? trim($l->vBC) : 0;
            $std->pIPI = (isset($l->pIPI)) ? trim($l->pIPI) : 0;
            $std->vIPI = (isset($l->vIPI)) ? trim($l->vIPI) : 0;
        }
        if (isset($var->IPI->IPINT)) {
            $l = $var->IPI->IPINT;
            $std->CST = (isset($l->CST)) ? trim($l->CST) : 0;
        }
        return $std;
    }
    public function II($var)
    {
        $std = new \stdClass;
        if (isset($var->II)) {
            $l = $var->II;
            $std->vBC = (isset($l->vBC)) ? trim($l->vBC) : 0;
            $std->vDespAdu = (isset($l->vDespAdu)) ? trim($l->vDespAdu) : 0;
            $std->vII = (isset($l->vII)) ? trim($l->vII) : 0;
            $std->vIOF = (isset($l->vIOF)) ? trim($l->vIOF) : 0;
        }
        return $std;
    }
    public function PIS($var)
    {
        $std = new \stdClass;
        if (isset($var->PIS->PISAliq)) {
            $l = $var->PIS->PISAliq;
            $std->CST = (isset($l->CST)) ? trim($l->CST) : 0;
            $std->vBC = (isset($l->vBC)) ? trim($l->vBC) : 0;
            $std->pPIS = (isset($l->pPIS)) ? trim($l->pPIS) : 0;
            $std->vPIS = (isset($l->vPIS)) ? trim($l->vPIS) : 0;
        }
        if (isset($var->PIS->PISNT)) {
            $l = $var->PIS->PISNT;
            $std->CST = (isset($l->CST)) ? trim($l->CST) : 0;
        }
        if (isset($var->PIS->PISOutr)) {
            $l = $var->PIS->PISOutr;
            $std->CST = (isset($l->CST)) ? trim($l->CST) : 0;
            $std->vBC = (isset($l->vBC)) ? trim($l->vBC) : 0;
            $std->pPIS = (isset($l->pPIS)) ? trim($l->pPIS) : 0;
            $std->vPIS = (isset($l->vPIS)) ? trim($l->vPIS) : 0;
        }
        if (isset($var->PIS->PISQtde)) {
            $l = $var->PIS->PISQtde;
            $std->CST = (isset($l->CST)) ? trim($l->CST) : 0;
            $std->vBC = (isset($l->vBC)) ? trim($l->vBC) : 0;
            $std->pPIS = (isset($l->pPIS)) ? trim($l->pPIS) : 0;
            $std->vPIS = (isset($l->vPIS)) ? trim($l->vPIS) : 0;
            $std->qBCProd_pis = (isset($l->qBCProd)) ? trim($l->qBCProd) : 0;
            $std->vAliqProd_pis = (isset($l->vAliqProd)) ? trim($l->vAliqProd) : 0;
        }
        return $std;
    }
    public function COFINS($var)
    {
        $std = new \stdClass;
        if (isset($var->COFINS->COFINSAliq)) {
            $l = $var->COFINS->COFINSAliq;
            $std->CST = (isset($l->CST)) ? trim($l->CST) : 0;
            $std->vBC = (isset($l->vBC)) ? trim($l->vBC) : 0;
            $std->pCOFINS = (isset($l->pCOFINS)) ? trim($l->pCOFINS) : 0;
            $std->vCOFINS = (isset($l->vCOFINS)) ? trim($l->vCOFINS) : 0;
        }
        if (isset($var->COFINS->COFINSNT)) {
            $l = $var->COFINS->COFINSNT;
            $std->CST = (isset($l->CST)) ? trim($l->CST) : 0;
        }
        if (isset($var->COFINS->COFINSOutr)) {
            $l = $var->COFINS->COFINSOutr;
            $std->CST = (isset($l->CST)) ? trim($l->CST) : 0;
            $std->vBC = (isset($l->vBC)) ? trim($l->vBC) : 0;
            $std->pCOFINS = (isset($l->pCOFINS)) ? trim($l->pCOFINS) : 0;
            $std->vCOFINS = (isset($l->vCOFINS)) ? trim($l->vCOFINS) : 0;
        }
        if (isset($var->COFINS->COFINSQtde)) {
            $l = $var->COFINS->COFINSQtde;
            $std->CST = (isset($l->CST)) ? trim($l->CST) : 0;
            $std->vBC = (isset($l->vBC)) ? trim($l->vBC) : 0;
            $std->pCOFINS = (isset($l->pCOFINS)) ? trim($l->pCOFINS) : 0;
            $std->vCOFINS = (isset($l->vCOFINS)) ? trim($l->vCOFINS) : 0;
            $std->qBCProd_cofins = (isset($l->qBCProd)) ? trim($l->qBCProd) : 0;
            $std->vAliqProd_cofins = (isset($l->vAliqProd)) ? trim($l->vAliqProd) : 0;
        }
        return $std;
    }
    public function getProdutos()
    {
        $Produtos = $this->xml->NFe->infNFe->det;
        $Itens = [];
        foreach ($Produtos as $l) {

            $ICMS = $this->ICMS($l->imposto);
            $IPI = $this->IPI($l->imposto);
            $II = $this->II($l->imposto);
            $PIS = $this->PIS($l->imposto);
            $COFINS = $this->COFINS($l->imposto);

            $std = new \stdClass;
            $std->nItem = $l->attributes()['nItem'];
            $std->cProd = trim($l->prod->cProd);
            $std->cEAN = trim($l->prod->cEAN);
            $std->xProd = trim($l->prod->xProd);
            $std->NCM = trim($l->prod->NCM);
            $std->nve = (isset($l->prod->NVE)) ? trim($l->prod->NVE) : NULL;
            $std->cest = (isset($l->prod->CEST)) ? trim($l->prod->CEST) : NULL;;
            $std->CFOP = trim($l->prod->CFOP);
            $std->uCom = trim($l->prod->uCom);
            $std->qCom = trim($l->prod->qCom);
            $std->vUnCom = trim($l->prod->vUnCom);
            $std->vProd = trim($l->prod->vProd);
            $std->cEANTrib = trim($l->prod->cEANTrib);
            $std->uTrib = trim($l->prod->uTrib);
            $std->qTrib = trim($l->prod->qTrib);
            $std->vUnTrib = trim($l->prod->vUnTrib);
            $std->vFrete = (isset($l->prod->vFrete)) ? trim($l->prod->vFrete) : 0;
            $std->vSeg = (isset($l->prod->vSeg)) ? trim($l->prod->vSeg) : 0;
            $std->vDesc = (isset($l->prod->vDesc)) ? trim($l->prod->vDesc) : 0;
            $std->vOutro = (isset($l->prod->vOutro)) ? trim($l->prod->vOutro) : 0;
            $std->indTot = trim($l->prod->indTot);
            $std->xPed = trim($l->prod->xPed);
            $std->nItemPed = trim($l->prod->nItemPed);
            $std->nFCI = trim($l->prod->nFCI);
            $std->tipoTributo = (isset($ICMS->tipoTributo)) ? $ICMS->tipoTributo : 'T';
            $std->orig = (isset($ICMS->orig)) ? $ICMS->orig : NULL;
            $std->cst_icms = (isset($ICMS->CST)) ? $ICMS->CST : NULL;
            $std->modBC = (isset($ICMS->modBC)) ? $ICMS->modBC : '3';
            $std->pRedBC = (isset($ICMS->pRedBC)) ? $ICMS->pRedBC : 0;
            $std->vBC = (isset($ICMS->vBC)) ?  $ICMS->vBC : 0;
            $std->pICMS = (isset($ICMS->pICMS)) ? $ICMS->pICMS : 0;
            $std->vICMS = (isset($ICMS->vICMS)) ? $ICMS->vICMS : 0;
            $std->vBCSTRet = (isset($ICMS->vBCSTRet)) ? $ICMS->vBCSTRet : 0;
            $std->pST = (isset($ICMS->pST)) ? $ICMS->pST : 0;
            $std->vICMSSubstituto = (isset($ICMS->vICMSSubstituto)) ? $ICMS->vICMSSubstituto : 0;
            $std->vICMSSTRet = (isset($ICMS->vICMSSTRet)) ? $ICMS->vICMSSTRet : 0;
            $std->vBCSTDest = (isset($ICMS->vBCSTDest)) ? $ICMS->vBCSTDest : 0;
            $std->vICMSSTDest = (isset($ICMS->vICMSSTDest)) ? $ICMS->vICMSSTDest : 0;
            $std->modBCST = (isset($ICMS->modBCST)) ? $ICMS->modBCST : 0;
            $std->pMVAST = (isset($ICMS->pMVAST)) ? $ICMS->pMVAST : 0;
            $std->vBCST = (isset($ICMS->vBCST)) ? $ICMS->vBCST : 0;
            $std->pICMSST = (isset($ICMS->pICMSST)) ? $ICMS->pICMSST : 0;
            $std->vICMSST = (isset($ICMS->vICMSST)) ? $ICMS->vICMSST : 0;
            $std->pCredSN = (isset($ICMS->pCredSN)) ? $ICMS->pCredSN : 0;
            $std->vCredICMSSN = (isset($ICMS->vCredICMSSN)) ? $ICMS->vCredICMSSN : 0;
            $std->pRedBCST = (isset($ICMS->pRedBCST)) ? $ICMS->pRedBCST : 0;
            $std->vICMSDeson = (isset($ICMS->vICMSDeson)) ? $ICMS->vICMSDeson : 0;
            $std->motDesICMS = (isset($ICMS->motDesICMS)) ? $ICMS->motDesICMS : 0;
            $std->vICMSOp = (isset($ICMS->vICMSOp)) ? $ICMS->vICMSOp : 0;
            $std->pDif = (isset($ICMS->pDif)) ? $ICMS->pDif : 0;
            $std->vICMSDif = (isset($ICMS->vICMSDif)) ? $ICMS->vICMSDif : 0;
            $std->vBCFCP = (isset($ICMS->vBCFCP)) ? $ICMS->vBCFCP : 0;
            $std->pFCP = (isset($ICMS->pFCP)) ? $ICMS->pFCP : 0;
            $std->vFCP = (isset($ICMS->vFCP)) ? $ICMS->vFCP : 0;
            $std->vBCFCPST = (isset($ICMS->vBCFCPST)) ? $ICMS->vBCFCPST : 0;
            $std->pFCPST = (isset($ICMS->pFCPST)) ? $ICMS->pFCPST : 0;
            $std->vFCPST = (isset($ICMS->vFCPST)) ? $ICMS->vFCPST : 0;
            $std->vBCFCPSTRet = (isset($ICMS->vBCFCPSTRet)) ? $ICMS->vBCFCPSTRet : 0;
            $std->pFCPSTRet = (isset($ICMS->pFCPSTRet)) ? $ICMS->pFCPSTRet : 0;
            $std->vFCPSTRet = (isset($ICMS->vFCPSTRet)) ? $ICMS->vFCPSTRet : 0;
            $std->pRedBCEfet = (isset($ICMS->pRedBCEfet)) ? $ICMS->pRedBCEfet : 0;
            $std->vBCEfet = (isset($ICMS->vBCEfet)) ? $ICMS->vBCEfet : 0;
            $std->pICMSEfet = (isset($ICMS->pICMSEfet)) ? $ICMS->pICMSEfet : 0;
            $std->vICMSEfet = (isset($ICMS->vICMSEfet)) ? $ICMS->vICMSEfet : 0;
            $std->enq_ipi = (isset($IPI->cEnq)) ? trim($IPI->cEnq) : '999';
            $std->cst_ipi = (isset($IPI->CST)) ? trim($IPI->CST) : '99';
            $std->vBCIPI = (isset($IPI->vBC)) ? trim($IPI->vBC) : 0;
            $std->pIPI = (isset($IPI->pIPI)) ? trim($IPI->pIPI) : 0;
            $std->vIPI = (isset($IPI->vIPI)) ? trim($IPI->vIPI) : 0;
            $std->vBCII = (isset($II->vBC)) ? trim($II->vBC) : 0;
            $std->vDespAdu = (isset($II->vDespAdu)) ? trim($II->vDespAdu) : 0;
            $std->vII = (isset($II->vII)) ? trim($II->vII) : 0;
            $std->vIOF = (isset($II->vIOF)) ? trim($II->vIOF) : 0;
            $std->cst_pis = (isset($PIS->CST)) ? trim($PIS->CST) : 0;
            $std->vBCPIS = (isset($PIS->vBC)) ? trim($PIS->vBC) : 0;
            $std->pPIS = (isset($PIS->pPIS)) ? trim($PIS->pPIS) : 0;
            $std->vPIS = (isset($PIS->vPIS)) ? trim($PIS->vPIS) : 0;
            $std->cst_cofins = (isset($COFINS->CST)) ? trim($COFINS->CST) : 0;
            $std->vBCCOFINS = (isset($COFINS->vBC)) ? trim($COFINS->vBC) : 0;
            $std->pCOFINS = (isset($COFINS->pCOFINS)) ? trim($COFINS->pCOFINS) : 0;
            $std->vCOFINS = (isset($COFINS->vCOFINS)) ? trim($COFINS->vCOFINS) : 0;
            $std->nDI = (isset($l->prod->DI->nDI)) ? trim($l->prod->DI->nDI) : NULL;
            $std->dDI = (isset($l->prod->DI->dDI)) ? trim($l->prod->DI->dDI) : NULL;
            $std->xLocDesemb = (isset($l->prod->DI->xLocDesemb)) ? trim($l->prod->DI->xLocDesemb) : NULL;
            $std->UFDesemb = (isset($l->prod->DI->UFDesemb)) ? trim($l->prod->DI->UFDesemb) : NULL;
            $std->dDesemb = (isset($l->prod->DI->dDesemb)) ? trim($l->prod->DI->dDesemb) : NULL;
            $std->tpViaTransp = (isset($l->prod->DI->tpViaTransp)) ? trim($l->prod->DI->tpViaTransp) : NULL;
            $std->vAFRMM = (isset($l->prod->DI->vAFRMM)) ? trim($l->prod->DI->vAFRMM) : 0;
            $std->tpIntermedio = (isset($l->prod->DI->tpIntermedio)) ? trim($l->prod->DI->tpIntermedio) : NULL;
            $std->cExportador = (isset($l->prod->DI->cExportador)) ? trim($l->prod->DI->cExportador) : NULL;
            $std->nAdicao = (isset($l->prod->DI->adi->nAdicao)) ? trim($l->prod->DI->adi->nAdicao) : NULL;
            $std->nSeqAdic = (isset($l->prod->DI->adi->nSeqAdic)) ? trim($l->prod->DI->adi->nSeqAdic) : NULL;
            $std->cFabricante = (isset($l->prod->DI->adi->cFabricante)) ? trim($l->prod->DI->adi->cFabricante) : NULL;
            $std->qBCProd_pis =  (isset($PIS->qBCProd_pis)) ? trim($PIS->qBCProd_pis) : 0;
            $std->vAliqProd_pis =  (isset($PIS->vAliqProd_pis)) ? trim($PIS->vAliqProd_pis) : 0;
            $std->qBCProd_cofins =  (isset($COFINS->qBCProd_cofins)) ? trim($COFINS->qBCProd_cofins) : 0;
            $std->vAliqProd_cofins =  (isset($COFINS->vAliqProd_cofins)) ? trim($COFINS->vAliqProd_cofins) : 0;



            $Itens[] = $std;
        }
        return (object)$Itens;
    }
    public function getParcelas()
    {
        $parcelas = [];
        if (isset($this->xml->NFe->infNFe->cobr)) {
            $par = $this->xml->NFe->infNFe->cobr;
            $fatura =  trim($this->xml->NFe->infNFe->cobr->fat->nFat);
            $parcelas = [];

            foreach ($par->dup as $p) {
                $std = new \stdClass;
                $std->fatura = $fatura;
                $std->parcela = trim($p->nDup);
                $std->dt_vencimento = trim($p->dVenc);
                $std->vlr_parcela = trim($p->vDup);
                $parcelas[] = $std;
            }
        }
        return (object) $parcelas;
    }
    public function getObs()
    {
        $obs = $this->xml->NFe->infNFe->infAdic;
        $std = new \stdClass;
        $std->infAdFisco = (isset($obs->infAdFisco)) ? (trim($obs->infAdFisco)) : NULL;
        $std->infCpl = (isset($obs->infCpl)) ? (trim($obs->infCpl)) : NULL;
        return $std;
    }
    public function getNotaCab()
    {
        $nota = [];
        $nota['chave'] = $this->getChave();
        $nota = array_merge($nota, (array)$this->getProtocolo());
        $nota = array_merge($nota, (array)$this->getIde());
        $nota = array_merge($nota, (array)$this->getEmitente());
        $nota = array_merge($nota, (array)$this->getDestinatario());
        $nota = array_merge($nota, (array)$this->getTotal());
        $nota = array_merge($nota, (array)$this->getTransporte());
        $nota = array_merge($nota, (array)$this->getObs());
        //$nota['parcelas'] = $this->getParcelas();
        //$nota['itens'] = $this->getProdutos();
        return (object)$nota;
    }
}
