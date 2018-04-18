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

# This is NOT yet ready to be used on a real site
This is in pre-alpha stages, and will likely break a lot.
