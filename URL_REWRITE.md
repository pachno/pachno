URL REWRITING
=============

Pachno uses URL rewriting to make URLs look more readable.
URL rewriting is what makes it possible to, instead of using URLs such as:

    viewissue.php?project_key=projectname&issue_id=123

use URLs such as:

    /projectname/issue/123.

It is important that Pachno and your web server is correctly set up
with url rewriting enabled for this to work.

You can read more about setting up URL rewriting, here:
* http://httpd.apache.org/docs/2.2/mod/mod_rewrite.html (Apache)
* http://support.microsoft.com/kb/324000/ (IIS)

We also provide documentation for setting up rewriting on other web servers
at https://projects.pachno.com/pachno/docs/categories/howto

For Apache, it is enough that the rewrite module (mod_rewrite) is installed
and enabled, and that the virtual host setup has set `AllowOverride All`
for the folder Pachno is located. With this setup, Apache should use
the `.htaccess` file located inside the `public/` folder.

If you for any reason cannot turn on `AllowOverride All` for that folder, look at
the `.htaccess` file Pachno bundles (located inside the `public/`
folder), and copy the necessary lines to your virtual host definition.


EXAMPLES
--------

### EXAMPLE 1: Virtualhost config

Pachno is installed in `/var/www/pachno`, and I want to
set up a virtual host for Pachno.

#### Apache setup

Set up the virtual host as usual, but point the `DocumentRoot`
for Pachno to the `public/` subfolder inside the main folder.
Make sure the apache virtual host setup has `AllowOverride All` for the folder
where Pachno is located, and make sure the `.htaccess` file inside
the `public/` folder is accessible to Apache.

If this is a permanent setup, you may also want to copy the .htaccess directives
into the virtual host setup after verifying the installation works as expected.

#### Pachno setup 

Set the hostname to the public hostname where you plan
to access Pachno. With this setup, Pachno will be located at the
top level, so set the URL subdirectory to `/`, which means "top level".


### EXAMPLE 2: Subfolder config

Pachno is installed in /var/www/pachno, and I want to
access it as a subfolder of the DocumentRoot, which is `/var/www`

#### Apache setup 

Make sure the apache host setup has `AllowOverride All` for the
folder Pachno is located, and make sure the .htaccess file inside the
`public/` folder is accessible to Apache. You may want to copy the main
folder content to a folder one level up (extract the main content of
the top `pachno/` folder directly to `/var/www`), so that
the `public/` folder inside the main folder is accessible
as `/var/www/public`.

#### Pachno setup

Set the hostname to the public hostname where you plan to
access Pachno. With this setup, Pachno will be located at either
__http://hostname/pachno/public/__ or __http://hostname/public/__
(see above), so set the URL subdirectory to `/`, which means "top level".
