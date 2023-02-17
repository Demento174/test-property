import {queryElement} from "../../common";
import {Config} from "../../settings";
import AddHandlerForEvent from "../../Controllers/AddHandlerForEvent";
const selectors = Config.modules.order.selectors;
const action = Config.modules.order.action;
export default class Order
{

    constructor()
    {
        this.wrapper=selectors.wrapper;

        if(!this.wrapper)
        {
            return ;
        }

        this.args = {};

        this.action = action;

        this.inputCity=selectors.inputCity;

        this.inputStreet=selectors.inputStreet;

        this.inputHouse=selectors.inputHouse;

        this.inputAppartment=selectors.inputAppartment;

        this.inputIndex=selectors.inputIndex;

        this.inputName=selectors.inputName;

        this.inputPhone=selectors.inputPhone;

        this.inputEmail=selectors.inputEmail;

        this.btn = selectors.btn;

        new AddHandlerForEvent(this.btn,'click',(event)=>
        {
            this.handler();
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

    set inputCity(selector)
    {
        this._inputCity = queryElement(selector);
    }

    get inputCity()
    {
        return this._inputCity;
    }

    set inputStreet(selector)
    {
        this._inputStreet = queryElement(selector);
    }

    get inputStreet()
    {
        return this._inputStreet;
    }

    set inputHouse(selector)
    {
        this._inputHouse = queryElement(selector);
    }

    get inputHouse()
    {
        return this._inputHouse;
    }

    set inputAppartment(selector)
    {
        this._inputAppartment = queryElement(selector);
    }

    get inputAppartment()
    {
        return this._inputAppartment;
    }

    set inputIndex(selector)
    {
        this._inputIndex = queryElement(selector);
    }

    get inputIndex()
    {
        return this._inputIndex;
    }

    set inputName(selector)
    {
        this._inputName = queryElement(selector);
    }

    get inputName()
    {
        return this._inputName;
    }

    set inputPhone(selector)
    {
        this._inputPhone = queryElement(selector);
    }

    get inputPhone()
    {
        return this._inputPhone;
    }

    set inputEmail(selector)
    {
        this._inputEmail = queryElement((selector));
    }

    get inputEmail()
    {
        return this._inputEmail;
    }

    set btn(selector)
    {
        this._btn = this.wrapper.querySelector(selector);
    }

    get btn()
    {
        return this._btn;
    }

    set_address(city='',street='',home='',appartment='',index='')
    {
        let response='';
        if(city)
        {
            response+=`Город: ${city} \n`;
        }

        if(street)
        {
            response+=`Улица: ${street} \n`;;
        }

        if(home)
        {
            response+=`Дом: ${home} \n`;;
        }

        if(appartment)
        {
            response+=`Квартира: ${appartment} \n`;;
        }

        if(index)
        {
            response+=`Индекс: ${index} \n`;;
        }

        return response;
    }

    setArgs()
    {

        if(!this.inputPhone.value)
        {
            this.inputPhone.style.border = '1px solid red';
            return ;
        }

        this.args =
            {
                address: this.set_address(this.inputCity.value,
                    this.inputStreet.value,
                    this.inputHouse.value,
                    this.inputAppartment.value,
                    this.inputIndex.value),

                name:this.inputName.value,

                phone:this.inputPhone.value,

                email:this.inputEmail.value,

               action:this.action

            }
    }


    handler()
    {
        this.setArgs();

        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            method: "POST",
            data:this.args,
            complete:(jqXHR, textStatus)=>
            {
                this.wrapper.classList.add('compleate');
                setTimeout(()=>{
                    location.href = '/';
                },10)
            },
        });
    }
}