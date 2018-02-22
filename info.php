<?php
//Fill out the following:
$domain="localhost";
$from_email="no-reply@$domain";
$DB_location="users.db";
/*Caching -- Do NOT use unless you really want to break something :-)
This will probably break everything, and will require you to fix it yourself.
The only reason you might want to do this is if you satisfy both of the following:
     * You don't have enough resources to run MySQL
     * SQLite simply can't handle the number of requests you get
THIS CURRENTLY IS NOT YET IMPLEMENTED,  but will do something like:
     * Check for a cached version of queries
     * If they exist, bypass SQLite completely, and use the cache
     * If there is no cache, then run the query, and cache it

SECURITY:
This should not decrease the security, because the only data that will be stored in the cache is:
     * Emails
     * Usernames
     * Hashed passwords
*/
$caching="no";
?>
