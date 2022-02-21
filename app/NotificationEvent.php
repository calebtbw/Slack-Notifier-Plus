<?php

namespace SS\SlackNotifierPlus\App;

use \WHMCS\Database\Capsule;

class NotificationEvent extends Connection {

    /**
     * Get admin uri
     *
     * @return string
     */
    public function AdminURI() {

        return \App::getSystemUrl() . \App::get_admin_folder_name() ;

    }

    /**
     * Run when module failed hook
     *
     * @param array $linkQuery
     * @param string $reason
     *
     * @return void
     */
    public function ModuleFailedEvent ( array $linkQuery, string $reason ) : void{

        $clientsservicesUrl = $this->AdminURI().'/clientsservices.php?userid='.$linkQuery['userid'].'&id='. $linkQuery['serviceid'];

        $messageBody ="Alert! Order failed to provision due to: ".$reason.". \n " .$clientsservicesUrl ." \n *Mark this message with a :heavy_check_mark: when solved*.";

        $message = (new Message())->username("WHMCS Bot")->text($messageBody);

        $this->call( 'chat.postMessage', $message->toArray() );

    }

    /**
     * Run on Slack cron files when found unpaid order send notification
     *
     * @param array $ordersID
     */
    public function UnpaidOrdersEvent(  array $orders ){

        $clientsservicesUrl = $this->AdminURI().'/clientsservices.php?';

        $messageBody ="Alert! These orders are pending activation. Please review these promptly \n ";

        foreach ( $orders as $order ){

            $serviesPorductID = $this->GetServesProductID($order['id'],$order['userid'] );

            $id = $order['id'];

            if( ! is_null( $serviesPorductID ) ){
                $id = $serviesPorductID->id;
            }

            $messageBody .= $clientsservicesUrl.'userid='.$order['userid'].'&id=' . $id ."\n";

        }

        $messageBody .= " *Mark this message with a :heavy_check_mark: when solved*.";

        $message = (new Message())->username("WHMCS Bot")->text($messageBody);

        $this->call( 'chat.postMessage', $message->toArray() );

    }

    /**
     * Get Serves Product ID By orderID & userID
     *
     * @param int $orderID
     * @param init $userID
     * @return mixed
     */
    public function GetServesProductID(int $orderID, int $userID ){

        if( empty( $orderID ) && empty( $userID ) ) return false;

        return Capsule::table('tblhosting')->select('id')->where([['orderid', '=', $orderID], ['userid', '=', $userID]])->first();

    }

}
