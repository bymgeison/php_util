<?php

namespace GX4\Service;

class TempoService
{
    public static function anoAtual()
    {
        return date('Y');
    }
    public static function mesAtual()
    {
        return date('m');
    }
    public static function diaAtual()
    {
        return date('d');
    }
    public static function semanaExtenso(int $semana)
	{
		$dias = [
			1 => 'Segunda-feira',
			2 => 'Terça-feira',
			3 => 'Quarta-feira',
			4 => 'Quinta-feira',
			5 => 'Sexta-feira',
			6 => 'Sábado',
			7 => 'Domingo',
		];

		return $dias[$semana]; // return  string
	}
    public static function meses()
    {
        return [
            '01'=>'Janeiro',
            '02'=>'Fevereiro',
            '03'=>'Março',
            '04'=>'Abril',
            '05'=>'Maio',
            '06'=>'Junho',
            '07'=>'Julho',
            '08'=>'Agosto',
            '09'=>'Setembro',
            '10'=>'Outubro',
            '11'=>'Novembro',
            '12'=>'Dezembro'
        ];
    }

    public static function anos(int $anosAntes, int $anosDepois = null)
    {
        $anoAtual = date('Y');
        $anoAtual -= $anosAntes;

        $anos = [];

        for($anoAtual; $anoAtual <= date('Y'); $anoAtual++)
        {
            $anos[$anoAtual] = $anoAtual;
        }

        for($anoAtual; $anoAtual <= date('Y') + is_null($anosDepois) ? $anosAntes : $anosDepois; $anoAtual++)
        {
            $anos[$anoAtual] = $anoAtual;
        }

        return $anos;
    }
}
