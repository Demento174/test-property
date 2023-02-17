import { queryElement } from "../common";
import AddHandlerForEvent from "./AddHandlerForEvent";

export default class SwitchingTheClass {
    constructor(elementEvent,elementSwitch,className='active')
    {
        this.elementEvent = elementEvent;
        this.elementSwitch = elementSwitch;
        this.className = className;

        if(this.elementEvent && this.elementSwitch )
        {
            new AddHandlerForEvent(this.elementEvent,'click',(e)=>{
                e.preventDefault();
                this.handler();

            });
        }

    }

    set elementEvent(value)
    {
        this._elementEvent = queryElement(value);
    }

    set elementSwitch(value)
    {
        this._elementSwitch = queryElement(value);
    }


    get elementEvent()
    {
        return this._elementEvent;
    }

    get elementSwitch()
    {
        return this._elementSwitch;
    }

    handler()
    {

        if(this.elementSwitch.classList.contains(this.className))
        {
            this.elementSwitch.classList.remove(this.className);
        }else
            {
                this.elementSwitch.classList.add(this.className);
            }
    }
}