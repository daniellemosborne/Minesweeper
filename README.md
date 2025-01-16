# Minesweeper

Instructions for Minesweeper

* Importing Files to Server *

1. Unzip 'Minesweeper_130.zip'
2. You should now have a folder named minesweeper with all of the necessary files inside
2. Navigate to /XAMPP/htdocs 
3. Place the minesweeper folder inside of htdocs

* Setting up the database *

Using phpMyAdmin:

1. Locate the minesweeper_db.sql file in the provided zipped folder.
2. Open phpMyAdmin
3. Create a new database:
   - Go to the 'Databases' tab.
   - Enter "user_accounts" as the name of the database.
   - Click 'Create'.

2. Click the newly created user_accounts database
3. Go to the Import tab
4. Choose the minesweeper_db.sql file and click 'Go'

Once those steps are completed, the database and its data should be ready to use.

* Running the Application *

1. Open a web browser.
2. Navigate to the signup page:

   http://localhost/minesweeper/signup.html

3. Create an account by filling in the required fields and clicking SIGN UP.
4. Login using the credentials you just created (email and password).
5. After logging in, you will be directed to the main menu (index.php), where you can:
   - Play game: Play Minesweeper in easy, medium, or hard mode
   - Leaderboard: View player statistics
   - Contact: Contact the authors of the game
   - Help: Access information and instructions about Minesweeper.
 6. To log out, click the Logout button from the main menu.




   
