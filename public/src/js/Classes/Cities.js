import {Config} from "../settings";

import AddHandlerForEvent from "../Controllers/AddHandlerForEvent";
import {queryElement} from "../common";

import Cookies from 'js-cookie'

const selectors = Config.modules.changeCity.selectors;
const attributes = Config.modules.changeCity.attributes;
export default class Cities{
    constructor()
    {
        this.addressElements = selectors.address;
        this.phoneElements = selectors.phone;
        this.emailElements = selectors.email;
        this.cities = selectors.selectCity;
        this.citiesLabel = selectors.cityLabel;

        this.closeWindowButton = selectors.closeWindowButton;

        this.mapWrapper = selectors.mapWrapper;

        this.attributeAddress=attributes.address;
        this.attributePhone=attributes.phone;
        this.attributeEmail=attributes.email;
        this.attributeMapX=attributes.mapX;
        this.attributeMapY=attributes.mapY;
        this.attributeMapCaption=attributes.mapCaption;

        this.cookiesClass= Cookies;

        new AddHandlerForEvent(this.cities,'click',(event)=>
        {
            const values  = this.settersValues(event.target);

            this.handler(values.title,values.address,values.phone,values.email,values.mapX,values.mapY,values.caption)
        })

        if(this.cookiesClass.get('city') !== undefined)
        {
            this.loadCityHandler(this.cookiesClass.get('city'));
        }
    }

    set addressElements(selector)
    {
        this._addressElements = document.querySelectorAll(selector);
    }

    get addressElements()
    {
        return this._addressElements;
    }

    set phoneElements(selector)
    {
        this._phoneElements = document.querySelectorAll(selector);
    }

    get phoneElements()
    {
        return this._phoneElements;
    }

    set emailElements(selector)
    {
        this._emailElements = document.querySelectorAll(selector);
    }

    get emailElements()
    {
        return this._emailElements;
    }

    set cities(selector)
    {
        this._cities = document.querySelectorAll(selector);
    }

    get cities()
    {
        return this._cities;
    }

    set closeWindowButton(selector)
    {
        this._closeWindowButton = document.querySelector(selector);
    }

    get closeWindowButton()
    {
        return this._closeWindowButton;
    }

    set citiesLabel(selector)
    {
        this._citiesLabel = document.querySelectorAll(selector);
    }

    get citiesLabel()
    {
        return this._citiesLabel;
    }

    set mapWrapper(selector)
    {
        this._mapWrapper = queryElement(selector);
    }

    get mapWrapper()
    {
        return this._mapWrapper;
    }

    set currentCity(title)
    {
        let result = false;
        this.cities.forEach(city=>
        {
            if(city.innerText === title)
            {
                result = city;
            }
        })
        this._currentCity = result;
    }

    get currentCity()
    {
        return this._currentCity;
    }

    set_address(value)
    {
        this.addressElements.forEach(item=>
        {
            item.innerHTML = value;
        });
    }

    set_phone(value)
    {
        this.phoneElements.forEach(item=>
        {
            item.href=`tel:${value}`;
            item.innerHTML = value;
        });
    }

    set_email(value)
    {
        this.emailElements.forEach(item=>
        {
            item.href=`mailto:${value}`;
            item.innerHTML = value;
        });
    }

    set_citiesLabel(title)
    {
        this.citiesLabel.forEach(city=>
        {
            city.innerHTML = title;
        })
    }


    set_map(x,y,caption)
    {
        this.mapWrapper.innerHTML = '';
        let myMap, myPlacemark;
        ymaps.ready(()=>{
            myMap = new ymaps.Map("map", {
                center: [x, y],
            zoom: 16,
                controls: ['fullscreenControl', 'zoomControl']

        });
            myPlacemark = new ymaps.Placemark([x, y], {
                hintContent: caption,
                    balloonContent: caption
            }, {
                iconLayout: 'default#image',
                    iconImageHref: '/wp-content/themes/template/public/img/placemark.svg',
                    iconImageSize: [33, 36],
                    iconImageOffset: [-16.5, -36]
            });
            myMap.geoObjects.add(myPlacemark);
            myMap.behaviors.disable('scrollZoom');
        });
    }

    save_city(city)
    {
        this.cookiesClass.set('city',city)
    }

    settersValues(element)
    {
        return {address : element.getAttribute(this.attributeAddress),
            phone : element.getAttribute(this.attributePhone),
            email : element.getAttribute(this.attributeEmail),
            mapX : element.getAttribute(this.attributeMapX),
            mapY : element.getAttribute(this.attributeMapY),
            caption : element.getAttribute(this.attributeMapCaption),
            title : element.innerHTML};

    }


    setters(title,address,phone,email,mapX,mapY,caption)
    {
        if(this.addressElements.length > 0)
        {
            this.set_address(address);
        }
        if(this.phoneElements.length > 0)
        {
            this.set_phone(phone);
        }
        if(this.emailElements.length > 0)
        {
            this.set_email(email);
        }
        if(this.citiesLabel.length > 0)
        {
            this.set_citiesLabel(title)
        }
        if(this.mapWrapper)
        {

            this.set_map(mapX,mapY,caption)
        }
    }



    handler(title,address,phone,email,mapX,mapY,caption)
    {
        const event = new Event('click');

        this.closeWindowButton.dispatchEvent(event)


        this.setters(title,address,phone,email,mapX,mapY,caption);

        this.save_city(title);
    }

    loadCityHandler(city)
    {
        this.currentCity = city
        if(this.currentCity)
        {
            const values = this.settersValues(this.currentCity);

            this.setters(values.title,values.address,values.phone,values.email,values.mapX,values.mapY,values.caption)
        }
    }
}