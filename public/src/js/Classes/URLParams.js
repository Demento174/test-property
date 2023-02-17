const queryString = require('query-string');

export default class URLParams{


    get_urlParams()
    {
        return queryString.parse(location.search);
    }

    add_urlParams_reload(params={})
    {
        if(Object.keys(params).length <= 0)
        {
            return ;
        }
        let parsed = this.get_urlParams();
        let separator = '?';
        location.search =separator+queryString.stringify(Object.assign(parsed,params));
    }

    add_urlParams(params,reload=false)
    {

        if(Object.keys(params).length <= 0)
        {
            return ;
        }
        if (history.pushState)
        {
            let parsed = this.get_urlParams();

            let separator = '?';

            history.pushState(null, null, separator+queryString.stringify(Object.assign(parsed,params)));

        }else {
            if(!reload)
            {
                console.warn('History API не поддерживается');
            }else
                {
                    this.add_urlParams_reload(params);
                }

        }
    }

    remove_urlParam(param,reload=false) {
        if (history.pushState) {
            let parsed = this.get_urlParams();
            delete parsed[param];
            let separator = '?';

            history.pushState(null, null, separator + queryString.stringify(parsed));

        } else {
            if (!reload) {
                console.warn('History API не поддерживается');
            } else {
                this.add_urlParams_reload(params);
            }
        }
    }

    clearGet()
    {
        window.history.replaceState({}, document.title, "/" + "");
    }

    reloadPageWithoutGET()
    {
        window.location = window.location.href.split("?")[0];

    }

}