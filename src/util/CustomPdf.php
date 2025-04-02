<?php

namespace GX4\Util;

use NFePHP\DA\Legacy\Pdf;

class CustomPdf extends Pdf
{
    public function textBox(
        $x,
        $y,
        $w,
        $h,
        $text = '',
        $aFont = array('font' => 'Times', 'size' => 8, 'style' => ''),
        $vAlign = 'T',
        $hAlign = 'L',
        $border = true,
        $link = '',
        $force = true,
        $hmax = 0,
        $vOffSet = 0,
        $fill = false
    ) {
        $oldY = $y;
        $temObs = false;
        $resetou = false;
        if ($w < 0) {
            return $y;
        }
        if (is_object($text)) {
            $text = '';
        }
        if (is_string($text)) {
            //remover espaços desnecessários
            $text = trim($text);
            //converter o charset para o fpdf
            $text = $this->convertToIso($text);
            //decodifica os caracteres html no xml
            $text = html_entity_decode($text);
        } else {
            $text = (string) $text;
        }

        //desenhar a borda da caixa
        if ($border && $fill) {
            $this->roundedRect($x, $y, $w, $h, 0, '1234', 'DF');
        } elseif ($border) {
            $this->roundedRect($x, $y, $w, $h, 0, '1234', 'D');
        } elseif ($fill) {
            $this->rect($x, $y, $w, $h, 'F');
        }
        //estabelecer o fonte
        $this->setFont($aFont['font'], $aFont['style'], $aFont['size']);
        //calcular o incremento
        $incY = $this->fontSize; //tamanho da fonte na unidade definida
        if (!$force) {
            //verificar se o texto cabe no espaço
            $n = $this->wordWrap($text, $w);
        } else {
            $n = 1;
        }
        //calcular a altura do conjunto de texto
        $altText = $incY * $n;
        //separar o texto em linhas
        $lines = explode("\n", $text);
        //verificar o alinhamento vertical
        if ($vAlign == 'T') {
            //alinhado ao topo
            $y1 = $y + $incY;
        }
        if ($vAlign == 'C') {
            //alinhado ao centro
            $y1 = $y + $incY + (($h - $altText) / 2);
        }
        if ($vAlign == 'B') {
            //alinhado a base
            $y1 = ($y + $h) - 0.5;
        }
        //para cada linha
        foreach ($lines as $line) {
            //verificar o comprimento da frase
            $texto = trim($line);
            $comp = $this->getStringWidth($texto);
            if ($force) {
                $newSize = $aFont['size'];
                while ($comp > $w) {
                    //estabelecer novo fonte
                    $this->setFont($aFont['font'], $aFont['style'], --$newSize);
                    $comp = $this->getStringWidth($texto);
                }
            }
            //ajustar ao alinhamento horizontal
            $x1 = $x;
            if ($hAlign == 'L') {
                $x1 = $x + 0.5;
            }
            if ($hAlign == 'C') {
                $x1 = $x + (($w - $comp) / 2);
            }
            if ($hAlign == 'R') {
                $x1 = $x + $w - ($comp + 0.5);
            }
            //escrever o texto
            if ($vOffSet > 0) {
                if ($y1 > ($oldY + $vOffSet)) {
                    if (!$resetou) {
                        $y1 = $oldY;
                        $resetou = true;
                    }
                    $this->text($x1, $y1, $texto);
                }
            } else {
                $this->text($x1, $y1, $texto);
            }
            //incrementar para escrever o proximo
            $y1 += $incY;
            if (($hmax > 0) && ($y1 > ($y + ($hmax - 1)))) {
                $temObs = true;
                break;
            }
        }
        return ($y1 - $y) - $incY;
    }

    private function convertToIso($text)
    {
        return mb_convert_encoding($text, 'ISO-8859-1', ['UTF-8', 'windows-1252']);
    }
}
