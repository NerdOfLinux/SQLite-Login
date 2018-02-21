# A simple login system using SQLite

## Setup:

```bash
sqlite3 users.db
```
```sql
CREATE TABLE pending (code TEXT UNIQUE NOT NULL,username TEXT UNIQUE NOT NULL, email TEXT UNIQUE NOT NULL, password TEXT NOT NULL);
CREATE TABLE users (username TEXT UNIQUE NOT NULL, email TEXT UNIQUE NOT NULL, password TEXT NOT NULL);
```
