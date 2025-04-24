<?php

namespace TGX4\Util;

use Exception;

class TGx4
{
    // Constantes para os dias da semana (agora o domingo é 0)
    const DOMINGO = 0;
    const SEGUNDA = 1;
    const TERCA   = 2;
    const QUARTA  = 3;
    const QUINTA  = 4;
    const SEXTA   = 5;
    const SABADO  = 6;

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

    /**
     * Valida um CPF ou CNPJ
     *
     * @param string $valor
     * @return bool
     * @throws Exception
     */
    public static function validaDocumento(string $valor): bool
    {
        // Remove tudo que não for número
        $numero = preg_replace('/\D/', '', $valor);

        // Verifica se é CPF (11 dígitos)
        if (strlen($numero) === 11) {
            if (preg_match('/^(\d)\1{10}$/', $numero)) {
                throw new Exception("CPF inválido: repetição de dígitos.");
            }

            for ($t = 9; $t < 11; $t++) {
                $soma = 0;
                for ($i = 0; $i < $t; $i++) {
                    $soma += $numero[$i] * (($t + 1) - $i);
                }

                $digitoEsperado = ($soma * 10) % 11;
                $digitoEsperado = ($digitoEsperado === 10) ? 0 : $digitoEsperado;

                if ($numero[$t] != $digitoEsperado) {
                    throw new Exception("CPF inválido: dígito verificador incorreto.");
                }
            }

            return true;

        } elseif (strlen($numero) === 14) { // CNPJ

            if (preg_match('/^(\d)\1{13}$/', $numero)) {
                throw new Exception("CNPJ inválido: repetição de dígitos.");
            }

            $peso1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
            $peso2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

            $soma1 = 0;
            $soma2 = 0;

            for ($i = 0; $i < 12; $i++) {
                $soma1 += $numero[$i] * $peso1[$i];
            }

            $digito1 = ($soma1 % 11) < 2 ? 0 : 11 - ($soma1 % 11);

            for ($i = 0; $i < 13; $i++) {
                $soma2 += $numero[$i] * $peso2[$i];
            }

            $digito2 = ($soma2 % 11) < 2 ? 0 : 11 - ($soma2 % 11);

            if ($numero[12] != $digito1 || $numero[13] != $digito2) {
                throw new Exception("CNPJ inválido: dígito verificador incorreto.");
            }

            return true;

        } else {
            throw new Exception("Documento inválido: tamanho incorreto.");
        }
    }

    /**
     * Formata um CPF ou CNPJ automaticamente
     *
     * @param string $valor
     * @return string
     * @throws Exception
     */
    public static function formataDocumento(string $valor): string
    {
        $numero = preg_replace('/\D/', '', $valor);

        if (strlen($numero) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $numero);
        }

        if (strlen($numero) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $numero);
        }

        throw new Exception("Documento inválido para formatação.");
    }

    /**
     * Retorna um array contendo o número e a descrição do dia da semana.
     *
     * @param int $semana Número do dia da semana (0 a 6), onde 0 é domingo.
     * @return array Array com as chaves 'numero' e 'descricao'.
     * @throws Exception Se o número do dia não estiver entre 0 e 6.
     */
    public static function semana(int $semana): array
    {
        $dias = [
            self::DOMINGO => ['numero' => self::DOMINGO, 'descricao' => 'Domingo'],
            self::SEGUNDA => ['numero' => self::SEGUNDA, 'descricao' => 'Segunda-feira'],
            self::TERCA   => ['numero' => self::TERCA, 'descricao' => 'Terça-feira'],
            self::QUARTA  => ['numero' => self::QUARTA, 'descricao' => 'Quarta-feira'],
            self::QUINTA  => ['numero' => self::QUINTA, 'descricao' => 'Quinta-feira'],
            self::SEXTA   => ['numero' => self::SEXTA, 'descricao' => 'Sexta-feira'],
            self::SABADO  => ['numero' => self::SABADO, 'descricao' => 'Sábado'],
        ];

        if (!array_key_exists($semana, $dias)) {
            throw new Exception("Dia da semana inválido! Deve ser um número entre 0 e 6.");
        }

        return $dias[$semana];
    }

    /**
     * Normaliza um texto removendo acentos e caracteres especiais.
     *
     * @param string $valor Texto a ser normalizado.
     * @param bool $maiusculas Define se o texto retornado deve ser em maiúsculas (true) ou minúsculas (false).
     * @return string Texto limpo, com ou sem caixa alta.
     */
    public static function normalizaTexto(string $valor, bool $maiusculas = true): string
    {
        $mapa = [
            "à" => "a", "á" => "a", "ã" => "a", "â" => "a", "ä" => "a",
            "è" => "e", "é" => "e", "ê" => "e", "ë" => "e",
            "ì" => "i", "í" => "i", "î" => "i", "ï" => "i",
            "ò" => "o", "ó" => "o", "õ" => "o", "ô" => "o", "ö" => "o",
            "ù" => "u", "ú" => "u", "û" => "u", "ü" => "u",
            "ç" => "c",

            "À" => "a", "Á" => "a", "Ã" => "a", "Â" => "a", "Ä" => "a",
            "È" => "e", "É" => "e", "Ê" => "e", "Ë" => "e",
            "Ì" => "i", "Í" => "i", "Î" => "i", "Ï" => "i",
            "Ò" => "o", "Ó" => "o", "Õ" => "o", "Ô" => "o", "Ö" => "o",
            "Ù" => "u", "Ú" => "u", "Û" => "u", "Ü" => "u",
            "Ç" => "c",

            "º" => "", "#" => "", "&" => "e", '"' => "", "'" => "",
            "´" => "", "`" => "", "¨" => "", "*" => "", "|" => "",
            ";" => "", "?" => "", "½" => "", "¿" => "", "ª" => "",
            "-" => "", "(" => "", ")" => "", "\\" => "", "/" => "",
            ":" => "", "<" => "", ">" => "", "$" => "",
        ];

        $texto = strtr($valor, $mapa);
        $texto = trim($texto);
        return $maiusculas ? strtoupper($texto) : strtolower($texto);
    }

    /**
     * Aplica uma máscara ao valor informado, utilizando o caractere "#" como posição a ser preenchida.
     *
     * @param string $mask Máscara desejada (use "#" como marcador de posição).
     * @param string $value Valor que será aplicado na máscara.
     * @return string Valor formatado com a máscara.
     */
    public static function applyMask(string $mask, string $value): string
    {
        if (empty($value)) {
            return $value;
        }

        $value = preg_replace('/\s+/', '', $value);
        $result = '';
        $index = 0;

        for ($i = 0; $i < strlen($mask); $i++) {
            if ($mask[$i] === '#' && isset($value[$index])) {
                $result .= $value[$index++];
            } else {
                $result .= $mask[$i];
            }
        }

        return $result;
    }

    /**
     * Gera uma senha aleatória com os caracteres definidos.
     *
     * @param int $length Tamanho desejado da senha.
     * @return string Senha gerada aleatoriamente.
     * @throws InvalidArgumentException Se o tamanho for menor que 1.
     */
    public static function generatePassword(int $length): string
    {
        if ($length < 1) {
            throw new InvalidArgumentException("O tamanho da senha deve ser maior que 0.");
        }

        $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ$!@&*$(){}';
        $characters = str_split($keyspace);
        shuffle($characters);
        return substr(implode('', $characters), 0, $length);
    }

    /**
     * Remove qualquer máscara de um valor, deixando apenas letras e números.
     *
     * @param string $value Valor com máscara.
     * @return string Valor limpo, sem símbolos ou espaços.
     */
    public static function removeMask(string $value): string
    {
        return preg_replace('/[^a-z\d]+/i', '', $value);
    }

    /**
     * Preenche uma string multibyte até o tamanho desejado, respeitando o alinhamento.
     *
     * @param string $str String a ser preenchida.
     * @param int $len Comprimento final desejado.
     * @param string $pad Caracter(es) de preenchimento.
     * @param int $align Tipo de alinhamento (STR_PAD_LEFT, STR_PAD_RIGHT, STR_PAD_BOTH).
     * @return string String formatada com preenchimento.
     */
    public static function mbStrPad(string $str, int $len, string $pad, int $align = STR_PAD_RIGHT): string
    {
        $strLen = mb_strlen($str);
        if ($strLen >= $len) {
            return $str;
        }

        $diff = $len - $strLen;
        $padding = mb_substr(str_repeat($pad, $diff), 0, $diff);

        switch ($align) {
            case STR_PAD_BOTH:
                $diffHalf = (int)($diff / 2 + 0.5);
                $leftPad = mb_substr(str_repeat($pad, $diffHalf), 0, $diffHalf);
                $rightPad = mb_substr(str_repeat($pad, $diff - $diffHalf), 0, $diff - $diffHalf);
                return $leftPad . $str . $rightPad;

            case STR_PAD_LEFT:
                return $padding . $str;

            case STR_PAD_RIGHT:
            default:
                return $str . $padding;
        }
    }

      /**
     * Grava um array associativo em um arquivo .ini.
     *
     * @param array $data Array associativo a ser salvo.
     * @param string $file Caminho completo do arquivo a ser salvo.
     * @param bool $hasSections Define se o array possui seções.
     * @return bool Retorna true em caso de sucesso, false caso contrário.
     */
    public static function saveIniFile(array $data, string $file, bool $hasSections = false): bool
    {
        $content = '';

        if ($hasSections) {
            foreach ($data as $section => $values) {
                $content .= "[{$section}]\n";
                foreach ($values as $key => $value) {
                    $content .= self::formatIniValue($key, $value);
                }
            }
        } else {
            foreach ($data as $key => $value) {
                $content .= self::formatIniValue($key, $value);
            }
        }

        return file_put_contents($file, $content) !== false;
    }

    /**
     * Formata um valor para o conteúdo do arquivo INI.
     *
     * @param string $key Chave do parâmetro.
     * @param mixed $value Valor associado.
     * @return string Linha formatada.
     */
    private static function formatIniValue(string $key, $value): string
    {
        $output = '';
        if (is_array($value)) {
            foreach ($value as $v) {
                $output .= "{$key}[] = \"" . addslashes($v) . "\"\n";
            }
        } elseif ($value === '') {
            $output .= "{$key} = \n";
        } else {
            $output .= "{$key} = \"" . addslashes($value) . "\"\n";
        }

        return $output;
    }
}
