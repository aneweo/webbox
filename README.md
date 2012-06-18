Webbox
======
A project to practice PHP  
A very simple version of Dropbox for private collaboration use

Usage:
------
There will be two folders for each authorized user: 
* Public (available for everyone to view and download)
* Private (with private key)

TODO:
----
* Login authentication (possibly using LDAP) &#x2713;
* Test multiple logins and uploadings &#x2713;
* Set up the database to keep track of uploaders and their uploading &#x2713;
* Only uploader can delete their own files (&#x2713;) and grant access to people by their emails &#x2713;
* Private feature &#x2713;
* Other features like granting other users Read-only/Write & Read access

Author:
-------
Bao Pham

Reference:
---------
Thank you:  
http://www.evoluted.net/thinktank/web-development/php-directory-listing-script  
http://buildinternet.com/2009/12/creating-your-first-php-application-part-1/  
http://samjlevy.com/2010/09/php-login-script-using-ldap-verify-group-membership/  
http://911-need-code-help.blogspot.ca/2009/06/generate-random-strings-using-php.html  

PHP Extensions:
-----------
This project uses the following extensions: LDAP, zip, mysql
