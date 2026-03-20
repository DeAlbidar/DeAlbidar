<?php  header('Content-type: application/xml; charset="utf-8"', true); ?>
<?php echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<?php
$urls = [
    ['loc' => 'https://www.dealbidar.com/', 'changefreq' => 'weekly', 'priority' => '1.0'],
    ['loc' => 'https://www.dealbidar.com/about', 'changefreq' => 'monthly', 'priority' => '0.8'],
    ['loc' => 'https://www.dealbidar.com/experience', 'changefreq' => 'monthly', 'priority' => '0.8'],
    ['loc' => 'https://www.dealbidar.com/projects', 'changefreq' => 'weekly', 'priority' => '0.9'],
    ['loc' => 'https://www.dealbidar.com/contact', 'changefreq' => 'monthly', 'priority' => '0.7'],
    ['loc' => 'https://www.dealbidar.com/download_cv', 'changefreq' => 'monthly', 'priority' => '0.6']
];
foreach ($urls as $page) {
?>
<url>
  <loc><?php echo $page['loc']; ?></loc>
  <changefreq><?php echo $page['changefreq']; ?></changefreq>
  <lastmod><?php echo date("Y-m-d"); ?></lastmod>
  <priority><?php echo $page['priority']; ?></priority>
</url>
<?php } ?>
</urlset>
