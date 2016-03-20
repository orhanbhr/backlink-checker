# Introduction #

The backlink checker tool is a simple PHP class/script, that checks hosts for backlinks on the page.

# Requirements #
  * PHP cli, version 5.x
  * A running mySQL server

# Installation #
To run the backlink checker you first have to make some configurations.
  * **Internal configuration:**<br />Set the value of the variable `$SQLUSER` to the user name of your mySQL user. Set the value of the variable `$SQLPASS` to your mySQL password and the value of `$SQLHOST` to your mySQL host (the default ist localhost)
  * **Otional:** Set the value of the variable `$SLEEPTIME` to a value of your choice. This value is the time to sleep until the next scan progress in seconds. Default is 1800 seconds, which are 30 minutes.
  * **mySQL configuration:**<br />Create a new database with a name of your choice and insert this name as a value for the `$SQLDB` variable.<br />Create a table with the name **links** in the database you just created including the following fields:
| **Field** | **Type** | **Attributes** | **Default** | **Extra** |
|:----------|:---------|:---------------|:------------|:----------|
| id        | int(10)  | unsigned       | --          | auto\_increment, primary\_key |
| host      | varchar(256) | --             | --          | --        |
| url       | varchar(256) | --             | --          | --        |
| active    | smallint(1)| unsigned       | 0           | --        |
| entered   | varchar(256) | --             | --          | --        |
| lastseen  | varchar(256) | --             |--           | --        |
| lastupdate | varchar(256) | --             | --          | --        |

  * The field **id** is a unique identifier for the data.
  * The field **host** holds the URL where the backlink should be found. The format is a normal URL format, with a leading http:// (e.g. http://example.com)
  * The field **url** holds the URL of the backlink itself. It's also stored in normal URL format, like the host URL.
  * The field **active** is an integer to determine if the link was found or not. Until now, two different states (0=inactive & 1=active).
  * The field **entered** is a unix timestamp of the time the link was entered into the database.
  * The field **lastseen** is also a timestamp with the time the link was last seen active.
  * The field **lastupdate** is a timestamp with the value the links data was last updated.

Now, you can enter links into this table via script or manually.

# Usage #
After the installation you can run the script by typing in your console:
`php backlink-checker.php`
Because all configuration is done internally, there are no further arguments required.