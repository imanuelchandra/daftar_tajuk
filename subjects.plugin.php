<?php
/**
 * Plugin Name: Subjects Untuk SLiMS
 * Plugin URI: -
 * Description: Plugins subject untuk SLIMS
 * Version: 1.0.0
 * Author: -
 * Author URI: -
 */
use SLiMS\Plugins;
use SLiMS\DB;

$plugin = Plugins::getInstance();

Plugins::getInstance()->registerAutoload(__DIR__);

$path =  __DIR__ . '/pages/opac/index.php';

Plugins::menu('opac', 'Tajuk Subjek', $path);

Plugins::register(Plugins::CONTENT_BEFORE_LOAD, function() {
    // Hook into output buffering to inject tags right before </head>
    ob_start(function($buffer) {
        $custom_assets = '
        <script src='.SWB.'plugins/daftar_tajuk/assets/js/vue-router.global.js></script>
        ';
        
        // Inject before closing head tag if it exists, otherwise prepend
        if (stripos($buffer, '</head>') !== false) {
            return str_ireplace('</head>', $custom_assets . '</head>', $buffer);
        }

        return $buffer;
        //return $custom_assets . $buffer;
    });
});


Plugins::register('custom_api_route', function ($router) {

    

    $router->map('GET', '/subjects', function() {

    //http://localhost/slimsjnl/index.php?p=api/subjects&page=2&limit=10

    $query = isset($_GET['query']) ? (int)$_GET['query']: 'A';
    $page = isset($_GET['page']) ? (int)$_GET['page']: 1;
    $page = max(1, $page);
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $limit = max(1, min(100, $limit)); // Cap limit to 100 max
    $offset = ($page - 1) * $limit;

    $criteria = '(bt.biblio_id IS NULL OR bt.topic_id IS NULL) ';

    if (isset($_GET['query']) and !empty($_GET['query'])) {
        $query = trim($_GET['query']);
        $criteria .= ' AND (t.topic LIKE \'' . $query . '%\')';
    }

    $countItems = DB::getInstance()->prepare('SELECT COUNT(*) FROM mst_topic AS t LEFT JOIN biblio_topic AS bt ON t.topic_id=bt.topic_id WHERE '.$criteria.' ORDER BY t.topic ASC');
    $countItems->execute();
    $totalItems = (int)$countItems->fetchColumn();
    $totalPages = ceil($totalItems / $limit);

    // Cek apakah ada halaman selanjutnya
    $hasMore = $page < $totalPages; 


    $topic = DB::getInstance()->prepare('SELECT t.topic_id, t.topic, t.classification, t.topic_type, t.auth_list  FROM mst_topic AS t LEFT JOIN biblio_topic AS bt ON t.topic_id=bt.topic_id WHERE '.$criteria.' ORDER BY t.topic ASC LIMIT '.$limit.' OFFSET '.$offset.';');
    $topic->execute();

    $return = array();
    

    while ($data = $topic->fetch(PDO::FETCH_ASSOC)) {
    
        if($data['topic_type'] == 't'){
            $buku_url = SWB.'index.php?subject='.rawurlencode($data['topic']).'&search=search';
            $charSort = mb_substr($data['topic'], 0, 1);
            // $str_output  .= '<dt id="#'.$charSort.'"><a href="'.$buku_url.'" class="titleField" itemprop="topic" property="topic" title="'.__('Lihat buku pada subjek ini').'">'.$data->topic.' (' .$data->classification. ')</a></dt>';

            $res = array();
            $res['topic_id'] = $data['topic_id'];
            $res['topic'] = $data['topic'];
            $res['classification'] = $data['classification'];
            $res['topic_type'] = $data['topic_type'];
            $res['dt_id'] = "#".$charSort;
            $res['buku_url'] = $buku_url;

            
            $voc_q = DB::getInstance()->prepare('SELECT * FROM mst_voc_ctrl WHERE topic_id='.$data['topic_id']);
            $voc_q->execute();
            while ($voc_d = $voc_q->fetch(PDO::FETCH_ASSOC)) { 
            $related_topic_id = (integer)$voc_d['related_topic_id'];
    
            $topic_rel_q = DB::getInstance()->prepare('SELECT topic, classification FROM mst_topic WHERE topic_id=' . $related_topic_id);
            $topic_rel_q->execute();

            $topic_rel_d = $topic_rel_q->fetch(PDO::FETCH_ASSOC);
           
            
            $buku_url_sub = SWB.'index.php?subject='.rawurlencode($topic_rel_d['topic']).'&search=search';
            $res['related_terms'][] = array(
                        'rt_id'=> $voc_d['rt_id'],
                        'rt_topic' => $topic_rel_d['topic'],
                        'rt_classification' => $topic_rel_d['classification'],
                        'buku_url_sub' => $buku_url_sub
                );
            // $str_output  .= '<dd>'.$voc_d->rt_id.' <a href="'.$buku_url_sub.'" class="titleField" itemprop="topic" property="topic" title="'.__('Lihat buku pada subjek ini').'">'.$topic_rel_d->topic.' (' .$topic_rel_d->classification. ')</a></dd>';
            }

            $return[] = $res;
        }

    }

        $response = array(
            'error' => false,
            'data' => $return,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'offset' => $offset,
                'total_page' => $totalPages,
                'has_more' => $hasMore
                // 'total_records' => (int)$totalRows,
                // 'total_pages' => ceil($totalRows / $limit)
            ]
            );
         withJson($response);

    }, 'subjects');


    //$router->map('GET', '/subjects', 'SubjectController@index', 'subjects');

});


function withJson($data){
        header('Content-type: application/json');
        if (is_array($data)) {
            echo json_encode($data);
        } else {
            echo $data;
        }

        return true;
}