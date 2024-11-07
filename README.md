# TVRecap

## Description
TVRecap is a web application that allows users to search for TV shows and movies and view information about them. Users can also create an account and add shows/movies to their watchlist. The pages are ${\textcolor{red}{ONLY}}$ ${\textcolor{red}{IN}}$ ${\textcolor{red}{FRENCH}}$ for the moment.

## Technologies Used
- [MySQL](https://www.mysql.com/)
- [Jquery](https://jquery.com/)
- AJAX
- [TMDB API](https://developer.themoviedb.org/docs/getting-started) (Content for sql files)
- [tmdbv3api](https://pypi.org/project/tmdbv3api/) (Python library to get data from the API)
- Postfix / Dovecot (mail server) (following this [debian tutorial](https://www.linuxbabe.com/mail-server/build-email-server-from-scratch-debian-postfix-smtp) Part 1, 2 and 4)
- Certbot (SSL certificate)
- [PHPMailer](https://github.com/PHPMailer/PHPMailer) (to send emails)

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
    const DB_NAME = "tvrecap"; #or the name of your database
    const DB_SERVER = "127.0.0.1";
    const DB_PORT = 3306;
?>
```
4. Add your TMDB API key to the python file in `ad/scripts/` folder to get the data from the API.
5. Configure your mail server to send emails. I used Postfix and Dovecot on a Debian server. You can follow the tutorial linked above to set up your mail server.
6. Install PHPMailer with composer `composer require phpmailer/phpmailer` (if you don't have composer, you can download it [here](https://getcomposer.org/download/))
7. Create a `phpMailerConf.php` file in the `php` directory with this content:
```
<?php

// Change the values with your mail server informations
$mail->isSMTP();
$mail->Host = 'your mail server';
$mail->SMTPAuth = true;
$mail->Username = 'your email address';
$mail->Password = 'your password';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;
```
8. Be careful, the site redirections use the `RewriteEngine` module of Apache. You need to enable it and configure each page in your configuration file. You will also need to change the redirections in html and JS files to match your domain. Adapt the following lines to your configuration.
Here are the lines to add:
```
    RewriteEngine On
    RewriteRule ^/register$ /html/register.html [L]
    RewriteRule ^/add$ /html/add.html [L]
    RewriteRule ^/movies$ /html/movies.html [L]
    RewriteRule ^/series$ /html/series.html [L]
    RewriteRule ^/settings$ /html/settings.html [L]
    RewriteRule ^/home$ /html/home.html [L]
    RewriteRule ^/contact$ /html/contact.html [L]
    RewriteRule ^/history$ /html/history.html [L]
    RewriteRule ^/resetPass$ /html/reset-password.html [L]
    RewriteRule ^/verifyAccount$ /html/verifaccount.html [L]
    RewriteRule ^/movieDetails$ /html/movie-details.html [L]
    RewriteRule ^/serieDetails$ /html/serie-details.html [L]
    RewriteRule ^/AdminHomePanel$ /ad/html/adhome.html [L]
    RewriteRule ^/AdminAddContent$ /ad/html/adadd.html [L]
    RewriteRule ^/AdminManageContent$ /ad/html/admanage.html [L]
    RewriteRule ^/AdminManageUsers$ /ad/html/adusers.html [L]
```
9. Please add a `connectDatabase.py` file into `ad/scripts/` folder to enable the connection to your database for content adding. The file needs to be like this:
```
# Connect to the database
import mysql.connector

def connectDatabase():
    conn = mysql.connector.connect(
        host="127.0.0.1",
        user="your database username",
        password="your database password",
        database="tvrecap" #or the name of your database
    )

    return conn
```

## Features to add
- [X] Add SMTP functionality to send verification email on account creation
- [X] Add SMTP functionality to send password reset email
- [X] Avatar choice on account parameters
- [X] Content adding page for admin account
- [ ] Responsive website (for screen under 15")
- [X] Remember me functionality on login
- [ ] Multi languages choice
- [ ] Search bar
      

## License
This project is licensed under the AFL-3.0 License - see the [LICENSE.md](LICENSE.md) file for details
