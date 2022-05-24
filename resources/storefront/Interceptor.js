class Interceptor
{
    /**
     *
     * @param parent
     */
    constructor(parent)
    {
        this.parent = parent;
        this.callbacks = {};
    }

    launch(callbacks)
    {
        let self = this;
        self.initHttpHook();
        self.initFetchHook();
        self.callbacks = callbacks;
    }

    calledByAlly(url){
        return (
            url.indexOf('ref=ug') != -1 ||
            url.indexOf('ref=wa') != -1 ||
            url.indexOf('ref=aov') != -1
        );
    }

    initHttpHook() {
        let self = this;

        (function(open) {
            XMLHttpRequest.prototype.open = function(method, url) {
                if (self.calledByAlly(url)) { open.apply(this, arguments); return true; }
                let request = this;

                request.addEventListener("readystatechange", function readyStateChange() {
                    if (request.readyState === 4) {
                        checkRequestState(request, method, url);
                        request.removeEventListener('readyStateChange', readyStateChange);
                    }
                });

                open.apply(request, arguments);
            };
        })(XMLHttpRequest.prototype.open);

        (function(send) {
            XMLHttpRequest.prototype.send = function() {
                let request = this;
                request['requestData'] = arguments[0];
                send.apply(request, arguments);
            };
        })(XMLHttpRequest.prototype.send);

        function checkRequestState(request, method, url){
            let supportedResponses = ['json', 'document'];
            let respType = request.responseType == '' ? (self.isJson(request.response) ? 'json' : null) : request.responseType;
            let response = respType == 'json' && typeof request.response == 'string' ? JSON.parse(request.response) : request.response;
            if(respType == "blob") readBlob(request, response, method, url);
            else if (request.status === 200 && supportedResponses.indexOf(respType) != -1)  self.resolve(request, response, method, url, 'ajax');
        }

        function readBlob(request, blob, method, url) {
            const reader = new FileReader();

            // This fires after the blob has been read/loaded.
            reader.addEventListener('loadend', (e) => {
                const text = e.target.result;
                let response = JSON.parse(text);
                self.resolve(request, response, method, url, 'blob')
            });

            // Start reading the blob as text.
            reader.readAsText(blob);
        }
    }

    initFetchHook() {
        let self = this;

        const fetch = window.fetch;
        window.fetch = (...args) => (async(args) => {
            let url = args[0];
            let request = args[1];
            let result = await fetch(...args);
            let response = result.clone();

            setTimeout(async function(){
                try {
                    if (request.method == 'GET') {
                        let data = await response.text();
                        response['content'] = data;
                        self.resolve(request, response, request.method, url, 'fetch');
                    } else if (request.method == 'POST') {
                        let data = await response.json();
                        response['content'] = data;
                        self.resolve(request, response, request.method, url, 'fetch');
                    }
                } catch (e) {
                    // TODO - improve later on for the cases where response is null or response is not valid json
                }
            }, 100);
            return result;
        })(args);

    }

    resolve(request, response, method, url, type) {
        let self = this;
        let isATC = (url.indexOf('/cart/add.js') != -1)
            || (url.indexOf('/cart/update.js') != -1)
            || (url.indexOf('/cart/change.js') != -1);

        if (method == 'POST' && url.indexOf('/wallets/checkouts.json') != -1) {
            self.watchBuyNow(request, response, type);
        }
        else if (method == 'POST' && isATC) {
            self.watchAddToCart(request, response, type, url);
        }
        else if (method == 'GET' && (url.indexOf('/cart?') != -1 || url.indexOf('/cart.js') != -1)) {
            self.watchViewCart(request, response, type);
        }
        else if (method == 'POST' && url.endsWith('/cart')) {
            self.watchViewCart(request, response, type);
        }
    }


    watchBuyNow(request, response, type) {
        let self = this;

        try{
            response.clone().json().then(body => process(body));
        }catch(e){
            process(response.content);
        }

        function process(body){
            if ('buyNow' in self.callbacks) self.callbacks.buyNow.apply(self.parent);
        }
    }

    watchViewCart(request, response, type) {
        let self = this;
        try {
            if (type == 'fetch') {
                self.parent.jax('/cart.js', function(resp){ processCart(resp); });
            } else {
                processCart(response);
            }

        } catch (e) { }

        function processCart(response)
        {
            if ('viewCart' in self.callbacks) self.callbacks.viewCart.apply(self.parent);
        }
    }

    watchAddToCart(request, response, type, url) {
        let self = this;
        if ('addToCart' in self.callbacks) self.callbacks.addToCart.apply(self.parent);
    }

    isJson(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

}

export default Interceptor;
