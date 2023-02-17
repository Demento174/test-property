export default class Validate{
    constructor(options={})
    {
        if(!options.data)
        {
            return;
        }
        this.result = true;

        this.data = options.data;
        this.invalidClass = !options.invalidClass?'is-invalid':options.invalidClass;

        this.minLengthString = !options.minLengthString?3:options.minLengthString;
        this.maxLengthString = !options.maxLengthString?0:options.maxLengthString;



        if(this.data.length > 1)
        {
           this.bustElements();

        }else if(!this.switchNodeName(this.data))
            {
                this.data.classList.add(this.invalidClass);
                this.result = false;
            }else
                {
                    this.data.classList.remove(this.invalidClass);
                }

    }

    bustElements()
    {
        this.data.forEach((item)=>{
            if(!this.switchNodeName(item))
            {
                item.classList.add(this.invalidClass);
                this.result = false;
            }else
                {
                    item.classList.remove(this.invalidClass);
                }
        });

    }

    switchNodeName(element)
    {
        switch (element.nodeName) {
            case 'INPUT':
                switch (element.type) {
                    case 'text':
                    case 'tel':
                    case 'email':
                    case 'hidden':
                    case 'password':
                    case 'button':
                    case 'reset':
                    case 'submit':
                        if(!this.checkLengthOfString(element.value))
                        {
                            return false;
                        }
                        return true;
                        break;
                    case 'checkbox':
                    case 'ratio':
                        if(!this.checkCheckboxInput(element))
                        {
                            return false;
                        }
                        return true;
                        break;
                    case 'file':
                        return true;
                        break;
                }
                break;
            case 'TEXTAREA':
                if(!this.checkLengthOfString(element.value))
                {
                    return false;
                }
                return true;
                break;
            case 'SELECT':
                switch (element.type) {
                    case 'select-one':
                        if(!this.checkSelect(element))
                        {
                            return false;
                        }
                        return true;
                        break;
                    case 'select-multiple':
                        break;
                }
                break;
            case 'BUTTON':
                switch(element){
                    case 'reset':
                    case 'submit':
                    case 'button':
                    break;
                }
                break;
            default:
                break;
        }
    }

    checkLengthOfString(value)
    {
        if(value.length < this.minLengthString || ( value.length > this.maxLengthString && this.maxLengthString !== 0 ))
        {
            return false;
        }

        return true;
    }

    checkCheckboxInput(element)
    {
        const nameElement = element.getAttribute('name');
        document.querySelectorAll(`[name = "${nameElement}"]`).forEach((item)=>{
            if(item.checked)
            {
                return true;
            }
        });

        return false;
    }

    checkSelect(element)
    {
        if(element.value === '' || element.value === '0' || element.value === 0)
        {
            return false;
        }
        return true;
    }
}
