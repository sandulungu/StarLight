<?php

/**
 * Generic blocks, commonly used in layouts
 */

?>
{require}Messages{/require}
{Messages/}

{if("var":"breadcrumbs")}
	{require}Breadcrumbs{/require}
    <div class="sl-breadcrumbs">
        {Breadcrumbs("last":false)/} &raquo;
    </div>
{/if}
{if("var":"title")}
    <h1>{$title}</h1>
{/if}
<div id="sl-main-content">
    {$content_for_layout}
</div>
<?php

Pheme::registerOutputBuffer('SiteContent', new PhemeParser(), true);



?>
<div class="sl-site-title">
    <h2>
        <a href="{url}/{/url}" title="{config}View.siteTitle{/config}">
            {config}Site.title{/config}
        </a>
    </h2>
</div>
<?php

Pheme::registerOutputBuffer('SiteTitle', new PhemeParser(), true);



?>
<div class="sl-site-mission">
    <p>
        {config}Site.mission{/config}
    </p>
</div>
<?php

Pheme::registerOutputBuffer('SiteMission', new PhemeParser(), true);



?>
<div class="sl-site-footer">
    <p class="sl-footer-menu">
        {require}SimpleMenu{/require}
        {SimpleMenu("id":0)/}
    </p>
    <p class="sl-copyright">
        {config}Site.copyright{/config}
    </p>
</div>
<?php

Pheme::registerOutputBuffer('SiteFooter', new PhemeParser(), true);



?>
<div class="sl-site-search">
    <form action="{url}/{$lang}/cms/cms_nodes{/url}" method="GET">
		<input type="text" name="q" />
        <button>{t}Search{/t}</button>
	</form>
</div>

{require}Api.GoogleAnalytics{/require}
{GoogleAnalytics/}
<?php

Pheme::registerOutputBuffer('SiteSearch', new PhemeParser(), true);
