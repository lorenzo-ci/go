# go
gestione ordini
questo è un programma per la gestione degli ordini per conto di aziende rappresentate da agenti di commercio
è stato adattato nel 2002 da PHPBoards
è stato creato per esigenze specifiche e potrebbe non essere adatto al pubblico

Filter Forms
------------
Wildcards depend on OMP_FILTER_STYLE (see conf.php). If this is set to true, then 
users must use MS-ACCESS notation (? and *). Else the backend standard values 
are used (for PostgreSQL they are _ and %).
The NULL string can be entered in search form input fields when searching for 
NULL record fields.
"between date1 and date2" can be entered to select a range of dates

SQL Statements
--------------
In SQL statements (see the $sql array) use ? and ! wildcards as per PEAR DB - but please note 
that ! should also be used for all those fields that can have 'NULL' as their value.

Locale
------
Supported locales are it_IT and en_US.
They are hard-coded in $conf['LC'] (see conf.php), in user.php and in dd-payments.php.
This applies to date format in makeData() (see in functions.php) 
and to the localization of HTML pages (see locale table in the backend DB).
Only Gregorian dates are supported as a consequence of the use of checkdate() 
in formCheckDate() (see functions.php).

A note on addslashes()
----------------------
Actually, you don't want to use addslashes() EVER when passing between pages( 
not in POST when passing in hidden fields, not in GET when appending to a URL).  
Only use addslashes() with a database, that's the only time it helps you. 
However, you still have work to do if you are sending variables to HTML pages.  
If via Hidden fields in a <FORM> tag, use htmlentities().  This will turn all 
HTML speical characters into something your browser will interpret as 
characters, not HTML.  When you get to the next page, you don't need to do any 
decoding. If you are using GET variables and appending to URL's, use urlencode() 
on the variable.  When you get to the next page, you don't need to do any 
decoding, your browser did it for you.

Global variables
----------------
Please see conf.php for a reference to constants.
Name            Type    Defined in      Description
$_OMP_conf      array   conf.php        various default configuration values
$_OMP_onload    string  conf.php        onload Javascript found in template 0
$_OMP_timer     object  base.php        PEAR Benchmark
$_OMP_logout    string  base.php        if true display login page
$_OMP_tpl       string  module          list of template numbers
$_OMP_lcl       string  module          list of locale string numbers
$_OMP_db        object  base.php        PEAR DB
$_OMP_tplg      string  base.php cp.php template group (127 for cp)
$_OMP_user      array   base.php        user info and preferences

Schemas
-------
Please set to -1 the length of primary key columns that are auto-increment.
