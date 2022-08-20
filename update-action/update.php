<?php
/* Declaring functions */
function get_char_symbols($ssstring, $number=1024){
    $sstring=str_split($ssstring);
    $index = 0;
    $strr = '';
    foreach($sstring as $k){
        if ($index < $number){
            $strr = $strr . $k;
        }else{
            return $strr;
        }
        $index += 1;
    }
}

/* Declaring variables */
$root = getcwd();
$api_key = str_replace("\n",'',file_get_contents($root.'/api.key'));
$rss_feed = 'https://api.rss2json.com/v1/api.json?rss_url='.urlencode('https://habr.com/en/rss/all/all/').'&count=40&api_key='.$api_key;
$rss_feed_f = json_decode(file_get_contents($rss_feed));
$config = $root . '/config.json';
$config_f = json_decode(file_get_contents($config));
$website_url = $config_f -> url;
$keywordss = $config_f -> keywords;
$descriptions = $config_f -> description;
$app_name = $config_f -> name;

/* Creating base files */
$robots = "User-agent: *\nDisallow: /update*\nDisallow:\nSitemap: ".$website_url."/sitemap.xml";
file_put_contents('robots.txt', $robots);

$sitemap = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
$sitemap .= "\n<url>";
$sitemap .= "\n   <loc>".$website_url."/</loc>";
$sitemap .= "\n   <lastmod>".date("Y-m-d")."</lastmod>";
$sitemap .= "\n</url>";

$posts_html = '';

print_r($config);

/* Posts go on */
foreach ($rss_feed_f->items as $post){
    $title = $post->title;
    $furl = str_replace('/','',str_replace(',','',str_replace(':','',str_replace(';','',str_replace('&','',str_replace('[','',str_replace(']','',str_replace(' ','-',str_replace('.','-',($title))))))))));
    $description = $post->description;
    $pubdate = $post->pubDate;
    $thumbnail = $post->thumbnail;
    if (!$thumbnail){
        $thumbnail = '/assets/images/post-preview.png';
    }
    $content = $post->content;
    $categories = $post->categories;
    $posts_keywords = '<div class="keywords">';
    $ind = 0;
    $seo_keywords = '';
    foreach ($categories as $cat){
        $posts_keywords .= '<div class="keyword">'.$cat.'</div>';
        if ($ind == 0){
            $seo_keywords = $cat;
            $ind = 1;
        }else{
            $seo_keywords .= ','.$cat;
        }
    }
    $post = file_get_contents($root.'/templates/post.html');
    $post = str_replace('%title%', $title, $post);
    $post = str_replace('%keywords_html%', $posts_keywords, $post);
    $post = str_replace('%keywords%', $seo_keywords, $post);
    $post = str_replace('%content%', $content, $post);
    $post = str_replace('%current_url%', $website_url.'/posts/'.$furl, $post);
    $post = str_replace('%img%', $thumbnail, $post);
    $post = str_replace('%description', str_replace("\n", '', strip_tags($content)), $post);
    $post = str_replace('%logo%', $app_name, $post);
    file_put_contents(($root.'/posts/'.$furl.'.html'), $post);
    $sitemap .= "\n<url>";
    $sitemap .= "\n   <loc>".$website_url.'/posts/'.$furl."/</loc>";
    $sitemap .= "\n   <lastmod>".explode(' ', $pubdate)[0]."</lastmod>";
    $sitemap .= "\n</url>";
    $posts_html .= '<div class="post"><div class="title">'.$title.'</div><div class="description">'.str_replace("\n", '', get_char_symbols(strip_tags($content), 100)).'</div><a class="btn" href="'.$website_url.'/posts/'.$furl.'">Read Post</a></div>';
}

$index_h = file_get_contents($root.'/templates/index.html');
$index_h = str_replace('%post_html%', $posts_html, $index_h);
$index_h = str_replace('%title%', $app_name, $index_h);
$index_h = str_replace('%description%', $descriptions, $index_h);
$index_h = str_replace('%keywords%', $keywordss, $index_h);
$index_h = str_replace('%current_url%', $website_url, $index_h);
$index_h = str_replace('%img%', $website_url.'/assets/images/preview.png', $index_h);
file_put_contents($root.'/index.html',$index_h);

$sitemap .= '</urlset>';
file_put_contents($root.'/sitemap.xml', $sitemap);
/*$index_h = str_replace('%img%', '', $index_h);*/
