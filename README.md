# PDF View

Extension for Kohana's View class that renders as a PDF instead of HTML. Uses [MPDF](http://www.mpdf1.com/mpdf/) to render normal HTML views as PDF Files.

Forked to fix for use View rendered templates with Kohana Smarty module.

mPDF version 5.6 !

## Installation

If your application is a Git repository:

    git submodule add git://github.com/seyfer/mpdf.git modules/mpdf
    git submodule update --init

Or clone the the module separately:

    cd modules
    git clone git://github.com/seyfer/mpdf.git mpdf

### Update DOMPDF

    cd modules/mpdf
    git submodule update --init

### Configuration

Edit `application/bootstrap.php` and add a the module:

    Kohana::modules(array(
        ...
        'mpdf' => 'modules/mpdf',
        ...
    ));

## Usage

Placed in a controller action:

    // Load a view using the PDF extension
    $mpdf = Kohana_MPDF::factory('pdf/example');

    //Or use it with some data and with Smarty
    $mpdf = Kohana_MPDF::factory("pdf/example.tpl", array("data" => $data));

    // Use CSS
    $mpdf->set_css('media/css/style.css');
    // And again. This is array.
    $mpdf->set_css('media/css/style2.css');

    //Render pdf with your html template and css
    $mpdf->render();

    //Check output with different output mode (see MPDF documentation).
    $mpdf->output('mpdf.pdf', 'S');
