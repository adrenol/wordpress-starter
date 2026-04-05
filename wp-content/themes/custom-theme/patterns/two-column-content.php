<?php
/**
 * Title: Две колонки с текстом
 * Slug: custom/two-column-content
 * Description: Универсальный двухколоночный блок для описания компании, услуг или проекта.
 * Categories: text
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"4rem"},"blockGap":"2rem"}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull" style="padding-top:4rem"><!-- wp:group {"className":"site-container","style":{"spacing":{"blockGap":"0.75rem"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group site-container"><!-- wp:heading {"textAlign":"center","level":2,"className":"text-4xl font-black tracking-tight text-zinc-950"} -->
<h2 class="wp-block-heading has-text-align-center text-4xl font-black tracking-tight text-zinc-950">Раздел с подробным описанием</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","className":"mx-auto max-w-2xl text-base leading-7 text-zinc-600"} -->
<p class="has-text-align-center mx-auto max-w-2xl text-base leading-7 text-zinc-600">Этот шаблон удобно использовать для истории бренда, описания процесса работы, презентации направления или объяснения ценности продукта.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"site-container","layout":{"type":"default"}} -->
<div class="wp-block-group site-container"><!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"2rem"}}}} -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3,"className":"text-2xl font-bold tracking-tight text-zinc-950"} -->
<h3 class="wp-block-heading text-2xl font-bold tracking-tight text-zinc-950">Понятная структура без перегрузки</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"text-base leading-7 text-zinc-600"} -->
<p class="text-base leading-7 text-zinc-600">В левую колонку можно вынести основной тезис, краткое описание подхода или ключевую мысль, которая должна прозвучать первой и задать тон всему разделу.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"text-base leading-7 text-zinc-600"} -->
<p class="text-base leading-7 text-zinc-600">Во второй колонке удобно разместить детали: особенности услуги, преимущества команды, этапы сотрудничества или дополнительные пояснения. Такой формат хорошо работает на экранах разной ширины и остаётся аккуратным даже после редактирования в админке.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"style":{"spacing":{"blockGap":"0.75rem"}}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#">Подробнее</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
