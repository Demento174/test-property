export function prevAll(element) {
    let result = [];

    while (element = element.previousElementSibling)
    {
        result.push(element);
    }

    return result;
}

export function nextAll(element) {
    let result = [];

    while (element = element.nextElementSibling)
    {
        result.push(element);
    }

    return result;
}


export function queryElement(selectorOrElement) {
    let element;
    if(typeof selectorOrElement === 'string')
    {
        element = document.querySelector(selectorOrElement)
    }else
    {
        element = selectorOrElement;
    }
    return element;
}

export function getParent(elemSelector, parentSelector) {
    const elem = queryElement(elemSelector);
    const parents = document.querySelectorAll(parentSelector);

    for (let i = 0; i < parents.length; i++) {
        const parent = parents[i];

        if (parent.contains(elem)) {
            return parent;
        }
    }

    return null;
}

export function removeClass(elements,className)
{
    elements.forEach(item=>{
        if(item.classList.contains(className))
        {

            item.classList.remove(className);
        }
    });
}


export function getXmlHttp(){
    let xmlhttp;

    try
    {

        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");

    }
    catch (e)
    {
        try
        {

            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

        }
        catch (E)
        {

            xmlhttp = false;
        }
    }

    if (!xmlhttp && typeof XMLHttpRequest!='undefined')
    {
        xmlhttp = new XMLHttpRequest();
    }

    return xmlhttp;

}


export function arrayBreakdown(array,size)
{
    let result = [];
    for (let i = 0; i <Math.ceil(array.length/size); i++){
        result[i] = array.slice((i*size), (i*size) + size);
    }
    return result;
}

export function convertNodeListToArray(nodeList) {

    let array = [];

    for (let i = nodeList.length >>> 0; i--;) {
        array[i] = nodeList[i];
    }

    return array;
}