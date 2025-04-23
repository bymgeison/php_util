class TGx4
{
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
}
