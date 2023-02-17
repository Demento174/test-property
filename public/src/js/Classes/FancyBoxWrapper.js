import { fancyBoxConfig } from "../settings";
import AddHandlerForEvent from "../Controllers/AddHandlerForEvent";
import $ from "jquery";
import fancybox from 'fancybox';
import 'fancybox/dist/css/jquery.fancybox.css';

export default class FancyBoxWrapper {
    constructor()
    {
        fancybox($);
        this.selector = fancyBoxConfig.selector;
        this.settings = fancyBoxConfig.options;
        this.elements = this.selector;
        this.galleryElements = fancyBoxConfig.selectorGallery;

        if(this.elements.length > 1)
        {
            this.handler();
        }

        if(this.galleryElements.length > 0)
        {
            this.handlerGallery()
        }

    }


    set elements (value)
    {
        this._elements = document.querySelectorAll(value);
    }

    get elements ()
    {
        return this._elements;
    }

    set galleryElements (selector)
    {
        this._galleryElements = document.querySelectorAll(selector);
    }

    get galleryElements ()
    {
        return this._galleryElements;
    }


    handler()
    {


        this.elements.forEach(item=>{
            new AddHandlerForEvent(item,'click',(e)=>{

                e.preventDefault();
                // this.settings.href=image.src;
                $.fancybox.open(item,this.settings);

            })
        });

    }

    handlerGallery()
    {

        this.galleryElements.forEach(item=>{

            item.querySelectorAll('a').forEach(element=>{

                element.classList.add(fancyBoxConfig.classNameZoom);

                element.setAttribute('data-fancybox',item.getAttribute('data-fancybox-warpper'));
                element.setAttribute('rel', 'gallery');
                element.setAttribute('data-type', 'image');

                new AddHandlerForEvent(element ,'click',e=>{
                    e.preventDefault();
                    $.fancybox.open( $(`[data-fancybox=${item.getAttribute('data-fancybox-warpper')}]`),this.settings);

                });

            });

        });
    }
}