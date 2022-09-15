# Assignment List

assignment_list is an unconventional calendar/todo manager. It can be used to manage tasks and was originally designed as a means for managing assignments for my college classes. A MySQL database is used to manage tables and groups, and the backend is handled by PHP.

### Requirements

- MySQL
- PHP

### Usage

It is recommended for first-time users to run the `setup.php` script, which will create a `config.php` file for the backend to connect to the MySQL database and tables with the necessary columns and and constraints. Bear in mind that the setup script assumes a MySQL database and a user with access to this database have already been created.

```
$ php setup.php
```

If no server is running, first start a PHP server:

```
$ php -S localhost:<port number>
```

Then navigate to `localhost:<port number>/assignment_list.html` in a web browser.

The first thing to do is to create a new "class" or group. Then, after a class has been created, assignments can be added to the class. Options for each assignment include **edit**, **clone**, **mark as done/not done**, and **delete**

### Extending

assignment_list supports custom CSS rules and JS scripting. The following paths will automatically be sourced by the page

- `custom/custom.css`
- `custom/custom.js`
