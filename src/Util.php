<?php

if(!function_exists('_limpar'))
{
    function _limpar($var)
	{
		$var = strtr(strtoupper($var), array(
			"." => NULL,
			"-" => NULL,
			"/" => NULL,
			")" => NULL,
			" " => NULL,
			"(" => NULL
		));
		$var = trim($var);
		return $var;
	}
}

if(!function_exists('_echo'))
{
    function _echo(...$texto)
	{
        foreach($texto as $value)
        {
            echo '<pre>';
            var_dump($value);
            echo '</pre>';
        }
	}
}

if(!function_exists('_trata'))
{
    function _trata($var)
	{
		$var = strtr(strtoupper($var), array(
			"à" => "A",
			"À" => "A",
			"è" => "E",
			"È" => "E",
			"ì" => "I",
			"Ì" => "I",
			"ò" => "O",
			"Ò" => "O",
			"ù" => "U",
			"Ù" => "U",
			"á" => "A",
			"Á" => "A",
			"ã" => "A",
			"Ã" => "A",
			"é" => "E",
			"É" => "E",
			"í" => "I",
			"Í" => "I",
			"ó" => "O",
			"ó" => "O",
			"ú" => "U",
			"Ú" => "U",
			"â" => "A",
			"Â" => "A",
			"ê" => "E",
			"Ê" => "E",
			"î" => "I",
			"Î" => "I",
			"ô" => "O",
			"Ô" => "O",
			"û" => "U",
			"Û" => "U",
			"Ç" => "C",
			"ç" => "C",
			"º" => NULL,
			"#" => NULL,
			"&" => "E",
			'"' => NULL,
			"'" => NULL,
			"´" => NULL,
			"`" => NULL,
			"¨" => NULL,
			"*" => NULL,
			"|" => NULL,
			";" => NULL,
			"&" => NULL,
			"?" => NULL,
			"½" => NULL,
			"¿" => NULL,
			"Ï" => NULL,
			"ª" => NULL,
			"-" => NULL,
			"(" => NULL,
			")" => NULL,
			"\\" => NULL,
			"/" => NULL,
			":" => NULL,
			'"' => NULL,
			'<' => NULL,
			'>' => NULL,
			//  "." => NULL,
			"$" => NULL,
		));
		$var = trim($var);
		return $var;
	}
}

if(!function_exists('_busca_cep'))
{
    function _busca_cep($cep)
	{
		$cep = preg_replace('/[^0-9]/', '', $cep);
		$url = 'https://viacep.com.br/ws/' . $cep . '/json/unicode/';
		$content = @file_get_contents($url);
		$cep_data = json_decode($content);
		return $cep_data;
	}
}

if(!function_exists('_mask'))
{
    function _mask($mask, $str)
	{
		if (empty($str)) {
			return $str;
		}

		$str = str_replace(" ", "", $str);

		for ($i = 0; $i < strlen($str); $i++) {
			$mask[strpos($mask, "#")] = $str[$i];
		}

		return $mask;
	}
}

if(!function_exists('_mask_telefone_celular'))
{
    function _mask_telefone_celular($numero)
	{

		$tam = strlen(preg_replace("/[^0-9]/", "", $numero));

		if ($tam == 13) { // COM CÓDIGO DE ÁREA NACIONAL E DO PAIS e 9 dígitos
			return "+".substr($numero,0,$tam-11)."(".substr($numero,$tam-11,2).") ".substr($numero,$tam-9,5)."-".substr($numero,-4);
		}
		if ($tam == 12) { // COM CÓDIGO DE ÁREA NACIONAL E DO PAIS
			return "+".substr($numero,0,$tam-10)."(".substr($numero,$tam-10,2).") ".substr($numero,$tam-8,4)."-".substr($numero,-4);
		}
		if ($tam == 11) { // COM CÓDIGO DE ÁREA NACIONAL e 9 dígitos
			return "(".substr($numero,0,2).") ".substr($numero,2,5)."-".substr($numero,7,11);
		}
		if ($tam == 10) { // COM CÓDIGO DE ÁREA NACIONAL
			return "(".substr($numero,0,2).") ".substr($numero,2,4)."-".substr($numero,6,10);
		}
		if ($tam <= 9) { // SEM CÓDIGO DE ÁREA
			return substr($numero,0,$tam-4)."-".substr($numero,-4);
		}
	}
}

if(!function_exists('_formata_numero'))
{
    function _formata_numero($valor, $moeda = false, $casas_decimais = 2)
	{
		if ($moeda)
		{
			return 'R$ '.number_format($valor, $casas_decimais, ",", ".");
		}
		else
		{
			return number_format($valor, $casas_decimais, ",", ".");
		}
	}
}

if(!function_exists('_formata_CPF_CNPJ'))
{
    function _formata_CPF_CNPJ($value)
	{
		$cnpj_cpf = preg_replace("/\D/", '', $value);

		if (strlen($cnpj_cpf) === 11) {
			return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
		}

		return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
	}
}

if(!function_exists('_gerar_senha'))
{
    function _gerar_senha($tamanho)
	{
	    $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ$!@&*$(){}';
        return substr(str_shuffle($keyspace), 0, $tamanho);
	}
}

if(!function_exists('_valida_CPF'))
{
    function _valida_CPF($value) {
		// Retira todos os caracteres que nao sejam 0-9
		$cpf = preg_replace("/[^0-9]/", "", $value);
		if ($cpf != '') {
			// cpfs inválidos
			$nulos = array("12345678909", "11111111111", "22222222222", "33333333333",
				"44444444444", "55555555555", "66666666666", "77777777777",
				"88888888888", "99999999999", "00000000000");

			if (strlen($cpf) != 11) {
				throw new Exception("CPF inválido!");
			}

			// Retorna falso se houver letras no cpf
			if (!(preg_match("/[0-9]/", $cpf))) {
				throw new Exception("CPF inválido!");
			}

			// Retorna falso se o cpf for nulo
			if (in_array($cpf, $nulos)) {
				throw new Exception("CPF inválido!");
			}

			// Calcula o penúltimo dígito verificador
			$acum = 0;
			for ($i = 0; $i < 9; $i++) {
				$acum += $cpf[$i] * (10 - $i);
			}

			$x = $acum % 11;
			$acum = ($x > 1) ? (11 - $x) : 0;
			// Retorna falso se o digito calculado eh diferente do passado na string
			if ($acum != $cpf[9]) {
				throw new Exception("CPF inválido!");
			}
			// Calcula o último dígito verificador
			$acum = 0;
			for ($i = 0; $i < 10; $i++) {
				$acum += $cpf[$i] * (11 - $i);
			}

			$x = $acum % 11;
			$acum = ($x > 1) ? (11 - $x) : 0;
			// Retorna falso se o digito calculado eh diferente do passado na string
			if ($acum != $cpf[10]) {
				throw new Exception("CPF inválido!");
			}
		}
    }
}

if(!function_exists('_valida_CNPJ'))
{
    function _valida_CNPJ($value) {
        $cnpj = preg_replace("@[./-]@", "", $value);
        if ($cnpj != '') {
            if (strlen($cnpj) != 14 or!is_numeric($cnpj)) {
                throw new Exception("CNPJ inválido!");
            }
            $k = 6;
            $soma1 = 0;
            $soma2 = 0;
            for ($i = 0; $i < 13; $i++) {
                $k = $k == 1 ? 9 : $k;
                $soma2 += (substr($cnpj, $i, 1) * $k);
                $k--;
                if ($i < 12) {
                    if ($k == 1) {
                        $k = 9;
                        $soma1 += (substr($cnpj, $i, 1) * $k);
                        $k = 1;
                    } else {
                        $soma1 += (substr($cnpj, $i, 1) * $k);
                    }
                }
            }

            $digito1 = $soma1 % 11 < 2 ? 0 : 11 - $soma1 % 11;
            $digito2 = $soma2 % 11 < 2 ? 0 : 11 - $soma2 % 11;

            $valid = (substr($cnpj, 12, 1) == $digito1 and substr($cnpj, 13, 1) == $digito2);

            if (!$valid) {
                throw new Exception("CNPJ inválido!");
            }
        }
    }
}

if(!function_exists('_remove_mask_numeric'))
{
    function _remove_mask_numeric($value)
	{
		$value = str_replace( '.', '',  $value);
		$value = str_replace( ',', '.', $value);
		$value = str_replace( 'R$', '', $value);
		$value = str_replace( ' ', '',  $value);

		return $value;
	}
}

if(!function_exists('_remove_mask'))
{
    function _remove_mask($value)
	{
		return preg_replace('/[^a-z\d]+/i', '', $value);
	}
}

if(!function_exists('_set_db_date'))
{
    function _set_db_date($data) // $data = string
	{
		return implode('-', array_reverse(explode('/', $data))); // format return = 'yyy-mm-dd'
	}
}

if(!function_exists('_set_format_date'))
{
    function _set_format_date($data) // $data = string
	{
		if(empty($data)){
            return '';
        }

        $dt = new DateTime($data);
        return $dt->format('d/m/Y');
	}
}

if(!function_exists('_set_format_date_time'))
{
    function _set_format_date_time($data, $mostrar_segundo = true) // $data = string
	{
		if(empty($data)){
            return '';
        }

        $dt = new DateTime($data);
        if($mostrar_segundo){
            return $dt->format('d/m/Y H:i:s');
        }
        else
        {
            return $dt->format('d/m/Y H:i');
        }
	}
}

if(!function_exists('_get_dia_semana_extenso'))
{
    function _get_dia_semana_extenso($semana) // $semana = int
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
}

if(!function_exists('_on_rename_file'))
{
    function _on_rename_file ($param = null)
	{
		chmod($param['oldname'], 777);
		rename($param['oldname'], $param['newname']);
		chmod($param['newname'], 777);
    }
}

if(!function_exists('_on_delete_file'))
{
    function _on_delete_file ($param = NULL)
    {
        if (file_exists($param['filelocation']))
        {
            unlink($param['filelocation']);
        }
    }
}

// dump
function d(...$args)
{
    $bt = debug_backtrace();
    while(count($bt) and strpos($bt[0]['file'], __FILE__) !== false) array_shift($bt);
    $title = ['d' => 'DUMP', 'dd' => 'DUMP AND DIE'][$bt[0]['function']] . " {$bt[0]['file']}:{$bt[0]['line']}";
    ob_start();
    foreach($args as $arg) var_dump($arg);
    $win = TWindow::create($title, 0.9999, 0.9999);
    $win->add(preg_replace(['/###:\d+:/', '/<small><\/small>\s*/'], '', str_replace(__FILE__, '###', ob_get_clean())));
    $win->show();
}

// dump e die
function dd(...$args) {
    d(...$args);
    die();
}

// print
function p(...$args)
{
    $bt = debug_backtrace();
    while(count($bt) and strpos($bt[0]['file'], __FILE__) !== false) array_shift($bt);
    $title = ['p' => 'PRINT', 'pp' => 'PRINT AND PAUSE'][$bt[0]['function']] . " {$bt[0]['file']}:{$bt[0]['line']}";
    ob_start();
    foreach($args as $arg) echo '<pre>'.print_r(key_pad($arg), 1).'</pre>';
    $win = TWindow::create($title, 0.9999, 0.9999);
    $win->add(ob_get_clean());
    $win->show();
}

// print e pause
function pp(...$args) {
    p(...$args);
    die();
}

// alinhar chaves de array à direita
function key_pad($arg)
{
    if (is_string($arg) and mb_strpos($arg, '@') === 0) return '<strong>'.ltrim($arg,'@').'</strong>';
    else if (is_array($arg)) {
        $max = 0;
        $pad = [];
        foreach($arg as $i => $v) $max = max(mb_strlen($i), $max);
        foreach($arg as $i => $v) $pad[mb_str_pad($i, $max, " ", STR_PAD_LEFT)] = key_pad($v);
        return $pad;
    }
    return $arg;
}

// PHP < 8.3.0
if (!function_exists('mb_str_pad')) {
    function mb_str_pad(string $str, int $len, string $pad, int $align = \STR_PAD_RIGHT): string
    {
       $strLen = \mb_strlen($str);
       if ($strLen >= $len) return $str;
       $diff = $len - $strLen;
       $padding = \mb_substr(\str_repeat($pad, $diff), 0, $diff);
       switch ($align) {
          case \STR_PAD_BOTH:
             $diffHalf = (int)($diff/2 + 0.5);
             $padding = \str_repeat($pad, $diffHalf);
             $result = "{$padding}{$str}{$padding}";
             break;
          case \STR_PAD_LEFT:
             $result = "{$padding}{$str}";
             break;
          case \STR_PAD_RIGHT:
          default:
             $result = "{$str}{$padding}";
             break;
       }
       return \mb_substr($result, 0, $len);
    }
}


if (!function_exists('write_ini_file')) {
	function write_ini_file($assoc_arr, $path, $has_sections = false) {
		$content = "";

		if ($has_sections) {
			foreach ($assoc_arr as $key => $elem) {
				$content .= "[" . $key . "]\n";
				foreach ($elem as $key2 => $elem2) {
					if (is_array($elem2)) {
						for ($i = 0; $i < count($elem2); $i++) {
							$content .= $key2 . "[] = \"" . $elem2[$i] . "\"\n";
						}
					} else if ($elem2 == "") {
						$content .= $key2 . " = \n";
					} else {
						$content .= $key2 . " = \"" . $elem2 . "\"\n";
					}
				}
			}
		} else {
			foreach ($assoc_arr as $key => $elem) {
				if (is_array($elem)) {
					for ($i = 0; $i < count($elem); $i++) {
						$content .= $key . "[] = \"" . $elem[$i] . "\"\n";
					}
				} else if ($elem == "") {
					$content .= $key . " = \n";
				} else {
					$content .= $key . " = \"" . $elem . "\"\n";
				}
			}
		}

		if (!$handle = fopen($path, 'w')) {
			return false;
		}

		if (!fwrite($handle, $content)) {
			return false;
		}

		fclose($handle);
		return true;
	}
}