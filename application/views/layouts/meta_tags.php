<?php
    $curPageName = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $page = parse_url($curPageName, PHP_URL_PATH);
    $page = implode('/', array_slice(explode('/', $page), 0, 3));
    $meta = $this->metadata_model->get_metadata($page);
    if(isset($meta) and count($meta) > 0) {
        $keywords = $meta['keywords'];
        $title = $meta['title'];
        $description = $meta['description'];
        $url = $meta['url'];
    } else{
        $keywords = 'Play Games ';
        $title = 'WinWinLabs ';
        $description = 'WinWinLabs';
        $url = $curPageName;  
    }
?>

<meta charset="utf-8">
<meta http-equiv="Cache-control" content="no-cache">
<meta name="viewport" content="width=device-width, initial-scale=1.0" >
<meta name="description" content="<?php echo $description; ?>">
<meta name="keywords" content="<?php echo $keywords; ?>">
<meta name="canonical" content="<?php echo $url; ?>">
<title><?php echo $title; ?></title>
<!-- Other meta tags -->

<noscript>
    <?php if (basename($_SERVER['REQUEST_URI']) != asset_url('jsError')) { ?>
        <meta http-equiv="Refresh" content="0;<?php echo asset_url('jsError'); ?>">
    <?php } ?>
</noscript>