# conCEPt
This is the CEP project for Steve McGough. It's an Online Document repository for marking group project work.
## Components of the System
### TableMaker
This is a class that actually generates the blank printable tables that are to be filled in by the user.
To use this, include the required php files:

```php
  require_once "../vendor/autoload.php";
  include "../reportmaker/TableMaker.php";
  include "../reportmaker/FileReader.php";
  include "../reportmaker/XMLConfigFileReader.php";
```
Then create a new TableMaker using the code:
```php
  $tm = new TableMaker();
  ```
 One can then use this instance to generate an HTML document (as a string), by reading from either an XML Config file directly, or from an XML String (useful if we end up storing data in a non-file based way):

```php
  $tm->getTableFromXMLFile("testTemplate.xml");
```
