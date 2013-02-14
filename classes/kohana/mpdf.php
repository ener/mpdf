<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Kohana wrapper on MPDF.
 *
 * @author     Seyfer <seyferseed@mail.ru>
 * @copyright  (c) 2012 Seyfer (Oleg Abrazhaev)
 * @license    GPL
 */
class Kohana_MPDF {

    private $mpdf;
    private $data;
    private $file;
    private $css = array();
    private $charset;
    private $format;
    private $default_font_size;

    public function set_default_font_size($default_font_size)
    {
        $this->default_font_size = $default_font_size;
    }

    public function set_charset($charset)
    {
        $this->charset = $charset;
    }

    public function set_format($format)
    {
        $this->format = $format;
    }

    public function set_css($css)
    {
        $this->css[] = $css;
    }

    public function __construct($file = NULL, $data = NULL)
    {
        $this->data = $data;
        $this->file = $file;
    }

    /**
     * Create instance
     * @param type $file - x.php, x.tpl if Smarty enabled
     * @param array $data - array(name, value)
     * @return \Kohana_MPDF
     */
    public static function factory($file = NULL, array $data = NULL)
    {
        return new Kohana_MPDF($file, $data);
    }

    /**
     * Render HTML with tpl
     * @param type $file - tpl
     * @return \mPDF
     */
    public function render($file = NULL)
    {
        $file = $file ? $file : $this->file;

        // Render the HTML normally
        // Using View::factory for compability with Smarty and other modules.
        $html = View::factory($file);

        foreach ($this->data as $name => $value)
            $html->set($name, $value);

        $rhtml = $html->render();

        $charset           = $this->charset ? $this->charset : Kohana::$charset;
        $format            = $this->format ? $this->format : 'A4';
        $default_font_size = $this->default_font_size ? $this->default_font_size : "8";

        //Create
        $this->mpdf = new mPDF($charset, $format, $default_font_size, 10, 10, 7, 7, 10, 10);

        //Set css
        if (!empty($this->css))
            foreach ($this->css as $css)
            {
                $stylesheet = file_get_contents($css);

                $this->mpdf->WriteHTML($stylesheet, 1);
            }

        //default preferences
        $this->mpdf->SetAutoFont(AUTOFONT_ALL);
        $this->mpdf->list_indent_first_level = 0;

        // Render the HTML to a PDF
        $this->mpdf->WriteHTML($rhtml, 2);

        return $this->mpdf;
    }

    /**
     * Delegate to mpdf
     */
    public function output($name = '', $dest = '')
    {
        return $this->mpdf->output($name, $dest);
    }

    /**
     * Call mpdf method if needed
     * @param type $name
     * @param type $arguments
     * @return type
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this->mpdf, $name))
        {
            return call_user_func_array(array($this->mpdf, $name), $arguments);
        }
    }

}

// End Kohana_MPDF
// Load mPDF configuration, this will prepare mPDF
require_once MODPATH . 'mpdf/vendor/mpdf.php';
?>