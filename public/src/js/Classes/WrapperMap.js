import { Config } from  '../settings';
import AddHandlerForEvent from "../Controllers/AddHandlerForEvent";

export default class WrapperMap {
    constructor()
    {
        this.element = document.querySelector(Config.modules.mapWrapper.selector);
        if(!this.element)
        {
            return;
        }

        this.handler();
    }


    createElement()
    {
        this.wrapper = document.createElement('div');
        this.wrapper.style.position= 'absolute';
        this.wrapper.style.width = '100%';
        this.wrapper.style.height='100%';
        this.wrapper.style.zIndex = '9';
        this.wrapper.style.backgroundColor ='rgba(255,255,255,0.8)';
        this.wrapper.style.color = '#000000';
        this.wrapper.style.textTransform = 'uppercase';
        this.wrapper.style.display = 'flex';
        this.wrapper.style.alignItems = 'center';
        this.wrapper.style.justifyContent = 'center';
        this.wrapper.style.top = '0';
        this.wrapper.style.right = '0';
        this.wrapper.innerHTML = Config.modules.mapWrapper.mapWrapperContent;

        this.element.style.position = 'relative';

        this.element.append(this.wrapper);
    }


    handler()
    {
        this.createElement();
        new AddHandlerForEvent(this.wrapper,'click',()=>{
            this.wrapper.style.display='none';
        })
    }
}