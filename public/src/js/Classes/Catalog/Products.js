import {Config} from "../../settings";
import {queryElement} from "../../common";
import AddHandlerForEvent from "../../Controllers/AddHandlerForEvent";
import Basket from "../WC/Basket";
const config = Config.modules.loadProducts.selectors.product
const attributes = Config.modules.basket.attributes;

export default class Products
{
    constructor()
    {
        this.wrapper = config.wrapper;
        this.productClone = config.item;
        this.products = config.item;
        this.attributeId=attributes.id;
        this.btnMore = Config.modules.loadProducts.selectors.loadMore.btn;
    }

    set wrapper(selector)
    {

        this._wrapper = queryElement(selector);
    }

    get wrapper()
    {
        return this._wrapper;
    }

    set productClone(selector)
    {
        if(this.wrapper)
        {

            this._productClone = this.wrapper.querySelector(selector).cloneNode(true);
        }else
            {
                this._productClone =false;
            }
    }

    get productClone()
    {
        return this._productClone;
    }

    set products(selector)
    {
        if(this.wrapper)
        {
            this._products = this.wrapper.querySelectorAll(selector);
        }else
            {
                this._products=[];
            }

    }

    get products()
    {
        return this._products;
    }

    set btnMore(selector)
    {
        this._btnMore = queryElement(selector);
    }

    get btnMore()
    {
        return this._btnMore;
    }

    get_count_products()
    {
        this.products = config.item;
        return this.products.length;


    }

    get_ids()
    {
        this.products = config.item;

        let result = [];
        this.products.forEach(product=>
        {
            result.push(product.getAttribute('data-id'));
        });
        return result;
    }

    insertProducts(data)
    {
        if(!this.wrapper)
        {
            return ;
        }
        this.wrapper.innerHTML='';

        data.forEach(item=>
        {
            let product = this.productClone.cloneNode(true);
            let element = this.productAssembly(product,item)
            this.wrapper.appendChild(element);
        })
        if(this.btnMore)
        {

            this.btnMore.style.display = 'flex';
        }

        // new Basket();
    }

    addProducts(data)
    {
        if(!this.wrapper)
        {
            return;
        }
            data.forEach(item=>
        {
            let product = this.productClone.cloneNode(true);
            let element = this.productAssembly(product,item)
            this.wrapper.appendChild(element);
        })
        // new Basket();
    }
    productAssembly(element,data)
    {
        element.setAttribute('data-id',data.id);

        element.querySelector(config.card.shortcard_thumb_link).href=data.link;

        element.querySelector(config.card.shortcard_thumb_img).src=data.img.url;
        element.querySelector(config.card.shortcard_thumb_img).alt=data.img.alt;

        element.querySelector(config.card.title).innerText= data.title;
        element.querySelector(config.card.title).href= data.link;


        element.querySelector(config.card.attributeWrapper).innerHTML = '';
        data.attributes.forEach(attribute=>
        {

                let li = document.createElement('li');
                li.innerText = `${attribute.title} : ${attribute.value}`;
                element.querySelector(config.card.attributeWrapper).appendChild(li);

        });





        element.querySelector(config.card.priceSupplier).innerText = data.price_supplier;

        element.querySelector(config.card.price).innerText = data.price;

        return element;

    }



}