import AddHandlerForEvent from "../Controllers/AddHandlerForEvent";
import { queryElement,getParent,removeClass } from "../common";
import  { Config } from "../settings";
const settings = Config.modules.tabs.selectors;

export default class TabController{
    constructor(selector)
    {
        this.settings = settings;
        this.wrapper = selector;

        if(this.wrapper)
        {
            this.links  = this.settings.links;
            this.tabs = this.settings.tabs;
            this.links.forEach(item=>{

                new AddHandlerForEvent(item,'click',event=>{

                    event.preventDefault();

                    this.handler(event.target);

                });
            });
        }

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
        if(element.nodeName !== 'A')
        {
            this._link = getParent(element,'a');
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




    handler(element)
    {
        this.link = element;
        this.tab = this.link.getAttribute('href');

        removeClass(this.links,settings.active);
        removeClass(this.tabs,settings.active);

        this.link.classList.add(settings.active);
        this.tab.classList.add(settings.active);
    }
}