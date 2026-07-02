{extends file="layout.tpl"}

{block name="title"}Статьи в блоге{/block}

{block name="content"}
    <div class="crumbs">Главная · Блог</div>

    {if $featuredPost}
        <a class="feature" href="/post/{$featuredPost.slug}">
            <div>
                <p class="feature-meta"><span>Свежая статья</span> ~ 10 мин&nbsp;&nbsp; {$featuredPost.created_at|date_format:"%d.%m.%Y"}</p>
                <h1>{$featuredPost.title}</h1>
                <p>{$featuredPost.description}</p>
            </div>
            {if $featuredPost.image}
                <img src="{$featuredPost.image}" alt="{$featuredPost.title}">
            {/if}
        </a>
    {/if}

    <section class="intro">
        <h1>Статьи в блоге</h1>
        <div class="topics">
            <a href="/">Все темы</a>
            <a href="/category/education">Образование</a>
            <a href="/category/design">Дизайн</a>
            <a href="/category/marketing">Маркетинг</a>
            <a href="/category/programming">Программирование</a>
            <a href="/category/analytics">Аналитика</a>
            <a href="/category/management">Менеджмент</a>
            <a href="/category/neural">Нейросети</a>
        </div>
    </section>

    {if $sections}
        <section class="section">
            <div class="cards cards--mixed">
                {foreach $sections as $section}
                    {foreach $section.posts as $post}
                        {include file="partials/post-card.tpl" post=$post}
                    {/foreach}
                {/foreach}
            </div>
        </section>
    {else}
        <div class="empty">
            <h2>Пока нет статей</h2>
            <p>Запустите сидер, чтобы наполнить блог тестовыми данными.</p>
        </div>
    {/if}
{/block}
