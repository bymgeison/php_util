# 🧩 Exemplo: Sobrescrevendo `default_values()` em uma classe filha

A classe `GX4\Database\TRecord` permite definir valores padrão automaticamente para novos registros.

Essa funcionalidade pode ser utilizada sobrescrevendo o método `default_values()` em qualquer classe filha. Veja abaixo um exemplo com a entidade `Cliente`.

```php
<?php
namespace App\Model;

use GX4\Database\TRecord;

class Cliente extends TRecord
{
    const TABLENAME  = 'clientes';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'serial';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);

        if (is_null($id)) {
            $this->default_values();
        }
    }

    /**
     * Define valores padrão para um novo cliente
     */
    public function default_values()
    {
        $this->ativo     = 'S';
        $this->estado    = 'RS';
        $this->data_cad  = date('Y-m-d');
    }
}
