import AddHandlerForEvent from "../Controllers/AddHandlerForEvent";
import { queryElement,getParent,removeClass } from "../common";
import  { settings } from "../settings";

export default class Tabs{
    constructor(selector=null)
    {
        this.settings = settings.selectors.tabs;

        document.querySelectorAll(selector?selector:this.settings.index).forEach(wrapper=>
        {
            this.wrapper = wrapper;


            if(this.wrapper)
            {
                this.links  = this.settings.links;


                this.links.forEach(item=>{

                    new AddHandlerForEvent(item,'click',event=>{

                        event.preventDefault();

                        this.handler(event.target,wrapper);

                    });
                });
            }
        })


    }


    set wrapper(selector)
    {
        this._wrapper = queryElement(selector);
    }

    set links(selector)
    {

        this._links = this.wrapper.querySelectorAll(selector);
    }

    set tabs(selector)
    {
        this._tabs = this.wrapper.querySelectorAll(selector);
    }

    set link(element)
    {

        if(!element.classList.contains('link'))
        {

            this._link = getParent(element,this.settings.links);
        }else
            {
                this._link = element;
            }
    }

    set tab(selector)
    {

        this._tab = queryElement(selector);
    }

    get tab()
    {
        return this._tab;
    }

    get link()
    {
        return this._link;
    }

    get wrapper()
    {
        return this._wrapper;
    }

    get links()
    {
        return this._links;
    }

    get tabs()
    {
        return this._tabs;
    }




    handler(element,wrapper)
    {
        this.wrapper = wrapper;
        this.link = element;
        this.tabs = this.settings.tabs;
        this.tab = this.link.getAttribute('href');

        // removeClass(this.links,settings.active);
        removeClass(this.tabs,this.settings.active);

        // this.link.classList.add(settings.active);
        this.tab.classList.add(this.settings.active);
    }
}