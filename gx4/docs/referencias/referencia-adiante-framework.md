# 📘 Referência ao Adianti Framework

Este documento fornece uma introdução prática aos principais recursos do [Adianti Framework](https://adiantiframework.com.br/api?index=indices/files), com foco nos pontos mais utilizados em conjunto com o PHP Útil.

---

## 🧱 Principais Componentes

### 🔹 `TRecord`
Classe base para mapeamento objeto-relacional (ORM).

```php
class Cliente extends TRecord
{
    const TABLENAME = 'clientes';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // auto-incremento
}
