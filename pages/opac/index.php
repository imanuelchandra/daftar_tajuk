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

template {
  display: block;
  margin-bottom: 20px;
}

.taxonomy-list { 
  /* display: grid; grid-template-columns: max-content auto; gap: 4px 16px; */
  float: left; 
  clear: left;
  margin-bottom: 0.5rem;
}

/* Parent categories */
.taxonomy-list .parent-term {
  font-weight: bold;
  font-size: 2rem;
  margin-bottom: 0.5rem;
  display: block;
}

/* Child categories shifted over to indicate nesting */
.taxonomy-list .child-term {
  margin-left: 1.5rem;
  font-size: 1.8rem;
  color: #555555;
  /* display: list-item;
  list-style-type: disc; */
  margin-bottom: 0.5rem;
  margin-left: 20px;
}

</style>

<div class="container py-4">
   <div class="row">
        <div class="col-md-8 offset-lg-4">
         


<?php

$str_output  = '<div id="app">';
$str_output  .= '<h1>Daftar Tajuk Subjek</h1>';

$abjad = range('A', 'Z');

$str_output  .='<div class="alphabet-index">';
$str_output  .='Pindah Ke: ';
foreach ($abjad as $huruf) {
    $str_output  .='[<a href="#" @click.prevent="scrollToSection(\'' . $huruf . '\')">' . $huruf . '</a> ] ';
}
$str_output  .='</div>';

$str_output  .='<dl class="taxonomy-list">';
$str_output  .='<template v-for="item in items" :key="item.topic_id">';
$str_output  .='<dt :id="item.dt_id" class="parent-term">{{ item.topic }} ({{ item.classification }})</dt>';
$str_output  .='<dd class="child-term" v-for="rt_item in item.related_terms" :key="rt_item.rt_id">';
$str_output  .='{{ rt_item.rt_id }} {{ rt_item.rt_topic }} ({{ rt_item.rt_classification }})';
$str_output  .='</dd>';
$str_output  .='</template>';
$str_output  .='</dl>';

$str_output  .='<div v-if="loading" class="loading">Loading more...</div>';
        

// $topic = DB::getInstance()->prepare('SELECT t.topic_id, t.topic, t.classification, t.topic_type, t.auth_list  FROM mst_topic AS t LEFT JOIN biblio_topic AS bt ON t.topic_id=bt.topic_id WHERE bt.biblio_id IS NULL OR bt.topic_id IS NULL ORDER BY t.topic ASC;');
// $topic->execute();
  
//  $str_output  .= '<dl>';
//  $result = [];
//  while ($data = $topic->fetchObject()) {
   
//      if($data->topic_type == 't'){
//         $buku_url = SWB.'index.php?subject='.rawurlencode($data->topic).'&search=search';
//         $charSort = mb_substr($data->topic, 0, 1);
//         $str_output  .= '<dt id="#'.$charSort.'"><a href="'.$buku_url.'" class="titleField" itemprop="topic" property="topic" title="'.__('Lihat buku pada subjek ini').'">'.$data->topic.' (' .$data->classification. ')</a></dt>';

        
//         $voc_q = DB::getInstance()->prepare('SELECT * FROM mst_voc_ctrl WHERE topic_id='.$data->topic_id);
//         $voc_q->execute();
//         while ($voc_d = $voc_q->fetchObject()) { 
//           $related_topic_id = (integer)$voc_d->related_topic_id;
   
//           $topic_rel_q = DB::getInstance()->prepare('SELECT topic, classification FROM mst_topic WHERE topic_id=' . $dbs->real_escape_string($related_topic_id));
//           $topic_rel_q->execute();

//           $topic_rel_d = $topic_rel_q->fetchObject();


//           $buku_url_sub = SWB.'index.php?subject='.rawurlencode($topic_rel_d->topic).'&search=search';
//           $str_output  .= '<dd>'.$voc_d->rt_id.' <a href="'.$buku_url_sub.'" class="titleField" itemprop="topic" property="topic" title="'.__('Lihat buku pada subjek ini').'">'.$topic_rel_d->topic.' (' .$topic_rel_d->classification. ')</a></dd>';
//         }
//     }

//  }
//  $str_output  .= '</dl>';

  $str_output  .= '</div>';

echo $str_output;

?>
        </div>
        <div class="col-md-4"></div>
  </div>
</div>

<?php
echo '<script type="module">
        const { createApp, ref, onMounted, onUnmounted } = Vue;
        

        const app = createApp({
             setup() {
                const items = ref([]);
                const page = ref(1);
                const limit = ref(20);
                const loading = ref(false);
                const hasMore = ref(true);

                const fetchItems = async () => {
                  if (loading.value || !hasMore.value) return;
                  loading.value = true;
                  
                  try {
                    const res = await fetch(`http://localhost/slimsjnl/index.php?p=api/subjects&page=${page.value}&limit=${limit.value}`);
                    const data = await res.json();

                    console.log("Page", page.value);
                    console.log(data);

                    hasMore.value = data.pagination.has_more;

                    console.log("hasMore", hasMore.value);
                    // //if (data.length < limit.value) hasMore.value = false;
                    
                    if (hasMore.value) {
                      items.value.push(...data.data);
                      page.value += 1;
                    }else{
                      items.value.push(...data.data);
                      hasMore.value = false;
                    }
                  } catch (err) {
                    console.error(err);
                  } finally {
                    loading.value = false;
                  }
                };

                const handleScroll = () => {
                  const bottomOfWindow = window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 50;
                  if (bottomOfWindow) {
                    fetchItems();
                  }
                };

                onMounted(() => {
                  fetchItems();
                  window.addEventListener("scroll", handleScroll);
                });

                onUnmounted(() => {
                  window.removeEventListener("scroll", handleScroll);
                });

                return { items, loading };
            },
            methods: {
                scrollToSection(param) {
                  console.log("Received parameter:", param);
                  const element = document.getElementById("#" + param);
                  if (element) {
                    element.scrollIntoView({ behavior: "smooth" });
                  }
                }
              }
        });
      
        app.mount("#app");

      </script>';