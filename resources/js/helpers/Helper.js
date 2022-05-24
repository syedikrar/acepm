class Helper {
    static getDomain() {
        return process.env.MIX_APP_DOMAIN;
    }
    static readCookie(key, json = false) {
        let cookie = null;
        let match = document.cookie.match(RegExp('(?:^|;\\s*)' + key + '=([^;]*)'));
        match = match ? match[1] : null;

        if (match != null) {
            try {
                cookie = decodeURIComponent(match);
                cookie = (json) ? JSON.parse(JSON.parse(cookie)) : cookie;
            } catch (e) { }
        }
        return cookie;
    }

    static writeCookie(key, val, days, json = false) {
        days = days == undefined ? 7 : days;
        let expires = new Date();
        expires.setTime(expires.getTime()+((24*days)*60*60*1000));
        val = (json) ? JSON.stringify(val) : val;
        document.cookie = key + '=' + encodeURIComponent(val) + '; expires=' + expires + '; path=/;secure';
    }

    static deleteCookie(name) {
        document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    }

    static toLocalTime(dateTime)
    {
        let stillUtc = moment.utc(dateTime).toDate();
        let local = moment(stillUtc).local();
        return local;
    }

    static replaceAll(str,mapObj){
        var re = new RegExp(Object.keys(mapObj).join("|"),"gi");

        return str.replace(re, function(matched){
            return mapObj[matched.toLowerCase()];
        });
    }

    static daysInstalled(user){
        let appInsatlledDate = this.toLocalTime(user.created_at);
        let daysInstalled = moment().diff(moment(appInsatlledDate).startOf('day'), 'days');
        return daysInstalled;
    }

    static getHigherPlan(currentPlan){
        currentPlan = currentPlan.toLowerCase();

        if (currentPlan == 'enterprise') return 'ENTERPRISE';

        let plans = [
            'basic',
            'enterprise'
        ];
        let index = plans.indexOf(currentPlan) + 1;
        return plans[index].toUpperCase();
    }

    static applyRules(rules, routes) {
        for (let i in routes) {
            routes[i].meta = routes[i].meta || {}

            if (!routes[i].meta.rules) {
                routes[i].meta.rules = []
            }
            routes[i].meta.rules.unshift(...rules)

            if (routes[i].children) {
                routes[i].children = Helper.applyRules(rules, routes[i].children)
            }
        }

        return routes
    }

    static debounce (fn, delay) {
        let timeoutID = null
        return function () {
            clearTimeout(timeoutID)
            let args = arguments
            let that = this
            timeoutID = setTimeout(function () {
                fn.apply(that, args)
            }, delay)
        }
    }
}

export default Helper;
