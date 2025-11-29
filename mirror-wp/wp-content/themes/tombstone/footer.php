<?php
// Сначала проверяем, является ли страница "kontakty"
if (is_page('kontakty')) {
    get_template_part( 'template-parts/call-to-action-1-black', 'block' );
    get_template_part( 'template-parts/form-1', 'block' ); 
} 
// Затем проверяем, не является ли страница главной
elseif (!is_front_page()) {
    get_template_part( 'template-parts/call-to-action-1-black', 'block' );
    get_template_part( 'template-parts/work-stages-g', 'block' );
}
?>


    

</main>

<footer class="mt-60 pt-90 bg-black full-width-bg">
    
    <div class="pb-45">
        <div class="grid gtc-2 gg-40">
            <div>
                <h5 class="txt-white">Адрес:</h5>
                <p class="pt-10 fs-18 txt-white opacity-7">г. Тюмень, ул.&nbsp;Федюнинского&nbsp;12, строение&nbsp;1, здание «Волна» 2&nbsp;этаж</p>
                <h5 class="txt-white pt-20">Телефон:</h5>
                <p class="pt-10 fs-18 txt-white opacity-7">+7 996 322 88 00</p>
                <h5 class="txt-white pt-20">Почта:</h5>
                <p class="pt-10 fs-18 txt-white opacity-7">Monument_tmn@mail.ru</p>
                <h5 class="txt-white pt-20">Часы работы:</h5>
                <p class="pt-10 fs-18 txt-white opacity-7">ПН-ВС без перерыва с 09:00 до 18:00</p>
            </div>
            <div class="grid gtc-3 gg-40">
                <div class="flex f-fd-c">
                    <a href="" class="h6 txt-white">Главная</a>
                    <a href="" class="pt-20 ff-inter-200 fs-18 uppercase txt-white opacity-8">Акции</a>
                    <a href="" class="pt-10 ff-inter-200 fs-18 uppercase txt-white opacity-8">О нас</a>
                    <a href="" class="pt-10 ff-inter-200 fs-18 uppercase txt-white opacity-8">Каталог</a>
                    <a href="" class="pt-10 ff-inter-200 fs-18 uppercase txt-white opacity-8">Портфолио</a>
                    <a href="" class="pt-10 ff-inter-200 fs-18 uppercase txt-white opacity-8">Порядок работы</a>
                    <a href="" class="pt-10 ff-inter-200 fs-18 uppercase txt-white opacity-8">Отзывы</a>
                    <a href="" class="pt-10 ff-inter-200 fs-18 uppercase txt-white opacity-8">Акции</a>
                </div>
                <div class="flex f-fd-c">
                    <a href="" class="h6 txt-white">Каталог</a>
                    <a href="" class="pt-20 ff-inter-200 fs-18 uppercase txt-white opacity-8">Взрослые</a>
                    <a href="" class="pt-10 ff-inter-200 fs-18 uppercase txt-white opacity-8">Детские</a>
                    <a href="" class="pt-10 ff-inter-200 fs-18 uppercase txt-white opacity-8">Участникам СВО</a>
                    <a href="" class="pt-10 ff-inter-200 fs-18 uppercase txt-white opacity-8">Со скидкой</a>
                    <a href="" class="pt-10 ff-inter-200 fs-18 uppercase txt-white opacity-8">Премиум</a>
                </div>
                <div class="flex f-fd-c">
                    <a href="" class="h6 txt-white">Контакты</a>
                </div>
            </div>
        </div>
    </div>
    <div class="ptb-40 grid gtc-3 gg-40 g-ai-c">
        <div class="flex gap-40">
            <img src="<? echo get_stylesheet_directory_uri() . '/assets/images/logo-white.png' ?>" alt="">
            <div class="inline-block">
                <p class="h6 txt-white">ООО «МОНУМЕНТ»</p>
                <p class="pt-10 ff-inter-700 fs-18 txt-white uppercase opacity-6"><?php echo date('Y'); ?> г.</p>
            </div>
        </div>
        <div class="flex f-fd-c gap-5">
            <a href="" class="ff-inter-200 fs-18 uppercase txt-white opacity-8">ПОЛЬЗОВАТЕЛЬСКОЕ СОГЛАШЕНИЕ</a>
            <a href="" class="ff-inter-200 fs-18 uppercase txt-white opacity-8">ПУБЛИЧНАЯ ОФЕРТА</a>
        </div>
        <div class="grid gtc-2 gg-40 g-ai-c">
            <div>
                <img src="<? echo get_stylesheet_directory_uri() . '/assets/images/icon-wa.png' ?>" alt="">
                <img src="<? echo get_stylesheet_directory_uri() . '/assets/images/icon-tg.png' ?>" alt="">
            </div>
            <div>
                <a href="" class="btn-alt ff-inter-700">Заказать звонок</a>
            </div>
        </div>
    </div>
    
</footer>
<?php wp_footer(); ?>


</body>
</html>
