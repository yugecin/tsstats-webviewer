
tsstats webviewer
-----------------
Webviewer for the tsstats bot, showing the gathered statistics in a fancy way, inspired by similar irc statistics.

See [yugecin/tsstats](https://github.com/yugecin/tsstats) for the actual statsbot.

### Examples/Demo
* [https://tsstats.thisisgaming.org/](https://tsstats.thisisgaming.org/) (since 24/08/2014)
* *do you use this? let me know, and I'll put a link here if you want*

### Notes
* This was made in the summer of 2014, and most parts have been left untouched since then. Expect weird code styles.

### Requirements
* Some webserver with php runtime - as long as it can use PDO it should be good (so at least php 5.0)
* MySQL/MariaDB db having some tsstats data

### Installation
* Get a database with tsstats (see [tsstats repo](https://github.com/yugecin/tsstats))
* Get a user for the database, it only really needs `SELECT` access
* Copy <kbd>config.sample.php</kbd> to <kbd>config.php</kbd> and edit everything as needed
* The live viewer expects that the tsstats bot places the viewer file in the same directory (as <kbd>teamspeak.txt<kbd>, see <kbd>view_viewer.php</kbd>)

### Credits
* Uses [silk icons](http://www.famfamfam.com/lab/icons/silk/) and [flag icons](http://www.famfamfam.com/lab/icons/flags/) from [famfamfam](http://www.famfamfam.com).

### License
[GPL-3.0](/LICENSE)

