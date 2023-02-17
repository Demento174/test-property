import { Config } from  '../settings';
import {queryElement} from "../common";
const settings = Config.modules.heightBlocksInGrid.selectors;

export default class HeightBlocksInGrid {
    constructor(options = [])
    {
        this.grids = options;

        this.grids.forEach(elements=>
        {
            this.handler(elements);
        });
    }

    set grids(value)
    {
        this._grids = [];

        let selectors = value.length === 0?settings:value;

        if(selectors.length !== 0)
        {

            selectors.forEach(item=>
            {
                this._grids.push(document.querySelectorAll(item));
            });
        }

    }

    get grids()
    {
        return this._grids;
    }



    handler(elements)
    {
        let maxHeight = 0;
        elements.forEach(element=>
        {
            if(element.offsetHeight > maxHeight)
            {
                maxHeight = element.offsetHeight;
            }
        });
        elements.forEach(element=>
        {
            element.style.height  = `${maxHeight}px`;
        });
    }
}