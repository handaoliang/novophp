Autoload
========
Class to organize autoloading by [PHP naming conventions](http://news.php.net/php.standards/2) and [PSR-0 standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md):

 * A fully-qualified namespace and class must have the following structure `\<Vendor Name>\(<Namespace>\)*<Class Name>`
 * Each namespace must have a top-level namespace ("Vendor Name").
 * Each namespace can have as many sub-namespaces as it wishes.
 * Each namespace separator is converted to a DIRECTORY\_SEPARATOR when loading from the file system.
 * Each "\_" character in the CLASS NAME is converted to a DIRECTORY\_SEPARATOR. The "\_" character has no special meaning in the namespace.
 * The fully-qualified namespace and class is suffixed with ".php" when loading from the file system.
 * Alphabetic characters in vendor names, namespaces, and class names may be of any combination of lower case and upper case.
 
How to use
==========

- Classes of all packages (libraries), placed in the "vendors" folder, will be autoloaded automatically (in first use);
- You can map namespace of package, placed in any folder: use *register_namespace_dir()* method;
- You can map any class also: use *register_class()* method;

License: [MIT](http://en.wikipedia.org/wiki/MIT_License)
