<?php namespace MX\Misc;

class CasesTransform
{
    /**
     * Internal string to transform
     *
     * @var string
     */
    protected $m_originalString = null;

    /**
     * Constructor
     */
    protected function __construct($string)
    {
        if (!is_string($string)) {
            throw new \Exception('A string is expected');
        }

        $this->m_originalString = $string;
    }

    /**
     * From
     *
     * Set the initial string to transform
     *
     * @param[in]   $string         String to transform
     * @return      \MX\Misc\Cases  Instance
     */
    public static function from($string)
    {
        $c = new CasesTransform($string);
        return ($c);
    }

    /**
     * toCamelCase
     *
     * @param[in]   $lcfirst    Is it lowerCamelCased?
     */
    public function toCamelCase($lcfirst=false)
    {
        $str = str_replace(' ', '', ucwords(str_replace('-', ' ', $this->m_originalString)));

        if ($lcfirst) {
            $str = lcfirst($str);
        }

        return ($str);
    }

    /**
     * toDashCase
     *
     * @param[in]   $ucwords=false    First letter of each words uppercased?
     */
    public function toDashCase($ucwords=false)
    {
        $str_spaced = $this->m_originalString[0].preg_replace('#([A-Z])#', ' $1', substr($this->m_originalString, 1));
        $str_arr = preg_split('#[ _\-\.\+\/]#', $str_spaced, null, PREG_SPLIT_NO_EMPTY);

        $str = implode(' ', $str_arr);
        if ($ucwords) {
            $str = ucwords($str);
        }

        return (str_replace(' ', '-', $str));
    }
}
