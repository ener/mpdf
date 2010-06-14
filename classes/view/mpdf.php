<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Render a view as a PDF.
 *
 * @author     Woody Gilk <woody.gilk@kohanaphp.com>
 * @copyright  (c) 2009 Woody Gilk
 * @license    MIT
 */
class View_MPDF extends View {

	public static function factory($file = NULL, array $data = NULL)
	{
		return new View_MPDF($file, $data);
	}

	public function render($file = NULL)
	{
		// Render the HTML normally
		$html = parent::render($file);

		// Render the HTML to a PDF

                $mpdf=new mPDF('UTF-8', 'A4');

                $mpdf->SetAutoFont(AUTOFONT_ALL);

                $mpdf->WriteHTML($html);

                return $mpdf->output();

	}

} // End View_PDF

// Load DOMPDF configuration, this will prepare DOMPDF
require_once MODPATH.'mpdf/vendor/mpdf.php';
?>