import 'tap.js/tap.js'
export default class AddHandlerForEvent{
    constructor( object, event, callback, useCapture = false)
    {

        this.event = event;
        this.callback = callback;
        this.useCapture = useCapture;

        if(object.length === undefined || object.nodeName === 'SELECT')
        {
            this.object = object;
            this.handler(this.object);
        }else
        {

            object.forEach(element=>
            {
                this.object = element;
                // this.handler(object)
                this.handler(this.object)
            });
        }
    }

    set event(event)
    {
        if(event !== 'click')
        {
            this._event = event;
        }else if(document.documentElement.clientWidth < 520)
        {

            this._event = 'tap'
        }else
        {
            this._event = event;
        }
    }

    get event()
    {
        return this._event;
    }

    set object(object)
    {
        if(document.documentElement.clientWidth < 520)
        {
            new Tap(object);
        }

        this._object = object;
    }

    get object()
    {
        return this._object;
    }

    // handler()
    // {
    //
    //     if (this.object.addEventListener)
    //     {
    //
    //         this.object.addEventListener(this.event, this.callback, this.useCapture);
    //     }
    //     else if (this.object.attachEvent)
    //     {
    //         this.object.attachEvent('on' + this.event, this.callback);
    //     } else
    //     {
    //         console.error("Add handler is not supported");
    //     }
    // }

    handler(object)
    {

        if (object.addEventListener)
        {

            object.addEventListener(this.event, this.callback, this.useCapture);
        }
        else if (object.attachEvent)
        {
            object.attachEvent('on' + this.event, this.callback);
        } else
        {
            console.error("Add handler is not supported");
        }
    }
}
