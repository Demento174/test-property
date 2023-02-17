import { Config } from  '../settings';
import AddHandlerForEvent from "../Controllers/AddHandlerForEvent";


const settings = Config.modules.FilterItems;

export default class FilterItems {
    constructor(options = [])
    {
        this.settings = options;

        this.settings.forEach(setting=>
        {
            const elementsControls = document.querySelectorAll(setting.control);
            const elementsItems = document.querySelectorAll(setting.items);
            const bootstrapOption = setting.bootstrap;

            if(elementsControls.length !== 0 || elementsItems.length !== 0 )
            {
                elementsControls.forEach(elementControl=>
                {
                    if(elementControl.classList.contains('active'))
                    {
                        this.switchElements(elementsItems,elementControl.getAttribute('data-cat'),bootstrapOption);
                    }
                    new AddHandlerForEvent(elementControl,'click',()=>
                    {
                           this.handler(elementsControls,elementControl,elementsItems,bootstrapOption);
                    });
                });
            }

        });
    }

    set settings(value)
    {
        this._settings = value.length === 0?settings:value;
    }

    get settings()
    {
        return this._settings;
    }


    switchClass(elements,elementActive,className='active')
    {
        elements.forEach(element=>
        {
            if(element.classList.contains(className))
            {
                element.classList.remove(className);
            }
        });
        elementActive.classList.add(className);
    }

    switchElements(elements,attributeValue,bootstrapOption=false,attributeName='data-cat')
    {

        elements.forEach(element=>
        {

            let displayValue = '';
            if(attributeValue !=='0' && element.getAttribute(attributeName) !== attributeValue)
            {
                displayValue = 'none';
            }

            if(!bootstrapOption)
            {
                element.style.display = displayValue;
            }else
                {
                    element.parentElement.style.display = displayValue;
                }
             console.log(displayValue);
        });
    }

    handler(elementsControls,elementControlActive,elementsItems,bootstrapOption=false,attribute='data-cat')
    {
        this.switchClass(elementsControls,elementControlActive);
        this.switchElements(elementsItems,elementControlActive.getAttribute(attribute),bootstrapOption);
    }
}