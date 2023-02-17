import 'bootstrap/dist/js/bootstrap.min';
import 'bootstrap/dist/css/bootstrap.min.css';
import '../less/style.less';
import * as $ from 'jquery';
import 'owl.carousel/dist/assets/owl.carousel.css';
import 'owl.carousel';


$('.carousel_1 .owl-carousel').owlCarousel({
    loop:true,
    margin:20,
    nav:true,
    responsive:{
        0:{
            items:1
        },
        600:{
            items:2
        },
        1000:{
            items:4
        }
    }
})

$('.carousel_2 .owl-carousel').owlCarousel({
    loop:true,
    margin:20,
    dots:false,
    nav:true,
    responsive:{
        0:{
            items:1
        },
        600:{
            items:2
        },
        1000:{
            items:3
        }
    }
})
let input = document.querySelector('input.hidden')
if(input)
{
    input.value = document.querySelector('.unit_reference span').innerText;
}