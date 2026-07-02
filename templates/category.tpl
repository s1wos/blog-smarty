{extends file="layout.tpl"}

{block name="title"}{$category.name} | Blog{/block}

{block name="content"}
    <a class="back-link" href="/">← На главную</a>

    <section class="intro small">
        <p>Категория</p>
        <h1>{$category.name}</h1>
        {if $category.description}
            <p>{$category.description}</p>
        {/if}
    </section>

    <div class="bar">
        <span>{$total} статей</span>
        <div>
            <a class="{if $sort == 'date'}is-active{/if}" href="?sort=date&page=1">По дате</a>
            <a class="{if $sort == 'views'}is-active{/if}" href="?sort=views&page=1">По просмотрам</a>
        </div>
    </div>

    {if $posts}
        <div class="cards">
            {foreach $posts as $post}
                {include file="partials/post-card.tpl" post=$post}
            {/foreach}
        </div>
        {include file="partials/pagination.tpl" page=$page totalPages=$totalPages sort=$sort}
    {else}
        <div class="empty">
            <h2>В категории пока нет статей</h2>
            <p>Попробуйте вернуться позже или открыть другую категорию.</p>
        </div>
    {/if}
{/block}
