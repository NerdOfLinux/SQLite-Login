# A simple login system using SQLite

## Setup:
Run in terminal:
```bash
wget https://raw.githubusercontent.com/NerdOfLinux/SQLite-Login/master/index.php -O account.php
sqlite3 .ht.users.db
```
In SQLite:
```sql
CREATE TABLE pending (code TEXT UNIQUE NOT NULL,username TEXT UNIQUE NOT NULL, email TEXT UNIQUE NOT NULL, password TEXT NOT NULL);
CREATE TABLE users (username TEXT UNIQUE NOT NULL, email TEXT UNIQUE NOT NULL, password TEXT NOT NULL);
```
## Notes:
I build this using things such as:

```sql
INSERT INTO users (username, email, password) VALUES(\"$userName\", \"$email\", \"$password\")
```
so you're free to add additional columns without breaking anything(credits, registration date, etc.)

However, this is designed to be **just** a login system. For any other functionality such as comments, a forum, etc., please use another SQLite(or MySQL) database, and use the session variables for authentication. 

## Sessions varialbes set:
Upon successful login, the following variables are set:
* loggedIn is set to true
* userName is set to the username
* id is set to the rowid in sqlite, which is mainly used when updating user info

## SQLite is powerful enough for this
Many people will likely think that SQLite is too weak for use in a production website, well, it's not. According to the [SQLite appropriate uses](https://www.sqlite.org/whentouse.html):

> SQLite works great as the database engine for most low to medium traffic websites (which is to say, most websites). The amount of web traffic that SQLite can handle depends on how heavily the website uses its database. Generally speaking, any site that gets fewer than 100K hits/day should work fine with SQLite. The 100K hits/day figure is a conservative estimate, not a hard upper bound. SQLite has been demonstrated to work with 10 times that amount of traffic.

> The SQLite website (https://www.sqlite.org/) uses SQLite itself, of course, and as of this writing (2015) it handles about 400K to 500K HTTP requests per day, about 15-20% of which are dynamic pages touching the database. Dynamic content uses about 200 SQL statements per webpage. This setup runs on a single VM that shares a physical server with 23 others and yet still keeps the load average below 0.1 most of the time.

So, unless your website is getting more than hundreds of thousands of daily requests, you're probably fine. 

Also, according to the [FAQs](https://www.sqlite.org/faq.html#q19):

> Actually, SQLite will easily do 50,000 or more INSERT statements per second on an average desktop computer.

Which means SQLite can handle hundreds to thousands of new users per second, which is very unlikely to get even on a popular website.

# This is NOT yet ready to be used on a real site
This is in pre-alpha stages, and will likely break a lot.

