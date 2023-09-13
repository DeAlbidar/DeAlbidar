<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require MAILER_DIR . 'vendor/phpmailer/phpmailer/src/Exception.php';
require MAILER_DIR . 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require MAILER_DIR . 'vendor/phpmailer/phpmailer/src/SMTP.php';

/**
 * Description of libs
 *
 * @author albidar
 */
class Libs {

    public static function Email($recipient, $recipient_name, $subject, $message) {
        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {
            //Server settings
            //$mail->SMTPDebug = 2;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'mail.watchghana.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'no-reply@watchghana.com';                 // SMTP username
            $mail->Password = '<PAUSA0000>';                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 26;                                    // TCP port to connect to
            //Recipients
            $mail->setFrom('no-reply@watchghana.com', 'WatchGhana.com');
            $mail->addAddress($recipient, $recipient_name);     // Add a recipient
            $mail->addReplyTo('no-reply@watchghana.com', 'No Reply');
            $mail->addCC('cc@watchghana.com');
            $mail->addBCC('bcc@watchghana.com');

            //Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $message;
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }
    }

    public static function MinifierCSS($cssFiles) {
        /**
         * Ideally, you wouldn't need to change any code beyond this point.
         */
        $buffer = "";
        foreach ($cssFiles as $cssFile) {
            $buffer .= file_get_contents($cssFile);
        }
        // Remove comments
        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
        // Remove space after colons
        $buffer = str_replace(': ', ':', $buffer);
        // Remove whitespace
        $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
        $buffer = str_replace(', ', ',', $buffer);
        $buffer = str_replace(' ,', ',', $buffer);

        // Remove space before brackets
        $buffer = str_replace('{ ', '{', $buffer);
        $buffer = str_replace('} ', '}', $buffer);
        $buffer = str_replace(' {', '{', $buffer);
        $buffer = str_replace(' }', '}', $buffer);

        // Remove last dot with comma
        $buffer = str_replace(';}', '}', $buffer);
        // Enable GZip encoding.
        //ob_start("ob_gzhandler");
        // Enable caching
        header('Cache-Control: public');
        // Expire in one day
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
        // Set the correct MIME type, because Apache won't set it for us
        //header("Content-type: text/css");
        // Write everything out
        echo('<style>' . $buffer . '</style>');
    }

    public static function Input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    public static function Date($datetime, $full = false) {
        date_default_timezone_set(DateTimeZone::listIdentifiers(DateTimeZone::UTC)[0]);
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full)
            $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    public static function Date_Format($datetime) {
        return date('l F j, Y, g:i a', strtotime($datetime));
    }

    public static function VideoDuration($filename) {
        $getID3 = new getID3;
        $file = $getID3->analyze($filename);
        return $file['playtime_string'];    // returns the codec_name property
    }

    public static function Hash($algo, $data, $salt) {
        $context = hash_init($algo, HASH_HMAC, $salt);
        hash_update($context, $data);
        return hash_final($context);
    }

    public static function ImageCreate($word) {
        $width = strlen($word) * 9.3;
        $height = 20;
        $image = imagecreate($width, $height);
        $background = imagecolorallocate($image, 0, 0, 0);
        $foreground = imagecolorallocate($image, 255, 255, 255);
        imagestring($image, 5, 5, 1, $word, $foreground);
        header("Content-type: image/jpeg");
        imagejpeg($image);
    }

    public static function split_words($text, $length) {
        if (strlen($text) <= $length) {
            echo $text;
        } else {
            $y = substr($text, 0, $length) . '...';
            echo $y;
        }
    }

    public static function seoUrl($string) {
        //Lower case everything
        $string = strtolower($string);
        //Make alphanumeric (removes all other characters)
        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
        //Clean up multiple dashes or whitespaces
        $string = preg_replace("/[\s-]+/", " ", $string);
        //Convert whitespaces and underscore to dash
        $string = preg_replace("/[\s_]/", "-", $string);
        return $string;
    }

    public static function strip_word_html($string, $allowable_tags = '', $strip_attrs = false, $preserve_comments = false, callable $callback = null) {
        $str = html_entity_decode($string);
        $allowable_tags = array_map('strtolower', array_filter(// lowercase
                        preg_split('/(?:>|^)\\s*(?:<|$)/', $allowable_tags, -1, PREG_SPLIT_NO_EMPTY), // get tag names
                        function( $tag ) {
                    return preg_match('/^[a-z][a-z0-9_]*$/i', $tag);
                } // filter broken
        ));
        $comments_and_stuff = preg_split('/(<!--.*?(?:-->|$))/', $str, -1, PREG_SPLIT_DELIM_CAPTURE);
        foreach ($comments_and_stuff as $i => $comment_or_stuff) {
            if ($i % 2) { // html comment
                if (!( $preserve_comments && preg_match('/<!--.*?-->/', $comment_or_stuff) )) {
                    $comments_and_stuff[$i] = '';
                }
            } else { // stuff between comments
                $tags_and_text = preg_split("/(<(?:[^>\"']++|\"[^\"]*+(?:\"|$)|'[^']*+(?:'|$))*(?:>|$))/", $comment_or_stuff, -1, PREG_SPLIT_DELIM_CAPTURE);
                foreach ($tags_and_text as $j => $tag_or_text) {
                    $is_broken = false;
                    $is_allowable = true;
                    $result = $tag_or_text;
                    if ($j % 2) { // tag
                        if (preg_match("%^(</?)([a-z][a-z0-9_]*)\\b(?:[^>\"'/]++|/+?|\"[^\"]*\"|'[^']*')*?(/?>)%i", $tag_or_text, $matches)) {
                            $tag = strtolower($matches[2]);
                            if (in_array($tag, $allowable_tags)) {
                                if ($strip_attrs) {
                                    $opening = $matches[1];
                                    $closing = ( $opening === '</' ) ? '>' : $closing;
                                    $result = $opening . $tag . $closing;
                                }
                            } else {
                                $is_allowable = false;
                                $result = '';
                            }
                        } else {
                            $is_broken = true;
                            $result = '';
                        }
                    } else { // text
                        $tag = false;
                    }
                    if (!$is_broken && isset($callback)) {
                        // allow result modification
                        call_user_func_array($callback, array(&$result, $tag_or_text, $tag, $is_allowable));
                    }
                    $tags_and_text[$j] = $result;
                }
                $comments_and_stuff[$i] = implode('', $tags_and_text);
            }
        }
        $str = implode('', $comments_and_stuff);
        return Libs::strReVerseAssoc($str);
    }

    public static function crypto_rand_secure($min, $max) {
        $range = $max - $min;
        if ($range < 0) {
            return $min;
        }
        $log = log($range, 2);
        $bytes = (int) ( $log / 8 ) + 1;
        $bits = (int) $log + 1;
        $filter = (int) ( 1 << $bits ) - 1;
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter;
        } while ($rnd >= $range);
        return $min + $rnd;
    }

    public static function generateToken($length) {
        $token = '';
        $codeAlphabet = "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[Libs::crypto_rand_secure(0, strlen($codeAlphabet))];
        }
        return $token;
    }

    public static function getYoutubeImage($e) {
        //GET THE URL
        $url = $e;

        $queryString = parse_url($url, PHP_URL_QUERY);

        parse_str($queryString, $params);

        $v = $params['v'];
        //DISPLAY THE IMAGE URL
        if (strlen($v) > 0) {
            return "https://i3.ytimg.com/vi/$v/hqdefault.jpg";
        }
    }

    public static function getXmlFile($file) {
        $xml = simplexml_load_file(URL . "libs/xml/" . $file . ".xml") or die("Error: Cannot create object");
        return $xml;
    }

    public static function displayMessage($v, $message) {
        if ($v == "success") {
            $_SESSION['msg'] = '<div class="alert alert-success">
                        <strong>Success: </strong> ' . $message . '
                </div>';
        } elseif ($v == "danger") {
            $_SESSION['msg'] = '<div class="alert alert-danger">
                        <strong>Danger:</strong> ' . $message . '
                </div>';
        } elseif ($v == "info") {
            $_SESSION['msg'] = '<div class="alert alert-info">
                            <strong>Info:</strong> ' . $message . '
                    </div>';
        } elseif ($v == "warning") {
            $_SESSION['msg'] = '<div class="alert alert-warning">
                        <strong>Warning:</strong> ' . $message . '
                </div>';
        }
    }

    public static function unsetSession() {
        if (isset($_SESSION['msg'])) {
            unset($_SESSION['msg']);
        }
    }

    public static function strReplaceAssoc($string) {
        $replace = array(
            '@@bq_s@' => '<blockquote class="blockquote blockquote_style01">',
            '@@bq_e@' => '</blockquote>',
            '@@h_s@' => '<h2>',
            '@@h_e@' => '</h2>',
            '@@b_s@' => '<b>',
            '@@b_e@' => '</b>',
            '@@u_s@' => '<u>',
            '@@u_e@' => '</u>'
        );
        return str_replace(array_keys($replace), array_values($replace), $string);
    }

    public static function strReVerseAssoc($string) {
        $replace = array(
            '@@bq_s@' => '',
            '@@bq_e@' => '',
            '@@h_s@' => '',
            '@@h_e@' => '',
            '@@b_s@' => '',
            '@@b_e@' => '',
            '@@u_s@' => '',
            '@@u_e@' => '',
            '[pic1]' => '',
            '[pic2]' => '',
            '[pic3]' => '',
            '[pic4]' => '',
            '[pic5]' => '',
            '[pic6]' => '',
            '[pic7]' => '',
            '[pic8]' => '',
            '[pic9]' => '',
            '[pic10]' => '',
            '[audio1]' => '',
            '[audio2]' => '',
            '[audio3]' => '',
            '[audio4]' => '',
            '[audio5]' => '',
            '[audio6]' => '',
            '[audio7]' => '',
            '[audio8]' => '',
            '[audio9]' => '',
            '[audio10]' => '',
            '[video1]' => '',
            '[video2]' => '',
            '[video3]' => '',
            '[video4]' => '',
            '[video5]' => '',
            '[video6]' => '',
            '[video7]' => '',
            '[video8]' => '',
            '[video9]' => '',
            '[video10]' => ''
        );
        return str_replace(array_keys($replace), array_values($replace), $string);
    }

    public static function shorten($number) {
        $suffix = ["", "K", "M", "B"];
        $precision = 1;
        for ($i = 0; $i < count($suffix); $i++) {
            $divide = $number / pow(1000, $i);
            if ($divide < 1000) {
                return round($divide, $precision) . $suffix[$i];
                break;
            }
        }
    }

}
