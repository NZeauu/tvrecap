# TVRecap

## Description
TVRecap is a web application that allows users to search for TV shows and movies and view information about them. Users can also create an account and add shows/movies to their watchlist. The pages are ${\textcolor{red}{ONLY}}$ ${\textcolor{red}{IN}}$ ${\textcolor{red}{FRENCH}}$ for the moment.

## Technologies Used
- [MySQL](https://www.mysql.com/)
- [Jquery](https://jquery.com/)
- AJAX
- [TMDB API](https://developer.themoviedb.org/docs/getting-started) (Content for sql files)
- Postfix / Dovecot (mail server) (following this [debian tutorial](https://www.linuxbabe.com/mail-server/build-email-server-from-scratch-debian-postfix-smtp) Part 1, 2 and 4)
- Certbot (SSL certificate)

## Installation
1. Clone the repository to your local machine or server
2. Create a new database and import the `model.sql` file to create the tables.
${\textcolor{red}{WARNING}}$: This will overwrite any existing tables with the same name. This is a MariaDB database, so it may not work with other SQL databases.
3. PLEASE ADD A `constants.php` FILE INTO THE PHP DIRECTORY WITH THIS CONTENT:
```
// Change the values with your login informations
<?php
    const DB_USER = "your user name";
    const DB_PASSWORD = "your password";
    const DB_NAME = "tvrecap";
    const DB_SERVER = "127.0.0.1";
    const DB_PORT = 3306;
?>
```
4. Add your TMDB API key to the python file in `ad/scripts/` folder to get the data from the API.
5. Configure your mail server to send emails. I used Postfix and Dovecot on a Debian server. You can follow the tutorial linked above to set up your mail server.

## Features to add
- [ ] Add SMTP functionality to send verification email on account creation
- [ ] Add SMTP functionality to send password reset email
- [X] Avatar choice on account parameters
- [ ] Content adding page for admin account
- [ ] Responsive website (for screen under 15")
- [ ] Remember me functionality on login
- [ ] Forget password
- [ ] Multi languages choice
- [ ] Search bar
      

## License
This project is licensed under the AFL-3.0 License - see the [LICENSE.md](LICENSE.md) file for details
