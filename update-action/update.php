<?php
function ConvertToUTF8($text){

    $encoding = mb_detect_encoding($text, mb_detect_order(), false);

    if($encoding == "UTF-8")
    {
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');    
    }


    $out = iconv(mb_detect_encoding($text, mb_detect_order(), false), "UTF-8//IGNORE", $text);


    return $out;
}


function st_split($string){
    $resultArr = [];
    $strLength = strlen($string);
    for ($i = 0; $i < $strLength; $i++){
        $resultArr[$i] = $string[$i];
    }
    return $resultArr;
}
/* Declaring functions */
function get_char_symbols($ssstring, $number=1024){
    $sstring=st_split($ssstring);
    $index = 0;
    $strr = '';
    foreach($sstring as $k){
        if ($index < $number){
            $strr = $strr . $k;
        }else{
            return ConvertToUTF8($strr);
        }
        $index += 1;
    }
}

function RusToLat($source) {
    if ($source) {
        $rus = [
            'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я',
            'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я'
        ];
        $lat = [
            'A', 'B', 'V', 'G', 'D', 'E', 'Yo', 'Zh', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Shch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya',
            'a', 'b', 'v', 'g', 'd', 'e', 'yo', 'zh', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'shch', 'y', 'y', 'y', 'e', 'yu', 'ya'
        ];
        return str_replace($rus, $lat, $source);
    }
}

function generateSeoURL($string, $wordLimit = 0){ 
    $separator = '-'; 
     
    if($wordLimit != 0){ 
        $wordArr = explode(' ', $string); 
        $string = implode(' ', array_slice($wordArr, 0, $wordLimit)); 
    } 
 
    $quoteSeparator = preg_quote($separator, '#'); 
 
    $trans = array( 
        '&.+?;'                 => '', 
        '[^\w\d _-]'            => '', 
        '\s+'                   => $separator, 
        '('.$quoteSeparator.')+'=> $separator 
    ); 
 
    $string = strip_tags($string); 
    foreach ($trans as $key => $val){ 
        $string = preg_replace('#'.$key.'#iu', $val, $string); 
    } 
 
    $string = strtolower($string); 
 
    return trim(trim($string, $separator)); 
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/* Declaring variables */
$root = getcwd();
$api_key = str_replace("\n",'',file_get_contents($root.'/api.key'));
$api_key2 = str_replace("\n",'',file_get_contents($root.'/api2.key'));
$queue_url = str_replace("\n",'',file_get_contents($root.'/api3.key'));
$rss_feed = 'https://api.rss2json.com/v1/api.json?rss_url='.urlencode('https://habr.com/en/rss/all/all/?fl=ru').'&count=60&api_key='.$api_key;
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
    $title = str_replace('amp;','',str_replace('&amp;',' ',$post->title));
    $links = explode('?', $post->link)[0];
    $furl = generateSeoURL(RusToLat($title), 4);
    $description = $post->description;
    $pubdate = $post->pubDate;
    $thumbnail = $post->thumbnail;
    if (!$thumbnail){
        $thumbnail = '/assets/images/post-preview.png';
    }
    $content = str_replace('<img ','<img alt="Image '.generateRandomString(6).'" ',$post->content);
    $categories = $post->categories;
    $posts_keywords = '';
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
    $postdata = http_build_query(
        array(
            
        )
    );
    $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-Type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );
    $context  = stream_context_create($opts);
    echo file_get_contents('http://api.carente.eu.org/requestbin.php?key='.$queue_url.'&request='.urlencode(base64_encode('https://script.google.com/macros/s/AKfycby6XK_viaR-FmH-s6-IRh-V3-_yFCcigSDxF86LTDZ3XaN4kC5ovOymg8HZEmf2i4x1/exec?key='.urlencode($api_key2).'&path='.urlencode($furl).'&url='.urlencode($links))).'&i=1', false, $context);
    $post = file_get_contents($root.'/templates/post.html');
    $post = str_replace('%title%', $title, $post);
    $post = str_replace('%keywords_html%', RusToLat($posts_keywords).$posts_keywords, $post);
    $post = str_replace('%keywords%', $seo_keywords, $post);
    $post = str_replace('%content%', str_replace('#habracut">', '#habracut" class="btn-read">',$content), $post);
    $post = str_replace('%current_url%', $website_url.'/posts/'.$furl, $post);
    $post = str_replace('%img%', $thumbnail, $post);
    $post = str_replace('%description', str_replace('"','',str_replace("\n", '', strip_tags($content))), $post);
    $post = str_replace('%logo%', $app_name, $post);
    $post = str_replace('%path%', $furl, $post);
    file_put_contents(($root.'/posts/'.$furl.'.html'), $post);
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

if ($handle = opendir($root.'/../posts/.')) {

    while (false !== ($entry = readdir($handle))) {

        if ($entry != "." && $entry != "..") {

            $sitemap .= "\n<url>";
            $sitemap .= "\n   <loc>".$website_url."/posts/".str_replace('.html','/',$entry)."</loc>";
            $sitemap .= "\n   <lastmod>".date("Y-m-d")."</lastmod>";
            $sitemap .= "\n</url>";
        }
    }

    closedir($handle);
}

$sitemap .= '</urlset>';
file_put_contents($root.'/sitemap.xml', $sitemap);
/*$index_h = str_replace('%img%', '', $index_h);*/
