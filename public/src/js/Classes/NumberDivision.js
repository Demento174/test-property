import { Config } from "../settings";
const settings = Config.modules.numberDivision;
import { queryElement } from "../common";

export default class NumberDivision {
    constructor(selector='')
    {
        this.elements = selector===''?settings.selector:selector;
        if(this.elements)
        {
            this.elements.forEach(item=>{
                item.innerText = this.handler(item.innerText);
            });
        }
    }

    set elements(selector)
    {
        this._elements = document.querySelectorAll(selector);
    }

    get elements()
    {
        return this._elements;
    }

    handler(numb)
    {
        numb = String(numb);

        switch (numb.length) {
            case 7:
                numb = numb.slice(0, 1) + ' ' + numb.slice(1, 4) + ' ' + numb.slice(4, 20);
                break;

            case 6:
                numb = numb.slice(0, 3) + ' ' + numb.slice(3, 6);
                break;

            case 5:
                numb = numb.slice(0, 2) + ' ' + numb.slice(2, 6);
                break;

            default:

        }

        return numb;
    }
}
