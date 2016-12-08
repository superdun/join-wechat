<div class="breadcrumbs">
    当前位置：
    <a href="<?=PATH?>">首页</a> &gt;

    <?
    if ( $base['menu'] == 'search' ) {
    ?>
        <a><?=$base['name']?></a> &gt; <a class="on"><?=$search?></a>
    <?
    } elseif( $base['menu'] == 'request' || $base['menu'] == 'testimonials' ){
    ?>
        <a><?=$base['name']?></a>
    <?
    } else {
        if(!empty($third['id'])){
    ?>
            <a href="<?=getCategoryUrl($base['id'])?>"><?=$base['name']?></a> &gt; <a href="<?=getCategoryUrl($second['id'])?>"><?=$second['name']?></a> &gt; <a href="<?=getCategoryUrl($third['id'])?>" class="on"><?=$third['name']?></a>
    <?
        } else {
    ?>
            <a href="<?=getCategoryUrl($base['id'])?>"><?=$base['name']?></a>&gt;<a href="<?=getCategoryUrl($second['id'])?>"  class="on"><?=$second['name']?></a>
    <?
        }
    }
    ?>
</div>
