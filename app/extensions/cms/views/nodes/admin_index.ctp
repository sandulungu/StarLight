<style type="text/css">
</style>


<?php
    if (false) {
        //$this = new SlView(); // for IDE
    }

    echo $this->SlHtml->h2(
        empty($tag) ? (
            !empty($user) ?
            __t('Books owned by {$user}', compact('user')) :
        __t('Books')) :
        __t('Books marked with "{$tag}" tag', compact('tag'))
    );
    $actions = $this->SlHtml->div('.actions', $this->Html->nestedList(array(
        $this->SlHtml->actionLink('add'),
    )));

    // show items
    echo Pheme::parseTranslate(
<<<end
    $actions
    <table>
        <th width="100">{t}Cover{/t}</th><th>{t}Book name{/t}</th><th width="100">{t}Actions{/t}</th>
end
    );

    $rows = array();
    foreach ($books as $book) {
        $borrowStatus = empty($book["Book"]["borrow_id"]) ?
            $this->SlHtml->em(__t('not borrowed')) :
<<<end
   <a class="external" href="http://www.facebook.com/profile.php?id={$book['Borrow']["User"]["fbid"]}" target="_blank">{$book['Borrow']["User"]["fullname"]}</a>
end;

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
        <h3>{$book["Book"]["title"]}</h3>
        {t}Author{/t}: <b>{$book["Book"]["author"]}</b>,
        {t}owner{/t}: <a href="http://www.facebook.com/profile.php?id={$book["User"]["fbid"]}">{$book["User"]["fullname"]}</a>,
        {t}borrowed to{/t}: $borrowStatus<br />
        ISBN: {$book["Book"]["isbn"]}
        <blockquote>{e}{$book["Book"]["description"]}{/e}</blockquote>
        {t}Tags{/t}: $tags<br />
        {t}Rating points{/t}: <b>{$book["Book"]["rating"]}</b>
    </td><td class="actions">
        $edit $delete
    </td></tr>
end
        );

        $rows[] = $row;
    }

    echo implode('', $rows);
    echo "</table>";
    echo $actions;
