# Hacker Experience
- An abandoned browser game that became open-source.
- Working on it on free time currently.
- Still need a lot of fixes.

# Requirements
- PHP 8
- Python 3.11
- python-is-python3 (If you need to use python3 instead of python, then this is needed!)
- python3-pip
- PHP-FPM 8
- Apache2
- Python venv
- bcrypt (Python module)
- numpy (Python module)
- mysql-connector-python (Python module)

# Tutorial on how to start the round
- Move this folder to /root and rename as hexc
- Create database called hexc, and root user with root password.
- Import game.sql to the new database with (mysql -p hexc < /root/hexc/game.sql)
- Copy the apache-hexc.conf or symlink to /etc/apache2/sites-enabled or /etc/httpd/conf.d
- Change ServerName inside the config to point to your domain or 127.0.0.1
- Create Python venv with (cd /root/hexc && python -m venv venv)
- Activate the python venv with (source /root/hexc/venv/bin/activate)
- Install python modules with (pip install bcrypt numpy mysql-connector-python)
- Start the round with (python /root/hexc/cron2/startNewRound.py)
- Done! Enjoy the hell of bugs and unfixed stuff :D

# Update
[28 April 2024]
- Fixed registration again
- Removed files and code to reduce codebase overall size.
- Included proper crontab and apache2 config.

[2022-2023]
- Game works now (Sort of)
- NPC software dataset still need update.
- Fixed mission.
- Research are sort of working now.
- Created new script to start round
- Fixed script to end round
- Fixed npc, vpc and log generation script.

# Changelog (Rough)
- ajax.php (Messy af.)
- Choosing bank accounts are not working (Not showing the list). :white_check_mark:﻿  Fixed.
- Choosing viruses inside Hacked Database are not working (Not showing the list). :white_check_mark:﻿  Fixed.
- Creating account without email registration instead of directly from Python script(Only need to implement inside index.php left, call using Python.class.php). :white_check_mark:﻿  Done.
- Patching some security vulnerability inside classes folder. (This can be later, y not)
- Implementing an actual new round creation script using python3.  :white_check_mark:﻿  Fixed.
- Clan are still half-broken. (Because it dependent with Forum apparently.) :white_check_mark:﻿  Fixed.
    - Creating clan works, but clan info still broken. :white_check_mark:﻿  Fixed.
- Software Upload option doesnt appear after clicking 'Upload'
- Hardware information inside software.php are not showing up, including the buttons to create folder, text, etc. :white_check_mark:﻿  Fixed.
- Research not working. :white_check_mark:﻿  Fixed.
- Buying license not working. :white_check_mark:﻿  Fixed.
- Riddle are not working. :white_check_mark:﻿  Fixed.
- Hacked target software list doesn't show hardware information. :white_check_mark:﻿  Fixed.
- Software list are broken inside NPC. :white_check_mark:﻿  Fixed.
- Own ip, money and time broken once hacked into target IP. :white_check_mark:﻿  Fixed.
- Hall of fame (Clan, Software, DDoS) are broken.
- Ranking (Clan, Software, DDoS) are broken.
- Log apparently broken now. (RIP). :white_check_mark:﻿  Fixed and created python script incase it broke again.
- Announcement from forum in Control Panel is broken because I dont use the forum. :white_check_mark:﻿  Fixed. Changed into changelog thats obtained from db instead.
- Tutorial mission broken as well. Dunno how to fix that for now.
- Javascript and CSS are outdated asf, some cant even work. Code also messy. :white_check_mark:﻿  Fixed. Updated most script and fixed some stuff.
