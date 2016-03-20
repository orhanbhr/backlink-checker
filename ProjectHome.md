The backlink checker is a simple PHP tool, to check the appearance of backlinks on other sites.
The tool itself is at a very early state of development, as there is almost no mySQL optimization of the database and tables; the input for the links is very strict and the script forgives you almost no errors. Also, for now there is more data fetched than used. The unused fields will be used in later versions and should not be edited or deleted, if you want compatability for later versions.

**NOTE:** This script is only for data fetching! There is no output or filtering of the data. Also, there is no functionality for data manipulation or insertion. If you want that, you have to write one yourself, but for data insertion some easy HTTP Form and some PHP code will do it for you and for the output simply fetch the data from the database and print it out in a nice HTML table with some PHP. Scripts for that can be found, if you know how to use Google.

My intention for it was just a quick solution to check backlinks on other sites, but I think the script can be extended very easily. Take it as a tutorial or a class for extending other projects.
