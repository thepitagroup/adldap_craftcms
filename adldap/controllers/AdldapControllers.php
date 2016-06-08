<?php
namespace Craft;

use Adldap\Adldap;

class AdldapController extends BaseController
{
 
 	protected $allowAnonymous = true;

        /**
         * Method to Authenticate user trying to login.
         * @throws \Adldap\Exceptions\Auth\BindException
         */
        public function actionLogin()
        {
            $this->requirePostRequest();

            $memberName = craft()->request->getRequiredPost('username');
            $memberPassword = craft()->request->getRequiredPost('password');
         
            $settings = craft()->plugins->getPlugin('adldap')->getSettings();
 
            $grphandle = $settings['group'];
        
            $config = [
                
                "domain_controllers"=>[$settings['domainControllers']],
                "base_dn"=>$settings['baseDN'],
                "admin_username"=>$settings['username'],
                "admin_password"=>$settings['password'],
                'ad_port' => $settings['port'],
                "account_suffix"=>$settings['accountSuffix'],
                "account_prefix"=>$settings['accountPrefix'],
                "admin_account_suffix"=>$settings['adminAccountSuffix'],
                "use_ssl"=>$settings['ssl'],
                "use_tls"=>$settings['tls'],
                "follow_referrals"=>$settings['referrals'],
                    
            ];
          
            $ldap = new Adldap();
            $provider = new \Adldap\Connections\Provider($config);
            $ldap->addProvider('default',$provider);
        
            try{
               $ldap->connect('default');
               $provider->auth()->bindAsAdministrator();
               $search = $provider->search();
                try {

                    if ($provider->auth()->attempt($memberName, $memberPassword)) {

                        $rec = $search->find($memberName);

                        $guid  = $this->_to_p_guid($rec['objectguid'][0]);

                        $query = craft()->db->createCommand();
                        $result = $query->select('id')->from('users')->where(['username'=>$memberName])->queryRow();
                        
                       // die("MB: ".$memberPassword);
                        //var_dump($result);exit;
                        if($result === false){
                            
                            $user = new UserModel;
                            $user->username  = $memberName;
                            $user->password  = $memberPassword;
                            //echo "USERNAME: ".$memberName;
                            $email =$rec['email'][0];
                            //var_dump($rec['email'][0]);
                            if(is_null($rec['email'][0])){
                                $email = $rec['userprincipalname'][0];
                            }
                           // echo "EMAIl: ".$email;
                            $user->email = $email;
                            $fname = $rec['givenname'][0];
                            if( is_null($rec['givenname'][0]) ){
                                $fname = $rec['cn'][0];
                            }
                            //echo"<br />FNAME: ".$fname;
                            $user->firstName = $fname;
                            $lname = $rec['sn'][0];
                            if(is_null($rec['sn'][0])){
                                $lname = $rec['cn'][0];
                            }
                            //echo"<br />LNAME: ".$lname;
                            $user->lastName = $lname;

                            $user->archived = 0;
                            $user->pending  = 0;
                            $user->suspended = 0;
                            $user->locked = 0;
                            $user->client = 0;
                            $user->admin = 0;
                            $user->weekStartDay = 0;
                            $user->passwordResetRequired = 0;
                            $user->lastLoginDate =date("Y-m-d H:i:s");
                            $user->dateCreated = date("Y-m-d H:i:s");
                            $user->dateUpdated = date("Y-m-d H:i:s");

                            try{
                                $id = craft()->users->saveUser($user);
                            } catch(\Exception $e){
                                 throw $e;
                            }
                            $q2 = craft()->db->createCommand();
                            $grp = $q2->select('id')->from('usergroups')->where(['handle'=>$grphandle])->queryRow();
                            //var_dump($grp);exit;
                            craft()->userGroups->assignUserToGroups($user->id, $grp['id']);


                            if (craft()->users->activateUser($user))
                            {
                                    craft()->userSession->setNotice(Craft::t('Successfully activated the user.'));
                            }
                            else
                            {
                                    craft()->userSession->setError(Craft::t('There was a problem activating the user.'));
                            }
                        }else {
                            $id = $result['id'];
                            $user = craft()->users->getUserById( $id );
                            $user->newPassword  = $memberPassword; //get password from post and submit it to get hashed 
                            craft()->users->saveUser($user); //save user 
                        }
                        craft()->userSession->loginByUserId($id,true);//LOGIN AS CRAFT USER CORRESPONDING TO username
                        $this->redirectToPostedUrl();//REDIRECT TO URL that the login from was called from.

                    } else {
                        // Credentials were incorrect.
                        craft()->userSession->setFlash('errorMessage', Craft::t('The Credentials given were not accepted / valid.'));
                    }

                } catch (\Adldap\Exceptions\Auth\UsernameRequiredException $e) {
                    // The user didn't supply a username.
                    //die("User did not provide a username");
                    craft()->userSession->setFlash('errorMessage', Craft::t('User did not provide username.'));
                } catch (\Adldap\Exceptions\Auth\PasswordRequiredException $e) {
                    // The user didn't supply a password.
                    craft()->userSession->setFlash('errorMessage', Craft::t('User did not provide password.'));
                     //die("User did not provide a password");
                }

            } catch (\Adldap\Exceptions\Auth\BindException $e) {
                craft()->userSession->setFlash('errorMessage', Craft::t('Can\'t bind to LDAP server.'));
                //die("Can't bind to LDAP server!");
            }

        
        }
        /**
         * Logout method return to home page with status QS
         */
        public function actionLogout()
        {
            craft()->userSession->logout(false);
            $this->redirect('?status=logout');
        }
        
        /**
         * Method to convert binary guid to string
         * @param binary $guid form AD
         * @return string
         */
        
        private function _to_p_guid( $guid )
        {
            $hex_guid = unpack( "H*hex", $guid );
            $hex    = $hex_guid["hex"];

            $hex1   = substr( $hex, -26, 2 ) . substr( $hex, -28, 2 ) . substr( $hex, -30, 2 ) . substr( $hex, -32, 2 );
            $hex2   = substr( $hex, -22, 2 ) . substr( $hex, -24, 2 );
            $hex3   = substr( $hex, -18, 2 ) . substr( $hex, -20, 2 );
            $hex4   = substr( $hex, -16, 4 );
            $hex5   = substr( $hex, -12, 12 );

            $guid = $hex1 . "-" . $hex2 . "-" . $hex3 . "-" . $hex4 . "-" . $hex5;

            return $guid;
        }


}
