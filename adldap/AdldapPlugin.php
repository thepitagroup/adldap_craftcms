<?php
namespace Craft;

class AdldapPlugin extends BasePlugin
{
    // =========================================================================
    // PLUGIN INFO
    // =========================================================================

    public function getName()
    {
        return Craft::t('ADLDAP');
    }

    public function getVersion()
    {
        return '0.0.1';
    }

    public function getSchemaVersion()
    {
        return '1.0.0';
    }

    public function getDeveloper()
    {
        return 'The Pita Group';
    }

    public function getDeveloperUrl()
    {
        return 'http://thepitagroup.com';
    }

    public function getPluginUrl()
    {
        return 'https://github.com/thepitagroup/adldap_craftcms';
    }

    public function getDocumentationUrl()
    {
        return $this->getPluginUrl() . '/blob/master/README.md';
    }

    public function getReleaseFeedUrl()
    {
        return 'https://raw.githubusercontent.com/thepitagroup/adldap_craftcms/master/changelog.json';
    }

    public function hasCpSection()
    {
        return false;
    }


    public function getSettingsHtml()
    {
        return craft()->templates->render('adldap/settings', [
            'settings' => $this->getSettings()
        ]);
    }

    protected function defineSettings()
    {
        return [
            "group"                 => AttributeType::String,
            "accountSuffix"         => AttributeType::String,
            "adminAccountSuffix"    => AttributeType::String,
            "accountPrefix"         => AttributeType::String,
            "baseDN"                => AttributeType::String,
            "domainControllers"     => AttributeType::String,
            "username"              => AttributeType::String,
            "password"              => AttributeType::String,  
            "ssl"                   => [AttributeType::Bool,'default'=>false],
            "tls"                   => [AttributeType::Bool,'default'=>false],
            "referrals"             => [AttributeType::Bool,'default'=>false],
            "port"                  => [AttributeType::Number,'default'=>389],     
        ];
    }

    public function registerSiteRoutes()
    {
       return [
           'adlogout'=>['action'=>'adldap/logout']
       ];
    }
    
    public function registerCpRoutes()
    {
        return [
            'adldap' => ['action' => 'adldap/dashboard'],
        ];
    }

    public function onAfterInstall()
    {
            craft()->plugins->savePluginSettings($this);
        
    }

    public function init()
    {
         require_once (CRAFT_PLUGINS_PATH.'/adldap/vendor/autoload.php');
         //AdldapPlugin::log('ADLDAP START');
    }


}
