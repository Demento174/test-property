import {Config} from "../../settings";
const action = Config.modules.loadProducts.action;
export default class LoadProducts{


    constructor(callback,args={},kwargs={})
    {
        this.args = args;
        this.kwargs = kwargs;
        this.callback =  callback;
        this.action = action;

        this.handler();
    }

    set action(value)
    {
        this._action = value;
    }

    get action()
    {
        return this._action;
    }

    set args(value)
    {
        this._args = value;
    }

    get args()
    {
        return this._args;
    }

    set kwargs(value)
    {
        this._kwargs = value;
    }

    get kwargs()
    {
        return this._kwargs;
    }

    set callback(value)
    {
        this._callback = value;
    }

    get callback()
    {
        return this._callback;
    }

    async handler()
    {

        let data={};
        data['args'] = this.args;

        data.action = this.action;

        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            method: "POST",
            data: data,
            complete:(jqXHR, textStatus)=>
            {

                this.callback(jqXHR, textStatus,this.kwargs)
            },
        });

    }

}