# PDF View

Extension for Kohana a PDF instead of HTML. Uses [MPDF](http://www.mpdf1.com/mpdf/) to render normal HTML views as PDF Files.

Forked to fix for use View rendered templates with Kohana Smarty module.
Now improoved with new features, like reading from string, merging PDF, etc.

mPDF version 5.7.1a !

## Installation

Use Composer!

To install from packagist [link](https://packagist.org/packages/seyfer/kohana-mpdf):

```
"seyfer/kohana-mpdf": "dev-master"
```

To install from Git:

    {
        "type": "package",
        "package": {
            "name": "kohana/modules/mpdf",
            "version": "3.3",
            "source": {
                "type": "git",
                "url": "https://github.com/seyfer/kohana-mpdf.git",
                "reference": "3.3/master"
            }
        }
    }

After installation go to module folder and execute

    composer install

This will load mPDF to vendor dir. After that add to your app `index.php` at the beginning

```
require vendor/autoload.php
```

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

    //Set data for wiew later
    $mpdf->setData($data);
    //Or directly to PDF
    $mpdf->setDataToPdf($data);

    // Use CSS
    $mpdf->setCss('media/css/style.css');
    // And again. This is array.
    $mpdf->setCss('media/css/style2.css');
    //Or set array of CSS path directly to PDF
    $mpdf->setCssToPdf($array);

    //Render pdf with your html template and css
    $mpdf->render();

    //Check output with different output mode (see MPDF documentation).
    $mpdf->output('mpdf.pdf', 'S');

Extended usage

    //You can set some options
    $mpdf->setCharset($charset);
    $mpdf->setSourceFile($filePath);
    $mpdf->setImportUse();
    $mpdf->setFormat('A4');

    //Save to tmp path
    $mpdf->saveTpmPdfFile($data, $name);
    //You can load PDF file in one command from tmp path
    $mpdf->loadPdfFile($fileName);

    //And finally! Parse PDF from string, not file
    $mpdf->parsePdfString($pdfBinData);

    //Merge different PDF to one
    $mpdf = new Kohana_MPDF();
    $mpdf->mergePdf(1.pdf);
    $mpdf->mergePdf(2.pdf);
    $mpdf->output();

You can call any mPDF methods.

For all mPDF methods see http://www.mpdf1.com/ documentation.
