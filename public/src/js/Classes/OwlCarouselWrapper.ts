import 'owl.carousel/dist/assets/owl.carousel.css';
import 'owl.carousel/dist/assets/owl.theme.default.min.css';
import 'owl.carousel';

import $ from "jquery";

export default class OwlCarouselWrapper {
    constructor(elements = {})
    {
        this.settings = elements;
        this.handler();
    }

    set settings (value)
    {
        this._settings = [];

        if(value.length > 0)
            this._settings.push(value);

        carousels.forEach(item=>{
            this._settings.push(item);
        });

    }

    get settings ()
    {
        return this._settings;
    }

    handler()
    {
        this.settings.forEach(item=>{
            if(item.selector)
            {
                if(carouselsConfig.classes.length > 0)
                {
                    carouselsConfig.classes.forEach(className=>
                    {
                        if(document.querySelectorAll(item.selector))
                        {
                            document.querySelectorAll(item.selector).forEach(element=>{
                                element.classList.add(className);
                            });

                        }

                    });
                }
                $(item.selector).owlCarousel(item.options);

            }
        });
    }
}
