<?php
dd(get_queried_object());
if(null===get_queried_object()->term_id)
    get_404_template();


renderBlock('blocks/intro',
    [
        'title'=>get_field('title')
    ]);