# Matrix_2_ISPConfig-PW_Provider
Authenticate users on a Matrix server against ISPConfig Mailboxes


# Installation
## On the dashboard ISPConfig server

- Create new directory: /var/www/matrix
- Copy check_credentials.php and soap_config.php to /var/www/matrix/
- Copy matrix.vhost to /etc/apache2/sites-available/
- Symlink /etc/apache2/sites-enabled/000-matrix.vhost -> /etc/apache2/sites-available/matrix.vhost
- restart apache

