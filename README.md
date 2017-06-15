# conCEPt
[![codebeat badge](https://codebeat.co/badges/c02753d8-0610-451f-b668-b1b45ee33e75)](https://codebeat.co/a/ryan-collins/projects/concept-master)

This is the CEP project for Steve McGough. It's an Online Document repository for marking group project work.

## Project Structure
The actual live system shall be found inside the 'conCEPt' folder.

Inside this, there are further folders to put information. Model should contain any code that accesses the database, Controller should contain code that renders the views/contains any logic, and the view contains the twig files that we render. The public folder contains any static resources (such as CSS, JS or images), as well as the various PHP files that act as our routing.

## PDF Generation
Code for PDF Generation is contained within the folder 'reportmaker'. It currently exists as a different composer project as the server needs wkhtmltopdf installed, which isn't the case for the standard CIS hosting. Documentation on how to install the PDF generator can be found in reportmaker/README.md, along with sample code on how to change the existing PHP code to work with it.

Technically, one server can serve the entire project. This will require merging the dependencies of the standard composer project with the dependencies of the report maker project. Then, place the generate_pdf.php file inside the public folder, and this should work perfectly.

## Dependencies:
+ PHP 5.3
+ Composer
+ Twig

Due to the unique login system, this must be hosted using the Durham University Community Hosting.
Ensure the project itself is placed inside the 'password' directory of the 'public_html' folder, so that the server will work correctly!

It currently uses Apache Basic Authentication to login to the system, so this needs to be enabled and working correctly.

PDF Generation needs the wkhtmltopdf binary to be installed.
