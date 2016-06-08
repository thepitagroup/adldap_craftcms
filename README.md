# adldap_craftcms
Craft CMS ADLAP2 Plugin

Plugin made for craft CMS 2.6.* to allow LDAP authentication.

1) You would use the {% requireLogin %} on you template to force the login process to be triggered.

2) In the general.php config file add or modify the entry: 'loginPath'=>'login'

The login part is the actual template which should reside at the root on the you main template folder ( not the plugin template folder)

You can customize the login.twig file to meet you business needs
You can also change the name of the file to be whatever you want just make sure to reflect that change on the 'loginPath' in general.php

The main element of that login.twig file are the 
a.  {% set redirect = craft.session.returnUrl  %}
'''html
b. <pre><code><input type="hidden" name="action" value="adldap/login"></code></pre> <!-- this tells craft where to find the plugin/action -->

c. <code><input type="hidden" name="redirect" value="{{redirect}}"></code> <!-- this tells craft where to go back to once logged in -->

3) Run composer.json found in the folder adldap which will create the vendor folders and files needed for the adldap2 library to function.



