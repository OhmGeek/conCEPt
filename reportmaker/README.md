# PDF Generator
In order to generate PDFs, you need to have a couple of things set up.

Firstly, the server must have wkHTMLtoPDF installed (as a binary file).
This can be installed from the operating system package manager on the server.

On Debian/Ubuntu:

```bash
sudo apt-get install wkhtmltopdf
```

If you haven't already downloaded the PHP dependencies, use the Composer package manager and run composer install to download all the necessary dependencies.


Now, in the generate_pdf.php file, set the 'binary' option to be the path to the binary
installed. This is particularly true if the server has been set up in a different way.

Now, modify the PDFModel in conCEPt/models so that it points to the URL of the PDF generator route. This will be in the file PDFModel.php

```php
public function getPDF($html_input)
 {
     $postdata = http_build_query(array('html' => $html_input));
     $url = 'http://path/to/generate_pdf.php';
     $options = array(

    ...
```
