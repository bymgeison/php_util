<?php

namespace GX4\Util;

use NFePHP\DA\NFe\Danfe;
use NFePHP\DA\Legacy\FilesFolders;

class CustomDanfe extends Danfe
{
    protected function monta(
        $logo = ''
    ) {
        $this->pdf       = '';
        $this->logomarca = $this->adjustImage($logo);
        //se a orientação estiver em branco utilizar o padrão estabelecido na NF
        if (empty($this->orientacao)) {
            if ($this->tpImp == '2') {
                $this->orientacao = 'L';
            } else {
                $this->orientacao = 'P';
            }
        }
        //instancia a classe pdf
        $this->pdf = new CustomPdf($this->orientacao, 'mm', $this->papel);
        //margens do PDF, em milímetros. Obs.: a margem direita é sempre igual à
        //margem esquerda. A margem inferior *não* existe na FPDF, é definida aqui
        //apenas para controle se necessário ser maior do que a margem superior
        // posição inicial do conteúdo, a partir do canto superior esquerdo da página
        $xInic = $this->margesq;
        if ($this->orientacao === 'P') {
            if ($this->papel === 'A4') {
                $this->maxW = 210;
                $this->maxH = 297;
            }
        } else {
            if ($this->papel === 'A4') {
                $this->maxW = 297;
                $this->maxH = 210;
                $xInic      = $this->margesq + 10;
                //se paisagem multiplica a largura do canhoto pela quantidade de canhotos
                //$this->wCanhoto *= $this->qCanhoto;
            }
        }
        //total inicial de paginas
        $totPag = 1;
        //largura imprimivel em mm: largura da folha menos as margens esq/direita
        $this->wPrint = $this->maxW - ($this->margesq * 2);
        //comprimento (altura) imprimivel em mm: altura da folha menos as margens
        //superior e inferior
        $this->hPrint = $this->maxH - $this->margsup - $this->marginf;
        // estabelece contagem de paginas
        $this->pdf->aliasNbPages();
        // fixa as margens
        $this->pdf->setMargins($this->margesq, $this->margsup);
        $this->pdf->setDrawColor(0, 0, 0);
        $this->pdf->setFillColor(255, 255, 255);
        // inicia o documento
        $this->pdf->open();
        // adiciona a primeira página
        $this->pdf->addPage($this->orientacao, $this->papel);
        $this->pdf->setLineWidth(0.1);
        $this->pdf->settextcolor(0, 0, 0);

        //##################################################################
        // CALCULO DO NUMERO DE PAGINAS A SEREM IMPRESSAS
        //##################################################################
        //Verificando quantas linhas serão usadas para impressão das duplicatas
        $linhasDup = 0;
        $qtdPag    = 0;
        if (isset($this->dup) && $this->dup->length > 0) {
            $qtdPag = $this->dup->length;
        } elseif (isset($this->detPag) && $this->detPag->length > 0) {
            $qtdPag = $this->detPag->length;
        }
        if (($qtdPag > 0) && ($qtdPag <= 7)) {
            $linhasDup = 1;
        } elseif (($qtdPag > 7) && ($qtdPag <= 14)) {
            $linhasDup = 2;
        } elseif (($qtdPag > 14) && ($qtdPag <= 21)) {
            $linhasDup = 3;
        } elseif ($qtdPag > 21) {
            // chinnonsantos 11/05/2016: Limite máximo de impressão de duplicatas na NFe,
            // só vai ser exibito as 21 primeiras duplicatas (parcelas de pagamento),
            // se não oculpa espaço d+, cada linha comporta até 7 duplicatas.
            $linhasDup = 3;
        }
        //verifica se será impressa a linha dos serviços ISSQN
        $linhaISSQN = 0;
        if ((isset($this->ISSQNtot)) && ($this->getTagValue($this->ISSQNtot, 'vServ') > 0)) {
            $linhaISSQN = 1;
        }
        //calcular a altura necessária para os dados adicionais
        if ($this->orientacao === 'P') {
            $this->wAdic = round($this->wPrint * 0.66, 0);
        } else {
            $this->wAdic = round(($this->wPrint - $this->wCanhoto) * 0.5, 0);
        }
        $fontProduto = ['font' => $this->fontePadrao, 'size' => 7, 'style' => ''];

        $this->hdadosadic = $this->calculoEspacoVericalDadosAdicionais();

        //altura disponivel para os campos da DANFE
        $hcabecalho    = 47; //para cabeçalho
        $hdestinatario = 25; //para destinatario
        $hduplicatas   = 12; //para cada grupo de 7 duplicatas
        if (isset($this->entrega)) {
            $hlocalentrega = 25;
        } else {
            $hlocalentrega = 0;
        }
        if (isset($this->retirada)) {
            $hlocalretirada = 25;
        } else {
            $hlocalretirada = 0;
        }
        $himposto    = 18; // para imposto
        $htransporte = 25; // para transporte
        $hissqn      = 11; // para issqn
        $hfooter     = 5; // para rodape
        $hCabecItens = 4; //cabeçalho dos itens

        $hOCUPADA    = $hcabecalho
            + $hdestinatario
            + $hlocalentrega
            + $hlocalretirada
            + ($linhasDup * $hduplicatas)
            + $himposto + $htransporte
            + ($linhaISSQN * $hissqn)
            + $this->hdadosadic
            + $hfooter
            + $hCabecItens
            + $this->sizeExtraTextoFatura();

        //alturas disponiveis para os dados
        $hDispo1 = $this->hPrint - $hOCUPADA;
        /*($hcabecalho +
        //$hdestinatario + ($linhasDup * $hduplicatas) + $himposto + $htransporte +
        $hdestinatario + $hlocalentrega + $hlocalretirada +
        ($linhasDup * $hduplicatas) + $himposto + $htransporte +
        ($linhaISSQN * $hissqn) + $this->hdadosadic + $hfooter + $hCabecItens +
        $this->sizeExtraTextoFatura());*/

        if ($this->orientacao === 'P') {
            $hDispo1 -= 24 * $this->qCanhoto; //para canhoto
            $w       = $this->wPrint;
        } else {
            $hcanhoto = $this->hPrint; //para canhoto
            $w        = $this->wPrint - $this->wCanhoto;
        }
        //$hDispo1 += 14;
        $hDispo2 = $this->hPrint - ($hcabecalho + $hfooter + $hCabecItens);
        //Contagem da altura ocupada para impressão dos itens
        $aFont     = ['font' => $this->fontePadrao, 'size' => 7, 'style' => ''];
        $numlinhas = 0;
        $hUsado    = $hCabecItens;
        $w2        = round($w * 0.25, 0);
        $hDispo    = $hDispo1;
        $totPag    = 1;
        $i         = 0;
        while ($i < $this->det->length) {
            $itemProd = $this->det->item($i);
            $mostrarUnidadeTributavel = false;
            $prod = $itemProd->getElementsByTagName('prod')->item(0);
            $veicProd = $prod->getElementsByTagName("veicProd")->item(0);
            $vUnCom = $prod->getElementsByTagName("vUnCom")->item(0)->nodeValue;
            $uTrib = $prod->getElementsByTagName("uTrib")->item(0);
            $qTrib = $prod->getElementsByTagName("qTrib")->item(0);
            $vUnTrib = !empty($prod->getElementsByTagName("vUnTrib")->item(0)->nodeValue)
                ? $prod->getElementsByTagName("vUnTrib")->item(0)->nodeValue
                : 0;
            //se as unidades forem diferentes e q qtda de qTrib for maior que 0
            //mostrat as unidades
            $mostrarUnidadeTributavel = (!$this->ocultarUnidadeTributavel
                && !empty($uTrib)
                && !empty($qTrib)
                && number_format($vUnCom, 2, ',', '') !== number_format($vUnTrib, 2, ',', '')
            );
            $hUsado += $this->calculeHeight($itemProd, $mostrarUnidadeTributavel);
            // Tag somente é gerada para veiculo 0k, e só é permitido um veiculo por NF-e por conta do detran
            // Verifica se a Tag existe
            if (!empty($veicProd)) {
                $hUsado += 22;
            }
            if ($hUsado > $hDispo) {
                $totPag++;
                $hDispo = $hDispo2;
                $hUsado = $hCabecItens;
                //$i--; // decrementa para readicionar o item que não coube nessa pagina na outra.
            }
            $i++;
        } //fim da soma das areas de itens usadas
        $qtdeItens = $i; //controle da quantidade de itens no DANFE
        //montagem da primeira página
        $pag = 1;

        $x = $this->margesq;
        $y = $this->margsup;
        //coloca o(s) canhoto(s) da NFe
        if ($this->orientacao === 'P') {
            $y = $this->canhoto($this->margesq, $this->margsup);
        } else {
            $this->canhoto($this->margesq, $this->margsup);
            $x = 25;
        }
        //coloca o cabeçalho
        $y = $this->header($x, $y, $pag, $totPag);
        //coloca os dados do destinatário
        $y = $this->destinatarioDANFE($x, $y + 1);
        //coloca os dados do local de retirada
        if (isset($this->retirada)) {
            $y = $this->localRetiradaDANFE($x, $y + 1);
        }
        //coloca os dados do local de entrega
        if (isset($this->entrega)) {
            $y = $this->localEntregaDANFE($x, $y + 1);
        }

        //Verifica as formas de pagamento da nota fiscal
        $formaPag = [];
        if (isset($this->detPag) && $this->detPag->length > 0) {
            foreach ($this->detPag as $k => $d) {
                $fPag            = !empty($this->detPag->item($k)->getElementsByTagName('tPag')->item(0)->nodeValue)
                    ? $this->detPag->item($k)->getElementsByTagName('tPag')->item(0)->nodeValue
                    : '0';
                $formaPag[$fPag] = $fPag;
            }
        }
        //caso tenha boleto imprimir fatura
        if ($this->dup->length > 0) {
            $y = $this->fatura($x, $y + 1);
        } elseif ($this->exibirTextoFatura) {
            //Se somente tiver a forma de pagamento sem pagamento não imprimir nada
            if (count($formaPag) == '1' && isset($formaPag[90])) {
                $y = $y;
            } else {
                //caso tenha mais de uma forma de pagamento ou seja diferente de boleto exibe a
                //forma de pagamento e o valor
                $y = $this->pagamento($x, $y + 1);
            }
        }
        //coloca os dados dos impostos e totais da NFe
        $y = $this->imposto($x, $y + 1);
        //coloca os dados do trasnporte
        $y = $this->transporte($x, $y + 1);
        //itens da DANFE
        $nInicial = 0;

        $y = $this->itens($x, $y + 1, $nInicial, $hDispo1, $pag, $totPag, $hCabecItens);

        //coloca os dados do ISSQN
        if ($linhaISSQN == 1) {
            $y = $this->issqn($x, $y + 4);
        } else {
            $y += 4;
        }
        //coloca os dados adicionais da NFe
        $y = $this->dadosAdicionais($x, $y, $this->hdadosadic);
        //coloca o rodapé da página
        if ($this->orientacao === 'P') {
            $this->rodape($xInic);
        } else {
            $this->rodape($xInic);
        }

        //loop para páginas seguintes
        for ($n = 2; $n <= $totPag; $n++) {
            // fixa as margens
            $this->pdf->setMargins($this->margesq, $this->margsup);
            //adiciona nova página
            $this->pdf->addPage($this->orientacao, $this->papel);
            //ajusta espessura das linhas
            $this->pdf->setLineWidth(0.1);
            //seta a cor do texto para petro
            $this->pdf->settextcolor(0, 0, 0);
            // posição inicial do relatorio
            $x = $this->margesq;
            $y = $this->margsup;
            //coloca o cabeçalho na página adicional
            $y = $this->header($x, $y, $n, $totPag);
            //coloca os itens na página adicional
            $y = $this->itens($x, $y + 1, $nInicial, $hDispo2, $n, $totPag, $hCabecItens);
            //coloca o rodapé da página
            if ($this->orientacao === 'P') {
                $this->rodape($this->margesq);
            } else {
                $this->rodape($this->margesq);
            }
            //se estiver na última página e ainda restar itens para inserir, adiciona mais uma página
            if ($n == $totPag && $this->qtdeItensProc < $qtdeItens) {
                $totPag++;
            }
        }
    }
    protected function header($x = 0, $y = 0, $pag = '1', $totPag = '1')
    {
        $oldX = $x;
        $oldY = $y;
        if ($this->orientacao === 'P') {
            $maxW = $this->wPrint;
        } else {
            if ($pag == 1) { // primeira página
                $maxW = $this->wPrint - $this->wCanhoto;
            } else { // páginas seguintes
                $maxW = $this->wPrint;
            }
        }
        //####################################################################################
        //coluna esquerda identificação do emitente
        $w = round($maxW * 0.41, 0);
        if ($this->orientacao === 'P') {
            $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => 'I'];
        } else {
            $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => 'B'];
        }
        $w1   = $w;
        $h    = 32;
        $oldY += $h;
        $this->pdf->textBox($x, $y, $w, $h);
        $texto = 'IDENTIFICAÇÃO DO EMITENTE';
        $this->pdf->textBox($x, $y, $w, 5, $texto, $aFont, 'T', 'C', 0, '');
        //estabelecer o alinhamento
        //pode ser left L, center C, right R, full logo L
        //se for left separar 1/3 da largura para o tamanho da imagem
        //os outros 2/3 serão usados para os dados do emitente
        //se for center separar 1/2 da altura para o logo e 1/2 para os dados
        //se for right separa 2/3 para os dados e o terço seguinte para o logo
        //se não houver logo centraliza dos dados do emitente
        // coloca o logo
        if (!empty($this->logomarca)) {
            $logoInfo = getimagesize($this->logomarca);
            //largura da imagem em mm
            $logoWmm = ($logoInfo[0] / 72) * 25.4;
            //altura da imagem em mm
            $logoHmm = ($logoInfo[1] / 72) * 25.4;
            if ($this->logoAlign === 'L') {
                $nImgW = round($w / 3, 0);
                $nImgH = round($logoHmm * ($nImgW / $logoWmm), 0);
                $xImg  = $x + 1;
                $yImg  = round(($h - $nImgH) / 2, 0) + $y;
                //estabelecer posições do texto
                $x1 = round($xImg + $nImgW + 1, 0);
                $y1 = round($h / 3 + $y, 0);
                $tw = round(2 * $w / 3, 0);
            } elseif ($this->logoAlign === 'C') {
                $nImgH = round($h / 3, 0);
                $nImgW = round($logoWmm * ($nImgH / $logoHmm), 0);
                $xImg  = round(($w - $nImgW) / 2 + $x, 0);
                $yImg  = $y + 3;
                $x1    = $x;
                $y1    = round($yImg + $nImgH + 1, 0);
                $tw    = $w;
            } elseif ($this->logoAlign === 'R') {
                $nImgW = round($w / 3, 0);
                $nImgH = round($logoHmm * ($nImgW / $logoWmm), 0);
                $xImg  = round($x + ($w - (1 + $nImgW)), 0);
                $yImg  = round(($h - $nImgH) / 2, 0) + $y;
                $x1    = $x;
                $y1    = round($h / 3 + $y, 0);
                $tw    = round(2 * $w / 3, 0);
            } elseif ($this->logoAlign === 'F') {
                $nImgH = round($h - 5, 0);
                $nImgW = round($logoWmm * ($nImgH / $logoHmm), 0);
                $xImg  = round(($w - $nImgW) / 2 + $x, 0);
                $yImg  = $y + 3;
                $x1    = $x;
                $y1    = round($yImg + $nImgH + 1, 0);
                $tw    = $w;
            }
            $type = (substr($this->logomarca, 0, 7) === 'data://') ? 'jpg' : null;
            $this->pdf->Image($this->logomarca, $xImg, $yImg, $nImgW, $nImgH, 'jpeg');
        } else {
            $x1 = $x;
            $y1 = round($h / 3 + $y, 0);
            $tw = $w;
        }
        // monta as informações apenas se diferente de full logo
        if ($this->logoAlign !== 'F') {
            //Nome emitente
            $aFont = ['font' => $this->fontePadrao, 'size' => 12, 'style' => 'B'];
            $texto = $this->emit->getElementsByTagName("xNome")->item(0)->nodeValue;
            $this->pdf->textBox($x1, $y1, $tw, 8, $texto, $aFont, 'T', 'C', 0, '');
            //endereço
            $y1     = $y1 + 5;
            $aFont  = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
            $fone   = !empty($this->enderEmit->getElementsByTagName("fone")->item(0)->nodeValue)
                ? $this->enderEmit->getElementsByTagName("fone")->item(0)->nodeValue
                : '';
            $lgr    = $this->getTagValue($this->enderEmit, "xLgr");
            $nro    = $this->getTagValue($this->enderEmit, "nro");
            $cpl    = $this->getTagValue($this->enderEmit, "xCpl", " - ");
            $bairro = $this->getTagValue($this->enderEmit, "xBairro");
            $CEP    = $this->getTagValue($this->enderEmit, "CEP");
            $CEP    = $this->formatField($CEP, "#####-###");
            $mun    = $this->getTagValue($this->enderEmit, "xMun");
            $UF     = $this->getTagValue($this->enderEmit, "UF");
            $texto  = $lgr . ", " . $nro . $cpl . "\n" . $bairro . " - "
                . $CEP . "\n" . $mun . " - " . $UF . " "
                . "Fone/Fax: " . $fone;
            $this->pdf->textBox($x1, $y1, $tw, 8, $texto, $aFont, 'T', 'C', 0, '');
        }

        //####################################################################################
        //coluna central Danfe
        $x  += $w;
        $w  = round($maxW * 0.17, 0); //35;
        $w2 = $w;
        $h  = 32;
        $this->pdf->textBox($x, $y, $w, $h);

        $texto = "DANFE";
        $aFont = ['font' => $this->fontePadrao, 'size' => 14, 'style' => 'B'];
        $this->pdf->textBox($x, $y + 1, $w, $h, $texto, $aFont, 'T', 'C', 0, '');
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $texto = 'Documento Auxiliar da Nota Fiscal Eletrônica';
        $h     = 20;
        $this->pdf->textBox($x, $y + 6, $w, $h, $texto, $aFont, 'T', 'C', 0, '', false);

        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $texto = '0 - ENTRADA';
        $y1    = $y + 14;
        $h     = 8;
        $this->pdf->textBox($x + 2, $y1, $w, $h, $texto, $aFont, 'T', 'L', 0, '');
        $texto = '1 - SAÍDA';
        $y1    = $y + 17;
        $this->pdf->textBox($x + 2, $y1, $w, $h, $texto, $aFont, 'T', 'L', 0, '');
        //tipo de nF
        $aFont = ['font' => $this->fontePadrao, 'size' => 12, 'style' => 'B'];
        $y1    = $y + 13;
        $h     = 7;
        $texto = $this->ide->getElementsByTagName('tpNF')->item(0)->nodeValue;
        $this->pdf->textBox($x + 27, $y1, 5, $h, $texto, $aFont, 'C', 'C', 1, '');
        //numero da NF
        $aFont = ['font' => $this->fontePadrao, 'size' => 10, 'style' => 'B'];
        $y1    = $y + 20;
        $numNF = str_pad(
            $this->ide->getElementsByTagName('nNF')->item(0)->nodeValue,
            9,
            "0",
            STR_PAD_LEFT
        );
        $numNF = $this->formatField($numNF, "###.###.###");
        $texto = "Nº. " . $numNF;
        $this->pdf->textBox($x, $y1, $w, $h, $texto, $aFont, 'C', 'C', 0, '');
        //Série
        $y1    = $y + 23;
        $serie = str_pad(
            $this->ide->getElementsByTagName('serie')->item(0)->nodeValue,
            3,
            "0",
            STR_PAD_LEFT
        );
        $texto = "Série " . $serie;
        $this->pdf->textBox($x, $y1, $w, $h, $texto, $aFont, 'C', 'C', 0, '');
        //numero paginas
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => 'I'];
        $y1    = $y + 26;
        $texto = "Folha " . $pag . "/" . $totPag;
        $this->pdf->textBox($x, $y1, $w, $h, $texto, $aFont, 'C', 'C', 0, '');

        //####################################################################################
        //coluna codigo de barras
        $x  += $w;
        $w  = ($maxW - $w1 - $w2); //85;
        $w3 = $w;
        $h  = 32;
        $this->pdf->textBox($x, $y, $w, $h);
        $this->pdf->setFillColor(0, 0, 0);
        $chave_acesso = str_replace('NFe', '', $this->infNFe->getAttribute("Id"));
        $bW           = 75;
        $bH           = 12;
        //codigo de barras
        $this->pdf->code128($x + (($w - $bW) / 2), $y + 2, $chave_acesso, $bW, $bH);
        //linhas divisorias
        $this->pdf->line($x, $y + 4 + $bH, $x + $w, $y + 4 + $bH);
        $this->pdf->line($x, $y + 12 + $bH, $x + $w, $y + 12 + $bH);
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $y1    = $y + 4 + $bH;
        $h     = 7;
        $texto = 'CHAVE DE ACESSO';
        $this->pdf->textBox($x, $y1, $w, $h, $texto, $aFont, 'T', 'L', 0, '');
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $y1    = $y + 8 + $bH;
        $texto = $this->formatField($chave_acesso, $this->formatoChave);
        $this->pdf->textBox($x + 2, $y1, $w - 2, $h, $texto, $aFont, 'T', 'C', 0, '');
        $y1                = $y + 12 + $bH;
        $aFont             = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $chaveContingencia = "";
        if (!empty($this->epec) && $this->tpEmis == '4') {
            $cabecalhoProtoAutorizacao = 'NÚMERO DE REGISTRO EPEC';
        } else {
            $cabecalhoProtoAutorizacao = 'PROTOCOLO DE AUTORIZAÇÃO DE USO';
        }
        if (($this->tpEmis == 2 || $this->tpEmis == 5)) {
            $cabecalhoProtoAutorizacao = "DADOS DA NF-E";
            $chaveContingencia         = $this->geraChaveAdicionalDeContingencia();
            $this->pdf->setFillColor(0, 0, 0);
            //codigo de barras
            $this->pdf->code128($x + 11, $y1 + 1, $chaveContingencia, $bW * .9, $bH / 2);
        } else {
            $texto = 'Consulta de autenticidade no portal nacional da NF-e';
            $this->pdf->textBox($x + 2, $y1, $w - 2, $h, $texto, $aFont, 'T', 'C', 0, '');
            $y1    = $y + 16 + $bH;
            $texto = 'www.nfe.fazenda.gov.br/portal ou no site da Sefaz Autorizadora';
            $this->pdf->textBox(
                $x + 2,
                $y1,
                $w - 2,
                $h,
                $texto,
                $aFont,
                'T',
                'C',
                0,
                'http://www.nfe.fazenda.gov.br/portal ou no site da Sefaz Autorizadora'
            );
        }

        //####################################################################################
        //Dados da NF do cabeçalho
        //natureza da operação
        $texto = 'NATUREZA DA OPERAÇÃO';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $w     = $w1 + $w2;
        $y     = $oldY;
        $oldY  += $h;
        $x     = $oldX;
        $h     = 7;
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = $this->ide->getElementsByTagName("natOp")->item(0)->nodeValue;
        $aFont = ['font' => $this->fontePadrao, 'size' => 10, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '');
        $x += $w;
        $w = $w3;
        //PROTOCOLO DE AUTORIZAÇÃO DE USO ou DADOS da NF-E
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $cabecalhoProtoAutorizacao, $aFont, 'T', 'L', 1, '');
        // algumas NFe podem estar sem o protocolo de uso portanto sua existencia deve ser
        // testada antes de tentar obter a informação.
        // NOTA : DANFE sem protocolo deve existir somente no caso de contingência !!!
        // Além disso, existem várias NFes em contingência que eu recebo com protocolo de autorização.
        // Na minha opinião, deveríamos mostra-lo, mas o  manual  da NFe v4.01 diz outra coisa...
        if (($this->tpEmis == 2 || $this->tpEmis == 5) && empty($this->epec)) {
            $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => 'B'];
            $texto = $this->formatField(
                $chaveContingencia,
                "#### #### #### #### #### #### #### #### ####"
            );
            $cStat = '';
        } else {
            $aFont = ['font' => $this->fontePadrao, 'size' => 10, 'style' => ''];
            if (!empty($this->epec)) {
                $texto = $this->epec['protocolo'] . ' - ' . $this->epec['data'];
                $cStat = '';
            } else {
                if (isset($this->nfeProc)) {
                    $texto  = !empty($this->nfeProc->getElementsByTagName("nProt")->item(0)->nodeValue)
                        ? $this->nfeProc->getElementsByTagName("nProt")->item(0)->nodeValue
                        : '';
                    $dtHora = $this->toDateTime(
                        $this->nfeProc->getElementsByTagName("dhRecbto")->item(0)->nodeValue
                    );
                    if ($texto != '' && $dtHora) {
                        $texto .= "  -  " . $dtHora->format('d/m/Y H:i:s');
                    }
                    $cStat = $this->nfeProc->getElementsByTagName("cStat")->item(0)->nodeValue;
                } else {
                    $texto = '';
                    $cStat = '';
                }
            }
        }
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'C', 0, '');
        //####################################################################################
        //INSCRIÇÃO ESTADUAL
        $w     = round($maxW * 0.250, 0);
        $y     += $h;
        $oldY  += $h;
        $x     = $oldX;
        $texto = 'INSCRIÇÃO ESTADUAL';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = $this->getTagValue($this->emit, "IE");
        $aFont = ['font' => $this->fontePadrao, 'size' => 10, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '');
        //INSCRIÇÃO MUNICIPAL
        $x     += $w;
        $texto = 'INSCRIÇÃO MUNICIPAL';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = $this->getTagValue($this->emit, "IM");
        $aFont = ['font' => $this->fontePadrao, 'size' => 10, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '');
        //INSCRIÇÃO ESTADUAL DO SUBST. TRIBUT.
        $x     += $w;
        $texto = 'INSCRIÇÃO ESTADUAL DO SUBST. TRIBUT.';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = !empty($this->emit->getElementsByTagName("IEST")->item(0)->nodeValue)
            ? $this->emit->getElementsByTagName("IEST")->item(0)->nodeValue
            : '';
        $aFont = ['font' => $this->fontePadrao, 'size' => 10, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '');
        //CNPJ
        $x     += $w;
        $w     = ($maxW - (3 * $w));
        $texto = 'CNPJ / CPF';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        //Pegando valor do CPF/CNPJ
        if (!empty($this->emit->getElementsByTagName("CNPJ")->item(0)->nodeValue)) {
            $texto = $this->formatField(
                $this->emit->getElementsByTagName("CNPJ")->item(0)->nodeValue,
                "###.###.###/####-##"
            );
        } else {
            $texto = !empty($this->emit->getElementsByTagName("CPF")->item(0)->nodeValue)
                ? $this->formatField(
                    $this->emit->getElementsByTagName("CPF")->item(0)->nodeValue,
                    "###.###.###-##"
                )
                : '';
        }
        $aFont = ['font' => $this->fontePadrao, 'size' => 10, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '');

        //####################################################################################
        //Indicação de NF Homologação, cancelamento e falta de protocolo
        $tpAmb = $this->ide->getElementsByTagName('tpAmb')->item(0)->nodeValue;
        //indicar cancelamento
        $resp = $this->statusNFe();
        if (!$resp['status']) {
            $n = count($resp['message']);
            $alttot = $n * 15;
            $x = 10;
            $y = $this->hPrint / 2 - $alttot / 2;
            $h = 15;
            $w = $maxW - (2 * $x);
            $this->pdf->settextcolor(170, 170, 170);

            foreach ($resp['message'] as $msg) {
                $aFont = ['font' => $this->fontePadrao, 'size' => 48, 'style' => 'B'];
                $this->pdf->textBox($x, $y, $w, $h, $msg, $aFont, 'C', 'C', 0, '');
                $y += $h;
            }
            $texto = $resp['submessage'];
            if (!empty($texto)) {
                $y += 3;
                $h = 5;
                $aFont = ['font' => $this->fontePadrao, 'size' => 20, 'style' => 'B'];
                $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'C', 'C', 0, '');
                $y += $h;
            }
            $y += 5;
            $w = $maxW - (2 * $x);
            $texto = "SEM VALOR FISCAL";
            $aFont = ['font' => $this->fontePadrao, 'size' => 48, 'style' => 'B'];
            $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'C', 'C', 0, '');
            $this->pdf->settextcolor(0, 0, 0);
        }
        if (!empty($this->epec) && $this->tpEmis == 4) {
            //EPEC
            $x = 10;
            $y = $this->hPrint - 130;
            $h = 25;
            $w = $maxW - (2 * $x);
            $this->pdf->SetTextColor(200, 200, 200);
            $texto = "DANFE impresso em contingência -\n" .
                "EPEC regularmente recebido pela Receita\n" .
                "Federal do Brasil";
            $aFont = ['font' => $this->fontePadrao, 'size' => 48, 'style' => 'B'];
            $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'C', 'C', 0, '');
            $this->pdf->SetTextColor(0, 0, 0);
        }

        return $oldY;
    } //fim header

    protected function destinatarioDANFE($x = 0, $y = 0)
    {
        //####################################################################################
        //DESTINATÁRIO / REMETENTE
        $oldX = $x;
        $oldY = $y;
        if ($this->orientacao === 'P') {
            $maxW = $this->wPrint;
        } else {
            $maxW = $this->wPrint - $this->wCanhoto;
        }
        $w     = $maxW;
        $h     = 7;
        $texto = 'DESTINATÁRIO / REMETENTE';
        $aFont = ['font' => $this->fontePadrao, 'size' => 7, 'style' => 'B'];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 0, '');
        //NOME / RAZÃO SOCIAL
        $w     = round($maxW * 0.61, 0);
        $w1    = $w;
        $y     += 3;
        $texto = 'NOME / RAZÃO SOCIAL';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = $this->dest->getElementsByTagName("xNome")->item(0)->nodeValue;
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        if ($this->orientacao === 'P') {
            $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '');
        } else {
            $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 1, '');
        }
        //CNPJ / CPF
        $x     += $w;
        $w     = round($maxW * 0.23, 0);
        $w2    = $w;
        $texto = 'CNPJ / CPF';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        //Pegando valor do CPF/CNPJ
        if (!empty($this->dest->getElementsByTagName("CNPJ")->item(0)->nodeValue)) {
            $texto = $this->formatField(
                $this->dest->getElementsByTagName("CNPJ")->item(0)->nodeValue,
                "###.###.###/####-##"
            );
        } else {
            $texto = !empty($this->dest->getElementsByTagName("CPF")->item(0)->nodeValue)
                ? $this->formatField(
                    $this->dest->getElementsByTagName("CPF")->item(0)->nodeValue,
                    "###.###.###-##"
                )
                : '';
        }
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'C', 0, '');
        //DATA DA EMISSÃO
        $x     += $w;
        $w     = $maxW - ($w1 + $w2);
        $wx    = $w;
        $texto = 'DATA DA EMISSÃO';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $dEmi = !empty($this->ide->getElementsByTagName("dEmi")->item(0)->nodeValue)
            ? $this->ide->getElementsByTagName("dEmi")->item(0)->nodeValue
            : '';
        if ($dEmi == '') {
            $dEmi  = !empty($this->ide->getElementsByTagName("dhEmi")->item(0)->nodeValue)
                ? $this->ide->getElementsByTagName("dhEmi")->item(0)->nodeValue
                : '';
            $aDemi = explode('T', $dEmi);
            $dEmi  = $aDemi[0];
        }
        $texto = $this->ymdTodmy($dEmi);
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        if ($this->orientacao === 'P') {
            $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'C', 0, '');
        } else {
            $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'C', 1, '');
        }
        //ENDEREÇO
        $w     = round($maxW * 0.47, 0);
        $w1    = $w;
        $y     += $h;
        $x     = $oldX;
        $texto = 'ENDEREÇO';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = $this->dest->getElementsByTagName("xLgr")->item(0)->nodeValue;
        $texto .= ', ' . $this->dest->getElementsByTagName("nro")->item(0)->nodeValue;
        $texto .= $this->getTagValue($this->dest, "xCpl", " - ");

        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '', true);
        //BAIRRO / DISTRITO
        $x     += $w;
        $w     = round($maxW * 0.21, 0);
        $w2    = $w;
        $texto = 'BAIRRO / DISTRITO';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = $this->dest->getElementsByTagName("xBairro")->item(0)->nodeValue;
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '');
        //CEP
        $x     += $w;
        $w     = $maxW - $w1 - $w2 - $wx;
        $w2    = $w;
        $texto = 'CEP';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = !empty($this->dest->getElementsByTagName("CEP")->item(0)->nodeValue)
            ? $this->dest->getElementsByTagName("CEP")->item(0)->nodeValue
            : '';
        $texto = $this->formatField($texto, "#####-###");
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'C', 0, '');
        //DATA DA SAÍDA
        $x     += $w;
        $w     = $wx;
        $texto = 'DATA DA SAÍDA/ENTRADA';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $dSaiEnt = !empty($this->ide->getElementsByTagName("dSaiEnt")->item(0)->nodeValue)
            ? $this->ide->getElementsByTagName("dSaiEnt")->item(0)->nodeValue
            : '';
        if ($dSaiEnt == '') {
            $dSaiEnt  = !empty($this->ide->getElementsByTagName("dhSaiEnt")->item(0)->nodeValue)
                ? $this->ide->getElementsByTagName("dhSaiEnt")->item(0)->nodeValue
                : '';
            $aDsaient = explode('T', $dSaiEnt);
            $dSaiEnt  = $aDsaient[0];
        }
        $texto = $this->ymdTodmy($dSaiEnt);
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'C', 0, '');
        //MUNICÍPIO
        $w     = $w1;
        $y     += $h;
        $x     = $oldX;
        $texto = 'MUNICÍPIO';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = $this->dest->getElementsByTagName("xMun")->item(0)->nodeValue;
        if (strtoupper(trim($texto)) === "EXTERIOR"
            && $this->dest->getElementsByTagName("xPais")->length > 0
        ) {
            $texto .= " - " . $this->dest->getElementsByTagName("xPais")->item(0)->nodeValue;
        }
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '');
        //UF
        $x     += $w;
        $w     = 8;
        $texto = 'UF';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = $this->dest->getElementsByTagName("UF")->item(0)->nodeValue;
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'C', 0, '');
        //FONE / FAX
        $x     += $w;
        $w     = round(($maxW - $w1 - $wx - 8) / 2, 0) + 1;
        $w3    = $w;
        $texto = 'FONE / FAX';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = !empty($this->dest->getElementsByTagName("fone")->item(0)->nodeValue)
            ? $this->dest->getElementsByTagName("fone")->item(0)->nodeValue
            : '';
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '');
        //INSCRIÇÃO ESTADUAL
        $x     += $w;
        $w     = $maxW - $w1 - $wx - 8 - $w3;
        $texto = 'INSCRIÇÃO ESTADUAL';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $IE    = $this->dest->getElementsByTagName("IE");
        $texto = ($IE && $IE->length > 0) ? $IE->item(0)->nodeValue : '';
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '');
        //HORA DA SAÍDA
        $x     += $w;
        $w     = $wx;
        $texto = 'HORA DA SAÍDA/ENTRADA';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $hSaiEnt = !empty($this->ide->getElementsByTagName("hSaiEnt")->item(0)->nodeValue)
            ? $this->ide->getElementsByTagName("hSaiEnt")->item(0)->nodeValue
            : '';
        if ($hSaiEnt == '') {
            $dhSaiEnt   = !empty($this->ide->getElementsByTagName("dhSaiEnt")->item(0)->nodeValue)
                ? $this->ide->getElementsByTagName("dhSaiEnt")->item(0)->nodeValue
                : '';
            $tsDhSaiEnt = $this->toDateTime($dhSaiEnt);
            if ($tsDhSaiEnt) {
                $hSaiEnt = $tsDhSaiEnt->format('H:i:s');
            }
        }
        $texto = $hSaiEnt;
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'C', 0, '');

        return ($y + $h);
    } //fim da função destinatarioDANFE

    protected function localEntregaDANFE($x = 0, $y = 0)
    {
        //####################################################################################
        //LOCAL DE ENTREGA
        $oldX = $x;
        if ($this->orientacao === 'P') {
            $maxW = $this->wPrint;
        } else {
            $maxW = $this->wPrint - $this->wCanhoto;
        }
        $w     = $maxW;
        $h     = 7;
        $texto = 'INFORMAÇÕES DO LOCAL DE ENTREGA';
        $aFont = ['font' => $this->fontePadrao, 'size' => 7, 'style' => 'B'];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 0, '');
        //NOME / RAZÃO SOCIAL
        $w     = round($maxW * 0.61, 0);
        $w1    = $w;
        $y     += 3;
        $texto = 'NOME / RAZÃO SOCIAL';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = '';
        if ($this->entrega->getElementsByTagName("xNome")->item(0)) {
            $texto = $this->entrega->getElementsByTagName("xNome")->item(0)->nodeValue;
        }
        $aFont = ['font' => $this->fontePadrao, 'size' => 10, 'style' => ''];
        if ($this->orientacao == 'P') {
            $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '');
        } else {
            $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 1, '');
        }
        //CNPJ / CPF
        $x     += $w;
        $w     = round($maxW * 0.23, 0);
        $w2    = $w;
        $texto = 'CNPJ / CPF';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        //Pegando valor do CPF/CNPJ
        if (!empty($this->entrega->getElementsByTagName("CNPJ")->item(0)->nodeValue)) {
            $texto = $this->formatField(
                $this->entrega->getElementsByTagName("CNPJ")->item(0)->nodeValue,
                "###.###.###/####-##"
            );
        } else {
            $texto = !empty($this->entrega->getElementsByTagName("CPF")->item(0)->nodeValue) ?
                $this->formatField(
                    $this->entrega->getElementsByTagName("CPF")->item(0)->nodeValue,
                    "###.###.###-##"
                ) : '';
        }
        $aFont = ['font' => $this->fontePadrao, 'size' => 10, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '');
        //INSCRIÇÃO ESTADUAL
        $x     += $w;
        $w     = $maxW - ($w1 + $w2);
        $wx    = $w;
        $texto = 'INSCRIÇÃO ESTADUAL';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = '';
        if ($this->entrega->getElementsByTagName("IE")->item(0)) {
            $texto = $this->entrega->getElementsByTagName("IE")->item(0)->nodeValue;
        }
        $aFont = ['font' => $this->fontePadrao, 'size' => 10, 'style' => ''];
        if ($this->orientacao === 'P') {
            $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '');
        } else {
            $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 1, '');
        }
        //ENDEREÇO
        $w     = round($maxW * 0.355, 0) + $wx;
        $w1    = $w;
        $y     += $h;
        $x     = $oldX;
        $texto = 'ENDEREÇO';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = $this->entrega->getElementsByTagName("xLgr")->item(0)->nodeValue;
        $texto .= ', ' . $this->entrega->getElementsByTagName("nro")->item(0)->nodeValue;
        $texto .= $this->getTagValue($this->entrega, "xCpl", " - ");
        $aFont = ['font' => $this->fontePadrao, 'size' => 10, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '', true);
        //BAIRRO / DISTRITO
        $x     += $w;
        $w     = round($maxW * 0.335, 0);
        $w2    = $w;
        $texto = 'BAIRRO / DISTRITO';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = $this->entrega->getElementsByTagName("xBairro")->item(0)->nodeValue;
        $aFont = ['font' => $this->fontePadrao, 'size' => 10, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '');
        //CEP
        $x     += $w;
        $w     = $maxW - ($w1 + $w2);
        $texto = 'CEP';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = !empty($this->entrega->getElementsByTagName("CEP")->item(0)->nodeValue) ?
            $this->entrega->getElementsByTagName("CEP")->item(0)->nodeValue : '';
        $texto = $this->formatField($texto, "#####-###");
        $aFont = ['font' => $this->fontePadrao, 'size' => 10, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'C', 0, '');
        //MUNICÍPIO
        $w     = round($maxW * 0.805, 0);
        $w1    = $w;
        $y     += $h;
        $x     = $oldX;
        $texto = 'MUNICÍPIO';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = $this->entrega->getElementsByTagName("xMun")->item(0)->nodeValue;
        if (strtoupper(trim($texto)) == "EXTERIOR" && $this->entrega->getElementsByTagName("xPais")->length > 0) {
            $texto .= " - " . $this->entrega->getElementsByTagName("xPais")->item(0)->nodeValue;
        }
        $aFont = ['font' => $this->fontePadrao, 'size' => 10, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '');
        //UF
        $x     += $w;
        $w     = 8;
        $texto = 'UF';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = $this->entrega->getElementsByTagName("UF")->item(0)->nodeValue;
        $aFont = ['font' => $this->fontePadrao, 'size' => 10, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'C', 0, '');
        //FONE / FAX
        $x     += $w;
        $w     = $maxW - $w - $w1;
        $texto = 'FONE / FAX';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = !empty($this->entrega->getElementsByTagName("fone")->item(0)->nodeValue) ?
            $this->entrega->getElementsByTagName("fone")->item(0)->nodeValue : '';
        $aFont = ['font' => $this->fontePadrao, 'size' => 10, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '');

        return ($y + $h);
    } //fim da função localEntregaDANFE

    protected function localRetiradaDANFE($x = 0, $y = 0)
    {
        //####################################################################################
        //LOCAL DE RETIRADA
        $oldX = $x;
        if ($this->orientacao === 'P') {
            $maxW = $this->wPrint;
        } else {
            $maxW = $this->wPrint - $this->wCanhoto;
        }
        $w     = $maxW;
        $h     = 7;
        $texto = 'INFORMAÇÕES DO LOCAL DE RETIRADA';
        $aFont = ['font' => $this->fontePadrao, 'size' => 7, 'style' => 'B'];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 0, '');
        //NOME / RAZÃO SOCIAL
        $w     = round($maxW * 0.61, 0);
        $w1    = $w;
        $y     += 3;
        $texto = 'NOME / RAZÃO SOCIAL';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = '';
        if ($this->retirada->getElementsByTagName("xNome")->item(0)) {
            $texto = $this->retirada->getElementsByTagName("xNome")->item(0)->nodeValue;
        }
        $aFont = ['font' => $this->fontePadrao, 'size' => 10, 'style' => ''];
        if ($this->orientacao === 'P') {
            $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '');
        } else {
            $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 1, '');
        }
        //CNPJ / CPF
        $x     += $w;
        $w     = round($maxW * 0.23, 0);
        $w2    = $w;
        $texto = 'CNPJ / CPF';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        //Pegando valor do CPF/CNPJ
        if (!empty($this->retirada->getElementsByTagName("CNPJ")->item(0)->nodeValue)) {
            $texto = $this->formatField(
                $this->retirada->getElementsByTagName("CNPJ")->item(0)->nodeValue,
                "###.###.###/####-##"
            );
        } else {
            $texto = !empty($this->retirada->getElementsByTagName("CPF")->item(0)->nodeValue) ?
                $this->formatField(
                    $this->retirada->getElementsByTagName("CPF")->item(0)->nodeValue,
                    "###.###.###-##"
                ) : '';
        }
        $aFont = ['font' => $this->fontePadrao, 'size' => 10, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '');
        //INSCRIÇÃO ESTADUAL
        $x     += $w;
        $w     = $maxW - ($w1 + $w2);
        $wx    = $w;
        $texto = 'INSCRIÇÃO ESTADUAL';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = '';
        if ($this->retirada->getElementsByTagName("IE")->item(0)) {
            $texto = $this->retirada->getElementsByTagName("IE")->item(0)->nodeValue;
        }
        $aFont = ['font' => $this->fontePadrao, 'size' => 10, 'style' => ''];
        if ($this->orientacao === 'P') {
            $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '');
        } else {
            $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 1, '');
        }
        //ENDEREÇO
        $w     = round($maxW * 0.355, 0) + $wx;
        $w1    = $w;
        $y     += $h;
        $x     = $oldX;
        $texto = 'ENDEREÇO';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = $this->retirada->getElementsByTagName("xLgr")->item(0)->nodeValue;
        $texto .= ', ' . $this->retirada->getElementsByTagName("nro")->item(0)->nodeValue;
        $texto .= $this->getTagValue($this->retirada, "xCpl", " - ");
        $aFont = ['font' => $this->fontePadrao, 'size' => 10, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '', true);
        //BAIRRO / DISTRITO
        $x     += $w;
        $w     = round($maxW * 0.335, 0);
        $w2    = $w;
        $texto = 'BAIRRO / DISTRITO';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = $this->retirada->getElementsByTagName("xBairro")->item(0)->nodeValue;
        $aFont = ['font' => $this->fontePadrao, 'size' => 10, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '');
        //CEP
        $x     += $w;
        $w     = $maxW - ($w1 + $w2);
        $texto = 'CEP';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = !empty($this->retirada->getElementsByTagName("CEP")->item(0)->nodeValue) ?
            $this->retirada->getElementsByTagName("CEP")->item(0)->nodeValue : '';
        $texto = $this->formatField($texto, "#####-###");
        $aFont = ['font' => $this->fontePadrao, 'size' => 10, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'C', 0, '');
        //MUNICÍPIO
        $w     = round($maxW * 0.805, 0);
        $w1    = $w;
        $y     += $h;
        $x     = $oldX;
        $texto = 'MUNICÍPIO';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = $this->retirada->getElementsByTagName("xMun")->item(0)->nodeValue;
        if (strtoupper(trim($texto)) === "EXTERIOR" && $this->retirada->getElementsByTagName("xPais")->length > 0) {
            $texto .= " - " . $this->retirada->getElementsByTagName("xPais")->item(0)->nodeValue;
        }
        $aFont = ['font' => $this->fontePadrao, 'size' => 10, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '');
        //UF
        $x     += $w;
        $w     = 8;
        $texto = 'UF';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = $this->retirada->getElementsByTagName("UF")->item(0)->nodeValue;
        $aFont = ['font' => $this->fontePadrao, 'size' => 10, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'C', 0, '');
        //FONE / FAX
        $x     += $w;
        $w     = $maxW - $w - $w1;
        $texto = 'FONE / FAX';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = !empty($this->retirada->getElementsByTagName("fone")->item(0)->nodeValue) ?
            $this->retirada->getElementsByTagName("fone")->item(0)->nodeValue : '';
        $aFont = ['font' => $this->fontePadrao, 'size' => 10, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '');

        return ($y + $h);
    } //fim da função localRetiradaDANFE

    protected function fatura($x, $y)
    {
        $linha       = 1;
        $h           = 8 + 3;
        $oldx        = $x;
        $textoFatura = $this->getTextoFatura();
        //verificar se existem duplicatas
        if ($this->dup->length > 0 || $textoFatura !== "") {
            //#####################################################################
            //FATURA / DUPLICATA
            $texto = "FATURA / DUPLICATA";
            if ($this->orientacao == 'P') {
                $w = $this->wPrint;
            } else {
                $w = 271;
            }
            $h     = 8;
            $aFont = ['font' => $this->fontePadrao, 'size' => 7, 'style' => ''];
            $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 0, '');
            $y       += 3;
            $dups    = "";
            $dupcont = 0;
            $nFat    = $this->dup->length;
            if ($nFat > 7) {
                $myH = 6;
                $myW = $this->wPrint;
                if ($this->orientacao == 'L') {
                    $myW -= $this->wCanhoto;
                }
                $aFont = ['font' => $this->fontePadrao, 'size' => 9, 'style' => ''];
                $texto = "Existem mais de 7 duplicatas registradas, portanto não "
                    . "serão exibidas, confira diretamente pelo XML.";
                $this->pdf->textBox($x, $y, $myW, $myH, $texto, $aFont, 'C', 'C', 1, '');

                return ($y + $h - 3);
            }
            if ($textoFatura !== "" && $this->exibirTextoFatura) {
                $myH = 6;
                $myW = $this->wPrint;
                if ($this->orientacao == 'L') {
                    $myW -= $this->wCanhoto;
                }
                $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
                $this->pdf->textBox($x, $y, $myW, $myH, $textoFatura, $aFont, 'C', 'L', 1, '');
                $y += $myH + 1;
            }
            if ($this->orientacao == 'P') {
                $w = round($this->wPrint / 7.018, 0) - 1;
            } else {
                $w = 28;
            }
            $increm = 1;
            foreach ($this->dup as $k => $d) {
                $nDup  = !empty($this->dup->item($k)->getElementsByTagName('nDup')->item(0)->nodeValue)
                    ? $this->dup->item($k)->getElementsByTagName('nDup')->item(0)->nodeValue
                    : '';
                $dDup  = !empty($this->dup->item($k)->getElementsByTagName('dVenc')->item(0)->nodeValue)
                    ? $this->ymdTodmy($this->dup->item($k)->getElementsByTagName('dVenc')->item(0)->nodeValue)
                    : '';
                $vDup  = !empty($this->dup->item($k)->getElementsByTagName('vDup')->item(0)->nodeValue)
                    ? 'R$ ' . number_format(
                        $this->dup->item($k)->getElementsByTagName('vDup')->item(0)->nodeValue,
                        2,
                        ",",
                        "."
                    )
                    : '';
                $h     = 8;
                $texto = '';
                if ($nDup != '0' && $nDup != '') {
                    $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
                    $this->pdf->textBox($x, $y, $w, $h, 'Num.', $aFont, 'T', 'L', 1, '');
                    $aFont = ['font' => $this->fontePadrao, 'size' => 7, 'style' => ''];
                    $this->pdf->textBox($x, $y, $w, $h, $nDup, $aFont, 'T', 'R', 0, '');
                } else {
                    $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
                    $this->pdf->textBox($x, $y, $w, $h, ($dupcont + 1) . "", $aFont, 'T', 'L', 1, '');
                }
                $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
                $this->pdf->textBox($x, $y, $w, $h, 'Venc.', $aFont, 'C', 'L', 0, '');
                $aFont = ['font' => $this->fontePadrao, 'size' => 7, 'style' => ''];
                $this->pdf->textBox($x, $y, $w, $h, $dDup, $aFont, 'C', 'R', 0, '');
                $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
                $this->pdf->textBox($x, $y, $w, $h, 'Valor', $aFont, 'B', 'L', 0, '');
                $aFont = ['font' => $this->fontePadrao, 'size' => 7, 'style' => ''];
                $this->pdf->textBox($x, $y, $w, $h, $vDup, $aFont, 'B', 'R', 0, '');
                $x       += $w + $increm;
                $dupcont += 1;
                if ($this->orientacao == 'P') {
                    $maxDupCont = 6;
                } else {
                    $maxDupCont = 8;
                }
                if ($dupcont > $maxDupCont) {
                    $y       += 9;
                    $x       = $oldx;
                    $dupcont = 0;
                    $linha   += 1;
                }
                if ($linha == 5) {
                    $linha = 4;
                    break;
                }
            }
            if ($dupcont == 0) {
                $y -= 9;
                $linha--;
            }

            return ($y + $h);
        } else {
            $linha = 0;

            return ($y - 2);
        }
    }

    protected function pagamento($x, $y)
    {
        $linha = 1;
        $h     = 8 + 3;
        $oldx  = $x;
        //verificar se existem cobranças definidas
        if (isset($this->detPag) && $this->detPag->length > 0) {
            //#####################################################################
            //Tipo de pagamento
            $texto = "PAGAMENTO";
            if ($this->orientacao === 'P') {
                $w = $this->wPrint;
            } else {
                $w = 271;
            }
            $h     = 8;
            $aFont = ['font' => $this->fontePadrao, 'size' => 7, 'style' => 'B'];
            $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 0, '');
            $y       += 3;
            $dups    = "";
            $dupcont = 0;
            if ($this->orientacao === 'P') {
                $w = round($this->wPrint / 3.968, 0) - 1;
            } else {
                $w = 28;
            }
            if ($this->orientacao === 'P') {
                $maxDupCont = 3;
            } else {
                $maxDupCont = 8;
            }
            $increm         = 1;
            $formaPagamento = [
                '01' => 'Dinheiro',
                '02' => 'Cheque',
                '03' => 'Cartão de Crédito',
                '04' => 'Cartão de Débito',
                '05' => 'Crédito Loja',
                '10' => 'Vale Alimentação',
                '11' => 'Vale Refeição',
                '12' => 'Vale Presente',
                '13' => 'Vale Combustível',
                '14' => 'Duplicata Mercantil',
                '15' => 'Boleto',
                '16' => 'Depósito Bancário',
                '17' => 'Pagamento Instantâneo (PIX)',
                '18' => 'Transferência Bancária, Carteira Digit.',
                '19' => 'Fidelidade, Cashback, Crédito Virtual',
                '90' => 'Sem pagamento',
                '99' => 'Outros'
            ];
            $bandeira       = [
                '01' => 'Visa',
                '02' => 'Mastercard',
                '03' => 'American',
                '04' => 'Sorocred',
                '05' => 'Diners',
                '06' => 'Elo',
                '07' => 'Hipercard',
                '08' => 'Aura',
                '09' => 'Cabal',
                '99' => 'Outros'
            ];
            foreach ($this->detPag as $k => $d) {
                $fPag  = !empty($this->detPag->item($k)->getElementsByTagName('tPag')->item(0)->nodeValue)
                    ? $this->detPag->item($k)->getElementsByTagName('tPag')->item(0)->nodeValue
                    : '0';
                $vPag  = !empty($this->detPag->item($k)->getElementsByTagName('vPag')->item(0)->nodeValue)
                    ? 'R$ ' . number_format(
                        $this->detPag->item($k)->getElementsByTagName('vPag')->item(0)->nodeValue,
                        2,
                        ",",
                        "."
                    )
                    : '';
                $h = 6;
                $texto = '';
                if (isset($formaPagamento[$fPag])) {
                    /*Exibir Item sem pagamento*/
                    if ($fPag == '90') {
                        continue;
                    }
                    $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
                    $this->pdf->textBox($x, $y, $w, $h, 'Forma', $aFont, 'T', 'L', 1, '');
                    $aFont = ['font' => $this->fontePadrao, 'size' => 7, 'style' => ''];
                    $this->pdf->textBox($x, $y, $w, $h, $formaPagamento[$fPag], $aFont, 'T', 'R', 0, '');
                } else {
                    $aFont = ['font' => $this->fontePadrao, 'size' => 7, 'style' => ''];
                    $this->pdf->textBox($x, $y, $w, $h, "Forma " . $fPag . " não encontrado", $aFont, 'T', 'L', 1, '');
                }
                $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
                $this->pdf->textBox($x, $y, $w, $h, 'Valor', $aFont, 'B', 'L', 0, '');
                $aFont = ['font' => $this->fontePadrao, 'size' => 7, 'style' => ''];
                $this->pdf->textBox($x, $y, $w, $h, $vPag, $aFont, 'B', 'R', 0, '');
                $x       += $w + $increm;
                $dupcont += 1;

                if ($dupcont > $maxDupCont) {
                    $y       += 9;
                    $x       = $oldx;
                    $dupcont = 0;
                    $linha   += 1;
                }
                if ($linha == 5) {
                    $linha = 4;
                    break;
                }
            }
            if ($dupcont == 0) {
                $y -= 9;
                $linha--;
            }

            return ($y + $h);
        } else {
            $linha = 0;

            return ($y - 2);
        }
    } //fim da função pagamento

    protected function impostoHelper($x, $y, $w, $h, $titulo, $campoImposto)
    {
        $value = 0;
        $value2 = 0;
        $the_field = $this->ICMSTot->getElementsByTagName($campoImposto)->item(0);
        if (isset($the_field)) {
            $value = $the_field->nodeValue;
            if ($campoImposto == 'vICMS') { // soma junto ao ICMS o FCP
                $the_field_aux = $this->ICMSTot->getElementsByTagName('vFCP')->item(0);
                if (isset($the_field_aux)) {
                    $value2 = $the_field_aux->nodeValue;
                }
            } elseif ($campoImposto == 'vST') { // soma junto ao ICMS ST o FCP ST
                $the_field_aux = $this->ICMSTot->getElementsByTagName('vFCPST')->item(0);
                if (isset($the_field_aux)) {
                    $value2 = $the_field_aux->nodeValue;
                }
            }
        }
        $valorImposto = number_format($value + $value2, 2, ",", ".");

        $fontTitulo = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $fontValor  = ['font' => $this->fontePadrao, 'size' => 10, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $titulo, $fontTitulo, 'T', 'L', 1, '');
        $this->pdf->textBox($x, $y, $w, $h, $valorImposto, $fontValor, 'B', 'R', 0, '');

        $next_x = $x + $w;

        return $next_x;
    }

    protected function transporte($x, $y)
    {
        $oldX = $x;
        if ($this->orientacao == 'P') {
            $maxW = $this->wPrint;
        } else {
            $maxW = $this->wPrint - $this->wCanhoto;
        }
        //#####################################################################
        //TRANSPORTADOR / VOLUMES TRANSPORTADOS
        $texto = "TRANSPORTADOR / VOLUMES TRANSPORTADOS";
        $w     = $maxW;
        $h     = 7;
        $aFont = ['font' => $this->fontePadrao, 'size' => 7, 'style' => 'B'];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 0, '');
        //NOME / RAZÃO SOCIAL
        $w1    = $maxW * 0.29;
        $y     += 3;
        $texto = 'NOME / RAZÃO SOCIAL';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w1, $h, $texto, $aFont, 'T', 'L', 1, '');
        if (isset($this->transporta)) {
            $texto = !empty($this->transporta->getElementsByTagName("xNome")->item(0)->nodeValue)
                ? $this->transporta->getElementsByTagName("xNome")->item(0)->nodeValue
                : '';
        } else {
            $texto = '';
        }
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $this->pdf->textBox($x, $y, $w1, $h, $texto, $aFont, 'B', 'L', 0, '');
        //FRETE POR CONTA
        $x     += $w1;
        $w2    = $maxW * 0.15;
        $texto = 'FRETE';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w2, $h, $texto, $aFont, 'T', 'L', 1, '');
        $tipoFrete = !empty($this->transp->getElementsByTagName("modFrete")->item(0)->nodeValue)
            ? $this->transp->getElementsByTagName("modFrete")->item(0)->nodeValue
            : '0';
        switch ($tipoFrete) {
            case 0:
                $texto = "0-Emitente";
                break;
            case 1:
                $texto = "1-Destinatário/Remetente";
                break;
            case 2:
                $texto = "2-De Terceiros";
                break;
            case 3:
                $texto = "3-Próprio por conta do Rem";
                break;
            case 4:
                $texto = "4-Próprio por conta do Dest";
                break;
            case 9:
                $texto = "9-Sem Frete";
                break;
        }
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $this->pdf->textBox($x, $y, $w2, $h, $texto, $aFont, 'C', 'L', 1, '');
        //CÓDIGO ANTT
        $x     += $w2;
        $texto = 'CÓDIGO ANTT';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w2, $h, $texto, $aFont, 'T', 'L', 1, '');
        if (isset($this->veicTransp)) {
            $texto = !empty($this->veicTransp->getElementsByTagName("RNTC")->item(0)->nodeValue)
                ? $this->veicTransp->getElementsByTagName("RNTC")->item(0)->nodeValue
                : '';
        } else {
            $texto = '';
        }
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $this->pdf->textBox($x, $y, $w2, $h, $texto, $aFont, 'B', 'C', 0, '');
        //PLACA DO VEÍC
        $x     += $w2;
        $texto = 'PLACA DO VEÍCULO';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w2, $h, $texto, $aFont, 'T', 'L', 1, '');
        if (isset($this->veicTransp)) {
            $texto = !empty($this->veicTransp->getElementsByTagName("placa")->item(0)->nodeValue)
                ? $this->veicTransp->getElementsByTagName("placa")->item(0)->nodeValue
                : '';
        } elseif (isset($this->reboque)) {
            $texto = !empty($this->reboque->getElementsByTagName("placa")->item(0)->nodeValue)
                ? $this->reboque->getElementsByTagName("placa")->item(0)->nodeValue
                : '';
        } else {
            $texto = '';
        }
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $this->pdf->textBox($x, $y, $w2, $h, $texto, $aFont, 'B', 'C', 0, '');
        //UF
        $x     += $w2;
        $w3    = round($maxW * 0.04, 0);
        $texto = 'UF';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w3, $h, $texto, $aFont, 'T', 'L', 1, '');
        if (isset($this->veicTransp)) {
            $texto = !empty($this->veicTransp->getElementsByTagName("UF")->item(0)->nodeValue)
                ? $this->veicTransp->getElementsByTagName("UF")->item(0)->nodeValue
                : '';
        } elseif (isset($this->reboque)) {
            $texto = !empty($this->reboque->getElementsByTagName("UF")->item(0)->nodeValue)
                ? $this->reboque->getElementsByTagName("UF")->item(0)->nodeValue
                : '';
        } else {
            $texto = '';
        }
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $this->pdf->textBox($x, $y, $w3, $h, $texto, $aFont, 'B', 'C', 0, '');
        //CNPJ / CPF
        $x     += $w3;
        $w     = $maxW - ($w1 + 3 * $w2 + $w3);
        $texto = 'CNPJ / CPF';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        if (isset($this->transporta)) {
            $texto = !empty($this->transporta->getElementsByTagName("CNPJ")->item(0)->nodeValue)
                ? $this->formatField(
                    $this->transporta->getElementsByTagName("CNPJ")->item(0)->nodeValue,
                    "##.###.###/####-##"
                )
                : '';
            if ($texto == '') {
                $texto = !empty($this->transporta->getElementsByTagName("CPF")->item(0)->nodeValue)
                    ? $this->formatField(
                        $this->transporta->getElementsByTagName("CPF")->item(0)->nodeValue,
                        "###.###.###-##"
                    )
                    : '';
            }
        } else {
            $texto = '';
        }
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'C', 0, '');
        //#####################################################################
        //ENDEREÇO
        $y     += $h;
        $x     = $oldX;
        $h     = 7;
        $w1    = $maxW * 0.44;
        $texto = 'ENDEREÇO';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w1, $h, $texto, $aFont, 'T', 'L', 1, '');
        if (isset($this->transporta)) {
            $texto = !empty($this->transporta->getElementsByTagName("xEnder")->item(0)->nodeValue)
                ? $this->transporta->getElementsByTagName("xEnder")->item(0)->nodeValue
                : '';
        } else {
            $texto = '';
        }
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $this->pdf->textBox($x, $y, $w1, $h, $texto, $aFont, 'B', 'L', 0, '');
        //MUNICÍPIO
        $x     += $w1;
        $w2    = round($maxW * 0.29, 0)+1.8;
        $texto = 'MUNICÍPIO';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w2, $h, $texto, $aFont, 'T', 'L', 1, '');
        if (isset($this->transporta)) {
            $texto = !empty($this->transporta->getElementsByTagName("xMun")->item(0)->nodeValue)
                ? $this->transporta->getElementsByTagName("xMun")->item(0)->nodeValue
                : '';
        } else {
            $texto = '';
        }
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $this->pdf->textBox($x, $y, $w2, $h, $texto, $aFont, 'B', 'L', 0, '');
        //UF
        $x     += $w2;
        $w3    = round($maxW * 0.04, 0);
        $texto = 'UF';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w3, $h, $texto, $aFont, 'T', 'L', 1, '');
        if (isset($this->transporta)) {
            $texto = !empty($this->transporta->getElementsByTagName("UF")->item(0)->nodeValue)
                ? $this->transporta->getElementsByTagName("UF")->item(0)->nodeValue
                : '';
        } else {
            $texto = '';
        }
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $this->pdf->textBox($x, $y, $w3, $h, $texto, $aFont, 'B', 'C', 0, '');
        //INSCRIÇÃO ESTADUAL
        $x     += $w3;
        $w     = $maxW - ($w1 + $w2 + $w3);
        $texto = 'INSCRIÇÃO ESTADUAL';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = '';
        if (isset($this->transporta)) {
            if (!empty($this->transporta->getElementsByTagName("IE")->item(0)->nodeValue)) {
                $texto = $this->transporta->getElementsByTagName("IE")->item(0)->nodeValue;
            }
        }
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'L', 0, '');
        //Tratar Multiplos volumes
        $volumes     = $this->transp->getElementsByTagName('vol');
        $quantidade  = 0;
        $especie     = '';
        $marca       = '';
        $numero      = '';
        $texto       = '';
        $pesoBruto   = 0;
        $pesoLiquido = 0;
        foreach ($volumes as $volume) {
            $quantidade  += !empty($volume->getElementsByTagName("qVol")->item(0)->nodeValue) ?
                $volume->getElementsByTagName("qVol")->item(0)->nodeValue : 0;
            $pesoBruto   += !empty($volume->getElementsByTagName("pesoB")->item(0)->nodeValue) ?
                $volume->getElementsByTagName("pesoB")->item(0)->nodeValue : 0;
            $pesoLiquido += !empty($volume->getElementsByTagName("pesoL")->item(0)->nodeValue) ?
                $volume->getElementsByTagName("pesoL")->item(0)->nodeValue : 0;
            $texto       = !empty($this->transp->getElementsByTagName("esp")->item(0)->nodeValue) ?
                $this->transp->getElementsByTagName("esp")->item(0)->nodeValue : '';
            if ($texto != $especie && $especie != '') {
                //tem várias especies
                $especie = 'VARIAS';
            } else {
                $especie = $texto;
            }
            $texto = !empty($this->transp->getElementsByTagName("marca")->item(0)->nodeValue)
                ? $this->transp->getElementsByTagName("marca")->item(0)->nodeValue
                : '';
            if ($texto != $marca && $marca != '') {
                //tem várias especies
                $marca = 'VARIAS';
            } else {
                $marca = $texto;
            }
            $texto = !empty($this->transp->getElementsByTagName("nVol")->item(0)->nodeValue)
                ? $this->transp->getElementsByTagName("nVol")->item(0)->nodeValue
                : '';
            if ($texto != $numero && $numero != '') {
                //tem várias especies
                $numero = 'VARIOS';
            } else {
                $numero = $texto;
            }
        }

        //#####################################################################
        //QUANTIDADE
        $y     += $h;
        $x     = $oldX;
        $h     = 7;
        $w1    = round($maxW * 0.10, 0)-0.35;
        $texto = 'QUANTIDADE';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w1, $h, $texto, $aFont, 'T', 'L', 1, '');
        if (!empty($quantidade)) {
            $texto = $quantidade;
            $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
            $this->pdf->textBox($x, $y, $w1, $h, $texto, $aFont, 'B', 'C', 0, '');
        }
        //ESPÉCIE
        $x     += $w1;
        $w2    = round($maxW * 0.17, 0);
        $texto = 'ESPÉCIE';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w2, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = $especie;
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $this->pdf->textBox($x, $y, $w2, $h, $texto, $aFont, 'B', 'L', 0, '');
        //MARCA
        $x     += $w2;
        $texto = 'MARCA';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w2, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = !empty($this->transp->getElementsByTagName("marca")->item(0)->nodeValue) ?
            $this->transp->getElementsByTagName("marca")->item(0)->nodeValue : '';
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $this->pdf->textBox($x, $y, $w2, $h, $texto, $aFont, 'B', 'L', 0, '');
        //NUMERAÇÃO
        $x     += $w2;
        $texto = 'NUMERAÇÃO';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w2, $h, $texto, $aFont, 'T', 'L', 1, '');
        $texto = $numero;
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $this->pdf->textBox($x, $y, $w2, $h, $texto, $aFont, 'B', 'C', 0, '');
        //PESO BRUTO
        $x     += $w2;
        $w3    = round($maxW * 0.20, 0);
        $texto = 'PESO BRUTO';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w3, $h, $texto, $aFont, 'T', 'L', 1, '');
        if (is_numeric($pesoBruto) && $pesoBruto > 0) {
            $texto = number_format($pesoBruto, 3, ",", ".");
        } else {
            $texto = '';
        }
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $this->pdf->textBox($x, $y, $w3, $h, $texto, $aFont, 'B', 'R', 0, '');
        //PESO LÍQUIDO
        $x     += $w3;
        $w     = $maxW - ($w1 + 3 * $w2 + $w3);
        $texto = 'PESO LÍQUIDO';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 1, '');
        if (is_numeric($pesoLiquido) && $pesoLiquido > 0) {
            $texto = number_format($pesoLiquido, 3, ",", ".");
        } else {
            $texto = '';
        }
        $aFont = ['font' => $this->fontePadrao, 'size' => 8, 'style' => ''];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'B', 'R', 0, '');

        return ($y + $h);
    } //fim transporte


    protected function itens($x, $y, &$nInicio, $hmax, $pag = 0, $totpag = 0, $hCabecItens = 7)
    {
        $oldX = $x;
        $oldY = $y;
        $totItens = $this->det->length;
        //#####################################################################
        //DADOS DOS PRODUTOS / SERVIÇOS
        $texto = "DADOS DOS PRODUTOS / SERVIÇOS";
        if ($this->orientacao === 'P') {
            $w = $this->wPrint;
        } else {
            if ($nInicio < 2) { // primeira página
                $w = $this->wPrint - $this->wCanhoto;
            } else { // páginas seguintes
                $w = $this->wPrint;
            }
        }
        $h     = 4;
        $aFont = ['font' => $this->fontePadrao, 'size' => 7, 'style' => 'B'];
        $this->pdf->textBox($x, $y, $w, $h, $texto, $aFont, 'T', 'L', 0, '');
        $y += 3;
        //desenha a caixa dos dados dos itens da NF
        $hmax  += 1;
        $texto = '';
        $this->pdf->textBox($x, $y, $w, $hmax);
        //##################################################################################
        // cabecalho LOOP COM OS DADOS DOS PRODUTOS
        //CÓDIGO PRODUTO
        $texto = "CÓDIGO PRODUTO";
        $w1    = round($w * 0.09, 0);
        $h     = 4;
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w1, $h, $texto, $aFont, 'C', 'C', false, '', false);
        $this->pdf->line($x + $w1, $y, $x + $w1, $y + $hmax);

        // $this->pdf->dashedVLine($x + $w1, $y, 0, $y + $hmax, 100);
        //DESCRIÇÃO DO PRODUTO / SERVIÇO
        $x     += $w1;
        $w2    = round($w * 0.25, 0);
        $texto = 'DESCRIÇÃO DO PRODUTO / SERVIÇO';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w2, $h, $texto, $aFont, 'C', 'C', 0, '', false);
        $this->pdf->line($x + $w2, $y, $x + $w2, $y + $hmax);
        // $this->pdf->dashedVLine($x + $w2, $y, 0.1, $y + $hmax, 100);
        //NCM/SH
        $x     += $w2;
        $w3    = round($w * 0.06, 0);
        $texto = 'NCM/SH';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w3, $h, $texto, $aFont, 'C', 'C', 0, '', false);
        $this->pdf->line($x + $w3, $y, $x + $w3, $y + $hmax);
        // $this->pdf->dashedVLine($x + $w3, $y, 0.1, $y + $hmax, 100);
        //O/CST ou O/CSOSN
        $x     += $w3;
        $w4    = round($w * 0.05, 0);
        $texto = 'O/CST'; // CRT = 2 ou CRT = 3
        if ($this->getTagValue($this->emit, 'CRT') == '1') {
            $texto = 'O/CSOSN'; //Regime do Simples CRT = 1
        }
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w4, $h, $texto, $aFont, 'C', 'C', 0, '', false);
        $this->pdf->line($x + $w4, $y, $x + $w4, $y + $hmax);
        // $this->pdf->dashedVLine($x + $w4, $y, 0.1, $y + $hmax, 100);
        //CFOP
        $x     += $w4;
        $w5    = round($w * 0.04, 0);
        $texto = 'CFOP';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w5, $h, $texto, $aFont, 'C', 'C', 0, '', false);
        $this->pdf->line($x + $w5, $y, $x + $w5, $y + $hmax);
        // $this->pdf->dashedVLine($x + $w5, $y, 0.1, $y + $hmax, 100);
        //UN
        $x     += $w5;
        $w6    = round($w * 0.03, 0);
        $texto = 'UN';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w6, $h, $texto, $aFont, 'C', 'C', 0, '', false);
        $this->pdf->line($x + $w6, $y, $x + $w6, $y + $hmax);
        // $this->pdf->dashedVLine($x + $w6, $y, 0.1, $y + $hmax, 100);
        //QUANT
        $x     += $w6;
        $w7    = round($w * 0.08, 0);
        $texto = 'QUANT';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w7, $h, $texto, $aFont, 'C', 'C', 0, '', false);
        $this->pdf->line($x + $w7, $y, $x + $w7, $y + $hmax);
        // $this->pdf->dashedVLine($x + $w7, $y, 0.1, $y + $hmax, 100);
        //VALOR UNIT
        $x     += $w7;
        $w8    = round($w * 0.06, 0);
        $texto = 'VALOR UNIT';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w8, $h, $texto, $aFont, 'C', 'C', 0, '', false);
        $this->pdf->line($x + $w8, $y, $x + $w8, $y + $hmax);
        // $this->pdf->dashedVLine($x + $w8, $y, 0.1, $y + $hmax, 100);
        //VALOR TOTAL
        $x     += $w8;
        $w9    = round($w * 0.06, 0);
        $texto = 'VALOR TOTAL';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w9, $h, $texto, $aFont, 'C', 'C', 0, '', false);
        $this->pdf->line($x + $w9, $y, $x + $w9, $y + $hmax);
        // $this->pdf->dashedVLine($x + $w9, $y, 0.1, $y + $hmax, 100);
        //VALOR DESCONTO
        $x     += $w9;
        $w10   = round($w * 0.05, 0);
        $texto = 'VALOR DESC';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w10, $h, $texto, $aFont, 'C', 'C', 0, '', false);
        $this->pdf->line($x + $w10, $y, $x + $w10, $y + $hmax);
        // $this->pdf->dashedVLine($x + $w10, $y, 0.1, $y + $hmax, 100);
        //B.CÁLC ICMS
        $x     += $w10;
        $w11   = round($w * 0.06, 0);
        $texto = 'B.CÁLC ICMS';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w11, $h, $texto, $aFont, 'C', 'C', 0, '', false);
        $this->pdf->line($x + $w11, $y, $x + $w11, $y + $hmax);
        // $this->pdf->dashedVLine($x + $w11, $y, 0.1, $y + $hmax, 100);
        //VALOR ICMS
        $x     += $w11;
        $w12   = round($w * 0.06, 0);
        $texto = 'VALOR ICMS';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w12, $h, $texto, $aFont, 'C', 'C', 0, '', false);
        $this->pdf->line($x + $w12, $y, $x + $w12, $y + $hmax);
        // $this->pdf->dashedVLine($x + $w12, $y, 0.1, $y + $hmax, 100);
        //VALOR IPI
        $x     += $w12;
        $w13   = round($w * 0.05, 0);
        $texto = 'VALOR IPI';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w13, $h, $texto, $aFont, 'C', 'C', 0, '', false);
        $this->pdf->line($x + $w13, $y, $x + $w13, $y + $hmax);
        // $this->pdf->dashedVLine($x + $w13, $y, 0.1, $y + $hmax, 100);
        //ALÍQ. ICMS
        $x     += $w13;
        $w14   = round($w * 0.04, 0);
        $texto = 'ALÍQ. ICMS';
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => ''];
        $this->pdf->textBox($x, $y, $w14, $h, $texto, $aFont, 'C', 'C', 0, '', false);
        $this->pdf->line($x + $w14, $y, $x + $w14, $y + $hmax);
        // $this->pdf->dashedVLine($x + $w14, $y, 0.1, $y + $hmax, 100);
        //ALÍQ. IPI
        $x     += $w14;
        $w15   = $w - ($w1 + $w2 + $w3 + $w4 + $w5 + $w6 + $w7 + $w8 + $w9 + $w10 + $w11 + $w12 + $w13 + $w14);
        $texto = 'ALÍQ. IPI';
        $this->pdf->textBox($x, $y, $w15, $h, $texto, $aFont, 'C', 'C', 0, '', false);
        $this->pdf->line($oldX, $y + $h + 1, $oldX + $w, $y + $h + 1);
        $y += 5;
        //##################################################################################
        // LOOP COM OS DADOS DOS PRODUTOS
        $i      = 0;
        $hUsado = $hCabecItens;
        $aFont  = ['font' => $this->fontePadrao, 'size' => 7, 'style' => ''];

        foreach ($this->det as $d) {
            if ($i >= $nInicio) {
                $thisItem = $this->det->item($i);
                //carrega as tags do item
                $prod         = $thisItem->getElementsByTagName("prod")->item(0);
                $imposto      = $this->det->item($i)->getElementsByTagName("imposto")->item(0);
                $ICMS         = $imposto->getElementsByTagName("ICMS")->item(0);
                $IPI          = $imposto->getElementsByTagName("IPI")->item(0);
                $textoProduto = $this->descricaoProduto($thisItem);
                //$veicProd     = $prod->getElementsByTagName("veicProd")->item(0);

                // Posição y dos dados das unidades tributaveis.
                $yTrib = $this->pdf->fontSize + .5;

                $uCom = $prod->getElementsByTagName("uCom")->item(0)->nodeValue;
                $vUnCom = $prod->getElementsByTagName("vUnCom")->item(0)->nodeValue;
                $uTrib = $prod->getElementsByTagName("uTrib")->item(0);
                $qTrib = $prod->getElementsByTagName("qTrib")->item(0);
                $cfop = $prod->getElementsByTagName("CFOP")->item(0)->nodeValue;
                $vUnTrib = !empty($prod->getElementsByTagName("vUnTrib")->item(0)->nodeValue)
                    ? $prod->getElementsByTagName("vUnTrib")->item(0)->nodeValue
                    : 0;
                // A Configuração serve para informar se irá exibir
                //   de forma obrigatória, estando diferente ou não,
                //   a unidade de medida tributária.
                // ========
                // A Exibição será realizada sempre que a unidade comercial for
                //   diferente da unidade de medida tributária.
                // "Nas situações em que o valor unitário comercial for diferente do valor unitário tributável,
                //   ambas as informações deverão estar expressas e identificadas no DANFE, podendo ser
                //   utilizada uma das linhas adicionais previstas, ou o campo de informações adicionais."
                // > Manual Integração - Contribuinte 4.01 - NT2009.006, Item 7.1.5, página 91.
                $mostrarUnidadeTributavel = (!$this->ocultarUnidadeTributavel
                    && !empty($uTrib)
                    && !empty($qTrib)
                    && number_format($vUnCom, 2, ',', '') !== number_format($vUnTrib, 2, ',', '')
                );

                // Informação sobre unidade de medida tributavel.
                // Se não for para exibir a unidade de medida tributavel, então
                // A Escrita irá começar em 0.
                if (!$mostrarUnidadeTributavel) {
                    $yTrib = 0;
                }
                $h = $this->calculeHeight($thisItem, $mostrarUnidadeTributavel);
                $hUsado += $h;

                $yTrib += $y;
                $diffH = $hmax - $hUsado;

                if (1 > $diffH && $i < $totItens) {
                    if ($pag == $totpag) {
                        $totpag++;
                    }
                        //ultrapassa a capacidade para uma única página
                        //o restante dos dados serão usados nas proximas paginas
                        $nInicio = $i;
                        break;
                }

                $y_linha = $y + $h;

                //corrige o x
                $x = $oldX;
                //codigo do produto
                $guup  = $i + 1;
                $texto = $prod->getElementsByTagName("cProd")->item(0)->nodeValue;
                $this->pdf->textBox($x, $y, $w1, $h, $texto, $aFont, 'T', 'C', 0, '');
                $x += $w1;

                //DESCRIÇÃO
                if ($this->orientacao === 'P') {
                    $this->pdf->textBox($x, $y, $w2, $h, $textoProduto, $aFont, 'T', 'L', 0, '', false);
                } else {
                    $this->pdf->textBox($x, $y, $w2, $h, $textoProduto, $aFont, 'T', 'L', 0, '', false);
                }
                $x += $w2;
                //NCM
                $texto = !empty($prod->getElementsByTagName("NCM")->item(0)->nodeValue) ?
                    $prod->getElementsByTagName("NCM")->item(0)->nodeValue : '';
                $this->pdf->textBox($x, $y, $w3, $h, $texto, $aFont, 'T', 'C', 0, '');
                $x += $w3;

                //GRUPO DE VEICULO NOVO
                $oldfont = $aFont;
                $veicnovo = $this->itemVeiculoNovo($prod);
                $aFont = ['font' => $this->fontePadrao, 'size' => 5, 'style' => ''];
                $this->pdf->textBox(
                    $x-$w3,
                    $y+4,
                    $this->wPrint-($w1+$w2)-2,
                    22,
                    $veicnovo,
                    $aFont,
                    'T',
                    'L',
                    0,
                    '',
                    true,
                    0,
                    0,
                    false
                );
                $aFont = $oldfont;
                //CST
                if (isset($ICMS)) {
                    $origem = $this->getTagValue($ICMS, "orig");
                    $cst    = $this->getTagValue($ICMS, "CST");
                    $csosn  = $this->getTagValue($ICMS, "CSOSN");
                    $texto  = $origem . "/" . $cst . $csosn;
                    $this->pdf->textBox($x, $y, $w4, $h, $texto, $aFont, 'T', 'C', 0, '');
                }
                //CFOP
                $x     += $w4;
                $texto = $prod->getElementsByTagName("CFOP")->item(0)->nodeValue;
                $this->pdf->textBox($x, $y, $w5, $h, $texto, $aFont, 'T', 'C', 0, '');
                //Unidade
                $x     += $w5;
                $texto = $uCom;
                $this->pdf->textBox($x, $y, $w6, $h, $texto, $aFont, 'T', 'C', 0, '');
                //Unidade de medida tributável
                $qTrib = $prod->getElementsByTagName("qTrib")->item(0)->nodeValue;
                if ($mostrarUnidadeTributavel) {
                    $texto = $uTrib->nodeValue;
                    $this->pdf->textBox($x, $yTrib, $w6, $h, $texto, $aFont, 'T', 'C', 0, '');
                }
                $x += $w6;
                if ($this->orientacao == 'P') {
                    $alinhamento = 'R';
                } else {
                    $alinhamento = 'R';
                }
                // QTDADE
                $qCom  = $prod->getElementsByTagName("qCom")->item(0);
                $texto = number_format($qCom->nodeValue, $this->qComCasasDec, ",", ".");
                $this->pdf->textBox($x, $y, $w7, $h, $texto, $aFont, 'T', $alinhamento, 0, '');
                // QTDADE Tributável
                if ($mostrarUnidadeTributavel) {
                    $qTrib = $prod->getElementsByTagName("qTrib")->item(0);
                    if (!empty($qTrib)) {
                        $texto = number_format($qTrib->nodeValue, $this->qComCasasDec, ",", ".");
                        $this->pdf->textBox($x, $yTrib, $w7, $h, $texto, $aFont, 'T', $alinhamento, 0, '');
                    }
                }
                $x += $w7;
                // Valor Unitário
                $vUnCom = $prod->getElementsByTagName("vUnCom")->item(0);
                $texto  = number_format($vUnCom->nodeValue, $this->vUnComCasasDec, ",", ".");
                $this->pdf->textBox($x, $y, $w8, $h, $texto, $aFont, 'T', $alinhamento, 0, '');
                // Valor Unitário Tributável
                if ($mostrarUnidadeTributavel) {
                    $vUnTrib = $prod->getElementsByTagName("vUnTrib")->item(0);
                    if (!empty($vUnTrib)) {
                        $texto = number_format($vUnTrib->nodeValue, $this->vUnComCasasDec, ",", ".");
                        $this->pdf->textBox($x, $yTrib, $w8, $h, $texto, $aFont, 'T', $alinhamento, 0, '');
                    }
                }
                $x += $w8;
                // Valor do Produto
                $texto = "";
                if (is_numeric($prod->getElementsByTagName("vProd")->item(0)->nodeValue)) {
                    $texto = number_format($prod->getElementsByTagName("vProd")->item(0)->nodeValue, 2, ",", ".");
                }
                $this->pdf->textBox($x, $y, $w9, $h, $texto, $aFont, 'T', $alinhamento, 0, '');
                $x += $w9;
                //Valor do Desconto
                $vdesc = !empty($prod->getElementsByTagName("vDesc")->item(0)->nodeValue)
                    ? $prod->getElementsByTagName("vDesc")->item(0)->nodeValue : 0;

                $texto = number_format($vdesc, 2, ",", ".");
                $this->pdf->textBox($x, $y, $w10, $h, $texto, $aFont, 'T', $alinhamento, 0, '');
                //Valor da Base de calculo
                $x += $w10;
                if (isset($ICMS)) {
                    $texto = !empty($ICMS->getElementsByTagName("vBC")->item(0)->nodeValue)
                        ? number_format(
                            $ICMS->getElementsByTagName("vBC")->item(0)->nodeValue,
                            2,
                            ",",
                            "."
                        )
                        : '0,00';
                    $this->pdf->textBox($x, $y, $w11, $h, $texto, $aFont, 'T', $alinhamento, 0, '');
                }
                //Valor do ICMS
                $x += $w11;
                if (isset($ICMS)) {
                    $texto = !empty($ICMS->getElementsByTagName("vICMS")->item(0)->nodeValue)
                        ? number_format(
                            $ICMS->getElementsByTagName("vICMS")->item(0)->nodeValue,
                            2,
                            ",",
                            "."
                        )
                        : '0,00';
                    $this->pdf->textBox($x, $y, $w12, $h, $texto, $aFont, 'T', $alinhamento, 0, '');
                }
                //Valor do IPI
                $x += $w12;
                if (isset($IPI)) {
                    $texto = !empty($IPI->getElementsByTagName("vIPI")->item(0)->nodeValue)
                        ? number_format(
                            $IPI->getElementsByTagName("vIPI")->item(0)->nodeValue,
                            2,
                            ",",
                            "."
                        )
                        : '0,00';
                } else {
                    $texto = '0,00';
                }
                $this->pdf->textBox($x, $y, $w13, $h, $texto, $aFont, 'T', $alinhamento, 0, '');
                // %ICMS
                $x += $w13;
                if (isset($ICMS)) {
                    $texto = !empty($ICMS->getElementsByTagName("pICMS")->item(0)->nodeValue)
                        ? number_format(
                            $ICMS->getElementsByTagName("pICMS")->item(0)->nodeValue,
                            2,
                            ",",
                            "."
                        )
                        : '0,00';
                    $this->pdf->textBox($x, $y, $w14, $h, $texto, $aFont, 'T', 'C', 0, '');
                }
                //%IPI
                $x += $w14;
                if (isset($IPI)) {
                    $texto = !empty($IPI->getElementsByTagName("pIPI")->item(0)->nodeValue)
                        ? number_format(
                            $IPI->getElementsByTagName("pIPI")->item(0)->nodeValue,
                            2,
                            ",",
                            "."
                        )
                        : '0,00';
                } else {
                    $texto = '0,00';
                }
                $this->pdf->textBox($x, $y, $w15, $h, $texto, $aFont, 'T', 'C', 0, '');


                // Dados do Veiculo Somente para veiculo 0 Km
                $veicProd = $prod->getElementsByTagName("veicProd")->item(0);
                // Tag somente é gerada para veiculo 0k, e só é permitido um veiculo por NF-e por conta do detran
                // Verifica se a Tag existe
                if (!empty($veicProd)) {
                    $y += $h - 10;
                    $this->dadosItenVeiculoDANFE($oldX + 3, $y, $nInicio, 3, $prod);
                    // linha entre itens
                    $this->pdf->dashedHLine($oldX, $y + 30, $w, 0.1, 1);
                    $y += 30;
                    $hUsado += 30;
                } else {
                    // linha entre itens
                    // $this->pdf->dashedHLine($oldX, $y, $w, 0.1, 1);
                    $this->pdf->line($oldX, $y, $w+2, $y);
                }
                $y += $h;
                $i++;
                //incrementa o controle dos itens processados.
                $this->qtdeItensProc++;
            } else {
                $i++;
            }
        }

        return $oldY + $hmax;
    }

    protected function rodape($x)
    {
        $y = $this->maxH - 4;
        if ($this->orientacao == 'P') {
            $w = $this->wPrint;
        } else {
            $w = $this->wPrint - $this->wCanhoto;
            $x = $this->wCanhoto;
        }
        $aFont = ['font' => $this->fontePadrao, 'size' => 6, 'style' => 'I'];
        $texto = "Impresso em " . date('d/m/Y') . " as " . date('H:i:s')
            . '  ' ;
        $this->pdf->textBox($x, $y, $w, 0, $texto, $aFont, 'T', 'L', false);
        $texto = $this->powered ? $this->creditos : '';
        $this->pdf->textBox($x, $y, $w, 0, $texto, $aFont, 'T', 'R', false, '');
    }

    // Renderize o DANFE e aplique as customizações
    public function render($dest = '', $mode = 'I')
    {
        return parent::render($dest, $mode);
    }
}