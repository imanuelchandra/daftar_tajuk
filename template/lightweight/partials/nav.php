<nav class="s-menu-content animated-fast" role="navigation">
  <a href="#" id="hide-menu" class="s-menu-toggle"><span></span></a>
  <h1>Menu</h1>
  <ul>
    <li><a href="index.php"><?php echo __('Home'); ?></a></li>
    <li><a href="index.php?p=subject"><?php echo __('Daftar Tajuk'); ?></a></li>
    <li><a href="index.php?p=news"><?php echo __('Library News'); ?></a></li>
    <li><a href="index.php?p=libinfo"><?php echo __('Library Information'); ?></a></li>
    <li><a href="index.php?p=peta" class="openPopUp" width="600" height="400"><?php echo __('Library Location'); ?></a></li>
    <li><a href="index.php?p=member"><?php echo __('Member Area'); ?></a></li>
    <li><a href="index.php?p=librarian"><?php echo __('Librarian'); ?></a></li>
    <li><a href="index.php?p=help"><?php echo __('Help on Search'); ?></a></li>
    <li><a href="index.php?p=login"><?php echo __('Librarian LOGIN'); ?></a></li>
    <li><a href="index.php?p=slimsinfo"><?php echo __('About SLiMS'); ?></a></li>
  </ul>

  <!-- Language Translator
  ============================================= -->
  <div class="s-menu-info">
    <form class="language" name="langSelect" action="index.php" method="get">
      <label class="language-info" for="select_lang"><?php echo __('Select Language'); ?></label>
      <span class="custom-dropdown custom-dropdown--emerald custom-dropdown--small">
      <?php
              //$langstr = '';
              $langstr ='<select name="select_lang" id="select_lang" title="Change language of this site" onchange="document.langSelect.submit();" class="custom-dropdown__select custom-dropdown__select--emerald">';
              $current_lang = '';
              $select_lang = isset($_COOKIE['select_lang'])?$_COOKIE['select_lang']:$sysconf['default_lang'];
              // require_once(LANG . 'localisation.php');
              foreach ($available_languages??[] AS $lang_index) {
                $selected = null;
                $lang_code = $lang_index[0];
                $lang_name = $lang_index[1];
                $code_arr = explode('_', $lang_code);
                $code_flag = strtolower($code_arr[1]);
                if ($lang_code == $select_lang) {
                  $current_lang = [
                    'name' => $lang_name,
                    'code' => $code_flag
                  ];
                }
              }
            ?>
       
        <?php

        //foreach ($current_lang??[] AS $lang) {
        //var_dump($current_lang["code"]);
          $langstr .='<option value="'.$current_lang['code'].'">'.$current_lang['name'].'</option>';
        //}
          

          $langstr .='</select>';
          echo $langstr;
        ?>
      </span>
    </form>
  </div>
</nav>
