<?php
namespace Controllers\Form;

use AC\Response\Json;

class FormController{

    private $settings;
    private $email;
    private $subject;
    private $page;
    private $body='';
    private $headers;
    private $boundary;
    static $delimiter = "\r\n";

    public function __construct($data,$settings=[])
    {

        $this->settings  = !$settings?require_once('settings.php'):$settings;
        $this->boundary = "--".md5(uniqid(time()));

        $this->set_email();

        if(!$this->email)
        {
            wp_die('Email address is undefined');
        }

        $this->set_subject();


//        $this->set_page(!isset($data['url'])?'':$data['url']);

        $this->set_body($data);
    
        $this->set_header(
            [
                'From'=>get_bloginfo().' <'.get_bloginfo('admin_email').'>',
                'MIME-Version'=>'1.0;',
                'Content-Type'=>'multipart/mixed; boundary='.$this->boundary.''
            ]);


        $this->handler();
    }

    private function set_email()
    {
        $this->email = $this->settings['email'];
    }

    private function set_subject()
    {
        $this->subject = get_bloginfo();
    }

    private function set_page($page='')
    {
        $url =empty($page)?(((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']):$page ;

        $this->page = 'Отправлено со страницы: '.$url;
    }

    private function set_body($data)
    {
        $this->body = "--".$this->boundary.self::$delimiter;
        $this->body .= "Content-Type: text/html; charset=UTF-8".self::$delimiter;
        $this->body .= self::$delimiter;

        foreach ($data as $key=>$value)
        {
            $item = json_decode(stripslashes($value),true);
            if(isset($item['title']) && isset($item['value']))
            {
                $this->body.= $item['title'].': '.$item['value']. '<br>';
            }elseif ($key==='type')
            {

                $this->body.= 'Форма: '.$value. '<br>';
            }
        }
        
        $this->body.= $this->page;


        if(isset($_FILES) && !empty($_FILES))
        {

            $this->set_file(array_shift($_FILES));
        }
    }

    private function set_header($headers=[])
    {
        foreach ($headers as $key=>$item)
        {
            $this->headers .= $key.': '.$item. "\r\n";
        }
    }

    private function set_file($input_file)
    {

        $file_name = $input_file['name'];
        $file_path = $input_file['tmp_name'];

        $fp = fopen($file_path,"r");
        if (!$fp)
        {
            print "Не удается открыть файл для отправки";
            exit();
        }
        $file = fread($fp, filesize($file_path));
        fclose($fp);

        $message_part = self::$delimiter."--$this->boundary".self::$delimiter;
        $message_part .= "Content-Type: application/octet-stream; name=\"$file_name\"".self::$delimiter;
        $message_part .= "Content-Transfer-Encoding: base64".self::$delimiter;
        $message_part .= "Content-Disposition: attachment; filename=\"$file_name\"".self::$delimiter;
        $message_part .= "\r\n";
        $message_part .= chunk_split(base64_encode($file));
        $message_part .= self::$delimiter."--$this->boundary--".self::$delimiter;

        $this->body .=$message_part;

    }

    private function handler()
    {
        if($this->body == '')
            wp_die('Body empty');

        die(mail( $this->email,$this->subject,  $this->body, $this->headers));
    }
}