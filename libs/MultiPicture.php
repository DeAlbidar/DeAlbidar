<?php
/**
 * Description of MultiPicture
 *
 * @author albidar
 */
class MultiPicture {
    
    public static function News($data){
        $body = MultiPicture::mediaNnews($data);
        return $body;
    }
    
    public static function mediaNnews($data){
        foreach ($data['news'] as $value) {
            $photo = $value['photoCount'];
            $audio = $value['audioCount'];
            $video = $value['videoCount'];
            $vid = $value['localVideoCount'];
            $description = $value['description'];
            $body = "";
            if($photo >= 1 || $audio > 0 || $video > 0 || $vid > 0){
                $body = MultiPicture::photo($description, $photo, $data);
                $body = MultiPicture::audio($body, $audio, $data);
                $body = MultiPicture::video($body, $video, $data);
                $body = MultiPicture::local_video($body, $vid, $data);
                return $body;
            } else{
                return $description;
            }
        }
    }

    public static function photo($desc, $count, $data) {
        /**
        * Multiple Image
        */
       if ($count >= 1) {
           $news = $desc;
            $track = array();
            $multiple_pic = array();
            foreach ($data['NewsPhoto'] as $photo) {
                $picture[] = $photo['file'];
                foreach ($picture as $k => $v) {
                    $track[] = '[pic' . ($k + 1) . ']';
                    $multiple_pic[] = '<figure><img src="' . URL . 'public/files/' . $v . '" onerror="this.onerror=null;this.src='."'".URL.'public/files/'.$v."'".';" class="img-responsive"  id="figureImage" alt="Foto"  /><figcaption>'.$photo['caption'].'</figcaption></figure>';
                }
                $body = str_replace($track, $multiple_pic, $news);
            }
            return $body;
       } else {
           return $desc;
       }
        
       /**
        * Multiple Image
        */
    }
    
    public static function audio($desc, $count, $data) {
        /**
        * Multiple Audio
        */
        if($count > 0){
            $news = $desc;
            $track = array();
            $multiple_audio = array();
            foreach ($data['NewsAudio'] as $val) {
                $audio[] = $val['file'];
                foreach ($audio as $k => $v) {
                    $track[] = '[audio' . ($k + 1) . ']';
                    $multiple_audio[] = '<figure><audio src="' . URL . 'public/files/' . $v . '"  onerror="this.onerror=null;this.src='."'".URL.'public/files/'.$v."'".';" controls="controls" style="width: 100%;" >Your browser does not support the audio tag.</audio><figcaption>'.$val['caption'].'</figcaption></figure>';
                }
                $body = str_replace($track, $multiple_audio, $news);
            }
            return $body;
        } else {
            return $desc;
        }
       /**
        * Multiple Audio
        */
    }
    
    public static function video($desc, $count, $data) {
        /**
        * Multiple Audio
        */
       if ($count > 0) {
           $news = $desc;
            $track = array();
            $multiple_video = array();
            foreach ($data['NewsVideo'] as $val) {
                $audio[] = $val['file'];
                foreach ($audio as $k => $v) {
                    $track[] = '[video' . ($k + 1) . ']';
                    $multiple_video[] = '<iframe src="https://www.youtube.com/embed/'.$v.'?autoplay=0" id="v_video" width="696" height="392" frameborder="0" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                }
                $body = str_replace($track, $multiple_video, $news);
            }
            return $body;
       } else {
           return $desc;
       }
       /**
        * Multiple Audio
        */
    }
    
    public static function local_video($desc, $count, $data) {
        /**
        * Multiple Local Video
        */
       if ($count > 0) {
           $news = $desc;
            $track = array();
            $multiple_video = array();
            foreach ($data['NewsLocalVideo'] as $val) {
                $audio[] = $val['file'];
                foreach ($audio as $k => $v) {
                    $track[] = '[vid' . ($k + 1) . ']';
                    //$multiple_video[] = '<video controls allowfullscreen width="100%" height="auto" ><source src="'.URL.'public/'.$v.'" type="video/mp4"><source src="'.URL.'public/'.$v.'" type="video/ogg">Your browser does not support the video tag.</video>';
                    $multiple_video[] = '<iframe src="' . URL . 'public/files/' . $v . '" onerror="this.onerror=null;this.src='."'".URL.'public/files/'.$v."'".';" id="v_video" width="696" height="392" frameborder="0" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                }
                $body = str_replace($track, $multiple_video, $news);
            }
            return $body;
       } else {
           return $desc;
       }
       /**
        * Multiple Local Video
        */
    }
}
