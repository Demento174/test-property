import {queryElement} from "../common";
import AddHandlerForEvent from "../Controllers/AddHandlerForEvent";
// import xhr from 'xhr';
import {Config} from "../settings";

export default class SearchField
{
    constructor()
    {

        const selectors = Config.modules.searchForm.selectors;
        this.wrapper = selectors.wrapper;
        if(!this.wrapper)
        {
            return ;
        }
        this.typeField = selectors.typeField;
        this.manufacturerField = selectors.manufacturerField;
        this.textField = selectors.textField;
        this.btn = selectors.btn;
        this.close= selectors.close;
        this.response = selectors.response;
        this.responseItem = selectors.responseItem;

        new AddHandlerForEvent(this.btn,'click',event=>
        {
            event.preventDefault();
            this.handler();
        })

        new AddHandlerForEvent(this.close,'click',event=>
        {
            event.preventDefault();
           this.closeResponse();
        });
    }

    set wrapper(selector)
    {
        this._wrapper = queryElement(selector);
    }

    get wrapper()
    {
        return this._wrapper;
    }

    set typeField(selector)
    {
        this._typeField = queryElement(selector);;
    }

    get typeField()
    {
        return this._typeField;
    }

    set manufacturerField(selector)
    {
        this._manufacturerField = queryElement(selector);;
    }

    get manufacturerField()
    {
        return this._manufacturerField;
    }

    set textField(selector)
    {
        this._textField = queryElement(selector);;
    }

    get textField()
    {
        return this._textField;
    }

    set btn(selector)
    {
        this._btn = queryElement(selector);;
    }

    get btn()
    {
        return this._btn;
    }

    set close(selector)
    {
        this._close = queryElement(selector);
    }

    get close()
    {
        return this._close;
    }

    set response(selector)
    {
        this._response = queryElement(selector);
    }

    get response()
    {
        return this._response;
    }

    set responseItem(selector)
    {

        let result  = this.response.querySelectorAll(selector)[0].cloneNode(true);
        result.querySelector('img').src = '';
        result.querySelector('a').innerText ='';
        result.querySelector('a').href ='';
        this._responseItem = result;
    }

    get responseItem()
    {
        return this._responseItem;
    }


    checkValues()
    {
        if(
            this.manufacturerField.value == '0' &&
            this.typeField.value == 0 &&
            this.textField.value.trim() == '' )
        {
            return  false;
        }
        return true;
    }

    closeResponse()
    {
        this.response.style.display = 'none';
    }

    handler()
    {

        if(!this.checkValues())
        {
            return
        }
        let data = {};
        data.action = 'search_form';
        data.type = this.typeField.value!=='0'?this.typeField.value:'';
        data.manufacturer = this.manufacturerField.value!=='0'?this.manufacturerField.value:'';
        data.text = this.textField.value.trim().toLowerCase();

        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            method: "POST",
            data: data,
            dataType:'json',
            complete: (jqXHR, textStatus)=>{

                this.createResponse(JSON.parse(jqXHR.responseText))

            },
        });
    }

    createResponse(items)
    {

        let close = this.response.querySelector('.close').cloneNode(true);
        new AddHandlerForEvent(close,'click',()=>{this.closeResponse()})

        if(items.length == 0)
        {
            this.response.innerText = 'По данному запросу нечего не найдено';

            this.response.append(close);
            this.response.append(this.responseItem.cloneNode(true));
            this.response.style.display='block';
            return ;

        }



        this.response.innerHTML =' ';
        this.response.append(close);
        items.forEach(item=>
        {
            let row = this.responseItem.cloneNode(true);

            row.querySelector('img').src =item.img.url
            row.querySelector('a').innerText =item.title
            row.querySelector('a').href =item.link
            this.response.append(row)
        });

        this.response.style.display='block';
    }

}



