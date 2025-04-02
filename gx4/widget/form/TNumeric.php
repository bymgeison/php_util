<?php
namespace GX4\Widget\Form;
class TNumeric extends \GX4\Widget\Form\TEntry
{
    public function __construct($name, $decimals, $decimalsSeparator, $thousandSeparator, $replaceOnPost = true, $reverse = FALSE, $allowNegative = TRUE)
    {
        parent::__construct($name);
        parent::setNumericMask($decimals, $decimalsSeparator, $thousandSeparator, $replaceOnPost, $reverse, $allowNegative);
    }

     /**
     * Define input allow negative
     */
    public function setAllowNegative($allowNegative)
    {
        $this->allowNegative = $allowNegative;
    }
}