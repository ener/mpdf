<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Render a view as a PDF.
 *
 * @author     Woody Gilk <woody.gilk@kohanaphp.com>
 * @copyright  (c) 2009 Woody Gilk
 * @license    MIT
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
     * @return \View_MPDF
     */
    public static function factory($file = NULL, array $data = NULL)
    {
        return new Kohana_MPDF($file, $data);
    }

    public function render($file = NULL)
    {
        $file = $file ? $file : $this->file;

        // Render the HTML normally
        // Using View::factory for compability with Smarty and other modules.
        $html = View::factory($file);

        foreach ($this->data as $name => $value)
            $html->set($name, $value);

        $rhtml = $html->render();

//        Debug::vars($rhtml);

        $charset           = $this->charset ? $this->charset : Kohana::$charset;
        $format            = $this->format ? $this->format : 'A4';
        $default_font_size = $this->default_font_size ? $this->default_font_size : "8";

        //Create
        $this->mpdf = new mPDF($charset, $format, $default_font_size, 10, 10, 7, 7, 10, 10);

        //Set css
        if (!empty($this->css))
            foreach ($this->css as $css)
            {
                $stylesheet = File::getFromFile($css);
//                Debug::vars($stylesheet, $css);
                $this->mpdf->WriteHTML($stylesheet, 1);
            }

        $this->mpdf->SetAutoFont(AUTOFONT_ALL);
        $this->mpdf->list_indent_first_level = 0;

        // Render the HTML to a PDF
        $this->mpdf->WriteHTML($rhtml, 2);

        return $this->mpdf;
    }

    //Delegate to mpdf
    public function output($name = '', $dest = '')
    {
        return $this->mpdf->output($name, $dest);
    }

}

// End View_PDF
// Load DOMPDF configuration, this will prepare DOMPDF
require_once MODPATH . 'mpdf/vendor/mpdf.php';
?>