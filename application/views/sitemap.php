<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    <url>
        <loc><?= base_url();?></loc>
        <priority>1.0</priority>
    </url>
 
    <?php foreach($allpages as $page) { ?>
 
    <url>
        <loc><?= $page->url ?></loc>
        <priority>0.5</priority>
    </url>
 
    <?php } ?>
</urlset>
