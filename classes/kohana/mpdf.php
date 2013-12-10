<?php

defined('SYSPATH') or die('No direct script access.');

define('_MPDF_PATH', MODPATH . 'mpdf/vendor/mPDF/');

// Load mPDF configuration, this will prepare mPDF
require_once _MPDF_PATH . 'mpdf.php';

/**
 * Kohana wrapper on MPDF.
 *
 * @author     Seyfer <seyferseed@mail.ru>
 * @copyright  (c) 2012 Seyfer (Oleg Abrazhaev)
 * @license    GPL
 */
class Kohana_MPDF {

    const WRITE_DEFAULT      = 0;
    const WRITE_CSS          = 1;
    const WRITE_HTML_BODY    = 2;
    const WRITE_HTML_PARSES  = 3;
    const WRITE_HTML_HEADERS = 4;

    /**
     *
     * @var \mPDF
     */
    private $mpdf;

    /**
     * data for tpl filling
     * @var type
     */
    private $data;

    /**
     * array of css path's
     * @var array
     */
    private $css = array();

    /**
     * charset for pdf
     * @var type
     */
    private $charset;

    /**
     * format (A4, etc)
     * @var type
     */
    private $format;

    /**
     *
     * @var \Kohana_View
     */
    private $view;

    /**
     * font size in px
     * @var type
     */
    private $defaultFontSize;

    /**
     * tpl path
     * @var type
     */
    private $tplPath;

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function setFilePath($tplPath)
    {
        $this->tplPath = $tplPath;
        return $this;
    }

    public function setDefaultFontSize($defaultFontSize)
    {
        $this->defaultFontSize = $defaultFontSize;
        return $this;
    }

    public function setCharset($charset)
    {
        $this->charset = $charset;
        return $this;
    }

    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * add one css path to array
     * @param type $css
     */
    public function setCss($css)
    {
        $this->css[] = $css;
        return $this;
    }

    public function getMpdf()
    {
        return $this->mpdf;
    }

    public function __construct($file = NULL, $data = NULL)
    {
        $this->data    = $data;
        $this->tplPath = $file;
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
     * init instance of mPDF wit settings
     */
    public function initMpdf()
    {
        $charset         = $this->charset ? $this->charset : Kohana::$charset;
        $format          = $this->format ? $this->format : 'A4';
        $defaultFontSize = $this->defaultFontSize ? $this->defaultFontSize : "8";

        //Create
        $this->mpdf = new mPDF($charset, $format, $defaultFontSize, 10, 10, 7, 7, 10, 10);

        return $this;
    }

    /**
     * init view for html render
     * @param type $tplPath
     * @return \Kohana_MPDF
     * @throws Exception
     */
    public function initView($tplPath = NULL)
    {
        if ($tplPath) {
            $this->setFilePath($tplPath);
        }

        if (!$this->tplPath) {
            throw new Exception(__METHOD__ . " set tpl path first");
        }

        $this->view = View::factory($this->tplPath);

        return $this;
    }

    /**
     * set data to view for rendering
     * @param type $data
     */
    public function setDataToView($data = array())
    {
        if ($data) {
            $this->setData($data);
        }

        if (is_array($this->data) && !empty($this->data)) {
            foreach ($this->data as $name => $value) {
                $this->view->set($name, $value);
            }
        }
        else if (!empty($this->data)) {
            $this->view->set("data", $this->data);
        }
    }

    /**
     * render current view
     * @return type
     * @throws Exception
     */
    public function renderView()
    {
        if (!$this->view) {
            throw new Exception(__METHOD__ . " set view first");
        }

        $rhtml = $this->view->render();

        return $rhtml;
    }

    /**
     * set html to pdf
     * @param type $rhtml
     * @return \Kohana_MPDF
     */
    public function setDataToPdf($rhtml)
    {
        //default preferences
        $this->mpdf->SetAutoFont(AUTOFONT_ALL);
        $this->mpdf->list_indent_first_level = 0;

        // Render the HTML to a PDF
        $this->mpdf->WriteHTML($rhtml, Kohana_MPDF::WRITE_HTML_BODY);

        return $this;
    }

    /**
     * Render HTML with tpl
     * @param type $file - tpl
     * @return \Kohana_MPDF
     */
    public function render($tplPath = NULL)
    {
        if ($tplPath) {
            $this->setFilePath($tplPath);
        }

        // Render the HTML normally
        // Using View::factory for compability with Smarty and other modules.
        $this->initView();

        $this->setDataToView();

        $rhtml = $this->renderView();

        if (!$this->mpdf) {
            $this->initMpdf();
        }

        //Set css
        $this->setCssToPdf();

        // Render the HTML to a PDF
        $this->setDataToPdf($rhtml);

        return $this;
    }

    /**
     * set array of css path to pdf
     * if exist
     */
    public function setCssToPdf($css = null)
    {
        if ($css && is_array($css)) {
            $this->css = $css;
        }

        if (!empty($this->css) && is_array($this->css)) {
            foreach ($this->css as $css) {
                $stylesheet = file_get_contents($css);

                $this->mpdf->WriteHTML($stylesheet, Kohana_MPDF::WRITE_CSS);
            }
        }

        return $this;
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
        if (method_exists($this->mpdf, $name)) {
            return call_user_func_array(array($this->mpdf, $name), $arguments);
        }
    }

}

// End Kohana_MPDF

