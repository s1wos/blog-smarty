{if $totalPages > 1}
    <nav class="pagination" aria-label="Пагинация">
        {for $itemPage=1 to $totalPages}
            {if $itemPage == $page}
                <span class="pagination__item pagination__item--active">{$itemPage}</span>
            {else}
                <a class="pagination__item" href="?sort={$sort}&page={$itemPage}">{$itemPage}</a>
            {/if}
        {/for}
    </nav>
{/if}
