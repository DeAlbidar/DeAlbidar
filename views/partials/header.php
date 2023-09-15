<!DOCTYPE html> 
<html lang="en"> 
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <head> 
        <base href="<?= (isset($this->url)) ? $this->url : 'https://www.dealbidar.com/' ?>" />        
        <meta charset="UTF-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="robots" content="index, follow, all" />
        <title><?= (isset($this->title)) ? $this->title : 'Welcome | dealbidar.com' ?></title>
        <meta name="description" content="<?= (isset($this->description)) ? $this->description : '' ?>"> 
        <meta name="title" content="<?= (isset($this->title)) ? $this->title : 'Welcome | dealbidar.com' ?>"> 
        <meta name="keywords" content="<?= (isset($this->keywords)) ? $this->keywords : '' ?>" />
        <!-- Mobile Metas -->         
        <meta name="author" content="<?= (isset($this->author)) ? $this->author : 'Ebenezer Albidar Narh | dealbidar.com' ?>" />
        <meta name="twitter:card" content="summary" />
        <meta name="twitter:site" content="@dealbidar" />
        <meta name="twitter:creator" content="@W3 Multimedia Ghana Limited" />
        <meta property="og:site_name"   content="dealbidar" />
        <meta property="fb:pages" content="1270250933001043" />
        <meta property="fb:admins" content="1353719048088166">
        <meta property="article:publisher" content="https://www.facebook.com/dealbidar" />
        <meta property="og:type"          content="article" />
        <meta property="og:image:type"   content="image/jpeg" />
        <meta property="og:image:type"   content="image/png" />
        <meta property="og:image:type"   content="image/gif" />
        <meta property="og:image:width"   content="1200px" />
        <meta property="og:image:height"   content="630px" />
        <meta content='yes' name='mobile-web-app-capable'/>
        <meta content='yes' name='apple-mobile-web-app-capable'/>
        <meta content='#395697' name='apple-mobile-web-app-status-bar-style'/>
        <meta content='WatchGhana Official' name='application-name'/>
        <meta content='WatchGhana Official' name='msapplication-tooltip'/>
        <meta content='WatchGhana Official' name='apple-mobile-web-app-title'/>
        <meta content='WatchGhana Official' property='og:site_name'/>
        <meta content='en_US' property='og:locale'/>
        <meta content='article' property='og:type'/>
        <meta content="<?= (isset($this->title)) ? $this->title : 'Welcome | dealbidar.com' ?>" name='twitter:title' property='og:title'/>
        <meta content="<?= (isset($this->image)) ? $this->image : 'https://www.dealbidar.com/public/assets/images/bg/bg-image-11.jpg' ?>" name='twitter:image' property='og:image'/>
        <meta content="<?= (isset($this->description)) ? $this->description : 'Welcome | dealbidar.com' ?>" name='twitter:description' property='og:description'/>
        <meta content="https://web.facebook.com/dealbidar/" property='article:author'/>
        <meta property="og:video" content="<?= (isset($this->meta_video)) ? $this->meta_video : 'https://www.youtube.com/watch?v=Vc3qvxm9E90' ?>" />
        <meta name="keywords" content="<?= (isset($this->keywords)) ? $this->keywords : 'W3 Multimedia Ghana Limited' ?>" />
        <meta name="description" content="<?= (isset($this->description)) ? $this->description : 'Welcome | dealbidar.com' ?>" />
        <meta property="og:url"  content="<?= (isset($this->url)) ? $this->url : 'https://www.dealbidar.com/index' ?>" />
        <meta property="og:title" content="<?= (isset($this->title)) ? $this->title : 'Welcome | dealbidar.com' ?>" />
        <meta property="og:description" content="<?= (isset($this->description)) ? $this->description : '' ?>" />
        <meta property="og:image" content="<?= (isset($this->image)) ? $this->image : 'https://www.dealbidar.com/public/assets/metatag.png' ?>" />
        <meta name="mobile-web-app-capable" content="yes">
        <!-- Favicon -->         
        <link rel="shortcut icon" type="image/x-icon" href="<?php echo URL . 'public/assets/images/favicon.ico' ?>">

        <?php
        if (isset($this->css)) {
            foreach ($this->css as $css) {
                echo '<link href="' . URL . $css . '" rel="stylesheet" />';
            }
        }
        ?>


        <script type="application/ld+json">
            {
            "@context": "http://schema.org",
            "@type": "Organization",
            "name": "Ebenezer Albidar Narh",
            "logo": "https://www.dealbidar.com/public/assets/images/logo/logo-07.png",
            "image": "https://www.dealbidar.com/public/assets/images/bg/bg-image-11.jpg",
            "url": "https://www.dealbidar.com",
            "contactPoint": [{
            "@type": "ContactPoint",
            "telephone": "+233-312-295-283",
            "contactType": "customer service"
            }],
            "sameAs": [
            "https://www.facebook.com/dealbidar",
            "https://instagram.com/dealbidar",
            "https://www.linkedin.com/in/dealbidar",
            "https://twitter.com/dealbidar"
            ]
            }
        </script>
        <script src="https://www.google.com/recaptcha/api.js?render=6Ld1XjoiAAAAAAHIxo7VpiLOSfzDXer_n-0hbzUM"></script>
        <script type='text/javascript' src='https://platform-api.sharethis.com/js/sharethis.js#property=633458e2c7599f001244b360&product=sop' async='async'></script>
    </head>
    <body class="white-version home-sticky spybody" data-spy="scroll" data-bs-target=".navbar-example2" data-offset="150">
        <meta name="twitter:card" content="summary" />
        <meta name="twitter:site" content="@dealbidar" />
        <meta name="twitter:creator" content="@W3 Multimedia Ghana Limited" />
        <meta property="og:url" content="<?= (isset($this->url)) ? $this->url : 'https://www.dealbidar.com/index' ?>" />
        <meta property="og:title" content="<?= (isset($this->title)) ? $this->title : 'Welcome | dealbidar.com' ?>" />
        <meta property="og:description" content="<?= (isset($this->description)) ? $this->description : ' ' ?>" />
        <meta property="og:image" content="<?= (isset($this->image)) ? $this->image : 'https://www.dealbidar.com/public/assets/images/bg/bg-image-11.jpg' ?>" />
        <meta property="og:video" content="<?= (isset($this->meta_video)) ? $this->meta_video : 'https://www.youtube.com/watch?v=Vc3qvxm9E90' ?>" />
        