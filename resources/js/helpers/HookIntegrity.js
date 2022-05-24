
import {api} from '~/api'

/**
 * Class to perform hook integrity check on the target shopify store
 * and see if the hook is in place, also used to re inject the hook if not fount.
 * @author Bawa
 */
class HookIntegrity {
    constructor() {
        // ----- vacant for now :)
    }

    check(callback, shop = undefined) {
        //----------For CSUI
        let url = (shop != undefined) ? api.path('integrity.check')+'?csui-shop='+shop : api.path('integrity.check');
        axios.get(url).then(function(response){
            callback(response.data);
        });
    }

    fix(checkResponse, callback, shop = undefined) {
        //----------For CSUI
        let url = (shop != undefined) ? api.path('integrity.fix')+'?csui-shop='+shop : api.path('integrity.fix');
        axios.patch(url, checkResponse).then(function(response){
            callback(response.data);
        });
    }
}

export default HookIntegrity;
