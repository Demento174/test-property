<?php

if(null===get_queried_object()->term_id)
    dd(404);
    //    get_404_template();
else
    get_template_part('Controllers/template-taxonomy');
