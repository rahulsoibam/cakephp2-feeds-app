# Install LAMP
## Install and setup Apache v2
```bash
sudo apt update
sudo apt upgrade

# Install Apache2
sudo apt install apache2

# Adjust firewall to allow web traffic
sudo ufw app list
sudo ufw app info "Apache Full"
```

```bash
OUTPUT:
Profile: Apache Full
Title: Web Server (HTTP,HTTPS)
Description: Apache v2 is the next generation of the omnipresent Apache web
server.

Ports:
  80,443/tcp
```

```bash
# Allow incoming HTTPS and HTTP for this profile
sudo ufw allow in "Apache Full"
```

Check if apache2 is working by visiting the public ip:
```bash
http://public_server_ip
```
In our case
```bash
http://142.93.216.202
```

## Install and setup MySQL
```bash
sudo apt install mysql-server
```
Run the mysql secure installation script to lock down and remove dangerous defaults
```bash
sudo mysql_secure_installation
```
To use password to authenticate the **root** MySQL user, switch the authentication method from `auth_socket` to `mysql_native_password`.

```bash
# Open up the MySQL prompt
sudo mysql

# Check the authentication method for each MySQL user account
mysql> SELECT user,authentication_string,plugin,host FROM mysql.user;

# run the ALTER USER command to change authentication method
mysql> ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'password';

# Flush privileges to reload grant tables and apply changes
mysql> FLUSH PRIVILEGES;

# Check the authentication method of each user to 
# confirm that root no longer authenticates using the auth_socket plugin
mysql> SELECT user,authentication_string,plugin,host FROM mysql.user;

# Exit from the MySQL prompt
mysql> quit;
```

## Install and setup PHP
```bash
sudo apt install php libapache2-mod-php php-mysql php-intl php-mbstring php-simplexml php-dev 
```
Tell apache2 to look for index.php instead of index.html
```bash
sudo vim /etc/apache2/mods-enabled/dir.conf

OUTPUT:
<IfModule mod_dir.c>
        DirectoryIndex index.html index.cgi index.pl index.php index.xhtml index.htm
</IfModule>
# vim: syntax=apache ts=4 sw=4 sts=4 sr noet
```
Move the php index file to the first position after the `Directory-Index` specification
```bash
<IfModule mod_dir.c>
    DirectoryIndex index.php index.html index.cgi index.pl index.xhtml index.htm
</IfModule>
```

Restart apache
```bash
sudo systemctl restart apache2
```

## Install mcrypt (Required for Cake PHP 2x)
Run the following commands in succession
```sudo
sudo apt install php-pear
sudo apt install gcc make autoconf libc-dev pkg-config
sudo apt install libmcrypt-dev
sudo pecl install mcrypt-1.0.1
```
When you are shown the prompt
```bash
libmcrypt prefix? [autodetect] :
```
Press `[Enter]` to autodetect

After successfully installing mcrypt through pecl, add mcrypt.so extension to php.ini

The output will look something like this:
```bash
...
Build process completed successfully
Installing '/usr/lib/php/20170718/mcrypt.so'    ---->   this is our path to mcrypt extension lib
install ok: channel://pecl.php.net/mcrypt-1.0.1
configuration option "php_ini" is not set to php.ini location
You should add "extension=mcrypt.so" to php.ini
```

Grab installation path and add to cli and apache2 php.ini configuration
```bash
sudo bash -c "echo extension=/usr/lib/php/20170718/mcrypt.so > /etc/php/7.2/cli/conf.d/mcrypt.ini"
sudo bash -c "echo extension=/usr/lib/php/20170718/mcrypt.so > /etc/php/7.2/apache2/conf.d/mcrypt.ini"
```

Verify that the extension was installed
Run the command:
```bash
php -i | grep "mcrypt"
```
If everything bodes well, the output should look something like this:
```bash
/etc/php/7.2/cli/conf.d/mcrypt.ini
Registered Stream Filters => zlib.*, string.rot13, string.toupper, string.tolower, string.strip_tags, convert.*, consumed, dechunk, convert.iconv.*, mcrypt.*, mdecrypt.*
mcrypt
mcrypt support => enabled
mcrypt_filter support => enabled
mcrypt.algorithms_dir => no value => no value
mcrypt.modes_dir => no value => no value
```

## Test if PHP is working on the server
Create `info.php` file in the web-root
```bash
sudo vim /var/www/html/info.php
```

Add the following php code to the file
```php
<?php
    phpinfo();
?>
```

Visit the following address on a web browser
```html
http://server_public_ip/info.php
```

Delete the file as the information can be abused by a malicious user
```bash
rm /var/www/html/info.php
```

# Setting up CakePHP 2.10
## Install Composer
Download the `composer-setup.php` script to the home directory
```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
```
Verify the downloaded file
```bash
php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
```
Install Composer
```bash
php composer-setup.php

# Unlink
php -r "unlink('composer-setup.php');"
```

Make the installation global
```bash
mv composer.phar /usr/local/bin/composer
```

## Setting up CakePHP 2.10 project
Change directory to /var/www
```bash
cd /var/www
```
Create a composer.json file and copy the following into it
```json
{
    "name": "test",
    "require": {
        "cakephp/cakephp": "2.10.*"
    },
    "config": {
        "vendor-dir": "Vendor/"
    }
}
```
Run the following to create the project:
```bash
composer install
```
Once composer has finished running, you should have a directory structure that looks like this
```bash
var/www/
    composer.lock
    composer.json
    Vendor/
        bin/
        autoload.php
        composer/
        cakephp/
```
Bake a CakePHP project
```bash
Vendor/bin/cake bake project testapp
```
This will create a CakePHP app called testapp

## Edit `CAKE_CORE_INCLUDE_PATH` 
```bash
vim /var/www/testapp/webroot/index.php
```
Find the `CAKE_CORE_INCLUDE_PATH` and edit it to `'/var/www/Vendor/cakephp/cakephp/lib'`
```bash
if (!defined('ROOT')) {
    define('ROOT', '/var/www');
}

if (!defined('APP_DIR')) {
    define('APP_DIR', 'testapp');
}

define('CAKE_CORE_INCLUDE_PATH',  ROOT . DS . 'Vendor' . DS . 'cakephp' . DS . 'cakephp' . DS . 'lib');
```


## Apache and mod_rewrite (.htaccess)

Enable mod_rewrite
```bash
sudo a2enmod rewrite
```

Make sure an `.htaccess` overwrite is allowed and that AllowOverride is set to all for the correct DocumentRoot

In `/etc/apache2/apache2.conf`, you should see
```bash
# Each directory to which Apache has access can be configured with respect
# to which services and features are allowed and/or disabled in that
# directory (and its subdirectories).
#
# First, we configure the "default" to be a very restrictive set of
# features.
#
<Directory />
    Options FollowSymLinks
    AllowOverride All
#    Order deny,allow
#    Deny from all
</Directory>
```

For users having apache 2.4 and above, you need to modify the configuration file for your httpd.conf or virtual host configuration to look like the following:
```bash
<Directory /var/www>
     Options FollowSymLinks
     AllowOverride All
     Require all granted
</Directory>
```
Make sure that `mod_rewrite` is loaded correctly. The file should contain something like:
```bash
LoadModule rewrite_module libexec/apache2/mod_rewrite.so
```

If not, add it somewhere in the `apache2.conf`

## Change apache document root 
```bash
vim /etc/apache2/sites-available/000-default.conf
```
In the `<VirtualHost:*80>` block, change the document root to `/var/www/testapp/webroot`
```bash
DocumentRoot /var/www/testapp/webroot
```

## Create and configure MySQL database
Login to mysql
```bash
mysql -u root -p
Password:
```
In the `sql>` prompt type the following:

Create database
```sql
CREATE DATABASE jobsenz
```
To work with the database, enter
```sql
USE jobsenz
```
Create the `users` table
```sql
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
);
```
Create the `feeds` table
```sql
CREATE TABLE feeds (
    feed_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    feed_title VARCHAR(255) NOT NULL,
    feed_description VARCHAR(65000) NOT NULL,
    created_date DATETIME DEFAULT NULL
);
```
## Fill dummy data into the feeds table
Dump the feeds table using `mysqldump` command
```bash
mysqldump -u root -p --nodata jobsenz feeds > feeds.sql
Password:
```
This should create a `feeds.sql` in the current folder
Open the file and copy the `CREATE TABLE` command:
```sql
CREATE TABLE `feeds` (
  `feed_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `feed_title` varchar(255) NOT NULL,
  `feed_description` varchar(65000) NOT NULL,
  `created_date` datetime DEFAULT NULL,
  PRIMARY KEY (`feed_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
```
Go to [http://filldb.info/dummy/step1](http://filldb.info/dummy/step1) and paste the schema and generate the dummy data.
Export and download the sql file.

Copy the `.sql` files to the remote server using `scp`
```bash
scp *.sql root@server_public_ip:/root
Password:
```
Run the sql file using MySQL prompt
```bash
# Open mysql prompt
mysql
# Source the sql file
mysql> source /root/dummydata.sql

```
## Connect CakePHP to database
Cd into the Config folder
```bash
cd /var/www/testapp/Config
```
Create working copy of the `database.php.default` file and rename to `database.php`
```bash
cp database.php.default database.php
```
Edit the database.php file
```bash
vim database.php
```
The file should look something like this after editing
```php
<?php
/**
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 */

class DATABASE_CONFIG {

    public $default = array(
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host' => 'localhost',
        'login' => 'root',
        'password' => 'Test12345',
        'database' => 'jobsenz',
        'prefix' => '', 
        //'encoding' => 'utf8',
    );  
}
```
Reload the webpage (`http://server_public_ip`) and it should say:

```html
Your database configuration file is present.
CakePHP is able to connect to the database.
```
# Creating the Feeds app
## Create a feed model
Create `Feed.php` (named as per CakePHP conventions)
```bash
vim /var/www/testapp/Model/Feed.php
```
Add the following lines of code:
```php
<?php
class Feed extends AppModel {

}
```

## Create a Feed controller
Create `FeedsController.php` inside the `Controller` folder
```bash
vim /var/www/testapp/Controller/FeedController
```
Edit it as follows
```php
<?php
class FeedsController extends AppController {
    public $helpers = array('Html', 'Form');

    public function index() {
        $this->set('feeds', $this->Feed->find('all'));
    }
}
```

## Create a Feed view
Create a Feeds folder inside `/var/www/testapp/View` and enter it
```bash
mkdir /var/www/testapp/View/Feeds
cd !$
```
Create an index.ctp file
```bash
vim index.ctp
```
Edit the file as follows
```html
<h1>Feeds</h1>
<table>
    <tr>
        <th>Id</th>
        <th>Title</th>
        <th>Description</th>
        <th>Created</th>
    </tr>

    <!-- Loop through and print -->
    <?php foreach ($feeds as $feed): ?>
    <tr>
        <td><?php echo $feed['Feed']['feed_id']; ?> </td>
        <td><?php echo $feed['Feed']['feed_title']; ?></td>
        <td><?php echo $feed['Feed']['feed_description']; ?></td>
        <td><?php echo $feed['Feed']['created_date']; ?></td>
    </tr>
    <?php endforeach; ?>
    <?php unset($feed) ?>
</table>
```

Visit `http://server_public_ip/feeds` to check if it is working or not. If all goes well, the page should show the data from the database

## Change the default routes
```bash
vim /var/www/testapp/Config/routes.php
```
Change the line
```php
Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
```
to 
```php
Router::connect('/', array('controller' => 'feeds', 'action' => 'index'));
```

Visiting `http://server_public_ip` should now show the same page as visiting `http://server_public_ip/feeds`

# User Authentication
## Create the User model
```php
// /var/www/testapp/Model/User.php
<?php
App::uses('AppModel', 'Model');

class User extends AppModel {
    public $validate = array(
        'username' => array(
            'rule' => 'notBlank',
            'message' => 'The username cannot be empty'
        ),
        'password' => array(
            'rule' => 'notBlank',
            'message' => 'A password is required'
        )
    );
}
```

## Create the UsersController
```php
// /var/www/testapp/Controller/UsersController
App::uses('AppController', 'Controller');

class UsersController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('add');
    }

    public function index() {
        $this->User->recursive = 0;
        $this->set('users', $this->paginate());
    }

    public function view($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->set('user', $this->User->findById($id));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Flash->success(__('The user has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Flash->error(
                __('The user could not be saved. Please, try again.')
            );
        }
    }

    public function edit($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->User->save($this->request->data)) {
                $this->Flash->success(__('The user has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Flash->error(
                __('The user could not be saved. Please, try again.')
            );
        } else {
            $this->request->data = $this->User->findById($id);
            unset($this->request->data['User']['password']);
        }
    }

    public function delete($id = null) {
        $this->request->allowMethod('post');

        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->User->delete()) {
            $this->Flash->success(__('User deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Flash->error(__('User was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }

}
```
## Create the views
```html
<!-- /var/www/testapp/View/Users/add.ctp -->
<div class="users form">
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo __('Add User'); ?></legend>
        <?php echo $this->Form->input('username');
        echo $this->Form->input('password');
        ?>
    </fieldset>
<?php $this->Form->end(__('Register')); ?>
</div>
```

## Authentication
```php
// /var/www/testapp/Controller/AppController
class AppController extends Controller {
    //...

    public $components = array(
        'Flash',
        'Auth' => array(
            'loginRedirect' => array(
                'controller' => 'feeds',
                'action' => 'index'
            ),
            'logoutRedirect' => array(
                'controller' => 'pages',
                'action' => 'display',
                'home'
            ),
            'authenticate' => array(
                'Form' => array(
                    'passwordHasher' => 'Blowfish'
                )
            )
        )
    );
}
```

Add the following lines to the `UsersController.php` file
```php
// /var/www/testapp/Controller/UsersController.php
public function beforeFilter() {
    parent::beforeFilter();
    // Allow users to register and logout.
    $this->Auth->allow('add', 'logout');
}

public function login() {
    if ($this->request->is('post')) {
        if ($this->Auth->login()) {
            return $this->redirect($this->Auth->redirectUrl());
        }
        $this->Flash->error(__('Invalid username or password, try again!'));
    }
}

public function logout() {
    return $this->redirect($this->Auth->logout());
}
```

## Enable password hashing add the following lines to
```php
// /var/www/testapp/Model/User.php
App::uses('AppModel', 'Model');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');

class User extends AppModel {

// ...

public function beforeSave($options = array()) {
    if (isset($this->data[$this->alias]['password'])) {
        $passwordHasher = new BlowfishPasswordHasher();
        $this->data[$this->alias]['password'] = $passwordHasher->hash(
            $this->data[$this->alias]['password']
        );
    }
    return true;
}

// ...
```

## Create the login view
```php
// /var/www/testapp/View/Users/login.ctp

<div class="users form">
<?php echo $this->Flash->render('auth'); ?>
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend>
            <?php echo __('Please enter your username and password'); ?>
        </legend>
        <?php echo $this->Form->input('username');
        echo $this->Form->input('password');
    ?>
    </fieldset>
<?php echo $this->Form->end(__('Login')); ?>
</div>
```
Configure, redit and create according to the project files