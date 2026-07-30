<?php
use SLiMS\DB;
use SLiMS\Url;

defined('INDEX_AUTH') or die('Direct access is not allowed!');


if (!isset($opac)) $opac = $this;

$path = str_replace(['\'', '"'], '', strip_tags($_GET['p']));

?>

<style>

/* Container reset and alignment */
.alphabet-index {
  margin: 20px 0;
  padding-right:50px;
}

dl { 
  /* display: grid; grid-template-columns: max-content auto; gap: 4px 16px; */
  float: left; clear: left;
}
dt { font-weight: bold; }
dd { margin-left: 20px; } 

</style>

<div class="container py-4">
   <div class="row">
        <div class="col-md-8 offset-lg-4">
         


<?php

$str_output  = '<h1>Daftar Subjek</h1>';

$abjad = range('A', 'Z');

$str_output  .='<div class="alphabet-index">';
$str_output  .='Pindah Ke: ';
foreach ($abjad as $huruf) {
    $str_output  .='[<a href="?char=' . $huruf . '">' . $huruf . '</a> ] ';
}
$str_output  .='</div>';


$topic = DB::getInstance()->prepare('SELECT t.topic_id, t.topic, t.classification, t.topic_type, t.auth_list  FROM mst_topic AS t LEFT JOIN biblio_topic AS bt ON t.topic_id=bt.topic_id WHERE bt.biblio_id IS NULL OR bt.topic_id IS NULL ORDER BY t.topic ASC;');
$topic->execute();
  
 $str_output  .= '<dl>';
 $result = [];
 while ($data = $topic->fetchObject()) {
   
     if($data->topic_type == 't'){
        $buku_url = SWB.'index.php?subject='.rawurlencode($data->topic).'&search=search';
        $str_output  .= '<dt><a href="'.$buku_url.'" class="titleField" itemprop="topic" property="topic" title="'.__('Lihat buku pada subjek ini').'">'.$data->topic.' (' .$data->classification. ')</a></dt>';

        
        $voc_q = DB::getInstance()->prepare('SELECT * FROM mst_voc_ctrl WHERE topic_id='.$data->topic_id);
        $voc_q->execute();
        while ($voc_d = $voc_q->fetchObject()) { 
          $related_topic_id = (integer)$voc_d->related_topic_id;
   
          $topic_rel_q = DB::getInstance()->prepare('SELECT topic, classification FROM mst_topic WHERE topic_id=' . $dbs->real_escape_string($related_topic_id));
          $topic_rel_q->execute();

          $topic_rel_d = $topic_rel_q->fetchObject();


          $buku_url_sub = SWB.'index.php?subject='.rawurlencode($topic_rel_d->topic).'&search=search';
          $str_output  .= '<dd>'.$voc_d->rt_id.' <a href="'.$buku_url_sub.'" class="titleField" itemprop="topic" property="topic" title="'.__('Lihat buku pada subjek ini').'">'.$topic_rel_d->topic.' (' .$topic_rel_d->classification. ')</a></dd>';
        }
    }

 }
 $str_output  .= '</dl>';

echo $str_output;

?>
        </div>
        <div class="col-md-4"></div>
  </div>
</div>