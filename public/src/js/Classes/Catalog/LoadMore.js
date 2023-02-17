import {Config} from "../../settings";
import {queryElement} from "../../common";
import Products from "./Products";
import AddHandlerForEvent from "../../Controllers/AddHandlerForEvent";
import LoadProducts from "./LoadProducts";
import URLParams from "../URLParams";

const config = Config.modules.loadProducts.selectors.loadMore;

export default class LoadMore
{
    constructor(ProductClass)
    {
        this.btn =config.btn;
        if(!this.btn)
        {
            return ;
        }

        this.productsClass =ProductClass;
        this.urlClass= new URLParams();

        new AddHandlerForEvent(this.btn,'click',(event)=>
        {
            event.preventDefault();
            this.handler();

        })
    }

    set btn(value)
    {
        this._btn = queryElement(value);
    }

    get btn()
    {
        return this._btn;
    }



    handler()
    {

        this.btn.classList.add('load');
        let args = Object.keys(this.urlClass.get_urlParams()).length == 0?{}:this.urlClass.get_urlParams();
        args.ids = this.productsClass.get_ids();

        let LoadProduct = new LoadProducts(
            this.callback,

            args,
            {
                btn:this.btn,
                productClass:this.productsClass
            },);



    }

    callback(jqXHR, textStatus,args ={} )
    {

        if(textStatus == 'success')
        {

            if(JSON.parse(jqXHR.responseText) && JSON.parse(jqXHR.responseText).length == 0)
            {
                args.btn.style.display = 'none';
            }
            args.btn.classList.remove('load');

            args.productClass.addProducts(JSON.parse(jqXHR.responseText));

        }

    }
}