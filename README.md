# Archive users to files solution
This solution is tested using the XAMPP Apache, PHP, MySQL environment.


## How to set up
* Setup an Apache, PHP, MySQL Environment.
* Setup MySQL and import the SQL dump file.
* Move the **archiveUsers.php** file to the Apache website directory.
* Edit the **archiveUsers.php** and change the following variables `$dbserver`, `$dbuser`, `$dbpass`, `$dbname`, `$dbtablename` and `$pathToArchiveFolder` to fit your server environment.

## How to use
The solution recieves the query through a GET request, that specifies what ID/ID's it should archive and delete from the MySQL database. The GET request would look along the lines of:
* `host.com/archiveUsers.php?ids=idToArchive` 

or
* `host.com/archiveUsers.php?ids=id1,id2,id3`

The solution will by default capitalise all letters and remove any unwanted characters while handling the query. This is to prevent SQL injections using the solution and make sure the query matches an users ID, unless the ID is incorrrect.

The solution can handle ID's following these formats:
* **XXXX-XXXXXX-XXX-XXXXX**
* **XXXXXXXXXXXXXXXXXXXX**

And they have no set length requirement. So if the id is wrong in any way except capitalisation, then the solution will not archive any user and respond with **Error: The query returned 0 results!** up to multiple times in case of multiple faulty ID's.

*This also means the solution will archive a user as long as the ID is correct no matter if the ID's before or after it are incorrect.*

All users archived will be writen to their own **.json** file with the ID as the files name and be deleted from the databse.
***In case something goes wrong while writing the file, the user will not be deleted from the database (this is to maintain data integrity).***

## How it works (step-by-step)
1. Connects to database.
2. Reads query.
3. Capitalises all characters and removes bad characters.
4. Splits ID's into an array of strings (*From this point on it handles every user individually*).
5. Queries the database about the ID.
6. In case of a successfull match encode the users data to the JSON format.
7. Save the JSON data to a .json file named after the users ID.
8. Read the newly created JSON file.
9. Check to see if the JSON file matches the encoded data.
10. In case the previous check was successfull delete user from the database.

# Final Thoughts

## Security concerns
The main concern would be if anyone got ahold of the solution file after it has been implemented into a running system since the files content holds the variables regarding server, user, password, etc. This can however be fixed by keeping the database details elsewhere.

Other than physical access to the file most security concernes i can think about are handled in the removal of bad characters from the query which stops SQL injections.

The only real danger when it comes to remote access to the file would be ID brute forcing, although since the ID's contain the number of symbols that they do, this highly unlikely to happen.


## Next Steps
There are a few changes that could be made to the handling of the database details, as an example you could make it necessary to send the database login along with the query. This could be an encrypted versions of the login (To stop a man in the middle attack in case of this change).

You could also add a IP whitelist to the solution which would stop anyone not added to the whitelist from accessing it.

Ultimately the solution should redirect you away from `host.com/archiveUsers.php`, to make sure people didn't tinker with the solution after use.

*You can always overengineer it.*
