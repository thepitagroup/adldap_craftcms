# adldap_craftcms
Craft CMS ADLAP2 Plugin

Plugin made for craft CMS 2.6.* to allow LDAP authentication.

You would use the {% requireLogin %} on you template to force the login process to be triggered.

in the general.php config file add or modify the 'loginPath'=>'urlYouWantToUse'

Run composer.json found in the folder adldap which will create the vendor folders and files needed for the adldap2 library to function.


