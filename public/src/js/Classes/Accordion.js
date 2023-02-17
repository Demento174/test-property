import {queryElement} from "../common";
import AddHandlerForEvent from "../Controllers/AddHandlerForEvent";

export default class Accordion{
    constructor(wrapper,selectors,options)
    {
         this.wrapper = wrapper;
         if(!this.wrapper)
         {
             return;
         }

         this.selectors = selectors;
         this.options = options;

         this.items = selectors.item;
         this.items.forEach(item=>
         {
             new AddHandlerForEvent(item,'click',()=>
             {
                 this.__handler(item)
             })
         })

    }

    set wrapper(selector)
    {
        this._wrapper = queryElement(selector);
    }

    get wrapper()
    {
        return this._wrapper;
    }

    set items(selector)
    {
        this._items = this.wrapper.querySelectorAll(selector);
    }

    get items()
    {
        return this._items;
    }



    __activeItem(item)
    {
        let title = item.querySelector(this.selectors.title);
        let bodyWrapper = item.querySelector(this.selectors.bodyWrapper);
        let body = item.querySelector(this.selectors.body);
        const bodyHeight = body.clientHeight

        item.classList.add(this.options.activeItem);
        title.classList.add(this.options.activeTitle);
        bodyWrapper.style.height = `${bodyHeight}px`;
    }

    __disableItem(item)
    {
        let title = item.querySelector(this.selectors.title);
        let bodyWrapper = item.querySelector(this.selectors.bodyWrapper);
        // let body = item.querySelector(this.selectors.body);
        // const bodyHeight = body.clientHeight

        item.classList.remove(this.options.activeItem);
        title.classList.remove(this.options.activeTitle);
        bodyWrapper.style.height = '';
    }

    __isActive(item)
    {
        return item.classList.contains(this.options.activeItem);
    }

    __handler(item)
    {
        if(this.__isActive(item))
        {
            this.__disableItem(item)
        }else
            {
                this.items.forEach(item=>this.__disableItem(item))
                this.__activeItem(item)
            }
    }
}