# adldap_craftcms
Craft CMS ADLAP2 Plugin

Plugin made for craft CMS 2.6.* to allow LDAP authentication on the FRONT-END of your web application.

0.  Like any other craft plugin copy the adldap folder into your plugin folder.

1.  You will use the {% requireLogin %} on the template you want to password protect via ldap and to force the login           process to be triggered.

2.  In the general.php config file add or modify the entry: 'loginPath'=>'login'
    The login part is the actual template name (login.twig) which should reside at the root on the you main template folder (not the plugin template folder)

    You can customize the login.twig file to meet you business needs (Styling and add other fields).
    You can also change the name of the file to be whatever you want, just make sure to reflect that change on the 'loginPath' in general.php

    The main element of that login.twig file are the
    
      a.  {% set redirect = craft.session.returnUrl  %}
      
      b.  < input type="hidden" name="action" value="adldap/login" ><!-- this tells craft where to find the plugin/action -->
      
      c.  < input type="hidden" name="redirect" value="{{redirect}}" > <!-- this tells craft where to go back to once logged in -->

3.  Run composer.json found in the folder adldap which will create the vendor folders and files needed for the adldap2         library to function (composer update).

4.  The AdldapPlugin file has most of the config properties handled. I will, after being installed in Craft CMS, create a      form allowing admin to configure the setting needed to connect to LDAP Provider.
    
    The main element of this file are the:

    a.  init method which point to the autoload file created by composer.
    
    b.  registerSiteRoutes method allows the developer to  change the route to the logout method in plugin. The default is     "adlogout".

5.  The AdldapController file has the logic for the connection, authentication to the ldap. 

    a.  The action of login in via ldap will create a CRAFT CMS user as well if the user does not exist, otherwise it will     update the password.
    
    b.  It pulls in the group handle that you set in the settings page and uses it to add to the login in user.
    
    c.  It will then use that CRAFT CMS user and fake as if the ldap user is using that to login.



