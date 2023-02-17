import { formConfig } from '../settings.js';
import AddHandlerForEvent from "../Controllers/AddHandlerForEvent";
import {getXmlHttp} from "../common";
import Validate from "./Validate";

export default class Forms {
    constructor()
    {
        this.settings =  formConfig;
        this.forms = this.settings.selectors.forms;

        if(this.forms)
        {
            new AddHandlerForEvent(this.forms,'submit',event=>{

                event.preventDefault();

                this.handler(event.target);

            });

        }
    }

    set forms(value)
    {

        this._forms = document.querySelectorAll(value);
    }

    get forms()
    {
        return this._forms;
    }

   async handler(form)
    {
        const arr = [];
        const XML = getXmlHttp();
        const inputs = form.querySelectorAll('input');

        arr.push.apply(arr,inputs);
        const textarea = form.querySelectorAll('textarea');
        arr.push.apply(arr,textarea);
        const button = form.querySelector(this.settings.selectors.send);
        const responseWrapper = form.querySelector(this.settings.selectors.response);

        let data = new FormData();
        let type = '';


        data.append('action',this.settings.action);


        arr.forEach(item=>{

            if(item.hasAttribute('data-name'))
            {
                if(item.required == true && new Validate({data:item}).result === false)
                {
                    console.log(item);
                    throw 'Input value Error';
                }

                let title  = !item.hasAttribute('data-title')?item.getAttribute('placeholder'):item.getAttribute('data-title');
                let name  = item.getAttribute('data-name');
                let value = item.type === 'file'?item.files[0]:item.value;

                if(name === 'type')
                {
                    data.append('type',item.value);
                    type = item.value;
                }else if(name !== 'exception')
                    {
                        data.append(name,item.type === 'file'?value:JSON.stringify({title:title,value:value}))
                    }


            }else
                {
                    console.log(item);

                }

        });



        XML.onreadystatechange = ()=> {

            if (XML.readyState == 4)
            {
                console.log(XML.statusText);
                if(XML.status == 200) {
                    if(type === 'createOrder')
                    {
                        document.location.href = '/'
                    }else
                        {
                            this.response(+XML.responseText,button,responseWrapper);
                        }
                }
            }
        };
        XML.open('POST', ajax.url, true);

        XML.send(data);
    }

    response(responseText,button,responseWrapper)
    {
        console.log(`Ответ сервера: ${responseText}`);

        button.style.display = 'none';
        responseWrapper.style.display = 'flex';
    }
}
