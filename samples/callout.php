<?php
/**
 * VMware vCloud SDK for PHP
 *
 * PHP version 5
 * *******************************************************
 * Copyright VMware, Inc. 2010-2013. All Rights Reserved.
 * *******************************************************
 *
 * @category    VMware
 * @package     VMware_VCloud_SDK
 * @subpackage  Samples
 * @author      Ecosystem Engineering
 * @disclaimer  this program is provided to you "as is" without
 *              warranties or conditions # of any kind, whether oral or written,
 *              express or implied. the author specifically # disclaims any implied
 *              warranties or conditions of merchantability, satisfactory # quality,
 *              non-infringement and fitness for a particular purpose.
 * @SDK version 5.5.0
 */
require_once dirname(__FILE__) . '/config.php';

/**
 * Sample for callout feature. It can configure AMQP settings in vCloud Director
 * and show how to get notification message from an AMQP compliant queue. 
 */
// Get parameters from command line
$shorts  = "";
$shorts .= "s:";
$shorts .= "u:";
$shorts .= "p:";
$shorts .= "v:";

$shorts .= "a::";
$shorts .= "b::";
$shorts .= "c::";
$shorts .= "d::";
$shorts .= "e::";
$shorts .= "f::";
$shorts .= "g::";
$shorts .= "h::";
$shorts .= "i::";
$shorts .= "j::";
$shorts .= "k::";
$shorts .= "l::";
$shorts .= "m::";
$shorts .= "n";
$shorts .= "o::";
$shorts .= "t";
$shorts .= "q:";


$longs  = array(
    "server:",    //-s|--server    [required]
    "user:",      //-u|--user      [required]
    "pswd:",      //-p|--pswd      [required]
    "sdkver:",    //-v|--sdkver    [required]
    "host::",     //-a|--host      [required when do settings or pulling messages]
    "port::",     //-b|--port
    "ruser::",    //-c|--ruser
    "rpswd::",    //-d|--rpswd
    "vhost::",    //-e|--vhost
    "act::",      //-f|--act
    "org::",      //-g|--org       [required when needs to filter message]
    "op::",       //-h|--op        [required when needs to filter message]
    "ex::",       //-i|--ex
    "type::",     //-j|--type
    "que::",      //-k|--que
    "key::",      //-l|--key
    "msg::",      //-m|--msg
    "set",        //-n|--set
    "tuser",      //-o|--tuser
    "test",       //-t|--test
    "certpath:",  //-q|--certpath  [optional] local certificate path
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$rhost = '';      // e.g. '10.20.140.120'
$rport = '5672';
$ruser = 'guest';
$rpswd = 'guest';
$sdkversion = null;
$rvhost = '/';
$action = 'resume'; // action name, resume/abort/fail
$orgName = null;    // e.g., 'DefaultOrg'
$op = null;         // e.g., 'vdcUpdateTemplate'
$exchange = 'systemExchange';
$exType = AMQP_EX_TYPE_FANOUT;
$queue = 'myQueue';
$rkey = 'routing.key';  // routing key
$message = "test";      // action reason message
$useSSL = false;
$set = null;       // if set, will do AMQP settings on vCloud Director side.
                   // otherwise, the sample does pull notification message from
                   // the queue to process.
$test = null;
$tuser = null;
$certPath = null;

// loop through command arguments
foreach (array_keys($opts) as $opt) switch ($opt)
{
    case "s":
        $server = $opts['s'];
        break;
    case "server":
        $server = $opts['server'];
        break;

    case "u":
        $user = $opts['u'];
        break;
    case "user":
        $user = $opts['user'];
        break;

    case "p":
        $pswd = $opts['p'];
        break;
    case "pswd":
        $pswd = $opts['pswd'];
        break;

    case "v":
        $sdkversion = $opts['v'];
        break;
    case "sdkver":
        $sdkversion = $opts['sdkver'];
        break;

    case "a":
        $rhost = $opts['a'];
        break;
    case "host":
        $rhost = $opts['host'];
        break;
        
    case "b":
        $rport = $opts['b'];
        break;
    case "port":
        $rport = $opts['port'];
        break;

    case "c":
        $ruser = $opts['c'];
        break;
    case "ruser":
        $ruser = $opts['ruser'];
        break;

    case "d":
        $rpswd = $opts['d'];
        break;
    case "rpswd":
        $rpswd = $opts['rpswd'];
        break;

    case "e":
        $rvhost = $opts['e'];
        break;
    case "vhost":
        $rvhost = $opts['vhost'];
        break;

    case "f":
        $action = $opts['f'];
        break;
    case "act":
        $action = $opts['act'];
        break;

    case "g":
        $orgName = $opts['g'];
        break;
    case "org":
        $orgName = $opts['org'];
        break;

    case "h":
        $op = $opts['h'];
        break;
    case "op":
        $op = $opts['op'];
        break;

    case "i":
        $exchange = $opts['i'];
        break;
    case "ex":
        $exchange = $opts['ex'];
        break;

    case "j":
        $exType = $opts['j'];
        break;
    case "type":
        $exType = $opts['type'];
        break;

    case "k":
        $queue = $opts['k'];
        break;
    case "que":
        $queue = $opts['que'];
        break;

    case "l":
        $rkey = $opts['l'];
        break;
    case "key":
        $rkey = $opts['key'];
        break;

    case "m":
        $message = $opts['m'];
        break;
    case "msg":
        $message = $opts['msg'];
        break;

    case "n":
        $set = true;
        break;
    case "set":
        $set = true;
        break;

    case "o":
        $tuser = $opts['o'];
        break;
    case "tuser":
        $tuser = $opts['tuser'];
        break;

    case "t":
        $test = true;
        break;
    case "test":
        $test = true;
        break;

    case "q":
        $certPath = $opts['q'];
        break;
    case "certpath":
        $certPath = $opts['certpath'];
        break;
}

// parameters validation
if ((!isset($server) || !isset($user) || !isset($pswd) || !isset($sdkversion)) ||
    ((true !== $test && true !== $set) &&
    (!isset($rhost) || !isset($orgName) || !isset($op))))
{
    echo "Error: missing required parameters\n";
    usage();
    exit(1);
}

$flag = true;
if (isset($certPath))
{
    $cert = file_get_contents($certPath);
    $data = openssl_x509_parse($cert);
    $encodeddata1 = base64_encode(serialize($data));

    // Split a server url by forward back slash
    $url = explode('/', $server);
    $url = end($url);

    // Creates and returns a stream context with below options supplied in options preset
    $context = stream_context_create();
    stream_context_set_option($context, 'ssl', 'capture_peer_cert', true);
    stream_context_set_option($context, 'ssl', 'verify_host', true);

    $encodeddata2 = null;
    if ($socket = stream_socket_client("ssl://$url:443/", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context))
    {
        if ($options = stream_context_get_options($context))
        {
            if (isset($options['ssl']) && isset($options['ssl']['peer_certificate']))
            {
                $x509_resource = $options['ssl']['peer_certificate'];
                $cert_arr = openssl_x509_parse($x509_resource);
                $encodeddata2 = base64_encode(serialize($cert_arr));
            }
        }
    }

    // compare two certificate as string
    if (strcmp($encodeddata1, $encodeddata2)==0)
    {
        echo "\n\nValidation of certificates is successful.\n\n";
        $flag=true;
    }
    else
    {
        echo "\n\nCertification Failed.\n";
        $flag=false;
    }
}

if ($flag==true)
{
    if (!isset($certPath))
    {
        echo "\n\nIgnoring the Certificate Validation --Fake certificate - DO NOT DO THIS IN PRODUCTION.\n\n";
    }
    $service = VMware_VCloud_SDK_Service::getService();
    $service->login($server, array('username'=>$user, 'password'=>$pswd), $httpConfig, $sdkversion);
    $sdkExt = $service->createSDKExtensionObj();

    // set AMQP settings in vCloud Director
    if (true === $set)
    {
        $sets = new VMware_VCloud_API_Extension_AmqpSettingsType();
        $sets->setAmqpHost($rhost);
        $sets->setAmqpPort($rport);
        $sets->setAmqpUsername($ruser);
        $sets->setAmqpPassword($rpswd);
        $sets->setAmqpExchange($exchange);
        $sets->setAmqpVHost($rvhost);
        $sets->setAmqpUseSSL($useSSL);
        $nsets = $sdkExt->updateAmqpSettings($sets);
        echo $nsets->export() . "\n";
        exit(0);
    }

    // test AMQP connection
    if (true === $test)
    {
        $settings = $sdkExt->getAmqpSettings();
        $settings->setAmqpPassword($rpswd);
        $conn = $sdkExt->testAmqpConnection($settings);
        if ($conn)
        {
            exit("AMQP Connection Succeeded\n");
        }
        exit("AMQP Connection Failed\n");
    }

    $tmps = explode('@', $user);
    $owner = isset($tuser) ? $tuser : $tmps[0];

    // RabbitMQ server login info
    $login = array(
        'host'=>$rhost,
        'port'=>$rport,
        'vhost'=>$rvhost,
        'login'=>$ruser,
        'password'=>$rpswd,
    );

    // Establish a connection with the AMQP broker
    $conn = new AMQPConnection($login);
    $conn->connect();

    // assume the AMQP broker has the specified exchange configured.
    #$ex = new AMQPExchange($conn);
    #$ex->declare($exchange, $exType);

    $q = new AMQPQueue($conn, $queue); // create a queue, if exsiting, do nothing.
    $q->declare();
    $q->bind($exchange, $rkey);  // binds the queue to the routing key on the exchange
    #$ex->bind($queue, $rkey);

    // this sample will keep on running until retrieves expected notification message.
    while (true)
    {
        echo "Please wait ...\n";
        sleep(10); // get message from the queue in every 10 seconds.
        $ret = $q->get();
        if (array_key_exists('msg', $ret))
        {
            $msg = $ret['msg']; // get the message
        }
        if (isset($msg))
        {
            echo $msg . "\n";
            // Get Notification data object from the notification message.
            $not = VMware_VCloud_API_Helper::parseString($msg);
            if ('VMware_VCloud_API_Extension_NotificationType' == get_class($not))
            {
                // use the entity URL to create an SDK blocking task object.
                $entityLink = getEntityLink('entity', $not);
                $orgLink = getEntityLink('up', $not);
                $userLink = getEntityLink('down', $not);
                echo "entity = " . $entityLink->get_name() . "\n";
                echo "org = " . $orgLink->get_name() . "\n";
                echo "user = " . $userLink->get_name() . "\n";
                // filter the notification message for this sample.
                if ((isset($entityLink) && $op == $entityLink->get_name()) &&
                    (isset($orgLink) && $orgName == $orgLink->get_name()) &&
                    (isset($userLink) && $owner == $userLink->get_name()))
                {
                    echo "message:\n $msg\n";
                    // get the blocking task vCloud entity ID
                    $blockingTaskId = $entityLink->get_id();
                    // get the blocking task resource URL from its entity ID
                    $blockingTaskUrl = VMware_VCloud_SDK_Helper::
                                       getUrlByEntityId($service, $blockingTaskId);
                    // create an SDK blocking task object
                    $sdkBlockingTask = $service->createSDKObj($blockingTaskUrl);

                    // create a parameter for action on the blocking task
                    $params = new VMware_VCloud_API_Extension_BlockingTaskOperationParamsType();
                    $params->setMessage($message); // a message for action reason
                    try
                    {
                        // take a resume/abort/fail action on the blocking task
                        $sdkBlockingTask->$action($params);
                        echo "Action $action finished\n";
                        break;
                    }
                    catch (Exception $e)
                    {
                        echo $e->getMessage() . "\n";
                        continue; // if failed, continue listening
                    }
                    $q->purge($queue); // optionally, purge the contents in the queue
                }
            }
        }
    }
    // end of while loop

    //$q->delete($queue);  // optionally, delete queue and its contents.
    $conn->disconnect();   // disconnect from the AMQP broker.
}
else
{
    echo "\n\nLogin Failed due to certification mismatch.";
    return;
}

function getEntityLink($rel, $obj, $method='getEntityLink')
{
    $links = VMware_VCloud_SDK_Helper::getContainedLinks(null, $rel, $obj,
                                                        $method);
    return (1 == count($links))? $links[0] : null;
}

/**
 * Print the help message of the sample.
 */
function usage()
{
    echo "Usage:\n\n";
    echo "  [Description]\n";
    echo "     This sample demonstrates VMware vCloud Director callout feature.\n";
    echo "     Before running this sample, you should have a RabbitMQ Server and\n";
    echo "     the PHP AMQP extension installed, configured and started. vCloud\n";
    echo "     Director should have at least one blocking task operation enabled in\n";
    echo "     order to get notification message. For example, the vdcUpdateTemplate\n";
    echo "     operation. See VMware_VCloud_SDK_Extension_TaskOps class for the supported\n";
    echo "     task operations. You can use enableblockingtasks.php sample or vCloud\n";
    echo "     Director GUI to enable blocking task operations.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php callout.php -s <server> -u <username> -p <password> -v <sdkversion> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname>        [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>             [req] User name in the form user@organization\n";
    echo "                                             for the vCloud Director.\n";
    echo "     -p|--pswd <password>             [req] Password for user.\n";
    echo "     -v|--sdkver <sdkversion>         [req] SDK Version e.g. 1.5, 5.1 and 5.5.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--host <rbtHost>              [opt] RabbitMQ server host IP. Required when need to set the\n";
    echo "                                             AMQP broker or need to get notification from the AMQP broker.\n";
    echo "     -b|--port <rbtPort>              [opt] RabbitMQ server listening port. Default is 5672.\n";
    echo "     -c|--ruser <rbtUser>             [opt] User name of RabbitMQ server. Default is guest.\n";
    echo "     -d|--rpswd <rbtPswd>             [opt] Password of RabbitMQ server. Default is guest.\n";
    echo "     -e|--vhost <vhost>               [opt] Virtual host. Default is /. \n";
    echo "     -f|--act <resume/abort/fail>     [opt] Action to be taken on the blocked task. Default is resume.\n";
    echo "     -g|--org <orgName>               [opt] Name of the organization that owns the task. Reqired when \n";
    echo "                                             needs to filter the notification.\n";
    echo "     -h|--op <op>                     [opt] Name of the blocking task operation. It should be enabled\n";
    echo "                                             in vCloud Director for the blocking notification.\n";
    echo "                                             Reqired when needs to filter the notification\n";
    echo "     -i|--ex <exchange>               [opt] Name of the exchange. Default is systemExchange.\n";
    echo "     -j|--type <type>                 [opt] Exchange type. Default is AMQP_EX_TYPE_FANOUT.\n";
    echo "     -k|--que <queue>                 [opt] Name of the queue. Default is myQueue.\n";
    echo "     -l|--key <key>                   [opt] Routing key used by the queue to bind to the exchange.\n";
    echo "     -m|--msg <msg>                   [opt] Message describes the reason of the action.\n";
    echo "     -n|--set                         [opt] For configuring AMQP settings in vCloud Director. When set,\n";
    echo "                                             the sample will not be pulling notification messages.\n";
    echo "     -o|--tuser <tuser>               [opt] User of the blocking task.\n";
    echo "     -t|--test                        [opt] Test AMQP connection.\n";
    echo "     -q|--certpath <certificatepath>  [opt] Local certificate's full path.\n";
    echo "\n";
    echo "   See PHP AMQP extension documentation: http://www.php.net/manual/en/book.amqp.php\n";
    echo "   The PHP AMQP extension get be downloaded from http://pecl.php.net/package/amqp\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php callout.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a=10.20.120.140 -g=DefaultOrg -h=vdcUpdateTemplate\n";
    echo "     # php callout.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a=10.20.120.140 -g=DefaultOrg -h=vdcUpdateTemplate -q certificatepath\n";
    echo "     # php callout.php -a=10.20.120.140 -g=DefaultOrg -h=vdcUpdateTemplate  // using config.php to set login credentials\n";
    echo "     # php callout.php -a=10.20.120.140 -g=DefaultOrg -h=vdcUpdateTemplate -o=system\n";
    echo "     # php callout.php -a=10.20.120.140 -n  // set the AMQP broker in vCloud Director\n";
    echo "     # php callout.php -n  // unset the AMQP broker in vCloud Director\n";
    echo "     # php callout.php -t  // test the AMQP connection\n\n";
}
?>
