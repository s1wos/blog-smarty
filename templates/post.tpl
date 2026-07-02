{extends file="layout.tpl"}

{block name="title"}{$post.title} | Blog{/block}

{block name="content"}
    <a class="back-link" href="javascript:history.back()">← Назад</a>

    <article class="article">
        <header>
            <p class="meta">Статья ~ 10 мин {$post.created_at|date_format:"%d.%m.%Y"} · {$post.views} просмотров</p>
            <h1>{$post.title}</h1>
            {if $categories}
                <div class="tags">
                    {foreach $categories as $category}
                        <a href="/category/{$category.slug}">{$category.name}</a>
                    {/foreach}
                </div>
            {/if}
        </header>

        {if $post.image}
            <img class="wide-img" src="{$post.image}" alt="{$post.title}">
        {/if}

        <div class="text">
            {$post.content nofilter}
        </div>
    </article>

    {if $relatedPosts}
        <section class="section">
            <h2>Похожие статьи</h2>
            <div class="cards">
                {foreach $relatedPosts as $post}
                    {include file="partials/post-card.tpl" post=$post}
                {/foreach}
            </div>
        </section>
    {/if}
{/block}
