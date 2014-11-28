####COOP
Coop is an open source stock managment system for co-ops, developed mainly by [@eyalrosen](https://github.com/eyalrosen). 
The COOP system is currently built for food co-ops and uses Hebrew as its UI language.

### Structure

- **application** - (almost) all the source code
- **cronjobs** - source code for cronjob tasks
- **public** - public assets (images, css, js etc.)
- **uploads** - store the user uploads (currently only product images)

### application/configs/application.ini
The configuration file. **todo:** move varibles to application varibles to address the [12 factors app criteria](http://12factor.net/).

Parameters you should be aware of when installing coops on a new dev/prod server:
- **database.*** - db connection.
- **uploads** - the directory to upload images of products. **should be writable - we had bugs when this folder wasn't writable.**
- **s3.*** - credentials for amazon s3. note that we have a differenct bucket for dev. (see "Third party services").
- **mailgun.*** - credentials for mailgun (see "Third party services").

### application/Controllers
- **applicatoin/CustomController.php** - a base class for controllers. Every control inherits from it.
- **ApiController.php** - a test. not being used anywhere. can be deleted.
- **Controller.php** - can be deleted.
- **CoopsController.php** - CRUD coops. access for users super-user privileges.
- **DebtController.php** - CRUD debts. access for managers.
- **DutyController.php** - CRUD and send email for duty reports (a google docs style editor from duty\manager panel).
- **DutyOrderController.php** - I think no one using it. not sure...
- **ErrorController.php** - being raised by zend framework when theres an exception.
- **FarmerController.php** - update list of supply for framer. update access only for farmer, read for manager.
- **HistoryController.php** - I think no one using it. not sure...
- **IndexController.php** - the login / logout page (when you go to http://www.the-domain-of-the-server.com/).
- **ManagerController.php** - all the stuff you see as a manager. various stuff.
- **UsersController.php** - the stuff you see as a user. various stuff.
- **manager/*** - you can ignore/delete it.

### application/models
(and application/models/Coop...)
- **Uploader.php/UploadImage.php** - one of them (maybe both?) in charge of uploading images of products
- **Categories.php** - CRUD categories of products.
- **Debt.php** - CRUD debts of users.
- **DutyReports.php** CRUD duty reports (filled by users in duty).
- **EmailDeliveries.php** - handles each mail delivery. see "Sending E-mails for Users".
- **EmailMessages.php** - handles each mail message (not delivery). see "Sending E-mails for Users".
- **JsonReports.php** - creating and updating JSON reports of weekly reports. see "Weekly Reports and Supply Report".
- **OrderItems.php** - CRUD of items (products) of an order.
- **OrderReportHistory.php** - I think no one is using it.
- **OrderViewType.php** - We used to have (maybe still do?) 2 ways of seeing the order page - list mode and gallery mode. This helps store a session to remember if the use prefers list mode or gallery mode.
- **Orders.php** - CRUD of orders. has advance fetching functions such as getUsersLastOrder() and getAllThisWeekOrders().
- **S3.php** - third-party code to communicate with Amazon S3.
- **Settings.php** - maybe being used for open for orders feature, maybe not. not sure...
- **Stock.php** - CRUD of the stock list being filled by the user in duty. updates the S3 reports after each save.
- **Supply.php and Supplies.php** - failed attempt to create supply features. not being used.
- **Users.php** - CRUD of users and authentication.
- **WeeklyReports.php** - generate weekly reports (being stored in Amazon S3).
- **duty_reports.tpl** - here by mistake, can be deleted.

### Cronjobs
- **close_coops.php and open_coops.php** - see 'Openning and Closing Coops'.
- **reset_coops.php** - see 'Reseting Coops'.
- **send_emails.php** - see 'Sending E-mails.php'.
- **special_scripts.php** - query weekly report for a specific coop and store it in Amazon S3. Not really a cronjob, I use it manually when there are problem. I use it like this: `http://domain.com/cronme.php?job=special_script&coop_id=1&reset_day=2014-09-20`.
- **move_reports_to_s3.php** - I think that I used it once for migration and it's no longer needed.

### Database Tables
- **coops** - list of coops. also include coop-related settings such as 'coop_last_reset_day'. we had a few time when users got there coop reset too early so we updated the 'coop_last_reset_day' manually in this table.
- **create_reports_script** - no longer in use (almost sure).
- **debts** - no longer in use. we store debt in comments field at users table.
- **duty_reports** - the duty reports (google docs style notes). the content of the reports is stored as html.
- **email_deliveries** - email need to be sent (see 'Sending E-mails' section below). we used to store those but we don't store them anymore, altough we haven't deleted them. The important flag is 'email_delivery_sent'.
- **email_msgs** - list of email messages (see 'Sending E-mails' section below).
- **email_tags** - list of varibles that can be added when composing E-mail messages. the varibles to the exact column name in 'users' table.
- **order_items** - many-to-one table between products and orders.
- **order_report_history** - empty table, can be deleted.
- **orders** - the orders. we can distinguish between this weeks orders and the previous once using the 'order_reset_day'. Note that we don't have any 'reset_days' table, we just store the 'coops.coop_last_reset_day' and 'orders.reset_day' as plain date.
- **prices** - we had a fundemental problem: what happens if we change the price of a product (or it's cost to coop) and then want to see a the weekly report of last month? the data won't be reliable because were using today's price instead of last month's price. 
So we started to store history change for prices and use the 'prices' table and used it when showing the weekly reports.
But then the query took to long and it's had many bugs so we decided to store the reports as plain JSON on Amazon S3.
So we don't need 'prices' table anymore BUT we still use it (because we didn't had a spare time to arrange it).
- **product_categories** - list of categories. each product would have a reference to here.
- **products** - list products.
- **stock** - the stock data, being filled by the duty. also using 'coop_reset_day'.
- **supply** - were not using supply, can be deleted.
- **users** - list of users.

### Sending E-mails for Users
The flow for sending E-mails is:

1. The user (with manager privileges) goes to 'Send E-mail' page. That's in ManagerController.php.
2. The user choose wheather to send to everyone in the coop or to a specific user. He can add varibles like 'First Name' and hits send.
3. A single row in the 'email_msgs' is being created with the data about the message (subject, content, date etc).
4. For every recipient a row 'email_deliveries' is being created. For instance, if I'm sending a message for the entire coop, for every (active) user in the coop a new row in 'email_deliveries' would be added. Note that the column 'email_delivery_sent' is marked as 0.
5. The cronjob 'send_emails.php' is running every minute. For every row in 'email_deliveries' where 'email_delivery_sent = 0' it sends it using Mailgun and sets 'email_delivery_sent = 1'. It's also in charge of changing the varibles with real values before sending it. In fact since were using Mailgun we don't need this cronjob anymore and the process can be done using the ManagerController.

### Weekly Report
The weekly report is a slow query (takes about 30+ seconds). It shouldn't be that slow. The main reason why it's slow is because it's using the 'prices' table, which we don't need anymore (since the entire report is being saved).

The function for quering the weekly report is in application/CustomController.php under baseWeeklyReport().

The report is being queried and then stored in Amazon S3 when:

1. The coop is being reset
2. The coops is being closed (manually or by cronjob)
2. The supply is being updated

### Reseting Coops
When the coop is coop is being reset then the list of orders is being 'cleared away'.

Under manager section the use sets the day in week when the coop would have reset. The day when the coop should reset is the DB under 'coops.coop_reset_day'. It's zero-indexes, which means that 0 is sunday, 1 is monday etc.

The cronjob **cronjobs/reset_coop.php** is running all the time (every 1 minute) and doing the following:

1. For every (active) coop...
2. Is today is reset day? If it is...
3. Generate weekly report and store it in Amazon S3
4. Set in DB 'coops.coop_last_reset_day' for today

### Openning and Closing Coops
When the coop is open the users can create\modify their orders and when it's closed they can't.
The coops can be closed in 2 ways:

1. **Manually** - under manager section check\uncheck the 'is open'. The code is in isOpenForOrdersAction() under ManagerController.php.
2. **Crobjob** - well... we will need a few paragraphs for that :)

To handle a cronjob for closing/openning the coop, the user goes to 'manager' section, then sets the day and hour for closing and openning the coop. (yet another embarrassing note: I think the time is being ignore...). Those fields are stored in the 'coops' table under coop_close_day, coop_close_time, coop_open_day and (... you guessed it!) coop_open_time.

The cronjobs 'close_coops.php' and 'open_coops.php' are running all the time (every minute). For each run they:

1. For each (active) coop...
2. Is today is open\close day? (depends if it's cloose_coops.php or open coops.php)
3. If it is, then open\close the coop

### Third party services
- **Amazon S3** - being used for storing weekly reports and were backup the DB. The backup is being done by a script running in cronjob and located in our production server /home/s3-mysql-backup.
- **Mailgun** - being used for sending E-mails at the send_emails.php cronjob. Also being used when sending duty reports and in ErrorController.php to notify developers about errors.
- **Awesome** - small CRUD utility I wrote. Sits under library/Awesome/DbTable.php, being used by almost all of the models.

### Deploying to production
1. SSH to our production server
2. `cd /home/eyal/www/coops-php`
3. `git pull origin master`

## Contributing

### Getting Set-up Locally  

#### Pre-requisites

* PHP 
* Server (Apache, PHP local server for development...)
* Mysql 

#### Setting Up The Database

* Creating a database from the coop database schema.
* Creating a user for the database with sufficient privileges to manipulate it. 

#### Configuring The App

The app configuration is stored in the source/application/configs/application.ini file. The main items we need to specify are:

* Database
    * database.params.username - A database user name with sufficient privileges to access to application database.
    * database.params.password - The database user password
    * database.params.dbname - The Database name

#### Setting PHP Environment

* Set `short_open_tag = On`. The application code uses `<? ?>` code blocks in some places in the source. This will allow the PHP parser to recognize that code.

#### Setting Up The Development Server

The application is a PHP application. The source serves as the deployment package also. All is needed is to point the server to the application source/public directory. 

* Set up an Apache server with site configuration pointing to the source/public directory as the docroot for serving the content.
* Alternatively, use php built-in web server. 
`php -S 0.0.0.0:8080 -t source/public'