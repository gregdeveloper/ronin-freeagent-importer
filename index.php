<?php
    session_start();

    require_once 'lib/PHP-OAuth2/Client.php';
    require_once 'lib/PHP-OAuth2/GrantType/IGrantType.php';
    require_once 'lib/PHP-OAuth2/GrantType/AuthorizationCode.php';

    require_once 'classes/API/Ronin.php';
    require_once 'classes/API/Freeagent.php';
    
    require_once 'classes/Import.php';

    /**
     * FREEAGENT
     */

    $freeagent = new API_Freeagent();

    $freeagent->identifier = ''; //Add your client_id
    $freeagent->secret = ''; //Add your secret

    //the URL of this script. doesn't have to be publicly accessible.
    $freeagent->script_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

    //the base URL of the API. shouldn't need to change this.
    //$freeagent->base_url = 'https://api.sandbox.freeagent.com/v2/'; //Sandbox
    $freeagent->base_url = 'https://api.freeagent.com/v2/'; //Production

    //create the OAuth client
    $freeagent->client = new OAuth2\Client(
            $freeagent->identifier,
            $freeagent->secret
        );

    if( isset($_SESSION['freeagent_access_token']) ){

        //when we have a token, just set up the client
        $freeagent->client->setAccessToken($_SESSION['freeagent_access_token']);
        $freeagent->client->setAccessTokenType(OAuth2\Client::ACCESS_TOKEN_BEARER);

    }else{

        //check what stage we're at
        if (empty($_GET['code']) && empty($_GET['token'])) {

            //no code and no token so redirect user to FreeAgent to log in
            $auth_url = $freeagent->client->getAuthenticationUrl(
                    $freeagent->base_url . 'approve_app', $freeagent->script_url
                );

            header('Location: ' . $auth_url);

        } elseif (isset($_GET['code'])) {

            //we have a code so use it to get an access token
            $response = $freeagent->client->getAccessToken(
                $freeagent->base_url . 'token_endpoint',
                'authorization_code',
                array('code' => $_GET['code'], 'redirect_uri' => $freeagent->script_url)
            );

            //normally you would store the token for use in future requests
            $token = $response['result']['access_token'];

            $_SESSION['freeagent_access_token'] = $token;

            header('Location: ' . $freeagent->script_url);
            //header('Location: ' . $freeagent->script_url . '?token=' . $token);

        }

    }

    /**
     * RONIN
     */

    $ronin = new API_Ronin();

    $ronin->token = ''; //Add your Ronin access token
    $ronin->domain = 'https://f8creates.roninapp.com/';
    
    /**
     * IMPORT
     */
    
    $import = new Import();
    $import->freeagent = $freeagent;
    $import->ronin = $ronin;

?>
<!DOCTYPE html>
<html>
<head>
    
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    
    <title></title>
    
</head>
<body>
    
    <?php
    
        if( isset($_GET['execute']) && $_GET['execute'] === 'true' ) {
            
            $test = false;
        }else{
            
            $test = true;
        }
    
        if( isset($_GET['action']) ) {
            
            switch($_GET['action']) {
                
                case 'contacts':
                    
                    echo '<h1>Contacts Import</h1>';

                    $response = $import->contacts($test);
                    
                    if($test){ ?>
    
                        <pre>
                            <?=print_r($response, true)?>
                        </pre>
                        <a href="?action=<?=$_GET['action']?>&execute=true">
                            Execute
                        </a>
    
                    <?php }else{ ?>
                        
                        <p>
                            <?=print_r($response, true)?>
                        </p>
                        
                    <?php }
                    
                    break;
                
                case 'estimates':
                    
                    echo '<h1>Estimates Import</h1>';

                    $response = $import->estimates($test);
                    
                    if($test){ ?>
                        
                        <pre>
                            <?=print_r($response, true)?>
                        </pre>
                        <a href="?action=<?=$_GET['action']?>&execute=true">
                            Execute
                        </a>
    
                    <?php }else{ ?>
                        
                        <p>
                            <?=print_r($response, true)?>
                        </p>
                        
                    <?php }
                    
                    break;
                
                case 'invoices':
                    
                    echo '<h1>Invoices Import</h1>';

                    $response = $import->invoices($test);

                    if($test){ ?>

                        <pre>
                            <?=print_r($response, true)?>
                        </pre>
                        <a href="?action=<?=$_GET['action']?>&execute=true">
                            Execute
                        </a>
    
                    <?php }else{ ?>
                        
                        <p>
                            <?=print_r($response, true)?>
                        </p>
                        
                    <?php }
                    
                    break;
                
                case 'projects':
                    
                    echo '<h1>Projects Import</h1>';

                    $response = $import->projects($test);
                    
                    if($test){ ?>
                        
                        <pre>
                            <?=print_r($response, true)?>
                        </pre>
                        <a href="?action=<?=$_GET['action']?>&execute=true">
                            Execute
                        </a>
    
                    <?php }else{ ?>
                        
                        <p>
                            <?=print_r($response, true)?>
                        </p>
                        
                    <?php }
                    
                    break;
                    
            }
            
        }else{ ?>
            
            <ul>

                <li>
                    <a href="?action=contacts">
                        Contacts
                    </a>
                </li>

                <li>
                    <a href="?action=estimates">
                        Estimates
                    </a>
                </li>

                <li>
                    <a href="?action=projects">
                        Projects
                    </a>
                </li>

                <li>
                    <a href="?action=invoices">
                        Invoices
                    </a>
                </li>
                
            </ul>
    <?php } ?>
        
</body>
</html>
