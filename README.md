# adldap_craftcms
Craft CMS ADLAP2 Plugin

Plugin made for craft CMS 2.6.* to allow LDAP authentication.

You would use the {% requireLogin %} on you template to force the login process to be triggered.

In the general.php config file add or modify the entry: 'loginPath'=>'login'

The login part is the actual template which should reside at the root on the you main template folder ( not the plugin template folder)

You can customize the login.twig file to meet you business needs

Run composer.json found in the folder adldap which will create the vendor folders and files needed for the adldap2 library to function.


