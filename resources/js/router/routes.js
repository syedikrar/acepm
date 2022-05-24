import {owner, limbo} from "./owner";
import guest from "./guest";
import seller from "./seller";
import Helper from "../helpers/Helper";

export default [
    {
        path: '*',
        redirect: {name: 'index'}
    },
    ...Helper.applyRules(['guest'], guest),
    ...Helper.applyRules(['owner', 'limbo'], limbo),
    ...Helper.applyRules(['owner', 'paid'], owner),
    ...Helper.applyRules(['seller'], seller)
]
