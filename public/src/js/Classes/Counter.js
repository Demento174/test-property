import AddHandlerForEvent from "../Controllers/AddHandlerForEvent";

export default class Counter{
    constructor(selectorBlock,step=1)
    {

        document.querySelectorAll(selectorBlock).forEach(block=>
        {

            const blockPosition = block.offsetTop;
            // const windowsHeight = document.documentElement.clientHeight
            let scrollToElem = blockPosition - 30;
            this.step  = step;
            new AddHandlerForEvent(window,'scroll',(e)=>
            {

                let winScrollTop = window.scrollY;

                if(winScrollTop > scrollToElem)
                {
                    block.querySelectorAll('[data-count]').forEach(counter=>
                    {
                        this.__handler(counter)
                    })
                }
            })

        })
    }


    __handler(counter)
    {

        let finish = counter.getAttribute('data-count').includes(',')?parseFloat(counter.getAttribute('data-count').replace(',','.')):Number(counter.getAttribute('data-count'))
        let start = counter.innerText.includes(',')?parseFloat(counter.innerText.replace(',','.')):Number(counter.innerText)

        if(start>=finish)
        {
            return false;
        }

        let time = this.step,
            cc = 1;

        let
            i = finish%1 === 0? 1 :1+ parseFloat((finish%1).toFixed(1)),
            num = finish,
            step = 1000 * time / Number(num),
            // step = 1000 * time ,
            that = counter;

            let int = setInterval(()=> {
                if (i <= num)
                {

                    that.innerHTML=i;
                } else
                    {
                        cc = cc + 1;
                        clearInterval(int);
                    }
                i++;
            }, step);

    }
}