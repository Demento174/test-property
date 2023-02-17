import {Config} from "../../settings";
import {queryElement} from "../../common";
import AddHandlerForEvent from "../../Controllers/AddHandlerForEvent";
import LoadProducts from "./LoadProducts";
import URLParams from "../URLParams";

const selectors = Config.modules.loadProducts.selectors.filters;
const attributes = Config.modules.loadProducts.attributes.filters;
export default class Filters{
    constructor(productClass)
    {
        this.categoryButtons = selectors.categoryButton;

        if(!this.categoryButtons)
        {
            return ;
        }
        this.atteributeValue = attributes.value;
        this.productClass = productClass;
        this.urlCLass = new URLParams();

        this.arg = this.urlCLass.get_urlParams();

        new AddHandlerForEvent(this.categoryButtons,'click',(event)=>
        {
            event.preventDefault();
            if(event.target.classList.contains('active'))
            {
                return ;
            }
            this.switchClassActive(event.target)
            this.handler(event.target.getAttribute(this.atteributeValue));
            this.queryProducts();
        });

    }

    set categoryButtons(selector)
    {
            this._categoryButtons = document.querySelectorAll(selector);
    }

    get categoryButtons()
    {
        return this._categoryButtons;
    }


    addArg(param,value)
    {
        this.arg[param]= value;
    }

    removeArg(param)
    {
        delete this.arg[param]
    }


    addURLparam(args={})
    {

        this.urlCLass.add_urlParams(args);
    }

    getURLparam(parameter)
    {
        return this.urlCLass.get_urlParams()[parameter];
    }


    switchClassActive(li)
    {
        this.categoryButtons.forEach(btn=>
        {
            if(btn.classList.contains('active'))
            {
                btn.classList.remove('active');
            }


        })
        li.classList.add('active');
    }

    handler(value)
    {


        if(value === '0')
        {
            this.removeArg('category')
            this.urlCLass.remove_urlParam('category')
        }else
            {
                this.addArg('category',value)
                this.addURLparam(this.arg)
            }


    }


    queryProducts()
    {

        var args = this.arg;
        delete args['page_id'];
        args['ids']= [];
        new LoadProducts(this.insertProducts,args,{productClass:this.productClass})
        delete args['ids'];

    }

    insertProducts(jqXHR, textStatus,args ={} )
    {
        if(textStatus == 'success')
        {

            args.productClass.insertProducts(JSON.parse(jqXHR.responseText));
        }
    }

}