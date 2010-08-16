{loop}
    {if("var":"link")}
        {MenuLink}{$text}{/MenuLink}
    {else}
        {$text}
    {/if}
    {!sep:loop} |
{/loop}
<?php

    Pheme::init('Menu');
    Pheme::registerOutputBuffer('SimpleMenu', 'Menu', true);
