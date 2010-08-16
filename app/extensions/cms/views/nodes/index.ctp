<style type="text/css">
</style>


<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    if (!empty($this->params['home'])) {
        $this->viewVars['before'] = Pheme::parseTranslate(
<<<end
<div style="padding:5px; text-align:justify;">


<form class="searchpageform" action={url}/book_club/books{/url}>  <h3 class="searchtext">Ce îți dorești să citești?</h3>
	<input name="q" onblur="if (this.value == '') {this.value = 'Caută...';}" onfocus="if (this.value == 'Caută...') {this.value = '';}" value="Caută..." class="searchpagefield">


	<input type="submit" value="Go" class="searchpagebutton">
	<div id="panel">

        <ul style="left:auto;
    list-style-type:none; display:inline;">
        <li style="display:inline;
    float:left;
    list-style-type:none;
    position:relative;
    width:300px;">
        <ul>
          <li>Artă</li>
     <li>Aventură</li>

     <li>Memorii</li>
     <li>Comedie</li>
     <li>Comunicare/New Media</li>
     <li>Critică</li>
     <li>Dezvoltare personală</li>
     <li>Economie </li>

     <li>Eseuri</li>
     <li>Gastronomie</li></ul></li>
       <li style="display:inline;
    float:left;
    list-style-type:none;
    position:relative;
    width:300px;">
     <ul>
     <li>Istorie</li>
     <li>Literatură contemporană</li>
     <li>Management/Marketing</li>

     <li>Proză scurtă</li>
     <li>Sociologie/antropologie</li>
     <li>Roman de dragoste</li>
     <li>Roman istoric</li>
     <li>Roman polițist</li>
     <li>Roman psihologic</li>

     <li>SF</li>

    </ul>
     </li>
        </ul>
    </div>

    <p class="slide"><a href="#" class="btn-slide">Mai mult</a></p>
    </form>



                 </div>
    <script type="text/javascript" src="jquery.js"></script>

    <script type="text/javascript">
    $(document).ready(function(){
        $(".btn-slide").click(function(){
            $("#panel").slideToggle("slow");
            $(this).toggleClass("active"); return false;
        });
    });
    </script>
end
        );
        
        SlConfigure::write('Asset.js.jquery', 'head');
    }

    // show items
    echo Pheme::parseTranslate(
<<<end
    <table>
end
    );

    $rows = array();
    foreach ($books as $book) {
        $borrowStatus = empty($book["Book"]["borrow_id"]) ?
            $this->SlHtml->em(__t('not borrowed')) :
<<<end
   <a class="external" href="http://www.facebook.com/profile.php?id={$book['Borrow']["User"]["fbid"]}" target="_blank">{$book['Borrow']["User"]["fullname"]}</a>
end;
        if ($book["User"]["id"] != 1) {
            $borrowLink = empty($book["Book"]["borrow_id"]) ?
                $this->SlHtml->link($this->SlHtml->span('Împrumută'),
                        '#',//array('controller' => 'borrows', 'action' => 'add', $book["Book"]["id"]),
                        array('confirm' => 'Proprietarul va primi un mesaj privat pe contrul său FaceBook și/sau email cu cererea d-voatră.\nContinuăm?', 'class' => 'button adauga')) :
                $this->SlHtml->link($this->SlHtml->span('Urmărește'),
                        '#',//array('controller' => 'borrows', 'action' => 'add', $book["Book"]["id"]),
                        array('confirm' => 'Proprietarul va primi un mesaj privat pe contrul său FaceBook și/sau email cu cererea d-voatră.\nContinuăm?', 'class' => 'button'));
        } else {
            $borrowLink = '';
        }

        $editLink = $book["User"]["id"] == 1 ?
            $this->SlHtml->link($this->SlHtml->span('Editare'), '#', array('class' => 'button')) : '';

        $edit = $this->SlHtml->actionLink('edit', $book['Book']['id']);
        $delete = $this->SlHtml->actionLink('delete', $book['Book']['id']);

        $thumb = $book['Book']['cover_filename'] ?
            $this->Html->image('/files/book_covers/thumb/small/' . $book['Book']['cover_filename']) :
            '';

        $tags = array();
        foreach ($book['Tag'] as $tag) {
            $tags[] = $this->SlHtml->link($tag['name'], array('controller' => 'books', 'tag' => $tag['id']));
        }
        $tags = implode(', ', $tags);

        $row = Pheme::parseTranslate(
<<<end
    {!preserveWhitespace}
    <tr><td>
        $thumb
    </td><td>
        <h4 class="title">{$book["Book"]["title"]}</h4>
        {t}Autor{/t}: <b>{$book["Book"]["author"]}</b>,
        {t}owner{/t}: <a href="http://www.facebook.com/profile.php?id={$book["User"]["fbid"]}">{$book["User"]["fullname"]}</a>,
        {t}borrowed to{/t}: $borrowStatus<br />
        ISBN: {$book["Book"]["isbn"]}
        <blockquote>{e}{$book["Book"]["description"]}{/e}</blockquote>
        {t}Tags{/t}: $tags<br />
        {t}Rating points{/t}: <b>{$book["Book"]["rating"]}</b>
    </td><td class="actions" style="vertical-align: middle">
        $borrowLink $editLink
    </td></tr>
end
        );

        $rows[] = $row;
    }

    echo implode('', $rows);
    echo "</table>";
