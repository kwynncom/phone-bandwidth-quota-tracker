# phone-bandwidth-quota-tracker
Track your remaining phone bandwidth quota: see MB / day you can use, etc.

https://kwynn.com/t/9/09/pquo.php - running at


Notes on .htaccess

You may need to enable rewrite

ls /etc/apache2/mods-enabled | grep rewrite
# that shows nothing, so
sudo a2enmod rewrite
sudo service apache2 graceful

I think in /etc/apache2/sites-enabled/ssl.conf this also assumes something like:

<Directory /blah/www>
# ...
	AllowOverride All
# ...
</Directory>

I'm not sure you can use rewrite in .htaccess without the relevant AllowOverride or "All," but I'm pushing my memory on that matter.
