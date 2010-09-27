<?php

/**
 * Send SMSes in Moldova
 */
class HqSmsComponent extends SlComponent {

    public function standartizePhoneNoMd($phone) {
        $matches = array();
        Sl::krumo($phone);
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (preg_match("/^((00)?373|0)?((60|69|68|78|79|67)[0-9]{6})$/", $phone, $matches)) {
            return "373{$matches[3]}";
        }
    }

    public function send($to, $message, $params = array()) {
        $params += SlConfigure::read('Api.hqSms') + array(
            'encoding' => 'utf-8',
            //'flash' => false,
            //'idx' => 123,
            'test' => false,
            'details' => false,
            //'date' => time(),
            //'datacoding' => 'gsm',
        );

        if (is_array($to)) {
            $multiple = true;
            foreach ($to as $i => &$phone) {
                $phone = $this->standartizePhoneNoMd($phone);
                if (empty($phone)) {
                    unset($to[$i]);
                }
            }
            $params['to'] = implode(',', $to);
        } else {
            $multiple = false;
            $params['to'] = $this->standartizePhoneNoMd($to);
        }
        if (empty($params['to'])) {
            return;
        }

        $protocol = $params['secure'] && SlConfigure::read('Sl.options.sslTransport') ? 'https' : 'http';
        unset($params['secure']);
        
        if (strlen($params['password']) != 32) {
            $params['password'] = md5($params['password']);
        }

        $params['message'] = $message;

        App::import('Core', 'HttpSocket');
        $socket = new HttpSocket();
        Sl::krumo($params);
        $result = $socket->post(
            "$protocol://www.hqsms.com/api/send.do", $params
        );
        
        if ($multiple) {
            $result = explode(';', $result);
            foreach ($result as &$item) {
                $item = explode(':', $item);
            }
            return $result;
        } else {
            return explode(':', $result);
        }
    }
    
}
