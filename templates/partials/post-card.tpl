<article class="card">
    <a class="card-top" href="/post/{$post.slug}">
        {if isset($post.description) && $post.description}
            <p>{$post.description}</p>
        {/if}
        {if $post.image}
            <img src="{$post.image}" alt="{$post.title}">
        {/if}
    </a>
    <div class="card-body">
        <p class="meta"><span>Дизайн</span> ~ 10 мин&nbsp;&nbsp; {$post.created_at|date_format:"%d.%m.%Y"}</p>
        <h3><a href="/post/{$post.slug}">{$post.title}</a></h3>
    </div>
</article>
