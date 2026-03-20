<?php
$defaultTitle = 'Ebenezer Albidar Narh | AI and Full-Stack Software Engineer in Ghana';
$defaultDescription = 'Ebenezer Albidar Narh is an AI and full-stack software engineer in Ghana building secure web platforms, enterprise systems, and digital transformation solutions.';
$defaultImage = URL . 'public/assets/images/bg/bg-image-11.jpg';
$canonicalUrl = isset($this->canonical) ? $this->canonical : (isset($this->url) ? $this->url : URL);

if ($canonicalUrl === URL . 'index') {
    $canonicalUrl = URL;
}

$metaTitle = htmlspecialchars(isset($this->title) ? $this->title : $defaultTitle, ENT_QUOTES, 'UTF-8');
$metaDescription = htmlspecialchars(isset($this->description) ? $this->description : $defaultDescription, ENT_QUOTES, 'UTF-8');
$metaKeywords = htmlspecialchars(isset($this->keywords) ? $this->keywords : 'Ebenezer Albidar Narh, software engineer Ghana, full-stack developer Ghana, AI engineer Ghana, enterprise systems developer', ENT_QUOTES, 'UTF-8');
$metaAuthor = htmlspecialchars(isset($this->author) ? $this->author : 'Ebenezer Albidar Narh', ENT_QUOTES, 'UTF-8');
$metaImage = htmlspecialchars(isset($this->image) ? $this->image : $defaultImage, ENT_QUOTES, 'UTF-8');
$metaRobots = htmlspecialchars(isset($this->robots) ? $this->robots : 'index,follow,max-image-preview:large', ENT_QUOTES, 'UTF-8');
$metaCanonical = htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8');

$structuredData = [
    [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => 'Ebenezer Albidar Narh',
        'url' => URL,
        'inLanguage' => 'en',
        'description' => html_entity_decode($metaDescription, ENT_QUOTES, 'UTF-8')
    ],
    [
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => 'Ebenezer Albidar Narh',
        'url' => URL,
        'image' => html_entity_decode($metaImage, ENT_QUOTES, 'UTF-8'),
        'jobTitle' => 'AI and Full-Stack Software Engineer',
        'worksFor' => [
            '@type' => 'Organization',
            'name' => 'InnInk Limited'
        ],
        'sameAs' => [
            'https://www.facebook.com/dealbidar',
            'https://instagram.com/dealbidar',
            'https://www.linkedin.com/in/dealbidar',
            'https://twitter.com/dealbidar'
        ]
    ],
    [
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => html_entity_decode($metaTitle, ENT_QUOTES, 'UTF-8'),
        'url' => $canonicalUrl,
        'description' => html_entity_decode($metaDescription, ENT_QUOTES, 'UTF-8')
    ]
];

if (!empty($this->structuredData) && is_array($this->structuredData)) {
    $structuredData = array_merge($structuredData, $this->structuredData);
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <base href="<?= htmlspecialchars(URL, ENT_QUOTES, 'UTF-8') ?>" />
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title><?= $metaTitle ?></title>
        <meta name="description" content="<?= $metaDescription ?>">
        <meta name="keywords" content="<?= $metaKeywords ?>">
        <meta name="author" content="<?= $metaAuthor ?>">
        <meta name="robots" content="<?= $metaRobots ?>">
        <meta name="theme-color" content="#0f172a">
        <link rel="canonical" href="<?= $metaCanonical ?>">
        <link rel="sitemap" type="application/xml" title="Sitemap" href="<?= htmlspecialchars(URL . 'sitemap', ENT_QUOTES, 'UTF-8') ?>">
        <link rel="shortcut icon" type="image/x-icon" href="<?= htmlspecialchars(URL . 'public/assets/images/favicon.ico', ENT_QUOTES, 'UTF-8') ?>">

        <meta property="og:locale" content="en_US">
        <meta property="og:site_name" content="Ebenezer Albidar Narh">
        <meta property="og:type" content="website">
        <meta property="og:url" content="<?= $metaCanonical ?>">
        <meta property="og:title" content="<?= $metaTitle ?>">
        <meta property="og:description" content="<?= $metaDescription ?>">
        <meta property="og:image" content="<?= $metaImage ?>">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@dealbidar">
        <meta name="twitter:creator" content="@dealbidar">
        <meta name="twitter:title" content="<?= $metaTitle ?>">
        <meta name="twitter:description" content="<?= $metaDescription ?>">
        <meta name="twitter:image" content="<?= $metaImage ?>">

        <?php
        if (isset($this->css)) {
            foreach ($this->css as $css) {
                echo '<link href="' . htmlspecialchars(URL . $css, ENT_QUOTES, 'UTF-8') . '" rel="stylesheet" />';
            }
        }
        ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <script type="application/ld+json"><?= json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
        <?php if (!empty($this->loadRecaptcha)) { ?>
        <script src="https://www.google.com/recaptcha/api.js?render=6Ld1XjoiAAAAAAHIxo7VpiLOSfzDXer_n-0hbzUM"></script>
        <?php } ?>
    </head>
    <body class="white-version home-sticky spybody" data-spy="scroll" data-bs-target=".navbar-example2" data-offset="150">
