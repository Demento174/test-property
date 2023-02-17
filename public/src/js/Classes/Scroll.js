import AddHandlerForEvent from "./AddHandlerForEvent";

export default class Scroll{
    constructor(selectorBtn,speed)
    {
        document.querySelectorAll(selectorBtn).forEach(btn=>
        {
            if(!btn.hasAttribute('data-target'))
            {
                throw 'scroll btn dont have important attribute';
            }else if(!document.querySelector(btn.getAttribute('data-target')))
                {
                    throw 'scroll btn directly to undefined element';
                }

            new AddHandlerForEvent(btn,'click',(e)=>
            {
                e.preventDefault();
                this.__handler(speed,document.querySelector(btn.getAttribute('data-target')))
            });
        });
    }

    __handler(speed,target)
    {

        target.scrollIntoView({
            behavior: speed,
            block: 'nearest'
        })
    }

}