# conCEPt
This is the CEP project for Steve McGough. It's an Online Document repository for marking group project work.

## Project Structure
The actual live system shall be found inside the 'conCEPt' folder.

Inside this, there are further folders to put information. Model should contain any code that accesses the database, Controller should contain code that renders the views/contains any logic, and the view contains the twig files that we render. The public folder contains any static resources (such as CSS, JS or images), as well as the various PHP files that act as our routing.

## Dependencies:
+ PHP 5.3
+ Composer
+ Twig

Due to the unique login system, this must be hosted using the Durham University Community Hosting.
Ensure the project itself is placed inside the 'password' directory of the 'public_html' folder, so that the server will work correctly!

It currently uses Apache Basic Authentication to login to the system, so this needs to be enabled and working correctly.

