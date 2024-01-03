# Matrix_2_ISPConfig-PW_Provider
Authenticate users on a Matrix server against ISPConfig Mailboxes


# Installation
## On the dashboard ISPConfig server

- Create new directory: /var/www/matrix
- Copy check_credentials.php and soap_config.php to /var/www/matrix/
- Copy matrix.vhost to /etc/apache2/sites-available/
- Symlink /etc/apache2/sites-enabled/000-matrix.vhost -> /etc/apache2/sites-available/matrix.vhost
- restart apache

## Matrix Server
I use https://github.com/spantaleev/matrix-docker-ansible-deploy for the Matrix deployment. For this, configure the parameters from the vars.yml file on your installation.

# Add new user on ISPConfig
- Create new Email Domain with your "matrix" as 3rd level: "matrix.mydomain.tld" (must be the same like the Matrix server still respond)
- Create a new Mailbox on the ISPConfig environment (could by have any domain)
- Create an Email-Alias like "loginname@matrix.mydomain.tld" and map the destination to the new created mailbox, where loginname is the username on the Matrix server.
