
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
* Copy [config.sample.php](/config.sample.php) to `config.php` and edit everything as needed
* The live viewer expects that the tsstats bot places the viewer file in the same directory (as `.hteamspeak.txt`, see [view_viewer.php](/view_viewer.php))
* For the viewer, you need to add the icons for every group manually in the `imgres` folder. `sgroup_ID.png` for server groups, `cgroup_ID.png` for channel groups. To get the id, open the permissions and check the numer next to the group. For example, if you see 'Admin (13)' in the servergroups, add the admin icon as `imgres/sgroup_13.png`.

### Credits
* Uses [silk icons](http://www.famfamfam.com/lab/icons/silk/) and [flag icons](http://www.famfamfam.com/lab/icons/flags/) from [famfamfam](http://www.famfamfam.com).
* [tsviewer.php](/tsviewer.php) was based on on teamspeak3 viewer for php5 by Sebastien Gerard, see [tsstatsu.sebastien.me](http://tsstatus.sebastien.me/).

### License
[GPL-3.0](/LICENSE)

